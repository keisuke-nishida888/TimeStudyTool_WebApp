<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeStudyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_study', function (Blueprint $table) {
            $table->id()->unique()->nullable(false);
            $table->integer('bpainhedno')->nullable(true);
            $table->integer('helpno')->nullable(true);
            $table->char('ymd', 8)->nullable(true);
            $table->integer('year')->nullable(true);
            $table->dateTime('start')->nullable(true);
            $table->dateTime('stop')->nullable(true);
            $table->string('task_name', 255)->nullable(true);
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
        Schema::dropIfExists('time_study');
    }
}
