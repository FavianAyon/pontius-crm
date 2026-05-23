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
        Schema::create('development_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('development_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('unit_number')->index();
            $table->string('slug')->unique();

            $table->string('flexmls_id')->nullable()->index();

            $table->string('status')->default('available')->index();

            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency')->default('USD');

            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();

            $table->string('floor')->nullable();
            $table->string('view_type')->nullable();

            $table->text('description')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['development_id', 'unit_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_units');
    }
};
