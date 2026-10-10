<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            $table->string('customer_name');

            $table->string('customer_contact')->nullable();

            $table->date('order_date');

            /*
             * pending_inventory_check
             *     Waiting for inventory checking.
             *
             * confirmed
             *     Stock is reserved for the order.
             *
             * partially_fulfilled
             *     Some quantity has already been released.
             *
             * fulfilled
             *     Everything has been released.
             *
             * for_purchasing
             *     Available stock is insufficient.
             *
             * cancelled
             *     Order was cancelled or voided.
             */
            $table->enum('status', [
                'pending_inventory_check',
                'confirmed',
                'partially_fulfilled',
                'fulfilled',
                'for_purchasing',
                'cancelled',
            ])->default('pending_inventory_check');

            /*
             * Inventory checking result.
             */
            $table->enum('inventory_check_status', [
                'pending',
                'available',
                'insufficient',
            ])->default('pending');

            $table->foreignId('inventory_checked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('inventory_checked_at')
                ->nullable();

            $table->text('inventory_check_notes')
                ->nullable();

            /*
             * Owner's decision for open orders.
             */
            $table->enum('owner_decision', [
                'confirmed',
                'for_purchasing',
                'cancelled',
            ])->nullable();

            $table->text('notes')->nullable();

            /*
             * User who created/recorded the order.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Prevent duplicate checkout submissions.
             *
             * The same checkout token can only create one order.
             */
            $table->string('checkout_token', 100)
                ->nullable()
                ->unique();

            /*
             * Whole-order void information.
             *
             * Voiding does not delete the order.
             * It records the reversal.
             */
            $table->timestamp('voided_at')
                ->nullable();

            $table->foreignId('voided_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('void_reason')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};