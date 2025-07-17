<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->unique()->nullable(false);
            $table->char('username',20)->nullable(false);
            $table->text('pass',255)->nullable(false);
            $table->char('authority',1)->nullable(true)->default(null);
            $table->integer('facilityno')->default(0);
            $table->char('delflag',1)->nullable(true)->default(null);
            $table->char('policyflag',1)->nullable(false)->default(0);
            $table->timestamp('insdatetime')->nullable()->useCurrent();
            $table->integer('insuserno');
            $table->timestamp('upddatetime')->nullable()->useCurrentOnUpdate();
            $table->integer('upduserno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
