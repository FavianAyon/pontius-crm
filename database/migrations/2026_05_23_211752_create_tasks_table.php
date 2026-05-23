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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('type')->default('general')->index();
            $table->string('status')->default('open')->index();
            $table->string('priority')->default('normal')->index();

            $table->timestamp('due_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
