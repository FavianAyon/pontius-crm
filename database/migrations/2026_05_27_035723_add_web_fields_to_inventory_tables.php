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
        Schema::table('listings', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->index();
            $table->string('public_status')->default('draft')->index();

            $table->text('description_es')->nullable();
            $table->text('description_en')->nullable();

            $table->string('seo_title_es')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->text('seo_description_es')->nullable();
            $table->text('seo_description_en')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });

        Schema::table('developments', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->index();
            $table->string('public_status')->default('draft')->index();

            $table->text('description_es')->nullable();
            $table->text('description_en')->nullable();

            $table->string('seo_title_es')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->text('seo_description_es')->nullable();
            $table->text('seo_description_en')->nullable();

            $table->string('developer_name')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('construction_status')->nullable();
        });

        Schema::table('development_units', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->index();
            $table->string('public_status')->default('draft')->index();

            $table->string('unit_type')->nullable();
            $table->string('orientation')->nullable();

            $table->text('description_es')->nullable();
            $table->text('description_en')->nullable();

            $table->string('seo_title_es')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->text('seo_description_es')->nullable();
            $table->text('seo_description_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_tables', function (Blueprint $table) {
            //
        });
    }
};
