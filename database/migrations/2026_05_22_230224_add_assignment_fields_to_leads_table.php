<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('registered_by_user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->after('registered_by_user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('intent')
                ->default('buy')
                ->after('interest_type')
                ->index();

            $table->string('interest_target_type')
                ->default('general')
                ->after('intent')
                ->index();

            $table->foreignId('development_id')
                ->nullable()
                ->after('interest_target_type')
                ->index();

            $table->foreignId('listing_id')
                ->nullable()
                ->after('development_id')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'registered_by_user_id',
                'assigned_to_user_id',
                'intent',
                'interest_target_type',
                'development_id',
                'listing_id',
            ]);
        });
    }
};
