<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_id',
        'processed_by',
        'receipt_number',
        'payment_date',
        'payment_method',
        'amount',
        'status',
        'reference_number',
        'check_number',
        'bank_name',
        'check_date',
        'maturity_date',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'check_date' => 'date',
        'maturity_date' => 'date',
    ];

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}