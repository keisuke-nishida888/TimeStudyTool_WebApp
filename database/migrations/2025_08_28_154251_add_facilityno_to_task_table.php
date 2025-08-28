<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // すでに列がある環境に配慮
        if (!Schema::hasColumn('task_table', 'facilityno')) {
            Schema::table('task_table', function (Blueprint $table) {
                // 施設IDの型に合わせる（多くは unsignedBigInteger）
                $table->unsignedBigInteger('facilityno')
                      ->nullable()               // 既存データ移行のため最初は nullable
                      ->after('task_category_no')
                      ->index();

                // 外部キー（施設テーブル名は実環境に合わせて）
                $table->foreign('facilityno')
                      ->references('id')
                      ->on('facility')
                      ->cascadeOnUpdate()
                      ->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('task_table', 'facilityno')) {
            Schema::table('task_table', function (Blueprint $table) {
                // 先にFKを落とす
                $table->dropForeign(['facilityno']);
                $table->dropColumn('facilityno');
            });
        }
    }
};
