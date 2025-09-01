<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * 管理者ダッシュボード
     * ・期間はクエリで指定（start_date, end_date）。未指定は直近30日。
     *   例）/admin/dashboard?start_date=2025-07-01&end_date=2025-07-31
     */
    public function dashboard(Request $request)
    {
        // ===== 期間 =====
        $start = $request->query('start_date');
        $end   = $request->query('end_date');

        if (!$start || !$end) {
            $end   = Carbon::now()->format('Y-m-d');
            $start = Carbon::now()->subDays(29)->format('Y-m-d'); // 直近30日
        }
        $from = Carbon::parse($start)->startOfDay();
        $to   = Carbon::parse($end)->endOfDay();

        // ===== 基本カウント =====
        $facilityCount = DB::table('facility')->count();
        $helperCount   = DB::table('helper')->count();

        // ===== 性別内訳（1=男, 2=女）=====
        $sexCountsRaw = DB::table('helper')
            ->select('sex', DB::raw('COUNT(*) as cnt'))
            ->groupBy('sex')
            ->pluck('cnt','sex')
            ->toArray();

        $sexCounts = [
            'male'   => isset($sexCountsRaw[1]) ? (int)$sexCountsRaw[1] : 0,
            'female' => isset($sexCountsRaw[2]) ? (int)$sexCountsRaw[2] : 0,
        ];

        // ===== 年齢分布（円グラフ用にバケット化）=====
        // バケット: ~19, 20-29, 30-39, 40-49, 50-59, 60+
        $ageBuckets = [
            '~19'   => 0,
            '20-29' => 0,
            '30-39' => 0,
            '40-49' => 0,
            '50-59' => 0,
            '60+'   => 0,
        ];

        $ages = DB::table('helper')->whereNotNull('age')->pluck('age');
        foreach ($ages as $age) {
            $a = (int)$age;
            if ($a <= 19)      $ageBuckets['~19']++;
            elseif ($a <= 29)  $ageBuckets['20-29']++;
            elseif ($a <= 39)  $ageBuckets['30-39']++;
            elseif ($a <= 49)  $ageBuckets['40-49']++;
            elseif ($a <= 59)  $ageBuckets['50-59']++;
            else               $ageBuckets['60+']++;
        }

        // ===== 都道府県別テーブル =====
        // 1) 期間の勤怠集計（helper単位）
        $tsAgg = DB::table('time_study as ts')
            ->select(
                'ts.helpno',
                DB::raw('SUM(TIMESTAMPDIFF(MINUTE, ts.start, ts.stop)) as total_min'),
                DB::raw('COUNT(DISTINCT DATE(ts.start)) as work_days')
            )
            ->whereBetween('ts.start', [$from, $to])
            ->groupBy('ts.helpno');

        // 2) helper + facility + (1) を結合
        $rows = DB::table('helper as h')
            ->leftJoin('facility as f', 'h.facilityno', '=', 'f.id')
            ->leftJoinSub($tsAgg, 'agg', function($join){
                $join->on('agg.helpno', '=', 'h.id');
            })
            ->select(
                'h.id as helper_id',
                'h.age',
                'h.facilityno',
                'f.address',
                DB::raw('COALESCE(agg.total_min, 0) as total_min'),
                DB::raw('COALESCE(agg.work_days, 0) as work_days')
            )
            ->get();

        // 3) 住所 → 都道府県 抽出 & 集計
        $prefStats = []; // [pref => ['people'=>..,'age_sum'=>..,'age_cnt'=>..,'work_sum_min'=>..,'overtime_sum_min'=>..]]

        foreach ($rows as $r) {
            $pref = $this->pickPrefecture($r->address);

            if (!isset($prefStats[$pref])) {
                $prefStats[$pref] = [
                    'people'            => 0,
                    'age_sum'           => 0,
                    'age_cnt'           => 0,
                    'work_sum_min'      => 0,
                    'overtime_sum_min'  => 0,
                ];
            }

            $prefStats[$pref]['people']++;

            if (!is_null($r->age) && $r->age !== '') {
                $prefStats[$pref]['age_sum'] += (int)$r->age;
                $prefStats[$pref]['age_cnt']++;
            }

            $totalMin = (int)$r->total_min;   // 期間内 総労働分（helper単位）
            $workDays = (int)$r->work_days;   // 出勤日数（distinct DATE(start)）

            $prefStats[$pref]['work_sum_min'] += $totalMin;

            // 簡易残業: 標準 8h/日 * 出勤日数 を超過した分を「残業」とみなす
            $stdPerDayMin = 8 * 60;
            $stdMin       = $workDays * $stdPerDayMin;
            $overtime     = max(0, $totalMin - $stdMin);
            $prefStats[$pref]['overtime_sum_min'] += $overtime;
        }

        // 4) 表示用レコードへ整形
        // 平均勤務時間/平均残業時間は「helper平均」（=合計 / 人数）
        $prefRows = [];
        foreach ($prefStats as $pref => $st) {
            $people = max(1, (int)$st['people']); // 0割防止
            $avgAge = ($st['age_cnt'] > 0) ? round($st['age_sum'] / $st['age_cnt'], 1) : null;

            $avgWorkHours    = round(($st['work_sum_min'] / $people) / 60, 1);
            $avgOvertimeHour = round(($st['overtime_sum_min'] / $people) / 60, 1);

            $prefRows[] = [
                'pref'      => $pref,
                'people'    => (int)$st['people'],
                'avg_age'   => $avgAge,              // null の場合はビュー側で「-」
                'avg_work'  => $avgWorkHours,        // 時間（h）
                'avg_over'  => $avgOvertimeHour,     // 時間（h）
            ];
        }

        // 都道府県順に並べる（不明は最後）
        usort($prefRows, function($a, $b){
            if ($a['pref'] === '不明') return 1;
            if ($b['pref'] === '不明') return -1;
            return strcmp($a['pref'], $b['pref']);
        });

        // まだプランは未定のため、プレースホルダ
        $planRatio = [
            'labels' => ['準備中'],
            'data'   => [100],
        ];

        return view('admin_dashboard', [
            'start'         => $start,
            'end'           => $end,
            'facilityCount' => $facilityCount,
            'helperCount'   => $helperCount,
            'sexCounts'     => $sexCounts,
            'ageBuckets'    => $ageBuckets,
            'planRatio'     => $planRatio,
            'prefRows'      => $prefRows,
        ]);
    }

    /**
     * 住所文字列から都道府県名を抽出
     */
    private function pickPrefecture($address)
    {
        $addr = (string)$address;
        if ($addr === '') return '不明';

        $prefs = [
            '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
            '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
            '新潟県','富山県','石川県','福井県','山梨県','長野県',
            '岐阜県','静岡県','愛知県','三重県',
            '滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
            '鳥取県','島根県','岡山県','広島県','山口県',
            '徳島県','香川県','愛媛県','高知県',
            '福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県',
            '沖縄県'
        ];

        foreach ($prefs as $p) {
            if (mb_strpos($addr, $p) !== false) return $p;
        }
        // 先頭2〜3文字で「都/道/府/県」を含むケース対策（例：東京都新宿区…）
        $head = mb_substr($addr, 0, 4);
        foreach ($prefs as $p) {
            if (mb_strpos($head, $p) !== false) return $p;
        }
        return '不明';
    }
}
