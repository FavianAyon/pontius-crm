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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('development_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title')->index();
            $table->string('slug')->unique();

            $table->string('status')->default('available')->index();
            $table->string('listing_type')->default('sale')->index();

            $table->string('property_type')->nullable()->index();

            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency')->default('USD');

            $table->string('location')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();

            $table->text('description')->nullable();

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
        Schema::dropIfExists('listings');
    }
};
