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
            Schema::create('publish_profiles', function (Blueprint $table) {
                $table->id();
                $table->morphs('publishable');
                $table->string('language', 5)->default('es')->index();
                $table->string('seo_title')->nullable();
                $table->text('seo_description')->nullable();
                $table->string('og_title')->nullable();
                $table->text('og_description')->nullable();
                $table->text('public_description')->nullable();
                $table->text('ai_summary')->nullable();
                $table->json('keywords')->nullable();
                $table->json('structured_data_json')->nullable();
                $table->json('api_payload')->nullable();
                $table->unsignedTinyInteger('content_score')->default(0)->index();
                $table->timestamp('generated_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->unique(['publishable_type', 'publishable_id', 'language'], 'publish_profiles_unique');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publish_profiles');
    }
};
