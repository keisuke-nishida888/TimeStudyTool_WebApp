<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;
use Illuminate\Http\Request;
use App\Models\Helper;
use App\Models\Facility;
use App\Models\bpainhed;
use App\Models\TimeStudy;
use Illuminate\Database\Eloquent\Collection;
//PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class HelperdataController extends Controller
{
    //
    public function index(Request $request)
    {

        //対象作業者の腰痛データと心拍データ
        //requestは作業者No(id)が送られてくる
        //対象作業者のデータを検索する
        if(isset($_POST["id"]))
        {
            $getdata = bpainhed::select()
            ->whereIn('helperno',[$_POST["id"]])
            ->orderBy('bpainhed.ymd','asc')
            ->orderBy('bpainhed.hms','asc')
            ->get();
            $ymdData = bpainhed::select("ymd")
            ->whereIn('helperno',[$_POST["id"]])
            ->orderBy('bpainhed.ymd','asc')
            ->groupBy('bpainhed.ymd')
            ->get();
            $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

            $ymdGroupData = json_decode(json_encode($ymdData,JSON_PRETTY_PRINT),true);
            $getdata2 = Helper::select('helper.id as Helper_id','helper.helpername','helper.facilityno','helper.delflag','facility.facility')
            ->whereIn('helper.id',[$_POST["id"]])
            ->whereNotIn('helper.delflag',[1])
            ->join('facility','facility.id','=','helper.facilityno')
            ->get();

            if(isset($getdata2[0]['facilityno'])) $facilityno = $getdata2[0]['facilityno'];
            else $facilityno = 0;
            $data2 = json_decode(json_encode($getdata2,JSON_PRETTY_PRINT),true);
        }
        else if(isset($_GET["helperno"]))
        {
            $getdata = bpainhed::select()
            ->whereIn('helperno',[$_GET["helperno"]])
            ->orderBy('bpainhed.ymd','asc')
            ->orderBy('bpainhed.hms','asc')
            ->get();
            $ymdData = bpainhed::select("ymd")
            ->whereIn('helperno',[$_GET["helperno"]])
            ->orderBy('bpainhed.ymd','asc')
            ->groupBy('bpainhed.ymd')
            ->get();
            $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
            $ymdGroupData = json_decode(json_encode($ymdData,JSON_PRETTY_PRINT),true);

            $getdata2 = Helper::select('helper.id as Helper_id','helper.helpername','helper.facilityno','helper.delflag','facility.facility')
            ->whereIn('helper.id',[$_GET["helperno"]])
            ->whereNotIn('helper.delflag',[1])
            ->join('facility','facility.id','=','helper.facilityno')
            ->get();
            if(isset($getdata2[0]['facilityno'])) $facilityno = $getdata2[0]['facilityno'];
            else $facilityno = 0;
            $data2 = json_decode(json_encode($getdata2,JSON_PRETTY_PRINT),true);
        }
        else
        {
            $data ="";
            $helperno = "";

            //パンくずリストからの遷移
            // URLパラメータの部分だけを変数に格納
            $param = $_SERVER['HTTP_REFERER'] ?? '';
            $tmp = [];
            if(isset($param))
            {
                //parse_url でURLを分解してパラメータのみ取得する
                parse_str(parse_url($param, PHP_URL_QUERY), $query);
                if(isset($query))
                {
                    if(isset($query['helperno']))  $helperno = $query['helperno'];
                    else $helperno = 0;
                }

                $getdata = bpainhed::select()
                ->whereIn('helperno',[$helperno])
                ->orderBy('bpainhed.ymd','asc')
                ->orderBy('bpainhed.hms','asc')
                ->get();
                $ymdData = bpainhed::select("ymd")
                ->whereIn('helperno',[$helperno])
                ->orderBy('bpainhed.ymd','asc')
                ->groupBy('bpainhed.ymd')
                ->get();
                $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
                $ymdGroupData = json_decode(json_encode($ymdData,JSON_PRETTY_PRINT),true);

                $getdata2 = Helper::select('helper.id as Helper_id','helper.helpername','helper.facilityno','helper.delflag','facility.facility')
                ->whereIn('helper.id',[$helperno])
                ->whereNotIn('helper.delflag',[1])
                ->join('facility','facility.id','=','helper.facilityno')
                ->get();

                if(isset($getdata2[0]['facilityno'])) $facilityno = $getdata2[0]['facilityno'];
                else $facilityno = 0;
                $data2 = json_decode(json_encode($getdata2,JSON_PRETTY_PRINT),true);
            }
            else
            {
                //施設ユーザでログインする場合に使用する
                //施設情報
                //施設ユーザのとき
                if(Auth::user()->authority == 3)
                {
                    if(isset(Auth::user()->facilityno))
                    {
                        $getdata = Facility::select()
                        ->whereIn('facility.id',[Auth::user()->facilityno])
                        ->whereNotIn('facility.delflag',[1])
                        ->get();
                        $ymdData = bpainhed::select("ymd")
                        ->whereIn('facility.id',[Auth::user()->facilityno])
                        ->orderBy('bpainhed.ymd','asc')
                        ->groupBy('bpainhed.ymd')
                        ->get();
                        $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
                        $ymdGroupData = json_decode(json_encode($ymdData,JSON_PRETTY_PRINT),true);
                        if(isset($getdata[0]['id'])) $facilityno = $getdata[0]['id'];
                        else $facilityno = "";
                    }
                    else $facilityno = "";
                }
                else $facilityno = "";
                $data = "";
                $page = 'mainmenu';
                $title = Common::$title[$page];
                $group = Common::$group[$page];
                return view($page, compact('title' ,'page','group','data','facilityno','ymdGroupData'));
            }
        }

        $timeStudyData = [];
        if (isset($data) && !empty($data)) {
            $bpainhedno = isset($data[0]['id']) ? $data[0]['id'] : null;
            if ($bpainhedno) {
                $timeStudyData = TimeStudy::where('bpainhedno', $bpainhedno)
                    ->orderBy('start')
                    ->get();
            }
        }

        // data2が設定されていない場合のデフォルト値を設定
        if (!isset($data2) || empty($data2)) {
            $data2 = [];
            \Log::warning('data2 is not set, using empty array');
        }
        
        $page = 'helperdata';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        return view($page, compact('title', 'page', 'group', 'data', 'data2', 'facilityno', 'ymdGroupData', 'timeStudyData'));
    }


    //データ表示
    public function Helperdata_disp(Request $request)
    {

        // file_put_contents($debug_path,$request.PHP_EOL,FILE_APPEND);
        //対象作業者の腰痛データと心拍データ
        //requestは作業者名()と時間が送られてくる
        //開始時刻から対象のテーブルを選択
        if(isset($_POST["helpername"]))
        {
            if(isset($_POST["ymd"]))
            {
                $Y = substr($_POST["ymd"],0,4);
                $M = substr($_POST["ymd"],4,2);
                $D = substr($_POST["ymd"],6,2);
            }


            //変数に入れたモデル名をインスタンス化
            $model = "App\Models\bpain".$M;
            $bpain    = new $model;
            $helperno = "bpain".$M.".helperno";
            $bpainID = "bpain".$M.".id";
            $day = "bpain".$M.".day";
            $hou = "bpain".$M.".hou";

            $bpainhed = bpainhed::select("id")
            ->whereIn('helperno',[$_POST["helpername"]])
            ->whereIn("ymd",[$_POST["ymd"]])
            ->whereIn('hms',[$_POST["hms"]])
            ->orderBy('bpainhed.id','asc')
            ->get();
            $headdata = json_decode(json_encode($bpainhed,JSON_PRETTY_PRINT),true);


            $bpainhedno = $headdata[0]['id'];

            $getdata = $bpain->select()
            // ->whereIn($helperno,[$_POST["helpername"]])
            ->whereIn('bpainhedno',[$bpainhedno])
            ->orderBy($day,'asc')
            ->orderBy($hou,'asc')
            ->get();

            if(intval($M) == 12)
            {
                $M_next =1;
                $Y = intval($Y)+1;
            }
            else $M_next = intval($M+1);
            //ヘッダテーブルのidと紐づくデータを取得する(boainday.bpainhedno)
            //計測開始日が月末最終日の場合は次の月もデータを確認する
            //date にはMonth+1を入れること
            $last_day = date("t", mktime(0, 0, 0, $M_next , 0,sprintf("%04d",$Y)));
            $last_day = sprintf("%02s",$last_day);
            $M_next = sprintf("%02s",$M_next);
            if($D == $last_day)
            {
                //変数に入れたモデル名をインスタンス化
                $model2 = "App\Models\bpain".$M_next;
                $bpain2    = new $model2;
                $helperno2 = "bpain".$M_next.".helperno";
                $bpainID2 = "bpain".$M_next.".id";
                $day2 = "bpain".$M_next.".day";
                $hou2 = "bpain".$M_next.".hou";


                $getdata2 = $bpain2->select()
                // ->whereIn($helperno2,[$_POST["helpername"]])
                ->whereIn('bpainhedno',[$bpainhedno])
                // ->orderBy($bpainID2,'asc')
                ->orderBy($day2,'asc')
                ->orderBy($hou2,'asc')
                ->get();

                //開始月のデータに結合する
                $getdata = $getdata->concat($getdata2);
            }

            return $getdata;
        }
    }


  
    //TimeStudyのグラフデータを取得するメソッド
    public function getGraphData(Request $request)
{
    try {
        $helpno       = (int)$request->input('helpno');
        $selectedDate = $request->input('selected_date');
        $graphType    = $request->input('graph_type', 'type'); // 'type' or 'category'

        if (!$helpno || !$selectedDate) {
            return response()->json(['message' => 'helpno or selected_date missing'], 422);
        }

        $rows = DB::table('time_study as ts')
            ->leftJoin('task_table as tt', 'ts.task_id', '=', 'tt.task_id')
            ->where('ts.helpno', $helpno)
            ->whereDate('ts.start', $selectedDate)
            ->select([
                DB::raw('COALESCE(tt.task_name, ts.task_name) as task_name'),
                'ts.start',
                'ts.stop',
                DB::raw('COALESCE(tt.task_type_no, 2) as task_type_no'),
                DB::raw('COALESCE(tt.task_category_no, 2) as task_category_no'),
            ])
            ->orderBy('ts.start')
            ->get();

        $taskNames = $rows->pluck('task_name')->unique()->values()->all();

        $taskIndividualDurations = [];
        $graphData = [];
        $timeSlots = [];
        for ($h = 0; $h < 24; $h++) {
            $timeSlots[] = sprintf('%02d:00', $h);
        }

        foreach ($taskNames as $taskName) {
            $taskIndividualDurations[$taskName] = [];
            $graphData[$taskName] = array_fill(0, 24, null);

            foreach ($rows->where('task_name', $taskName) as $rec) {
                $startTs = strtotime($rec->start);
                $stopTs  = strtotime($rec->stop);
                if ($startTs === false || $stopTs === false || $stopTs <= $startTs) continue;

                $startDec = (int)date('H', $startTs) + ((int)date('i', $startTs))/60;
                $stopDec  = (int)date('H', $stopTs)  + ((int)date('i', $stopTs))/60;

                $taskIndividualDurations[$taskName][] = [
                    'start' => $rec->start,
                    'stop'  => $rec->stop,
                    'start_hour' => (int)date('H', $startTs),
                    'start_minute' => (int)date('i', $startTs),
                    'stop_hour' => (int)date('H', $stopTs),
                    'stop_minute' => (int)date('i', $stopTs),
                    'start_time_decimal' => $startDec,
                    'stop_time_decimal'  => $stopDec,
                    'duration' => max(0, (int)round(($stopTs - $startTs) / 60)),
                    'task_type_no' => (int)$rec->task_type_no,
                    'task_category_no' => (int)$rec->task_category_no,
                ];

                // 表示タイプに応じて色番号（type/category）を埋める
                for ($h = 0; $h < 24; $h++) {
                    if ($startDec < ($h + 1) && $stopDec > $h) {
                        $graphData[$taskName][$h] = ($graphType === 'category')
                            ? (int)$rec->task_category_no
                            : (int)$rec->task_type_no;
                    }
                }
            }
        }

        return response()->json([
            'timeSlots' => $timeSlots,
            'taskNames' => $taskNames,
            'graphData' => $graphData,
            'graphType' => $graphType,
            'taskIndividualDurations' => $taskIndividualDurations,
        ]);
    } catch (\Throwable $e) {
        \Log::error('getGraphData failed: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        return response()->json(['message' => 'server error', 'detail' => $e->getMessage()], 500);
    }
}


    
    // 期間指定：日別×作業名の合計（分）を返す
// ---- 期間指定：日別×3分類（介護種別＆カテゴリ）の合計（分）を返す ----
public function summary(Request $request)
{
    try {
        $helpno = (int)$request->input('helpno');
        $start  = $request->input('start_date'); // "YYYY-MM-DD"
        $end    = $request->input('end_date');   // "YYYY-MM-DD"

        if (!$helpno || !$start || !$end) {
            return response()->json([
                'days' => [],
                'directTotals' => [], 'indirectTotals' => [], 'otherTotals' => [],
                'physicalTotals' => [], 'mentalTotals' => [], 'otherTotalsCategory' => [],
                'directByTask' => [], 'indirectByTask' => [], 'otherByTask' => [],
            ], 200);
        }

        $from = Carbon::parse($start)->startOfDay();
        $to   = Carbon::parse($end)->endOfDay();

        // 日付配列
        $days = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $days[] = $d->format('Y-m-d');
        }
        $dayIndex = array_flip($days);
        $N = count($days);

        // トータル配列
        $directTotals   = array_fill(0, $N, 0); // 介護種別：直接(0)
        $indirectTotals = array_fill(0, $N, 0); // 介護種別：間接(1)
        $otherTotals    = array_fill(0, $N, 0); // 介護種別：その他(2)

        $physicalTotals      = array_fill(0, $N, 0); // カテゴリ：肉体(0)
        $mentalTotals        = array_fill(0, $N, 0); // カテゴリ：精神(1)
        $otherTotalsCategory = array_fill(0, $N, 0); // カテゴリ：その他(2)

        // （互換）タスク別（使わなければ無視されます）
        $directByTask   = [];
        $indirectByTask = [];
        $otherByTask    = [];

        $rows = DB::table('time_study as ts')
            ->leftJoin('task_table as tt', 'ts.task_id', '=', 'tt.task_id')
            ->where('ts.helpno', $helpno)
            ->whereBetween('ts.start', [$from, $to])
            ->select([
                DB::raw('COALESCE(tt.task_name, ts.task_name) as task_name'),
                DB::raw('COALESCE(tt.task_type_no, 2) as task_type_no'),
                DB::raw('COALESCE(tt.task_category_no, 2) as task_category_no'),
                'ts.start',
                'ts.stop',
            ])
            ->orderBy('ts.start')
            ->get();

        foreach ($rows as $r) {
            $startAt = Carbon::parse($r->start);
            $stopAt  = Carbon::parse($r->stop);
            if ($stopAt->lt($startAt)) continue;

            $ymd = $startAt->format('Y-m-d');
            if (!isset($dayIndex[$ymd])) continue;
            $idx = $dayIndex[$ymd];

            $min  = max(0, (int)$stopAt->diffInMinutes($startAt));
            $type = (int)$r->task_type_no;      // 0=直接 1=間接 2=その他
            $cat  = (int)$r->task_category_no;  // 0=肉体 1=精神 2=その他

            // 介護種別トータル
            if ($type === 0) {
                $directTotals[$idx] += $min;
                if (!isset($directByTask[$r->task_name])) $directByTask[$r->task_name] = array_fill(0, $N, 0);
                $directByTask[$r->task_name][$idx] += $min;
            } elseif ($type === 1) {
                $indirectTotals[$idx] += $min;
                if (!isset($indirectByTask[$r->task_name])) $indirectByTask[$r->task_name] = array_fill(0, $N, 0);
                $indirectByTask[$r->task_name][$idx] += $min;
            } else {
                $otherTotals[$idx] += $min;
                if (!isset($otherByTask[$r->task_name])) $otherByTask[$r->task_name] = array_fill(0, $N, 0);
                $otherByTask[$r->task_name][$idx] += $min;
            }

            // カテゴリトータル
            if     ($cat === 0) { $physicalTotals[$idx]      += $min; }
            elseif ($cat === 1) { $mentalTotals[$idx]        += $min; }
            else                { $otherTotalsCategory[$idx] += $min; }
        }

        return response()->json([
            'days' => $days,

            // 介護種別（typeモード）
            'directTotals'   => $directTotals,
            'indirectTotals' => $indirectTotals,
            'otherTotals'    => $otherTotals,

            // カテゴリ（categoryモード）
            'physicalTotals'      => $physicalTotals,       // 肉体(赤)
            'mentalTotals'        => $mentalTotals,         // 精神(紫)
            'otherTotalsCategory' => $otherTotalsCategory,  // その他(灰)

            // 互換（未使用なら無視）
            'directByTask'   => $directByTask,
            'indirectByTask' => $indirectByTask,
            'otherByTask'    => $otherByTask,
        ]);
    } catch (\Throwable $e) {
        \Log::error('summary failed: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        return response()->json(['message' => 'server error', 'detail' => $e->getMessage()], 500);
    }
}


/** タスク別配列の初期化（互換用） */
private function ensureArr(array &$map, string $taskName, int $len): void
{
    if (!isset($map[$taskName])) {
        $map[$taskName] = array_fill(0, $len, 0);
    }
}
    // 画面表示用（GET）
    /**
     * データ比較（GET表示）
     * /comparison?helper_a=75&helper_b=79&start_date=YYYY-MM-DD&end_date=YYYY-MM-DD
     */
    public function comparison(Request $request)
    {
        // 入力
        $helperA = (int) $request->query('helper_a');
        $helperB = (int) $request->query('helper_b');
        $start   = $request->query('start_date');  // "YYYY-MM-DD"
        $end     = $request->query('end_date');    // "YYYY-MM-DD"

        // 作業者プルダウン
        $helpersQ = DB::table('helper')
            ->select('id', 'helpername', 'facilityno')
            ->orderBy('helpername', 'asc');

        // 施設ユーザなら自施設で絞る（任意）
        if (Auth::check() && !empty(Auth::user()->facilityno)) {
            $helpersQ->where('facilityno', Auth::user()->facilityno);
        }
        $helpers = $helpersQ->get();

        // 画面で使う変数を初期化
        $problemTasks = [
            'p1' => ['label' => '①事件・事故', 'names' => ['事件・事故']],
            'p2' => ['label' => '②苦情対応',   'names' => ['苦情対応']],
            'p3' => ['label' => '③不要な作業', 'names' => ['不要な作業']],
        ];
        $days  = [];   // [ ['ymd'=>'20250701','label'=>'7/1<br>計測'], ... ]
        $table = [];   // [ helperId => ['_name'=>..., 'p1'=>[0..n], 'p1_avg'=>.., 'p1_effect'=>.., 'p2'=>.., ...] ]

        // 集計は期間が揃っていて、A/Bのどちらかが選ばれている時だけ実行
        if ($start && $end && ($helperA || $helperB)) {
            $from = Carbon::parse($start)->startOfDay();
            $to   = Carbon::parse($end)->endOfDay();

            // 日付列を作成
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $days[] = [
                    'ymd'   => $d->format('Ymd'),
                    'label' => $d->format('n/j') . '<br>計測',
                ];
            }
            // ymd -> index の逆引き表
            $dayIndex = array_flip(array_column($days, 'ymd'));

            // A/B それぞれ集計
            foreach ([$helperA, $helperB] as $hid) {
                if (!$hid) continue;

                $hRow = $helpers->firstWhere('id', (int)$hid);
                $table[$hid] = [
                    '_name' => $hRow->helpername ?? ('ID:' . $hid),
                    'p1' => [], 'p2' => [], 'p3' => [],
                ];
                // 0 で日数分初期化
                foreach (['p1','p2','p3'] as $k) {
                    $table[$hid][$k] = array_fill(0, count($days), 0);
                }

                // 期間中の time_study を取得（start の日時で絞る）
                $rows = DB::table('time_study')
                    ->select('start', 'stop', 'task_name')
                    ->where('helpno', $hid)
                    ->whereBetween('start', [$from, $to])
                    ->orderBy('start')
                    ->get();

                foreach ($rows as $r) {
                    // 日付インデックス
                    $startAt = self::toCarbon($r->start);
                    $stopAt  = self::toCarbon($r->stop);
                    if (!$startAt || !$stopAt) continue;

                    $ymdStr = $startAt->format('Ymd');
                    if (!isset($dayIndex[$ymdStr])) continue; // 範囲外はスキップ
                    $idx = $dayIndex[$ymdStr];

                    // 分換算
                    $min = max(0, $stopAt->diffInMinutes($startAt));

                    // どの「問題あり」バケットに入れるか（名称に含まれているかで判定）
                    $bucket = null;
                    foreach ($problemTasks as $key => $meta) {
                        foreach ($meta['names'] as $nm) {
                            if ($nm !== '' && mb_strpos((string)$r->task_name, $nm) !== false) {
                                $bucket = $key; break 2;
                            }
                        }
                    }
                    if (!$bucket) continue;

                    $table[$hid][$bucket][$idx] += $min;
                }

                // 平均と効果（初日→最終日の差）
                foreach (['p1','p2','p3'] as $k) {
                    $arr = $table[$hid][$k];
                    $n   = max(1, count($arr));
                    $avg = (int) round(array_sum($arr) / $n);

                    $effect = 0;
                    if ($n >= 2) {
                        $effect = (int) ($arr[0] - $arr[$n-1]); // お好みの式に変更可
                    }
                    $table[$hid]["{$k}_avg"]    = $avg;
                    $table[$hid]["{$k}_effect"] = $effect;
                }
            }
        }

        // ← ここが重要：ビューへ必要な変数を全部渡す
        return view('comparison', compact(
            'helpers', 'start', 'end', 'helperA', 'helperB',
            'problemTasks', 'days', 'table'
        ));
    }

    private static function toCarbon($dt)
    {
        if (!$dt) return null;
        $dt = str_replace('/', '-', $dt);
        try { return Carbon::parse($dt); } catch (\Throwable $e) { return null; }
    }
}
