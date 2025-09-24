<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'user_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'change_amount',
        'status',
        'payment_method',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->orderItems->sum('total_price');
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->change_amount = max(0, $this->paid_amount - $this->total_amount);
    }

    public function complete(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Update inventory for each item
        foreach ($this->orderItems as $item) {
            $item->product->decreaseStock($item->quantity);
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update customer totals if customer exists
        if ($this->customer) {
            $this->customer->updateTotals();
        }

        return true;
    }

    public function cancel(): bool
    {
        if (!in_array($this->status, ['pending', 'completed'])) {
            return false;
        }

        // If order was completed, restore inventory
        if ($this->status === 'completed') {
            foreach ($this->orderItems as $item) {
                $item->product->increaseStock($item->quantity);
            }
        }

        $this->update(['status' => 'cancelled']);

        // Update customer totals if customer exists
        if ($this->customer) {
            $this->customer->updateTotals();
        }

        return true;
    }
}