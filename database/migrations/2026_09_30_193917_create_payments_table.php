<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_order_id')
                ->constrained('customer_orders')
                ->restrictOnDelete();

            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Every payment/receipt gets a unique receipt number.
             */
            $table->string('receipt_number')
                ->unique();

            $table->date('payment_date');

            $table->enum('payment_method', [
                'cash',
                'gcash',
                'maya',
                'bank_transfer',
                'check',
                'pdc',
                'credit',
            ]);

            $table->decimal('amount', 12, 2);

            /*
             * paid:
             *     Cash and completed electronic payments.
             *
             * pending:
             *     Check/PDC awaiting clearing.
             *
             * cleared:
             *     Previously pending payment that cleared.
             *
             * unpaid:
             *     Credit/utang.
             *
             * voided:
             *     Payment cancelled through a void operation.
             */
            $table->enum('status', [
                'paid',
                'pending',
                'cleared',
                'unpaid',
                'voided',
            ])->default('paid');

            $table->string('reference_number')
                ->nullable();

            $table->string('check_number')
                ->nullable();

            $table->string('bank_name')
                ->nullable();

            $table->date('check_date')
                ->nullable();

            $table->date('maturity_date')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};