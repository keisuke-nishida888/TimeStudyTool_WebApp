<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeauserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meauser', function (Blueprint $table) {
            $table->id()->unique()->nullable(false);
            $table->char('ymd',9)->nullable(false);
            $table->integer('helperno')->nullable(false);
            $table->integer('wearableno')->nullable(false);
            $table->integer('backpainno')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meauser');
    }
}
