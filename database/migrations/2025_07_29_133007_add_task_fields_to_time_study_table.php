<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaskFieldsToTimeStudyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_study', function (Blueprint $table) {
            $table->string('task_name')->nullable()->after('task_id');
            $table->integer('task_type_no')->nullable()->after('task_name');
            $table->integer('task_category_no')->nullable()->after('task_type_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_study', function (Blueprint $table) {
            $table->dropColumn(['task_name', 'task_type_no', 'task_category_no']);
        });
    }
}
