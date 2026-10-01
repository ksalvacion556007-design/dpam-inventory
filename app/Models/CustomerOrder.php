<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_contact',
        'order_date',
        'status',
        'inventory_check_status',
        'inventory_checked_by',
        'inventory_checked_at',
        'inventory_check_notes',
        'owner_decision',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'inventory_checked_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Customer Order Items
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | User Who Created the Order
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | User Who Checked Inventory
    |--------------------------------------------------------------------------
    */

    public function inventoryCheckedBy()
    {
        return $this->belongsTo(
            User::class,
            'inventory_checked_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}