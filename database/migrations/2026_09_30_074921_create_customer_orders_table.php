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
             * confirmed
             * partially_fulfilled
             * fulfilled
             * for_purchasing
             * cancelled
             *
             * pending_inventory_check:
             * Order has been recorded and is waiting for
             * the Secretary to check inventory availability.
             *
             * confirmed:
             * Secretary confirmed sufficient availability
             * and the Owner confirmed the customer order.
             *
             * partially_fulfilled:
             * Some requested quantity has already been
             * physically released through Stock Out.
             *
             * fulfilled:
             * All requested quantities have been physically
             * released through Stock Out.
             *
             * for_purchasing:
             * Available inventory is insufficient and the
             * Owner decides that purchasing is required.
             *
             * cancelled:
             * The customer order will not proceed.
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
             * Result of the Secretary inventory check.
             */
            $table->enum('inventory_check_status', [
                'pending',
                'available',
                'insufficient',
            ])->default('pending');

            /*
             * Secretary who performed the inventory check.
             */
            $table->foreignId('inventory_checked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Date and time when the inventory was checked.
             */
            $table->timestamp('inventory_checked_at')
                ->nullable();

            /*
             * Notes recorded during the inventory check.
             */
            $table->text('inventory_check_notes')
                ->nullable();

            /*
             * Owner's decision after the inventory check.
             */
            $table->enum('owner_decision', [
                'confirmed',
                'for_purchasing',
                'cancelled',
            ])->nullable();

            /*
             * Additional customer order notes.
             */
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