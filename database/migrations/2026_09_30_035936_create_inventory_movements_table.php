<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------------------
            |
            | The product affected by the inventory movement.
            |
            */

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | SYSTEM USER WHO RECORDED THE MOVEMENT
            |--------------------------------------------------------------------------
            |
            | Identifies the employee/user who performed the transaction.
            |
            | Example:
            | Owner
            | Secretary
            |
            */

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | MOVEMENT TYPE
            |--------------------------------------------------------------------------
            |
            | stock_in:
            |     Actual goods received from a supplier.
            |
            | stock_out:
            |     Actual goods released/delivered to a customer.
            |
            | adjustment:
            |     Difference between system stock and actual physical count.
            |
            */

            $table->enum('movement_type', [
                'stock_in',
                'stock_out',
                'adjustment',
            ]);


            /*
            |--------------------------------------------------------------------------
            | ACTUAL TRANSACTION DATE
            |--------------------------------------------------------------------------
            |
            | This is the actual date when the inventory transaction happened.
            |
            | Stock In:
            |     Date the supplier actually delivered/DPAM received the goods.
            |
            | Stock Out:
            |     Date the products were actually released/delivered to
            |     the customer and the receipt was issued.
            |
            | Adjustment:
            |     Date the physical stock count/adjustment was performed.
            |
            | This is separate from created_at.
            |
            | created_at = date/time the record was entered into the system.
            | transaction_date = actual date of the inventory transaction.
            |
            */

            $table->date('transaction_date');


            /*
            |--------------------------------------------------------------------------
            | QUANTITY
            |--------------------------------------------------------------------------
            |
            | Stock In:
            |     Positive quantity.
            |
            | Stock Out:
            |     Negative quantity.
            |
            | Adjustment:
            |     Positive or negative difference.
            |
            */

            $table->integer('quantity');


            /*
            |--------------------------------------------------------------------------
            | STOCK BALANCE
            |--------------------------------------------------------------------------
            |
            | Running inventory balance.
            |
            | stock_before:
            |     Quantity before the transaction.
            |
            | stock_after:
            |     Quantity after the transaction.
            |
            | These values allow the Stock Card to display the running
            | "STOCK ON HAND" balance shown in the physical DPAM Stock Card.
            |
            */

            $table->integer('stock_before');

            $table->integer('stock_after');


            /*
            |--------------------------------------------------------------------------
            | STOCK CARD - SUPPLIER / CUSTOMER
            |--------------------------------------------------------------------------
            |
            | The physical DPAM Stock Card has a:
            |
            |     Supplier - Customer
            |
            | column.
            |
            | This stores the name involved in the actual transaction.
            |
            | Stock In:
            |     Supplier name.
            |
            | Stock Out:
            |     Customer name.
            |
            | A text snapshot is used so historical Stock Card records remain
            | understandable even if supplier/customer information changes later.
            |
            */

            $table->string('supplier_customer')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | STOCK CARD - UNIT COST
            |--------------------------------------------------------------------------
            |
            | Used mainly for Stock In / Receive transactions.
            |
            | This corresponds to the "Cost" column of the physical Stock Card.
            |
            */

            $table->decimal('unit_cost', 12, 2)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | STOCK CARD - UNIT SELLING PRICE
            |--------------------------------------------------------------------------
            |
            | Used mainly for Stock Out / Sale transactions.
            |
            | This corresponds to the "Price" column of the physical Stock Card.
            |
            */

            $table->decimal('unit_price', 12, 2)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | STOCK CARD - AMOUNT
            |--------------------------------------------------------------------------
            |
            | Used mainly for Stock Out / Sale transactions.
            |
            | Example:
            |
            | Quantity = 2
            | Unit Price = 5,500
            |
            | Amount = 11,000
            |
            | This corresponds to the "Amount" column of the physical Stock Card.
            |
            */

            $table->decimal('amount', 12, 2)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | GENERAL REASON
            |--------------------------------------------------------------------------
            |
            | Examples:
            |
            | Stock In:
            |     Supplier delivery
            |
            | Stock Out:
            |     Customer delivery / release
            |
            | Adjustment:
            |     Physical count adjustment
            |
            */

            $table->string('reason')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | CUSTOMER ORDER REFERENCE
            |--------------------------------------------------------------------------
            |
            | Used mainly for Stock Out.
            |
            | This connects the actual customer delivery/release
            | to the Customer Order.
            |
            | IMPORTANT:
            | Customer Order creation does NOT create this movement.
            |
            */

            $table->string('customer_order_reference')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | RECEIPT NUMBER
            |--------------------------------------------------------------------------
            |
            | Used mainly for Stock Out.
            |
            | This is the same Receipt Number that will appear in:
            |
            | Inventory → Stock Out → Receipt Number
            |
            | The Stock Card will display this value as:
            |
            | Receipt Number
            |
            | This replaces the need for a separate "Inv. No." field.
            |
            */

            $table->string('receipt_number')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | RECEIVED BY
            |--------------------------------------------------------------------------
            |
            | Name of the person who received the delivered products.
            |
            | Used mainly for Stock Out / customer delivery.
            |
            */

            $table->string('received_by')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | GENERAL REFERENCE
            |--------------------------------------------------------------------------
            |
            | Used for supporting documents/references.
            |
            | Examples:
            |
            | Stock In:
            |     PO-2026-0001
            |
            | Adjustment:
            |     Count Sheet
            |
            */

            $table->string('reference')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            |
            | created_at:
            |     When the movement was recorded in the system.
            |
            | updated_at:
            |     When the movement record was last updated.
            |
            | NOTE:
            | transaction_date is the actual business transaction date.
            |
            */

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};