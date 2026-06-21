<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 12, 2);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->date('target_date')->nullable();
            $table->date('start_date')->nullable();
            $table->string('color', 7)->default('#10b981');
            $table->string('icon', 32)->nullable();
            $table->boolean('is_completed')->default(false);
            $table->date('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_completed']);
        });

        Schema::create('goal_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('contribution_date');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('goal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_contributions');
        Schema::dropIfExists('goals');
    }
};