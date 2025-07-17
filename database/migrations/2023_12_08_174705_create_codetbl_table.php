<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodetblTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codetbl', function (Blueprint $table) {
            $table->integer('codeno')->nullable(false);
            $table->integer('dispno')->nullable(false);
            $table->integer('value')->default(0);
            $table->char('selectname',50)->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('codetbl');
    }
}
