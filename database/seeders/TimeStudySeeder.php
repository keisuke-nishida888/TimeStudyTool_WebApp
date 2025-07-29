<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TimeStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // サンプルデータを作成（task_tableと連携）
        $sampleData = [
            [
                'timestudy_id' => Str::uuid(),
                'helpno' => 1,
                'task_id' => 1, // task_tableのtask_idと連携
                'start' => '2024-01-15 08:00:00',
                'stop' => '2024-01-15 09:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timestudy_id' => Str::uuid(),
                'helpno' => 1,
                'task_id' => 2, // task_tableのtask_idと連携
                'start' => '2024-01-15 10:00:00',
                'stop' => '2024-01-15 11:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timestudy_id' => Str::uuid(),
                'helpno' => 1,
                'task_id' => 3, // task_tableのtask_idと連携
                'start' => '2024-01-15 14:00:00',
                'stop' => '2024-01-15 15:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timestudy_id' => Str::uuid(),
                'helpno' => 1,
                'task_id' => 4, // task_tableのtask_idと連携
                'start' => '2024-01-15 16:00:00',
                'stop' => '2024-01-15 16:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timestudy_id' => Str::uuid(),
                'helpno' => 1,
                'task_id' => 1, // 同じtask_idで別の時間帯
                'start' => '2024-01-15 19:00:00',
                'stop' => '2024-01-15 20:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // データベースに挿入
        foreach ($sampleData as $data) {
            DB::table('time_study')->insert($data);
        }
    }
}
