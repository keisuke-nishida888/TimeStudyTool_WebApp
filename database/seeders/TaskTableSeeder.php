<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('task_table')->insert([
            [
                'task_name' => '食事介助PPP',
                'task_type_no' => 1,
                'task_category_no' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task_name' => '入浴介助OOO',
                'task_type_no' => 1,
                'task_category_no' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task_name' => '移動介助AAA',
                'task_type_no' => 2,
                'task_category_no' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task_name' => '排泄介助III',
                'task_type_no' => 1,
                'task_category_no' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
