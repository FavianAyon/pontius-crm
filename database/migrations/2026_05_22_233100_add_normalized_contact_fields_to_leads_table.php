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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('normalized_email')->nullable()->index()->after('email');
            $table->string('normalized_phone')->nullable()->index()->after('phone');
            $table->string('normalized_whatsapp')->nullable()->index()->after('whatsapp');

            $table->boolean('is_duplicate')->default(false)->index();
            $table->foreignId('duplicate_of_lead_id')
                ->nullable()
                ->constrained('leads')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            //
        });
    }
};
