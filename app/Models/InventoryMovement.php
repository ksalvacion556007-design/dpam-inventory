<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | PRODUCT / USER
        |--------------------------------------------------------------------------
        */

        'product_id',
        'user_id',


        /*
        |--------------------------------------------------------------------------
        | INVENTORY MOVEMENT
        |--------------------------------------------------------------------------
        */

        'movement_type',
        'transaction_date',
        'quantity',
        'stock_before',
        'stock_after',


        /*
        |--------------------------------------------------------------------------
        | STOCK CARD DETAILS
        |--------------------------------------------------------------------------
        */

        'supplier_customer',
        'unit_cost',
        'unit_price',
        'amount',


        /*
        |--------------------------------------------------------------------------
        | REFERENCES / TRANSACTION DETAILS
        |--------------------------------------------------------------------------
        */

        'reason',
        'customer_order_reference',
        'receipt_number',
        'received_by',
        'reference',

    ];


    protected $casts = [

        'transaction_date' => 'date',

        'quantity' => 'integer',

        'stock_before' => 'integer',

        'stock_after' => 'integer',

        'unit_cost' => 'decimal:2',

        'unit_price' => 'decimal:2',

        'amount' => 'decimal:2',

    ];


    /*
    |--------------------------------------------------------------------------
    | PRODUCT
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    /*
    |--------------------------------------------------------------------------
    | USER WHO RECORDED THE MOVEMENT
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}