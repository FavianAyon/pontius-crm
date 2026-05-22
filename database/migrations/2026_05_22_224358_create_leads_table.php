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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            // Datos principales
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            // Contacto
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('whatsapp')->nullable()->index();
            // Origen del lead
            $table->string('source')->nullable()->index();
            $table->string('campaign')->nullable();
            $table->string('medium')->nullable();
            // Interés comercial
            $table->string('interest_type')->nullable()->index();
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->string('preferred_location')->nullable();
            $table->string('preferred_language')->default('es');
            // Pipeline
            $table->string('status')->default('new')->index();
            $table->string('priority')->default('normal')->index();
            // Seguimiento
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable()->index();
            // Notas
            $table->text('notes')->nullable();
            // Para automatizaciones futuras
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
        Schema::dropIfExists('leads');
    }
};
