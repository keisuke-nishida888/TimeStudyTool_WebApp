<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('group_id'); // プライマリキー
            $table->string('group_name', 100)->nullable(false); // グループ名
            $table->unsignedBigInteger('facilityno'); // 外部キー

            // 外部キー制約（facilityテーブルのidを参照）
            $table->foreign('facilityno')
                  ->references('id')
                  ->on('facility')
                  ->onDelete('cascade');

            // タイムスタンプ（作成日・更新日）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}