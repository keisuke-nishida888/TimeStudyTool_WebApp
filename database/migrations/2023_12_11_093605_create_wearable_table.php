<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWearableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wearable', function (Blueprint $table) {
            $table->id()->unique()->nullable(false);
            $table->char('devicename',20)->nullable(false);
            $table->char('userid',40)->nullable(true)->default(null);
            $table->char('passwd',40)->nullable(true)->default(null);
            $table->char('clientid',20)->nullable(true)->default(null);
            $table->char('clientsc',40)->nullable(true)->default(null);
            $table->char('auth',60)->nullable(true)->default(null);
            $table->integer('helperno')->default(0);
            $table->char('delflag',1)->nullable(true)->default(null);
            $table->timestamp('insdatetime')->nullable(true);
            $table->integer('insuserno')->nullable(true);
            $table->timestamp('upddatetime')->nullable(true);
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
        Schema::dropIfExists('wearable');
    }
}
