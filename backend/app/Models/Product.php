<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'barcode',
        'category_id',
        'price',
        'cost',
        'stock_quantity',
        'low_stock_threshold',
        'unit',
        'image',
        'is_active',
        'track_inventory',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
        'track_inventory' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= low_stock_threshold')
                    ->where('track_inventory', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($query) {
            $query->where('track_inventory', false)
                  ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function isLowStock(): bool
    {
        return $this->track_inventory && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isInStock(): bool
    {
        return !$this->track_inventory || $this->stock_quantity > 0;
    }

    public function decreaseStock(int $quantity): bool
    {
        if (!$this->track_inventory) {
            return true;
        }

        if ($this->stock_quantity >= $quantity) {
            $this->stock_quantity -= $quantity;
            return $this->save();
        }

        return false;
    }

    public function increaseStock(int $quantity): bool
    {
        if (!$this->track_inventory) {
            return true;
        }

        $this->stock_quantity += $quantity;
        return $this->save();
    }
}