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

        'checkout_token',

        'voided_at',
        'voided_by',
        'void_reason',
    ];

    protected $casts = [
        'order_date' => 'date',
        'inventory_checked_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(
            CustomerOrderItem::class
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventoryCheckedBy()
    {
        return $this->belongsTo(
            User::class,
            'inventory_checked_by'
        );
    }

    public function voidedBy()
    {
        return $this->belongsTo(
            User::class,
            'voided_by'
        );
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(
            InventoryMovement::class
        );
    }

    /**
     * Total order amount.
     */
    public function getTotalAmountAttribute(): float
    {
        return round(
            (float) $this->items->sum('subtotal'),
            2
        );
    }

    /**
     * Payments that have actually been collected.
     *
     * Paid and cleared payments count toward collected amount.
     */
    public function getCollectedAmountAttribute(): float
    {
        return round(
            (float) $this->payments
                ->whereIn(
                    'status',
                    [
                        'paid',
                        'cleared',
                    ]
                )
                ->sum('amount'),
            2
        );
    }

    /**
     * Pending check/PDC amounts.
     */
    public function getPendingAmountAttribute(): float
    {
        return round(
            (float) $this->payments
                ->where(
                    'status',
                    'pending'
                )
                ->sum('amount'),
            2
        );
    }

    /**
     * Amount still unpaid/uncleared.
     */
    public function getBalanceAmountAttribute(): float
    {
        if ($this->status === 'cancelled') {
            return 0.0;
        }

        return round(
            max(
                0,
                $this->total_amount
                    - $this->collected_amount
            ),
            2
        );
    }

    /**
     * Amount that can still be newly paid.
     *
     * Pending payments are excluded because they are already
     * committed against the order.
     */
    public function getPayableAmountAttribute(): float
    {
        if ($this->status === 'cancelled') {
            return 0.0;
        }

        return round(
            max(
                0,
                $this->total_amount
                    - $this->collected_amount
                    - $this->pending_amount
            ),
            2
        );
    }

    /**
     * Overall payment status.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        if (
            $this->total_amount > 0
            && $this->balance_amount <= 0.004
        ) {
            return 'paid';
        }

        if ($this->collected_amount > 0) {
            return 'partial';
        }

        if ($this->pending_amount > 0) {
            return 'pending';
        }

        return 'unpaid';
    }

    /**
     * Total quantity still not fulfilled.
     */
    public function getRemainingQuantityAttribute(): int
    {
        if ($this->status === 'cancelled') {
            return 0;
        }

        return (int) $this->items->sum(
            function ($item) {
                return max(
                    0,
                    (int) $item->quantity
                    - (int) $item->fulfilled_quantity
                );
            }
        );
    }
}