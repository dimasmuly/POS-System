<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'user', 'orderItems.product']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,digital_wallet,bank_transfer',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // Create the order
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'user_id' => $request->user()->id,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'paid_amount' => $request->paid_amount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'subtotal' => 0,
                'total_amount' => 0,
                'change_amount' => 0,
            ]);

            $subtotal = 0;

            // Add order items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock availability
                if (!$product->isInStock() || 
                    ($product->track_inventory && $product->stock_quantity < $item['quantity'])) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $discountAmount = $item['discount_amount'] ?? 0;
                $totalPrice = ($product->price * $item['quantity']) - $discountAmount;
                $subtotal += $totalPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $totalPrice,
                    'discount_amount' => $discountAmount,
                ]);
            }

            // Update order totals
            $order->subtotal = $subtotal;
            $order->total_amount = $subtotal + $order->tax_amount - $order->discount_amount;
            $order->change_amount = max(0, $order->paid_amount - $order->total_amount);
            $order->save();

            // Complete the order if payment is sufficient
            if ($order->paid_amount >= $order->total_amount) {
                $order->complete();
            }

            $order->load(['customer', 'user', 'orderItems.product']);

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order,
            ], 201);
        });
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'user', 'orderItems.product']);
        return response()->json($order);
    }

    public function complete(Request $request, Order $order)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,digital_wallet,bank_transfer',
        ]);

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Order cannot be completed',
            ], 400);
        }

        $order->update([
            'paid_amount' => $request->paid_amount,
            'payment_method' => $request->payment_method,
        ]);

        $order->calculateTotals();
        $order->save();

        if ($request->paid_amount >= $order->total_amount) {
            $order->complete();
        }

        $order->load(['customer', 'user', 'orderItems.product']);

        return response()->json([
            'message' => 'Order completed successfully',
            'order' => $order,
        ]);
    }

    public function cancel(Order $order)
    {
        if (!$order->cancel()) {
            return response()->json([
                'message' => 'Order cannot be cancelled',
            ], 400);
        }

        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => $order,
        ]);
    }

    public function dailySales(Request $request)
    {
        $date = $request->get('date', today());
        
        $sales = Order::completed()
            ->whereDate('completed_at', $date)
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(discount_amount) as total_discount')
            )
            ->first();

        return response()->json($sales);
    }

    public function salesSummary(Request $request)
    {
        $period = $request->get('period', 'today');
        
        $query = Order::completed();
        
        switch ($period) {
            case 'today':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
        }
        
        $summary = $query->select(
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_sales'),
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('SUM(discount_amount) as total_discount'),
            DB::raw('AVG(total_amount) as average_order_value')
        )->first();

        return response()->json($summary);
    }
}