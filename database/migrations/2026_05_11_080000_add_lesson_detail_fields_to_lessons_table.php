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
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('important_note_title')->nullable()->after('content');
            $table->text('important_note_text')->nullable()->after('important_note_title');
            $table->json('key_terms')->nullable()->after('important_note_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn([
                'important_note_title',
                'important_note_text',
                'key_terms',
            ]);
        });
    }
};
