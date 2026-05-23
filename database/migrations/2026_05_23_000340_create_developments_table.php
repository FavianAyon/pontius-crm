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
        Schema::create('developments', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();
            $table->string('slug')->unique();

            $table->string('status')->default('active')->index();
            $table->string('sales_status')->default('pre_sale')->index();

            $table->string('location')->nullable();
            $table->text('description')->nullable();

            $table->integer('total_units')->nullable();
            $table->integer('available_units')->nullable();

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
        Schema::dropIfExists('developments');
    }
};
