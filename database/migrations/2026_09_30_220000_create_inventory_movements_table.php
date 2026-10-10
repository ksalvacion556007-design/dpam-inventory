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
             * Product affected by the movement.
             */
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * User who recorded the movement.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Customer order related to the movement.
             *
             * RESTRICT is intentional so historical inventory movements
             * do not lose their customer-order relationship.
             */
            $table->foreignId('customer_order_id')
                ->nullable()
                ->constrained('customer_orders')
                ->restrictOnDelete();

            /*
             * Inventory movement types.
             *
             * opening_balance:
             *     Initial stock entered when a product is created.
             *     This establishes the beginning balance.
             *
             * stock_in:
             *     Actual supplier delivery received into inventory.
             *
             * stock_out:
             *     Actual customer order/release that deducts stock.
             *
             * return:
             *     Returned goods.
             *
             * damaged:
             *     Goods removed from usable inventory because they
             *     are damaged or unusable.
             *
             * adjustment:
             *     Physical inventory adjustment.
             *
             * void_reversal:
             *     Stock restored because a previous stock-out was voided.
             */
            $table->enum('movement_type', [
                'opening_balance',
                'stock_in',
                'stock_out',
                'return',
                'damaged',
                'adjustment',
                'void_reversal',
            ]);

            /*
             * Actual business transaction date.
             */
            $table->date('transaction_date');

            /*
             * Quantity affected by the movement.
             *
             * Examples:
             *
             * opening_balance = +100
             * stock_in        = +10
             * stock_out       = -5
             * return          = +2
             * damaged         = -2
             * adjustment      = +2 / -2
             * void_reversal   = +5
             */
            $table->integer('quantity');

            /*
             * Running inventory balance.
             *
             * stock_before:
             *     Balance immediately before this movement.
             *
             * stock_after:
             *     Balance immediately after this movement.
             */
            $table->integer('stock_before');

            $table->integer('stock_after');

            /*
             * Stock card supplier/customer snapshot.
             */
            $table->string('supplier_customer')
                ->nullable();

            /*
             * Supplier purchase cost.
             *
             * Used mainly for supplier Stock In.
             */
            $table->decimal('unit_cost', 12, 2)
                ->nullable();

            /*
             * Customer selling price.
             */
            $table->decimal('unit_price', 12, 2)
                ->nullable();

            /*
             * Quantity × applicable cost/price.
             */
            $table->decimal('amount', 12, 2)
                ->nullable();

            /*
             * Reason for the movement.
             */
            $table->string('reason')
                ->nullable();

            /*
             * Snapshot of customer order number.
             *
             * The actual relationship is customer_order_id.
             */
            $table->string('customer_order_reference')
                ->nullable();

            /*
             * Receipt associated with the transaction.
             */
            $table->string('receipt_number')
                ->nullable();

            /*
             * Person who received or released the goods.
             */
            $table->string('received_by')
                ->nullable();

            /*
             * Additional reference.
             *
             * Examples:
             * PO number
             * Batch number
             * Adjustment reference
             */
            $table->string('reference')
                ->nullable();

            /*
             * For void_reversal:
             * points to the original inventory movement.
             */
            $table->foreignId('reversal_of_id')
                ->nullable()
                ->constrained('inventory_movements')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * Useful indexes for Inventory Activity / Stock Card.
             */
            $table->index([
                'product_id',
                'transaction_date',
            ]);

            $table->index([
                'movement_type',
                'transaction_date',
            ]);

            $table->index('customer_order_reference');

            $table->index('receipt_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};