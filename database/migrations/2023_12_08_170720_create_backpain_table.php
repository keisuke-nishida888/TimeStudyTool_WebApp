<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackpainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backpain', function (Blueprint $table) {
            $table->id()->unique()->nullable(false);
            $table->char('devicename',20)->nullable(false);
            $table->integer('helperno');
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
        Schema::dropIfExists('backpain');
    }
}
