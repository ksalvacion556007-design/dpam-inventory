<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'purchase_order_id',
        'batch_number',
        'received_date',
        'expiration_date',
        'best_before_date',
        'quantity_received',
        'quantity_remaining',
        'unit_cost',
        'status',
    ];

    protected $casts = [
        'received_date' => 'date',
        'expiration_date' => 'date',
        'best_before_date' => 'date',
        'quantity_received' => 'integer',
        'quantity_remaining' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getExpirationStatusAttribute(): string
    {
        if ($this->expiration_date === null) {
            return 'NO EXPIRATION';
        }

        if ($this->expiration_date->isPast()) {
            return 'EXPIRED';
        }

        if ($this->expiration_date->lte(now()->addDays(30))) {
            return 'EXPIRING SOON';
        }

        return 'VALID';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date !== null
            && $this->expiration_date->isPast();
    }

    public function getIsDepletedAttribute(): bool
    {
        return (int) $this->quantity_remaining <= 0;
    }
}