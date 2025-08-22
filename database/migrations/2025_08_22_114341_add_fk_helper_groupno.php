<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkHelperGroupno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('helper', function (Blueprint $table) {
            // groups が既にある状態で FK を追加
            $table->foreign('groupno')
                  ->references('group_id')->on('groups')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('helper', function (Blueprint $table) {
            $table->dropForeign(['groupno']);
        });
    }
    
}
