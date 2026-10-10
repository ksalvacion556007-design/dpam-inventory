<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'contact_person',
        'contact_number',
        'email',
        'address',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | PURCHASE ORDERS
    |--------------------------------------------------------------------------
    */

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPLIER BRANDS
    |--------------------------------------------------------------------------
    */

    public function brands()
    {
        return $this->hasMany(SupplierBrand::class);
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_supplier'
        )->withTimestamps();
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
    | ACTIVE SUPPLIER SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where(
            'suppliers.status',
            'active'
        );
    }
}