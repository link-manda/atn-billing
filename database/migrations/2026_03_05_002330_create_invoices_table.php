<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();

            $table->foreignId('subscription_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('clinic_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('amount', 12, 2);

            $table->decimal('tax', 12, 2)->default(0);

            $table->decimal('total', 12, 2);

            $table->string('status')->default('draft');

            $table->date('due_date');

            $table->timestamp('paid_at')->nullable();

            $table->string('pdf_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
