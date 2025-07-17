<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpain08Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpain08', function (Blueprint $table) {
            $table->id()->unique()->nullable(false);
            $table->integer('backpainno')->nullable(false);
            $table->integer('helperno')->nullable(false);
            $table->char('day',2)->nullable(false);
            $table->char('hou',2)->nullable(false);
            $table->char('min1',4)->nullable(true)->default(null);
            $table->integer('ftilt1')->default(0);
            $table->integer('twist1')->default(0);
            $table->char('min2',4)->nullable(true)->default(null);
            $table->integer('ftilt2')->default(0);
            $table->integer('twist2')->default(0);
            $table->char('min3',4)->nullable(true)->default(null);
            $table->integer('ftilt3')->default(0);
            $table->integer('twist3')->default(0);
            $table->char('min4',4)->nullable(true)->default(null);
            $table->integer('ftilt4')->default(0);
            $table->integer('twist4')->default(0);
            $table->char('min5',4)->nullable(true)->default(null);
            $table->integer('ftilt5')->default(0);
            $table->integer('twist5')->default(0);
            $table->char('min6',4)->nullable(true)->default(null);
            $table->integer('ftilt6')->default(0);
            $table->integer('twist6')->default(0);
            $table->char('min7',4)->nullable(true)->default(null);
            $table->integer('ftilt7')->default(0);
            $table->integer('twist7')->default(0);
            $table->char('min8',4)->nullable(true)->default(null);
            $table->integer('ftilt8')->default(0);
            $table->integer('twist8')->default(0);
            $table->char('min9',4)->nullable(true)->default(null);
            $table->integer('ftilt9')->default(0);
            $table->integer('twist9')->default(0);
            $table->char('min10',4)->nullable(true)->default(null);
            $table->integer('ftilt10')->default(0);
            $table->integer('twist10')->default(0);
            $table->char('min11',4)->nullable(true)->default(null);
            $table->integer('ftilt11')->default(0);
            $table->integer('twist11')->default(0);
            $table->char('min12',4)->nullable(true)->default(null);
            $table->integer('ftilt12')->default(0);
            $table->integer('twist12')->default(0);
            $table->integer('bpainhedno')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpain08');
    }
}
