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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_account_id')
                ->constrained('accounts')
                ->onDelete('cascade');
            $table->foreignId('to_account_id')
                ->constrained('accounts')
                ->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED'])
                ->default('PENDING');
            $table->string('reference')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
