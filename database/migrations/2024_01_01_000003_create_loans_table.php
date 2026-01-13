<?php
// Migration for the loans table used by the BTS app.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('borrower_id');
            $table->unsignedBigInteger('item_id');
            $table->string('loan_date');
            $table->string('due_date');
            $table->string('returned_at')->nullable();
            $table->string('status')->default('open');
            $table->string('return_condition')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('borrower_id')->references('id')->on('borrowers');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
