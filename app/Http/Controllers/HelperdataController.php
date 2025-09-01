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
        $helpno = $request->input('helpno');
        $selectedDate = $request->input('selected_date');
        $graphType = $request->input('graph_type'); // 'type' or 'category'
    
        $rows = \DB::table('time_study')
            ->join('task_table', 'time_study.task_id', '=', 'task_table.task_id')
            ->where('time_study.helpno', $helpno)
            ->whereDate('time_study.start', $selectedDate)
            ->select(
                'task_table.task_name',
                'time_study.start',
                'time_study.stop',
                'task_table.task_type_no',
                'task_table.task_category_no'
            )
            ->orderBy('time_study.start')
            ->get();
    
        $taskNames = $rows->pluck('task_name')->unique()->values();
        $taskIndividualDurations = [];
        $graphData = [];
        $timeSlots = [];
        // 24時間を1時間ごとのスロットで生成
        for ($h = 0; $h < 24; $h++) {
            $timeSlots[] = sprintf('%02d:00', $h);
        }
    
        // 各作業ごとの個別データ
        foreach ($taskNames as $taskName) {
            $taskIndividualDurations[$taskName] = [];
            $graphData[$taskName] = array_fill(0, 24, null); // 1時間ごとの配列（nullで初期化）
            foreach ($rows->where('task_name', $taskName) as $rec) {
                $start = strtotime($rec->start);
                $stop = strtotime($rec->stop);
                $startDecimal = (float)date('H', $start) + ((float)date('i', $start) / 60);
                $stopDecimal = (float)date('H', $stop) + ((float)date('i', $stop) / 60);
                $taskIndividualDurations[$taskName][] = [
                    'start' => $rec->start,
                    'stop' => $rec->stop,
                    'start_hour' => (int)date('H', $start),
                    'start_minute' => (int)date('i', $start),
                    'stop_hour' => (int)date('H', $stop),
                    'stop_minute' => (int)date('i', $stop),
                    'start_time_decimal' => $startDecimal,
                    'stop_time_decimal' => $stopDecimal,
                    'duration' => round(($stop - $start) / 60),
                    'task_type_no' => $rec->task_type_no,
                    'task_category_no' => $rec->task_category_no
                ];
                // グラフデータ（横軸：時間帯ごとに該当していたら色用番号を入れる）
                for ($h = 0; $h < 24; $h++) {
                    if ($startDecimal < ($h + 1) && $stopDecimal > $h) {
                        $graphData[$taskName][$h] = ($graphType === 'type') ? $rec->task_type_no : $rec->task_category_no;
                    }
                }
            }
        }
    
        return response()->json([
            'timeSlots' => $timeSlots,
            'taskNames' => $taskNames,
            'graphData' => $graphData,
            'graphType' => $graphType,
            'taskIndividualDurations' => $taskIndividualDurations
        ]);
    }
    
    // 期間指定：日別×作業名の合計（分）を返す
public function summary(Request $request)
{
    $helpno = $request->input('helpno');
    $start  = $request->input('start_date'); // "YYYY-MM-DD"
    $end    = $request->input('end_date');   // "YYYY-MM-DD"

    if (!$helpno || !$start || !$end) {
        return response()->json(['error' => 'bad request'], 400);
    }

    // 期間（日単位・両端含む）
    $period = CarbonPeriod::create(Carbon::parse($start), Carbon::parse($end));
    $days   = [];
    foreach ($period as $d) $days[] = $d->format('Y-m-d');
    $dayIndex = array_flip($days); // "YYYY-MM-DD" => 0..N

    // 期間のレコードを取得（開始日の属する日で集計）
    $rows = DB::table('time_study')
        ->join('task_table', 'time_study.task_id', '=', 'task_table.task_id')
        ->where('time_study.helpno', $helpno)
        ->whereBetween(DB::raw('DATE(time_study.start)'), [$start, $end])
        ->select(
            'task_table.task_name',
            'task_table.task_type_no',
            'time_study.start',
            'time_study.stop'
        )
        ->orderBy('time_study.start')
        ->get();

    // 準備
    $directByTask   = []; // task => [day0, day1, ...]（分）
    $indirectByTask = [];
    $otherByTask    = [];

    $directTotals   = array_fill(0, count($days), 0);
    $indirectTotals = array_fill(0, count($days), 0);

    // 集計
    foreach ($rows as $r) {
        $dateKey = Carbon::parse($r->start)->format('Y-m-d');
        if (!isset($dayIndex[$dateKey])) continue;
        $idx = $dayIndex[$dateKey];

        // 分に丸め（※レコードが日跨ぎしない前提。跨ぐ可能性がある場合はクリップ処理を追加）
        $minutes = max(0, (int) round((strtotime($r->stop) - strtotime($r->start)) / 60));

        // タスク別に配列を初期化
        $ensureArr = function (&$arr, $task) use ($days) {
            if (!isset($arr[$task])) $arr[$task] = array_fill(0, count($days), 0);
        };

        // 種別で仕分け（0:直接 / 1:間接 / それ以外:その他）
        if ((int)$r->task_type_no === 0) {
            $ensureArr($directByTask, $r->task_name);
            $directByTask[$r->task_name][$idx] += $minutes;
            $directTotals[$idx] += $minutes;
        } elseif ((int)$r->task_type_no === 1) {
            $ensureArr($indirectByTask, $r->task_name);
            $indirectByTask[$r->task_name][$idx] += $minutes;
            $indirectTotals[$idx] += $minutes;
        } else {
            $ensureArr($otherByTask, $r->task_name);
            $otherByTask[$r->task_name][$idx] += $minutes;
        }
    }

    return response()->json([
        'days'            => $days,
        'directByTask'    => $directByTask,
        'indirectByTask'  => $indirectByTask,
        'otherByTask'     => $otherByTask,
        'directTotals'    => $directTotals,
        'indirectTotals'  => $indirectTotals,
    ]);
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
