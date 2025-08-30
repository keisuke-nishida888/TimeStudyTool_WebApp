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

    //比較画面
    public function comparison(Request $request)
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


        $page = 'comparison';
        $title = Common::$title[$page];
        $group = Common::$group[$page];

        return view($page, compact('title' ,'page','group','data','data2','facilityno','ymdGroupData'));
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
    



}
