<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            $table->string('po_number')
                ->unique();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            /*
             * Optional link to the customer order that caused
             * purchasing to be necessary.
             */
            $table->foreignId('customer_order_id')
                ->nullable()
                ->constrained('customer_orders')
                ->nullOnDelete();

            $table->date('po_date');

            /*
             * draft:
             *     PO being prepared.
             *
             * pending:
             *     Waiting for approval/submission.
             *
             * approved:
             *     Approved and waiting for supplier delivery.
             *
             * partially_received:
             *     Some goods have been received.
             *
             * received:
             *     All ordered goods have been received.
             *
             * cancelled:
             *     PO cancelled.
             */
            $table->enum('status', [
                'draft',
                'pending',
                'approved',
                'cancelled',
                'partially_received',
                'received',
            ])->default('draft');

            $table->text('notes')
                ->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};