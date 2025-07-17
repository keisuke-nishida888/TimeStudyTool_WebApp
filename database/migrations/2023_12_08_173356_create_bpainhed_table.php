<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpainhedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpainhed', function (Blueprint $table) {
            $table->id()->unique()->nullable(false);
            $table->integer('backpainno')->nullable(false);
            $table->integer('helperno')->nullable(false);
            $table->char('ymd',8)->nullable(false);
            $table->char('hms',6)->nullable(false);
            $table->integer('fxc')->default(0);
            $table->char('fxt',6)->nullable(true)->default(null);
            $table->char('fxa',4)->nullable(true)->default(null);
            $table->integer('txc')->default(0);
            $table->char('txt',6)->nullable(true)->default(null);
            $table->char('txa',4)->nullable(true)->default(null);
            $table->float('risk')->default(0);
            $table->char('sthms',6)->nullable(true)->default(null);
            $table->char('edhms',6)->nullable(true)->default(null);
            $table->char('alhms',6)->nullable(true)->default(null);
            $table->integer('flim')->default(0);
            $table->integer('hplim')->default(0);
            $table->integer('hmlim')->default(0);
            $table->integer('wearableno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpainhed');
    }
}
