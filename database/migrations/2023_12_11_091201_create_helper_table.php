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
            $table->char('helpername',20)->nullable(false);
            $table->integer('wearableno')->default(0);
            $table->integer('facilityno')->default(0);
            $table->char('position',1)->nullable(false);
            $table->integer('backpainno')->default(0);
            $table->char('age',3)->default(0);
            $table->char('sex',3)->default(0);
            $table->char('pic1',1)->nullable(true)->default(null);
            $table->char('pic2',1)->nullable(true)->default(null);
            $table->char('pic3',1)->nullable(true)->default(null);
            $table->char('pic4',1)->nullable(true)->default(null);
            $table->char('pic5',1)->nullable(true)->default(null);
            $table->char('delflag',1)->nullable(true)->default(null);
            $table->char('jobfrom',4)->nullable(true)->default(null);
            $table->char('jobto',4)->nullable(true)->default(null);
            $table->char('measufrom',8)->nullable(true)->default(null);
            $table->char('measuto',8)->nullable(true)->default(null);
            $table->timestamp('insdatetime')->nullable()->useCurrent();
            $table->integer('insuserno');
            $table->timestamp('upddatetime')->nullable()->useCurrentOnUpdate();
            $table->integer('upduserno')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('helper');
    }
}
