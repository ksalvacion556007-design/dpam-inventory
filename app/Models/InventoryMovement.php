<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'customer_order_id',

        'movement_type',
        'transaction_date',

        'quantity',
        'stock_before',
        'stock_after',

        'supplier_customer',

        'unit_cost',
        'unit_price',
        'amount',

        'reason',

        'customer_order_reference',
        'receipt_number',
        'received_by',
        'reference',

        'reversal_of_id',
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customerOrder()
    {
        return $this->belongsTo(
            CustomerOrder::class
        );
    }

    /**
     * Original movement that this movement reverses.
     */
    public function reversalOf()
    {
        return $this->belongsTo(
            self::class,
            'reversal_of_id'
        );
    }

    /**
     * Reversal movement generated from this movement.
     */
    public function reversal()
    {
        return $this->hasOne(
            self::class,
            'reversal_of_id'
        );
    }
}