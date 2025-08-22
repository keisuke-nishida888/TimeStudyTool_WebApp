<?php

// database/migrations/2025_08_21_184250_add_groupno_columns_to_facility_and_helper.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // helper に groupno 追加（facility 側はもう追加しない）
        if (!Schema::hasColumn('helper', 'groupno')) {
            Schema::table('helper', function (Blueprint $table) {
                $table->unsignedBigInteger('groupno')->nullable()->after('facilityno');
                $table->index('groupno');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('helper', 'groupno')) {
            Schema::table('helper', function (Blueprint $table) {
                $table->dropIndex(['groupno']);
                $table->dropColumn('groupno');
            });
        }
    }
};

