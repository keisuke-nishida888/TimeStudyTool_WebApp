<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeStudy;
use App\Models\bpainhed;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Reader\Csv;


class TimeStudyController extends Controller
{
    public function upload(Request $request)
    {

        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt',
                'helpername' => 'required|numeric',
            ]);

            $file = $request->file('csv_file');
            $helpno = $request->input('helpername');

            $reader = new Csv();
            $reader->setInputEncoding('UTF-8');
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            Log::info('rows count: ' . count($rows));
            Log::info('rows dump: ' . print_r($rows, true));
            $baseDate = null;
foreach ($rows as $index => $row) {
    if ($index === 0) {
        // 1行目に「2025年5月13日」などの日付が入っている
        if (preg_match('/(\d{4})年(\d{1,2})月(\d{1,2})日/', $row[0], $matches)) {
            $baseDate = sprintf('%04d/%02d/%02d', $matches[1], $matches[2], $matches[3]);
        }
        continue; // 日付行は処理しない
    }
    if ($index === 1) continue; // 2行目はヘッダー（作業名/START/STOP）

    $taskName = $row[0] ?? null;
    $startTime = $row[1] ?? null;
    $stopTime = $row[2] ?? null;

    if (!$baseDate || !$startTime) continue; // 日付や時刻がなければスキップ

    // 日付＋時刻を合成
    $startDateTime = $baseDate . ' ' . $startTime;
    $stopDateTime = $baseDate . ' ' . $stopTime;

    // 日付パース
    $startDate = \DateTime::createFromFormat('Y/m/d H:i:s', $startDateTime)
        ?: \DateTime::createFromFormat('Y/m/d H:i', $startDateTime);
    $ymd = $startDate ? $startDate->format('Ymd') : null;

    Log::info("startDateTime = {$startDateTime}");
    Log::info("startDate = " . ($startDate ? $startDate->format('Ymd') : 'null'));

    // bpainhedRecord検索・DB保存の処理はここから今まで通り続ける
    $bpainhedRecord = bpainhed::where('helperno', $helpno)
        ->where('ymd', $ymd)
        ->first();

    if (!$bpainhedRecord) {
        Log::warning("bpainhed not found: helpno={$helpno}, ymd={$ymd}");
        continue;
    }
    $bpainhedno = $bpainhedRecord->id;

    TimeStudy::create([
        'bpainhedno' => $bpainhedno,
        'helpno' => $helpno,
        'ymd' => $ymd,
        'year' => $startDate->format('Y'),
        'start' => $startDateTime,
        'stop' => $stopDateTime,
        'task_name' => $taskName
    ]);
}


            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('CSV upload failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
