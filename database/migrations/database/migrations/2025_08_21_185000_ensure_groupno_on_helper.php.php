<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('helper') && !Schema::hasColumn('helper', 'groupno')) {
            Schema::table('helper', function (Blueprint $table) {
                // MySQL なら after を付けてもよい（任意）
                if (DB::getDriverName() === 'mysql') {
                    $table->unsignedBigInteger('groupno')->nullable()->after('facilityno');
                } else {
                    $table->unsignedBigInteger('groupno')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('helper') && Schema::hasColumn('helper', 'groupno')) {
            Schema::table('helper', function (Blueprint $table) {
                $table->dropColumn('groupno');
            });
        }
    }
};
