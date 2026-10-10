<?php

namespace App\Services;

use App\Models\DocumentSequence;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate the next document number.
     *
     * Examples:
     * ORD-2026-0001
     * REC-2026-0001
     *
     * IMPORTANT:
     * This method intentionally does NOT start its own DB transaction.
     *
     * SalesService already runs the operation inside a transaction,
     * so the sequence increment becomes part of that same transaction.
     * If the sale/order fails, the sequence increment rolls back too.
     */
    public function next(
        string $key,
        string $prefix,
        int $pad = 4,
        ?int $year = null
    ): string {
        $year ??= (int) now()->year;

        /*
         * Create the sequence row if it does not exist yet.
         */
        DB::table('document_sequences')->insertOrIgnore([
            'key' => $key,
            'year' => $year,
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
         * Lock the sequence row so two simultaneous transactions
         * cannot receive the same document number.
         */
        $sequence = DocumentSequence::query()
            ->where('key', $key)
            ->where('year', $year)
            ->lockForUpdate()
            ->firstOrFail();

        /*
         * If the sequence is still zero, check existing records.
         *
         * This protects the system if records already existed before
         * the document_sequences table was introduced.
         */
        if ((int) $sequence->last_number === 0) {
            $highest = $this->highestExisting(
                $key,
                $prefix,
                $year
            );

            if ($highest > 0) {
                $sequence->last_number = $highest;
            }
        }

        $sequence->last_number =
            (int) $sequence->last_number + 1;

        $sequence->save();

        return sprintf(
            '%s-%d-%0' . $pad . 'd',
            $prefix,
            $year,
            $sequence->last_number
        );
    }

    /**
     * Find the highest existing number for a document type/year.
     */
    private function highestExisting(
        string $key,
        string $prefix,
        int $year
    ): int {
        [$table, $column] = match ($key) {
            'customer_order' => [
                'customer_orders',
                'order_number',
            ],

            'receipt' => [
                'payments',
                'receipt_number',
            ],

            default => [
                null,
                null,
            ],
        };

        if (!$table || !$column) {
            return 0;
        }

        $highest = 0;

        $numbers = DB::table($table)
            ->where(
                $column,
                'like',
                "{$prefix}-{$year}-%"
            )
            ->pluck($column);

        foreach ($numbers as $number) {
            $number = (string) $number;

            $position = strrpos($number, '-');

            if ($position === false) {
                continue;
            }

            $sequenceNumber = (int) substr(
                $number,
                $position + 1
            );

            $highest = max(
                $highest,
                $sequenceNumber
            );
        }

        return $highest;
    }
}