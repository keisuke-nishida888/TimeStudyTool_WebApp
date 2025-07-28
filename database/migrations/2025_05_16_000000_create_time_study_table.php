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
            $table->string('timestudy_id')->primary();
            $table->integer('helpno')->nullable(true);
            $table->integer('task_id')->nullable(true);
            $table->dateTime('start')->nullable(true);
            $table->dateTime('stop')->nullable(true);
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
