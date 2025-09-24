<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'total_spent',
        'total_orders',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'total_spent' => 'decimal:2',
        'total_orders' => 'integer',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function completedOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function updateTotals(): void
    {
        $completedOrders = $this->completedOrders;
        
        $this->update([
            'total_orders' => $completedOrders->count(),
            'total_spent' => $completedOrders->sum('total_amount'),
        ]);
    }
}