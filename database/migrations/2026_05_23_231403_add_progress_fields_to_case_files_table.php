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
        Schema::table('case_files', function (Blueprint $table) {
            $table->unsignedTinyInteger('documents_progress_percent')
                ->default(0)
                ->after('description')
                ->index();

            $table->unsignedInteger('pending_documents_count')
                ->default(0)
                ->after('documents_progress_percent')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            //
        });
    }
};
