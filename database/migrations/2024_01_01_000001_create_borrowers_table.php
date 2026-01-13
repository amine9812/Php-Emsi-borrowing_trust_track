<?php
// Migration for the borrowers table used by the BTS app.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowers', function (Blueprint $table): void {
            $table->id();
            $table->text('name');
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->integer('trust_score')->default(100);
            $table->string('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
