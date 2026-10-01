<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function customerOrderItems()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }
}