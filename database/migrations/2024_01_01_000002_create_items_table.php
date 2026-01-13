<?php
// Migration for the items table used by the BTS app.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();
            $table->text('name');
            $table->text('category')->nullable();
            $table->text('serial')->nullable();
            $table->text('notes')->nullable();
            $table->integer('is_active')->default(1);
            $table->string('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
