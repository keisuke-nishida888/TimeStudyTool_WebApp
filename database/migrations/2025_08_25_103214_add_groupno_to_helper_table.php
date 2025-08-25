<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
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
