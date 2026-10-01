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
             * Customer order workflow:
             *
             * pending_inventory_check
             * inventory_checked
             * confirmed
             * for_purchasing
             * ready_for_delivery
             * partially_fulfilled
             * delivered
             * fulfilled
             * cancelled
             */
            $table->enum('status', [
                'pending_inventory_check',
                'inventory_checked',
                'confirmed',
                'for_purchasing',
                'ready_for_delivery',
                'partially_fulfilled',
                'delivered',
                'fulfilled',
                'cancelled',
            ])->default('pending_inventory_check');

            /*
             * Result of the Secretary inventory check.
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

            $table->timestamp('inventory_checked_at')->nullable();

            $table->text('inventory_check_notes')->nullable();

            /*
             * Owner's decision after inventory checking.
             */
            $table->enum('owner_decision', [
                'confirmed',
                'for_purchasing',
                'cancelled',
            ])->nullable();

            $table->text('notes')->nullable();

            /*
             * User who created the customer order.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};