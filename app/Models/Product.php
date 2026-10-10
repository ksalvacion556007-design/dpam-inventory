<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'brand',
        'category_id',
        'api',
        'base_oil',
        'unit',
        'package_size',
        'unit_price',
        'reorder_level',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'reorder_level' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /*
    |--------------------------------------------------------------------------
    | CURRENT INVENTORY
    |--------------------------------------------------------------------------
    */

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    /*
    |--------------------------------------------------------------------------
    | INVENTORY MOVEMENTS
    |--------------------------------------------------------------------------
    */

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /*
    |--------------------------------------------------------------------------
    | INVENTORY BATCHES
    |--------------------------------------------------------------------------
    */

    public function inventoryBatches()
    {
        return $this->hasMany(InventoryBatch::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPLIERS
    |--------------------------------------------------------------------------
    |
    | A product can have many suppliers.
    |
    | Supplier status does NOT change the product's status.
    |
    */

    public function suppliers()
    {
        return $this->belongsToMany(
            Supplier::class,
            'product_supplier'
        )->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    public function customerOrderItems()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SELLABLE PRODUCTS
    |--------------------------------------------------------------------------
    */

    public function scopeSellable($query)
    {
        return $query->where('status', 'active');
    }

    /*
    |--------------------------------------------------------------------------
    | RESERVED STOCK
    |--------------------------------------------------------------------------
    */

    public function scopeWithReservedTotal($query)
    {
        return $query->withSum([
            'customerOrderItems as reserved_total' => function ($q) {
                $q->whereHas('customerOrder', function ($order) {
                    $order->whereIn(
                        'status',
                        [
                            'confirmed',
                            'partially_fulfilled',
                        ]
                    );
                });
            },
        ], 'reserved_quantity');
    }

    /*
    |--------------------------------------------------------------------------
    | RESERVED QUANTITY
    |--------------------------------------------------------------------------
    */

    public function reservedQuantity(
        ?int $excludeOrderId = null
    ): int {
        return (int) $this->customerOrderItems()
            ->when(
                $excludeOrderId,
                function ($query) use ($excludeOrderId) {
                    $query->where(
                        'customer_order_id',
                        '!=',
                        $excludeOrderId
                    );
                }
            )
            ->whereHas(
                'customerOrder',
                function ($order) {
                    $order->whereIn(
                        'status',
                        [
                            'confirmed',
                            'partially_fulfilled',
                        ]
                    );
                }
            )
            ->sum('reserved_quantity');
    }

    /*
    |--------------------------------------------------------------------------
    | STOCK STATUS
    |--------------------------------------------------------------------------
    */

    public static function stockStatusFor(
        int $currentStock,
        int $reorderLevel
    ): string {
        if ($currentStock <= 0) {
            return 'OUT OF STOCK';
        }

        if ($currentStock <= $reorderLevel) {
            return 'LOW STOCK';
        }

        return 'IN STOCK';
    }

    public function getStockStatusAttribute(): string
    {
        return self::stockStatusFor(
            (int) ($this->inventory?->current_stock ?? 0),
            (int) $this->reorder_level
        );
    }

    /*
    |--------------------------------------------------------------------------
    | AVAILABLE STOCK
    |--------------------------------------------------------------------------
    */

    public function getAvailableStockAttribute(): int
    {
        $currentStock = (int) (
            $this->inventory?->current_stock ?? 0
        );

        $reserved = array_key_exists(
            'reserved_total',
            $this->attributes
        )
            ? (int) $this->attributes['reserved_total']
            : $this->reservedQuantity();

        return max(
            0,
            $currentStock - $reserved
        );
    }
}