<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helper', function (Blueprint $table) {
            $table->id(); // unique() は不要（PKで一意）
            $table->char('helpername', 20);
            $table->integer('wearableno')->default(0);
            $table->integer('facilityno')->default(0);
        
            // ← ここで確実に作成
            $table->unsignedBigInteger('groupno')->nullable();
        
            $table->char('position', 1);
            $table->integer('backpainno')->default(0);
            // char に数値 default は型警告の元なので、文字として '0' を推奨
            $table->char('age', 3)->default('0');
            $table->char('sex', 3)->default('0');
        
            $table->char('pic1', 1)->nullable();
            $table->char('pic2', 1)->nullable();
            $table->char('pic3', 1)->nullable();
            $table->char('pic4', 1)->nullable();
            $table->char('pic5', 1)->nullable();
        
            $table->char('delflag', 1)->nullable();
            $table->char('jobfrom', 4)->nullable();
            $table->char('jobto', 4)->nullable();
            $table->char('measufrom', 8)->nullable();
            $table->char('measuto', 8)->nullable();
        
            $table->timestamp('insdatetime')->nullable()->useCurrent();
            $table->integer('insuserno');
            $table->timestamp('upddatetime')->nullable()->useCurrentOnUpdate();
            $table->integer('upduserno')->nullable();
        
            // FK を後で貼るなら、ここでの index は省略可（FKが自動でindex作るため）
            // 残す場合は重複indexに注意
            // $table->index('groupno', 'helper_groupno_idx');
            $table->index('facilityno', 'helper_facilityno_idx');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // テーブルごと落とせば FK も一緒に削除されます
        Schema::dropIfExists('helper');
    }
}
