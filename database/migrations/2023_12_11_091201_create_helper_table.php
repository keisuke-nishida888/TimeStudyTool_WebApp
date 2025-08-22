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
            $table->id()->unique()->nullable(false);
        
            $table->char('helpername', 20)->nullable(false);
            $table->integer('wearableno')->default(0);
            $table->integer('facilityno')->default(0);
        
            // groupsへの参照は「カラム定義のみに」する（FKはここに書かない）
            $table->unsignedBigInteger('groupno')->nullable();
        
            $table->char('position', 1)->nullable(false);
            $table->integer('backpainno')->default(0);
            $table->char('age', 3)->default(0);
            $table->char('sex', 3)->default(0);
        
            $table->char('pic1', 1)->nullable()->default(null);
            $table->char('pic2', 1)->nullable()->default(null);
            $table->char('pic3', 1)->nullable()->default(null);
            $table->char('pic4', 1)->nullable()->default(null);
            $table->char('pic5', 1)->nullable()->default(null);
        
            $table->char('delflag', 1)->nullable()->default(null);
            $table->char('jobfrom', 4)->nullable()->default(null);
            $table->char('jobto', 4)->nullable()->default(null);
            $table->char('measufrom', 8)->nullable()->default(null);
            $table->char('measuto', 8)->nullable()->default(null);
        
            $table->timestamp('insdatetime')->nullable()->useCurrent();
            $table->integer('insuserno');
            $table->timestamp('upddatetime')->nullable()->useCurrentOnUpdate();
            $table->integer('upduserno')->nullable();
        
            // インデックスは置いてOK
            $table->index('groupno');
            $table->index('facilityno');
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
