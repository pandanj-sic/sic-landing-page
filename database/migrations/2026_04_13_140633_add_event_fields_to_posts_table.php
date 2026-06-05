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
        Schema::table('posts', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('metadata');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('location')->nullable()->after('end_date');
            $table->string('type')->default('article')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'location']);
            $table->string('type')->default('post')->change();
        });
    }
};
