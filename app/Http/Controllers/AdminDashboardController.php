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
        // 上部カード
        $facilityCount = DB::table('facility')->count();
        $helperCount   = DB::table('helper')->count();
        $sexCounts = [
            'male'   => DB::table('helper')->where('sex', 1)->count(),
            'female' => DB::table('helper')->where('sex', 2)->count(),
        ];

        $ageBuckets = $this->buildAgeBuckets();
        $prefRows   = $this->buildPrefRows();

        return view('admin_dashboard', compact(
            'facilityCount', 'helperCount', 'sexCounts', 'ageBuckets', 'prefRows'
        ));
    }

    private function buildAgeBuckets()
    {
        $ages = DB::table('helper')->whereNotNull('age')->pluck('age');
        $b = ['~19'=>0,'20-29'=>0,'30-39'=>0,'40-49'=>0,'50-59'=>0,'60-69'=>0,'70~'=>0];
        foreach ($ages as $age) {
            $a = (int)$age;
            if ($a <= 19) $b['~19']++;
            elseif ($a <= 29) $b['20-29']++;
            elseif ($a <= 39) $b['30-39']++;
            elseif ($a <= 49) $b['40-49']++;
            elseif ($a <= 59) $b['50-59']++;
            elseif ($a <= 69) $b['60-69']++;
            else $b['70~']++;
        }
        return $b;
    }

    private function buildPrefRows()
    {
        $PREFS = [
            '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県',
            '埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県',
            '岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
            '鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県',
            '佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'
        ];

        // address から都道府県を抽出して facility_id をひも付け
        $facilities = DB::table('facility')->select('id','address','facility')->get();
        $prefToFacilityIds = [];
        foreach ($facilities as $f) {
            $pref = $this->extractPref($f->address, $PREFS);
            if (!$pref) continue;
            if (!isset($prefToFacilityIds[$pref])) $prefToFacilityIds[$pref] = [];
            $prefToFacilityIds[$pref][] = $f->id;
        }

        $rows = [];
        foreach ($prefToFacilityIds as $pref => $facilityIds) {
            // 該当施設の全作業者
            $helpers = DB::table('helper')
                ->select('id','facilityno','age','sex')
                ->whereIn('facilityno', $facilityIds)
                ->get();

            $helperIds = $helpers->pluck('id')->all();

            // 男女比
            $maleCount   = $helpers->where('sex', 1)->count();
            $femaleCount = $helpers->where('sex', 2)->count();

            // 平均年齢
            $avgAge = $helpers->avg('age'); // null は除外

            // ===== 1人1日あたりの平均勤務時間（分） =====
            $avgDailyMin = 0.0;
            $avgOverMin  = 0.0; // 8h=480分超過を残業とみなす簡易版
            if (!empty($helperIds)) {
                $daily = DB::table('time_study as ts')
                    ->selectRaw('ts.helpno, DATE(ts.start) as d')
                    ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, ts.start, ts.stop)) as m')
                    ->whereIn('ts.helpno', $helperIds)
                    ->whereNotNull('ts.start')
                    ->whereNotNull('ts.stop')
                    ->groupBy('ts.helpno', DB::raw('DATE(ts.start)'))
                    ->get();

                // 平均（分）
                $avgDailyMin = (float) ($daily->avg('m') ?: 0);

                // 残業平均（分）
                $overArr = [];
                foreach ($daily as $r) {
                    $m = (int)$r->m;
                    $overArr[] = max(0, $m - 480);
                }
                $avgOverMin = count($overArr) ? array_sum($overArr) / count($overArr) : 0.0;
            }

            $rows[] = [
                'pref'           => $pref,
                // 施設数：その都道府県に属する facility の数
                'facility_count' => count($facilityIds),
                // 人数：該当施設に所属する helper 数
                'people'         => $helpers->count(),
                // 平均年齢（年）
                'avg_age'        => $avgAge !== null ? round($avgAge, 1) : null,
                // 平均勤務（時間）
                'avg_work'       => round($avgDailyMin / 60, 1),
                // 平均残業（時間）
                'avg_over'       => round($avgOverMin / 60, 1),
                // 男女比の元データ
                'male_count'     => $maleCount,
                'female_count'   => $femaleCount,
            ];
        }

        // 都道府県名で並び替え
        usort($rows, function($a, $b){
            return strcmp($a['pref'], $b['pref']);
        });

        return $rows;
    }

    private function extractPref($address, $PREFS)
    {
        if (!$address) return null;
        foreach ($PREFS as $p) {
            if (mb_strpos($address, $p) !== false) return $p;
        }
        return null;
    }

    public function prefCompare(\Illuminate\Http\Request $request)
    {
        $prefs = array_values(array_unique(array_filter((array)$request->input('prefs', []))));
        if (empty($prefs)) {
            return response()->json(['rows' => [], 'range' => null]);
        }
    
        $from = \Carbon\Carbon::now()->subDays(30)->startOfDay();
        $to   = \Carbon\Carbon::now()->endOfDay();
    
        $rows = [];
    
        foreach ($prefs as $pref) {
            // 施設ID
            $facIds = \DB::table('facility')
                ->where('address', 'like', "%{$pref}%")
                ->pluck('id');
    
            $facilityCount = $facIds->count();
    
            // 作業者
            $helpers = \DB::table('helper')
                ->whereIn('facilityno', $facIds)
                ->get(['id', 'age', 'sex']);
    
            $people = $helpers->count();
            $avgAge = $people ? round((float)$helpers->avg('age'), 1) : null;
            $male   = $helpers->where('sex', 1)->count();
            $female = $helpers->where('sex', 2)->count();
    
            // 勤務・残業（helper×日で集計）
            $avgWorkH = 0.0;
            $avgOverH = 0.0;
    
            if ($people > 0) {
                $helperIds = $helpers->pluck('id');
    
                $mins = \DB::table('time_study as ts')
                    ->selectRaw('ts.helpno, DATE(ts.start) as d, SUM(TIMESTAMPDIFF(MINUTE, ts.start, ts.stop)) as min')
                    ->whereIn('ts.helpno', $helperIds)
                    ->whereBetween('ts.start', [$from, $to])
                    ->groupBy('ts.helpno', \DB::raw('DATE(ts.start)'))
                    ->get();
    
                if ($mins->count()) {
                    $avgWorkMin = (float) $mins->avg('min');
    
                    // ★ ここを無名関数に変更（arrow function 使用禁止）
                    $avgOverMin = (float) $mins->map(function ($r) {
                        return max(0, ((int)$r->min - 480)); // 8h=480min 超過分
                    })->avg();
    
                    $avgWorkH = round($avgWorkMin / 60, 1);
                    $avgOverH = round($avgOverMin / 60, 1);
                }
            }
    
            $malePct   = $people ? round($male * 100 / $people) : 0;
            $femalePct = $people ? (100 - $malePct) : 0;
    
            $rows[] = [
                'pref'           => $pref,
                'facility_count' => $facilityCount,
                'people'         => $people,
                'avg_age'        => $avgAge,
                'avg_work_h'     => $avgWorkH,
                'avg_over_h'     => $avgOverH,
                'ratio'          => "{$malePct}%:{$femalePct}%",
            ];
        }
    
        return response()->json([
            'rows'  => $rows,
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
        ]);
    }
    
    
        // 既存: dashboard() で $facilities を渡すと施設選択がドロップダウンになります
        // 例）
        // public function dashboard() {
        //   $facilities = DB::table('facility')->select('id','facility')->orderBy('facility')->get();
        //   ... 既存集計 ...
        //   return view('admin_dashboard', compact('facilities', /* 既存の変数たち */));
        // }
    
        /** 施設内の作業者一覧を返す（JSON） */
        public function facilityHelpers(Request $request)
        {
            $fid = (int) $request->input('facility_id');
            if (!$fid) return response()->json(['helpers'=>[]]);
    
            $helpers = DB::table('helper')
                ->select('id', 'helpername')
                ->where('facilityno', $fid)
                ->where(function($q){
                    // delflag がある環境向けの安全ガード
                    $q->whereNull('delflag')->orWhere('delflag', '<>', 1);
                })
                ->orderBy('helpername')
                ->get();
    
            return response()->json(['helpers' => $helpers]);
        }
    
        /** 指定期間×選択作業者の time_study をタスク別に合計（分）で返す（JSON） */
        public function taskSummary(Request $request)
        {
            $fid   = (int) $request->input('facility_id');
            $ids   = (array) ($request->input('helper_ids') ?? []);
            $start = $request->input('start_date');
            $end   = $request->input('end_date');
    
            if (!$fid || !$start || !$end) {
                return response()->json(['rows'=>[]], 200);
            }
    
            // ヘルパー未指定なら施設内すべて
            if (empty($ids)) {
                $ids = DB::table('helper')->where('facilityno', $fid)->pluck('id')->all();
            }
            if (empty($ids)) return response()->json(['rows'=>[]], 200);
    
            $from = Carbon::parse($start)->startOfDay();
            $to   = Carbon::parse($end)->endOfDay();
    
            // MySQL 前提：TIMESTAMPDIFF(MINUTE, start, stop) で分を集計
            // task_table に task_name が無い（taskname のみ）環境にも配慮したい場合は
            // ts.task_name を優先し、無ければ tt.task_name を使う運用が安全
            $rows = DB::table('time_study as ts')
                ->leftJoin('task_table as tt', 'ts.task_id', '=', 'tt.task_id')
                ->whereIn('ts.helpno', $ids)
                ->whereBetween('ts.start', [$from, $to])
                ->whereNotNull('ts.stop')
                ->selectRaw('COALESCE(ts.task_name, tt.task_name) as task_name')
                ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, ts.start, ts.stop)) as minutes')
                ->groupBy('task_name')
                ->orderByDesc('minutes')
                ->get()
                ->map(function($r){
                    $r->task_name = $r->task_name ?? '-';
                    $r->minutes   = (int) $r->minutes;
                    return $r;
                });
    
            return response()->json(['rows' => $rows], 200);
        }
    

}

