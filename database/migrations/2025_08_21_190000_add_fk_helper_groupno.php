<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        // helper, groups が既に存在し、かつ groupno 列がある場合のみ
        if (Schema::hasTable('helper') && Schema::hasTable('groups') && Schema::hasColumn('helper', 'groupno')) {
            Schema::table('helper', function (Blueprint $table) {
                // 既にFKがある環境に二重適用しないよう try/catch でも可
                $table->foreign('groupno', 'fk_helper_groupno')
                      ->references('group_id')->on('groups')
                      ->cascadeOnUpdate()
                      ->nullOnDelete(); // 親削除でNULLに
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('helper')) {
            Schema::table('helper', function (Blueprint $table) {
                // FK名は上で付けた明示名
                $table->dropForeign('fk_helper_groupno');
            });
        }
    }
};
