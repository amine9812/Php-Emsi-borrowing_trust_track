<?php
// Migration for the trust_events table used by the BTS app.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trust_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('borrower_id');
            $table->unsignedBigInteger('loan_id')->nullable();
            $table->string('event_type');
            $table->integer('points_delta');
            $table->text('reason');
            $table->string('created_at');

            $table->foreign('borrower_id')->references('id')->on('borrowers');
            $table->foreign('loan_id')->references('id')->on('loans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trust_events');
    }
};
