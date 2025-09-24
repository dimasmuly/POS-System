<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'total_price',
        'discount_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($orderItem) {
            if ($orderItem->product) {
                $orderItem->product_name = $orderItem->product->name;
                $orderItem->product_sku = $orderItem->product->sku;
                $orderItem->unit_price = $orderItem->product->price;
            }
            
            $orderItem->total_price = ($orderItem->unit_price * $orderItem->quantity) - $orderItem->discount_amount;
        });

        static::updating(function ($orderItem) {
            $orderItem->total_price = ($orderItem->unit_price * $orderItem->quantity) - $orderItem->discount_amount;
        });
    }
}