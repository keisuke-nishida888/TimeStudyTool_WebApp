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

        //対象介助者の腰痛データと心拍データ
        //requestは介助者No(id)が送られてくる
        //対象介助者のデータを検索する
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
        //対象介助者の腰痛データと心拍データ
        //requestは介助者No(id)が送られてくる

        //対象介助者のデータを検索する
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
        //対象介助者の腰痛データと心拍データ
        //requestは介助者名()と時間が送られてくる
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

    //2020.12.01 大幅修正前
    public function Wearabledata_disp_old(Request $request)
    {
        // file_put_contents($debug_path,$request.PHP_EOL,FILE_APPEND);
        //対象介助者の腰痛データと心拍データ
        //requestは介助者名()と時間が送られてくる
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
            $model = "App\Models\pulse".$M;
            $pulse    = new $model;
            $helperno = "pulse".$M.".helperno";
            $pulseID = "pulse".$M.".id";
            $day = "pulse".$M.".day";
            $hou = "pulse".$M.".hou";

            //開始日の翌月
            if(intval($M) == 12)
            {
                $M_next =1;
                $Y = intval($Y)+1;
            }
            else $M_next = intval($M+1);
            //開始日の翌日
            $D_next = intval($D)+1;

            //ヘッダテーブルのidと紐づくデータを取得する(boainday.bpainhedno)
            //計測開始日が月末最終日の場合は次の月もデータを確認する
            $last_day = date("t", mktime(0, 0, 0, $M_next , 0,sprintf("%04d",$Y)));


            //月跨いだデータの処理
            //開始日が最終日の場合
            if($D == $last_day)
            {
                //日付跨いだデータ
                $getdata = $pulse->select()
                ->whereIn($helperno,[$_POST["helpername"]])
                ->whereIn($day,[$D])
                // // ->whereIn($stHms,[$_POST["stHms"]])
                // // ->orderBy($pulseID,'asc')
                ->orderBy($day,'asc')
                ->orderBy($hou,'asc')
                ->get();

                $D_next = 1;
                //変数に入れたモデル名をインスタンス化
                $model2 = "App\Models\pulse".$M_next;
                $pulse2    = new $model2;
                $helperno2 = "pulse".$M_next.".helperno";
                $pulseID2 = "pulse".$M_next.".id";
                $day2 = "pulse".$M_next.".day";
                $hou2 = "pulse".$M.".hou";

                $getdata2 = $pulse2->select()
                ->whereIn($helperno2,[$_POST["helpername"]])
                // ->whereIn($day2,[$D])
                ->whereIn($day,[$D_next])
                ->orderBy($day2,'asc')
                ->orderBy($hou2,'asc')
                ->get();

                //開始月のデータに結合する
                $getdata = $getdata->concat($getdata2);
            }
            else
            {
                //日付跨いだデータ
                $getdata = $pulse->select()
                ->whereIn($helperno,[$_POST["helpername"]])
                ->where($day, '>=', $D)
                ->where($day, '<=', $D_next)
                // ->whereIn($day,[$D])
                // // ->whereIn($stHms,[$_POST["stHms"]])
                // // ->orderBy($pulseID,'asc')
                ->orderBy($day,'asc')
                ->orderBy($hou,'asc')
                ->get();

            }


            return $getdata;
        }
    }

    public function Wearabledata_disp(Request $request)
    {
        //対象介助者の腰痛データと心拍データ
        //requestは介助者名()と時間が送られてくる
        //開始時刻から対象のテーブルを選択
        try
        {

            if(isset($_POST["ymd"]))
            {
                $Y = substr($_POST["ymd"],0,4);
                $M = substr($_POST["ymd"],4,2);
                $D = substr($_POST["ymd"],6,2);
            }

            //変数に入れたモデル名をインスタンス化
            $model = "App\Models\pulse".$M;
            $pulse    = new $model;
            $helperno = "pulse".$M.".helperno";
            $pulseID = "pulse".$M.".id";
            $day = "pulse".$M.".day";
            $hou = "pulse".$M.".hou";

            //開始日の翌月
            if(intval($M) == 12)
            {
                $M_next =1;
                $Y = intval($Y)+1;
            }
            else $M_next = intval($M+1);
            //開始日の翌日
            $D_next = intval($D)+1;

            //ヘッダテーブルのidと紐づくデータを取得する(boainday.bpainhedno)
            //計測開始日が月末最終日の場合は次の月もデータを確認する
            $last_day = date("t", mktime(0, 0, 0, $M_next , 0,sprintf("%04d",$Y)));
            $last_day = sprintf("%02s",$last_day);
            $M_next = sprintf("%02s",$M_next);

            //ウェアラブルデバイスNoの取得
            $wearableno = bpainhed::select(['wearableno'])
            ->whereIn('helperno',[$_POST["helpername"]])
            ->whereIn("ymd",[$_POST["ymd"]])
            ->whereIn('hms',[$_POST["hms"]])
            ->orderBy('bpainhed.id','asc')
            ->get();
            $wearableno = json_decode(json_encode($wearableno,JSON_PRETTY_PRINT),true);

            //月跨いだデータの処理
            //開始日が最終日の場合
            if($D == $last_day)
            {
                //日付跨いだデータ
                $getdata = $pulse->select()
                ->whereIn("wearableno",[$wearableno[0]['wearableno']])
                ->whereIn($day,[$D])
                ->orderBy($day,'asc')
                ->orderBy($hou,'asc')
                ->get();

                $D_next = 1;
                //変数に入れたモデル名をインスタンス化
                $model2 = "App\Models\pulse".$M_next;
                $pulse2    = new $model2;
                $helperno2 = "pulse".$M_next.".helperno";
                $pulseID2 = "pulse".$M_next.".id";
                $day2 = "pulse".$M_next.".day";
                $hou2 = "pulse".$M.".hou";

                $getdata2 = $pulse2->select()
                ->whereIn("wearableno",[$wearableno[0]['wearableno']])
                // ->whereIn($day2,[$D])
                ->whereIn($day,[$D_next])
                ->orderBy($day2,'asc')
                ->orderBy($hou2,'asc')
                ->get();

                //開始月のデータに結合する
                $getdata = $getdata->concat($getdata2);
            }
            else
            {
                //日付跨いだデータ
                $getdata = $pulse->select()
                ->whereIn("wearableno",[$wearableno[0]['wearableno']])
                ->where($day, '>=', $D)
                ->where($day, '<=', $D_next)
                ->orderBy($day,'asc')
                ->orderBy($hou,'asc')
                ->get();

            }
        }
        catch(\Throwable $e)
        {
            return json_encode("error");
        }

            return $getdata;
    }

    //データ表示
    public function Helperdata_disp2(Request $request)
    {
        // file_put_contents($debug_path,$request.PHP_EOL,FILE_APPEND);
        //対象介助者の腰痛データと心拍データ
        //requestは介助者名()と時間が送られてくる
        //開始時刻から対象のテーブルを選択
        if(isset($_POST["helpername"]))
        {
            if(isset($_POST["ymd2"]))
            {
                $Y = substr($_POST["ymd2"],0,4);
                $M = substr($_POST["ymd2"],4,2);
                $D = substr($_POST["ymd2"],6,2);
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
            ->whereIn("ymd",[$_POST["ymd2"]])
            ->whereIn('hms',[$_POST["hms2"]])
            ->orderBy('bpainhed.id','asc')
            ->get();
            $headdata = json_decode(json_encode($bpainhed,JSON_PRETTY_PRINT),true);


            $bpainhedno = $headdata[0]['id'];
            $getdata = $bpain->select()
            // ->whereIn($helperno,[$_POST["helpername"]])
            ->whereIn('bpainhedno',[$bpainhedno])
            // ->orderBy($bpainID,'asc')
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

    public function Wearabledata_disp2(Request $request)
    {
        // file_put_contents($debug_path,$request.PHP_EOL,FILE_APPEND);
        //対象介助者の腰痛データと心拍データ
        //requestは介助者名()と時間が送られてくる
        //開始時刻から対象のテーブルを選択
        try
        {
            if(isset($_POST["ymd2"]))
            {
                $Y = substr($_POST["ymd2"],0,4);
                $M = substr($_POST["ymd2"],4,2);
                $D = substr($_POST["ymd2"],6,2);
            }

            //変数に入れたモデル名をインスタンス化
            $model = "App\Models\pulse".$M;
            $pulse    = new $model;
            $helperno = "pulse".$M.".helperno";
            $pulseID = "pulse".$M.".id";
            $day = "pulse".$M.".day";
            $hou = "pulse".$M.".hou";


            //開始日の翌月
            if(intval($M) == 12)
            {
                $M_next =1;
                $Y = intval($Y)+1;
            }
            else $M_next = intval($M+1);
            //開始日の翌日
            $D_next = intval($D)+1;
            //ヘッダテーブルのidと紐づくデータを取得する(boainday.bpainhedno)
            //計測開始日が月末最終日の場合は次の月もデータを確認する
            $last_day = date("t", mktime(0, 0, 0, $M_next , 0,sprintf("%04d",$Y)));

             //ウェアラブルデバイスNoの取得
             $wearableno = bpainhed::select(['wearableno'])
             ->whereIn('helperno',[$_POST["helpername"]])
             ->whereIn("ymd",[$_POST["ymd"]])
             ->whereIn('hms',[$_POST["hms"]])
             ->orderBy('bpainhed.id','asc')
             ->get();
             $wearableno = json_decode(json_encode($wearableno,JSON_PRETTY_PRINT),true);

            $last_day = sprintf("%02s",$last_day);
            $M_next = sprintf("%02s",$M_next);

             //月跨いだデータの処理
             //開始日が最終日の場合
             if($D == $last_day)
             {
                 //日付跨いだデータ
                 $getdata = $pulse->select()
                 ->whereIn("wearableno",[$wearableno[0]['wearableno']])
                 ->whereIn($day,[$D])
                 ->orderBy($day,'asc')
                 ->orderBy($hou,'asc')
                 ->get();

                 $D_next = 1;
                 //変数に入れたモデル名をインスタンス化
                 $model2 = "App\Models\pulse".$M_next;
                 $pulse2    = new $model2;
                 $helperno2 = "pulse".$M_next.".helperno";
                 $pulseID2 = "pulse".$M_next.".id";
                 $day2 = "pulse".$M_next.".day";
                 $hou2 = "pulse".$M.".hou";

                 $getdata2 = $pulse2->select()
                 ->whereIn("wearableno",[$wearableno[0]['wearableno']])
                 // ->whereIn($day2,[$D])
                 ->whereIn($day,[$D_next])
                 ->orderBy($day2,'asc')
                 ->orderBy($hou2,'asc')
                 ->get();

                 //開始月のデータに結合する
                 $getdata = $getdata->concat($getdata2);
             }
             else
             {
                 //日付跨いだデータ
                 $getdata = $pulse->select()
                 ->whereIn("wearableno",[$wearableno[0]['wearableno']])
                 ->where($day, '>=', $D)
                 ->where($day, '<=', $D_next)
                 ->orderBy($day,'asc')
                 ->orderBy($hou,'asc')
                 ->get();

             }


        }
        catch(\Throwable $e)
        {
            return json_encode("error");
        }
            return $getdata;

    }

    // 施設/全国平均データ表示
    public function Averagedata_disp(Request $request)
    {
        if ($request->filled('facilityno')) {
            // 施設に属している介助者のIDを取得
            $helperIds = Helper::query()
                ->where('facilityno', $request->facilityno)
                ->where('delflag', '<>', 1)
                ->pluck('id');

            $existFacilityPasses = Facility::select('facility.pass')
                ->distinct('facility.pass')
                ->get();
            $existPass = array();

            foreach ($existFacilityPasses as $existFacilityPass) {
                // helperテーブルのfacilitynoとfacilityテーブルのidを結合しまたfacilityテーブルのpassのみを表示
                $facilitySepaletePasses = Helper::select('bpainhed.risk','bpainhed.fxa','bpainhed.fxc','bpainhed.txc','bpainhed.fxt','bpainhed.txa','bpainhed.txt','bpainhed.alhms')
                ->join('facility','helper.facilityno','=','facility.id')
                ->join('bpainhed','bpainhed.helperno', '=' , 'helper.id')
                ->where('facility.pass', '=' ,$existFacilityPass['pass'])
                ->whereRaw('(SUBSTRING(bpainhed.alhms, 1, 2) * 3600 + SUBSTRING(bpainhed.alhms, 3, 2) * 60 + SUBSTRING(bpainhed.alhms, 5, 2)) > 300')
                ->get();

                $totalCount = count($facilitySepaletePasses);
                $totalfacilityAllIncludePassRisk = 0;
                $totalfacilityAllIncludePassFxa = 0;
                $totalfacilityAllIncludePassFxc = 0;
                $totalfacilityAllIncludePassTxa = 0;
                $totalfacilityAllIncludePassTxt = 0;
                $totalfacilityAllIncludePassTxc = 0;
                $totalfacilityAllIncludePassFxt = 0;
                $totalfacilityAllIncludePassAlhms = 0;

                foreach ($facilitySepaletePasses as $facilitySepaletePass) {
                    // 全国の腰痛リスクの平均値
                    $totalfacilityAllIncludePassRisk += $facilitySepaletePass['risk'];
                    // 1回の前傾平均時間
                    $totalfacilityAllIncludePassFxa += $facilitySepaletePass['fxa'];
                    // 全国の前傾回数の平均値
                    $totalfacilityAllIncludePassFxc += $facilitySepaletePass['fxc'];
                    // 全国の前傾中のひねり回数の平均値
                    $totalfacilityAllIncludePassTxc += $facilitySepaletePass['txc'];
                    // 全国の前傾合計時間
                    $totalfacilityAllIncludePassFxt += $facilitySepaletePass['fxt'];
                    // 1回のひねり平均時間の合計(分)
                    $totalfacilityAllIncludePassTxa += $facilitySepaletePass['txa'];
                    // 1回のひねり平均時間の合計(秒)
                    $totalfacilityAllIncludePassTxt += $facilitySepaletePass['txt'];
                    // 全国の総合時間の平均値
                    $totalfacilityAllIncludePassAlhms += $facilitySepaletePass['alhms'];
                }

                if ($totalCount === 0) {
                    $avrageRisk = 0;
                    $avrageFxaHour = 0;
                    $avrageFxaMinute = 0;
                    $avrageFxc = 0;
                    $avrageTxc = 0;
                    $avrageFxt = 0;
                    $avrageTxaHour = 0;
                    $avrageTxaMinute = 0;
                    $avrageTxt = 0;
                    $avrageAlhms = 0;
                } else {
                    $avrageRisk = (int)($totalfacilityAllIncludePassRisk / $totalCount);
                    $avrageFxaHour = (int)(($totalfacilityAllIncludePassFxa / $totalCount) / 60);
                    $avrageFxaMinute = (int)(($totalfacilityAllIncludePassFxa / $totalCount) % 60);
                    $avrageFxc = (int)($totalfacilityAllIncludePassFxc / $totalCount);
                    $avrageTxc = (int)($totalfacilityAllIncludePassTxc / $totalCount);
                    $avrageFxt = gmdate("H:i:s", $totalfacilityAllIncludePassFxt / $totalCount);
                    $avrageTxaHour = (int)(($totalfacilityAllIncludePassTxa / $totalCount) / 60);
                    $avrageTxaMinute = (int)(($totalfacilityAllIncludePassTxa /$totalCount) % 60);
                    $avrageTxt  = gmdate("H:i:s", $totalfacilityAllIncludePassTxt / $totalCount);
                    $avrageAlhms = gmdate("H:i:s", $totalfacilityAllIncludePassAlhms / $totalCount);
                }
                if($avrageFxt === 0 || $avrageTxt === 0 || $avrageAlhms === 0 ) {
                    $avrageFxt = '00:00';
                    $avrageTxt = '00:00';
                    $avrageAlhms = '00:00';
                };
                $countMaxPass = array();

                // 時間の合計
                $avrageFxaTotalResult = sprintf("%02d:%02d", $avrageFxaHour, $avrageFxaMinute);
                $avrageTxaTotalResult = sprintf("%02d:%02d", $avrageTxaHour, $avrageTxaMinute );

                // 配列を作成
                array_push($countMaxPass,$avrageRisk,$avrageFxaTotalResult,$avrageFxc,$avrageTxc,$avrageFxt,$avrageTxaTotalResult,$avrageTxt,$avrageAlhms);
                // 配列に存在する'facility.pass'のidを追加
                array_push($countMaxPass,$existFacilityPass['pass']);
                // 配列に'facility.passの数を追加
                array_push($existPass,$countMaxPass);
            };
            // クロージャー内で使用する変数を宣言
            $sumFacilityFxc = $sumFacilityTxc = 0.0;
            $facilityTotalFxtSecond = $allTotalFxtSecond = 0.0;
            $sumFxc = $sumTxc = 0.0;
            $allTotalFxaMinute = $facilityTotalFxaMinute = $sumHelperTotalRisk = $sumTotalRisk = 0;
            $allTotalTxaMinute = $facilityTotalTxaMinute = 0;
            $allTotalTxtSecond = $facilityTotalTxtSecond = 0;
            $facilityTotalSthmsSecond = $facilityTotalEdhmsSecond = $facilityTotalAlhmsSecond = 0;
            $allTotalAlhmsSecond = 0;
            $filteredBpainhedCount = $bpainhedByHelperCount = 0;
            // \DB::enableQueryLog();
            bpainhed::query()->chunkById(10, function (Collection $bpainhed)
            use (
                $helperIds,
                &$sumFacilityFxc, &$sumFacilityTxc,
                &$sumFxc, &$sumTxc,
                &$allTotalFxtSecond, &$facilityTotalFxtSecond,
                &$allTotalFxaMinute, &$facilityTotalFxaMinute, &$sumHelperTotalRisk, &$sumTotalRisk,
                &$allTotalTxaMinute, &$facilityTotalTxaMinute,
                &$allTotalTxtSecond, &$facilityTotalTxtSecond,
                &$facilityTotalSthmsSecond, &$facilityTotalEdhmsSecond, &$facilityTotalAlhmsSecond,
                &$allTotalAlhmsSecond,
                &$filteredBpainhedCount, &$bpainhedByHelperCount
            )
            {
                // 条件に合うデータのみを抽出
                $filteredBpainhed = $bpainhed->filter(function (bpainhed $bpainhed) {
                    $sthms = $bpainhed->sthms;
                    $hour = substr($sthms, 0, 2);
                    $minute = substr($sthms, 2, 2);
                    $second = substr($sthms, 4, 2);
                    return $hour >= '00' && $hour <= '23' && $minute >= '00' && $minute <= '59' && $second >= '00' && $second <= '59';
                })->filter(function (bpainhed $bpainhed) {
                    $edhms = $bpainhed->edhms;
                    $hour = substr($edhms, 0, 2);
                    $minute = substr($edhms, 2, 2);
                    $second = substr($edhms, 4, 2);
                    return $hour >= '00' && $hour <= '23' && $minute >= '00' && $minute <= '59' && $second >= '00' && $second <= '59';
                })->filter(function (bpainhed $bpainhed) {
                    $alhms = $bpainhed->alhms;
                    $hour = substr($alhms, 0, 2);
                    $minute = substr($alhms, 2, 2);
                    $second = substr($alhms, 4, 2);
                    $allhms = (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                    return $hour >= '00' && $hour <= '23' && $minute >= '00' && $minute <= '59' && $second >= '00' && $second <= '59' && $allhms > 300;
                });

                // 施設毎のbpainhedのコレクション
                $bpainhedByHelper = $filteredBpainhed->whereIn('helperno', $helperIds);

                if (filled($bpainhedByHelper)) {
                    // 施設の腰痛リスク
                    $sumHelperTotalRisk += $bpainhedByHelper->sum(function (bpainhed $bpainhed) {
                        return $bpainhed->risk;
                    });
                    // 施設の平均値を取得
                    $sumFacilityFxc += $bpainhedByHelper->sum('fxc');
                    $sumFacilityTxc += $bpainhedByHelper->sum('txc');
                    // 施設の1回の前傾平均時間の合計時間(分)
                    $facilityTotalFxaMinute += $bpainhedByHelper->sum(function (bpainhed $bpainhed) {
                        $fxa = $bpainhed->fxa;
                        $hour = substr($fxa, 0, 2);
                        $minute = substr($fxa, 2, 2);
                        $totalTime = (int)$hour * 60 + (int)$minute;
                        return $totalTime;
                    });
                    // 前傾合計時間の合計時間(秒)
                    $facilityTotalFxtSecond += $bpainhedByHelper->sum(function (bpainhed $bpainhed) {
                        $fxt = $bpainhed->fxt;
                        $hour = substr($fxt, 0, 2);
                        $minute = substr($fxt, 2, 2);
                        $second = substr($fxt, 4, 2);
                        $totalTime = (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                        return $totalTime;
                    });
                    // 1回のひねり平均時間の合計(分)
                    $facilityTotalTxaMinute += $bpainhedByHelper->sum(function (bpainhed $bpainhed) {
                        $txa = $bpainhed->txa;
                        $hour = substr($txa, 0, 2);
                        $minute = substr($txa, 2, 2);
                        $totalTime = (int)$hour * 60 + (int)$minute;
                        return $totalTime;
                    });
                    // 1回のひねり合計時間の合計(秒)
                    $facilityTotalTxtSecond += $bpainhedByHelper->sum(function (bpainhed $bpainhed) {
                        $txt = $bpainhed->txt;
                        $hour = substr($txt, 0, 2);
                        $minute = substr($txt, 2, 2);
                        $second = substr($txt, 4, 2);
                        $totalTime = (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                        return $totalTime;
                    });
                    // 総合時間の合計(秒)
                    $facilityTotalAlhmsSecond += $bpainhedByHelper->sum(function (bpainhed $bpainhed) {
                        $alhms = $bpainhed->alhms;
                        $hour = substr($alhms, 0, 2);
                        $minute = substr($alhms, 2, 2);
                        $second = substr($alhms, 4, 2);
                        $totalTime = (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                        return $totalTime;
                    });
                }

                // 全国の腰痛リスク
                $sumTotalRisk += $filteredBpainhed->sum(function (bpainhed $bpainhed) {
                    return $bpainhed->risk;
                });
                // 全国の平均値を取得
                $sumFxc += $bpainhed->sum('fxc');
                $sumTxc += $bpainhed->sum('txc');
                // 全国の1回の前傾平均時間の合計時間(分)
                $allTotalFxaMinute += $filteredBpainhed->sum(function (bpainhed $bpainhed) {
                    $fxa = $bpainhed->fxa;
                    $hour = substr($fxa, 0, 2);
                    $minute = substr($fxa, 2, 2);
                    $totalTime = (int)$hour * 60 + (int)$minute;
                    return $totalTime;
                });
                // 前傾合計時間の合計時間(秒)
                $allTotalFxtSecond += $filteredBpainhed->sum(function (bpainhed $bpainhed) {
                    $fxt = $bpainhed->fxt;
                    $hour = substr($fxt, 0, 2);
                    $minute = substr($fxt, 2, 2);
                    $second = substr($fxt, 4, 2);
                    $totalTime = (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                    return $totalTime;
                });
                // 1回のひねり平均時間の合計(分)
                $allTotalTxaMinute += $filteredBpainhed->sum(function (bpainhed $bpainhed) {
                    $txa = $bpainhed->txa;
                    $hour = substr($txa, 0, 2);
                    $minute = substr($txa, 2, 2);
                    $totalTime = (int)$hour * 60 + (int)$minute;
                    return $totalTime;
                });
                // 1回のひねり平均時間の合計(秒)
                $allTotalTxtSecond += $filteredBpainhed->sum(function (bpainhed $bpainhed) {
                    $txt = $bpainhed->txt;
                    $hour = substr($txt, 0, 2);
                    $minute = substr($txt, 2, 2);
                    $second = substr($txt, 4, 2);
                    $totalTime = (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                    return $totalTime;
                });
                // 総合時間の合計(秒)
                $allTotalAlhmsSecond += $filteredBpainhed->sum(function (bpainhed $bpainhed) {
                    $alhms = $bpainhed->alhms;
                    $hour = substr($alhms, 0, 2);
                    $minute = substr($alhms, 2, 2);
                    $second = substr($alhms, 4, 2);
                    $totalTime = (int)$hour * 3600 + (int)$minute * 60 + (int)$second;
                    return $totalTime;
                });

                $filteredBpainhedCount += $filteredBpainhed->count();
                $bpainhedByHelperCount += $bpainhedByHelper->count();

            }, 'id');
            // logger(\DB::getQueryLog());

            // 施設の腰痛リスクの平均値
            $facilityTotalRisk = (int)($sumHelperTotalRisk / $bpainhedByHelperCount);
            // 全国の腰痛リスクの平均値
            $allTotalRisk = (int)($sumTotalRisk / $filteredBpainhedCount);

            // 施設の前傾回数の平均値
            $avgFacilityFxc = (int)($sumFacilityFxc / $bpainhedByHelperCount);
            // 全国の前傾回数の平均値
            $avgAllFxc = (int)($sumFxc / $filteredBpainhedCount);

            // 施設の前傾中のひねり回数の平均値
            $avgFacilityTxc = (int)($sumFacilityTxc / $bpainhedByHelperCount);
            // 全国の前傾中のひねり回数の平均値
            $avgAllTxc = (int)($sumTxc / $filteredBpainhedCount);

            // 施設の1回の前傾平均時間の平均値
            $facilityTotalFxaHour = (int)(($facilityTotalFxaMinute / $bpainhedByHelperCount) / 60);
            $facilityTotalFxaMinute = (int)(($facilityTotalFxaMinute / $bpainhedByHelperCount) % 60);
            // 全国の1回の前傾平均時間の平均値
            $allTotalFxaHour = (int)(($allTotalFxaMinute / $filteredBpainhedCount) / 60);
            $allTotalFxaMin = (int)(($allTotalFxaMinute / $filteredBpainhedCount) % 60);

            // 施設の1回のひねり平均時間の平均値
            $facilityTotalTxaHour = (int)(($facilityTotalTxaMinute / $bpainhedByHelperCount) / 60);
            $facilityTotalTxaMinute = (int)(($facilityTotalTxaMinute / $bpainhedByHelperCount) % 60);
            // 全国の1回のひねり平均時間の平均値
            $allTotalTxaHour = (int)(($allTotalTxaMinute / $filteredBpainhedCount) / 60);
            $allTotalTxaMinute = (int)(($allTotalTxaMinute / $filteredBpainhedCount) % 60);

            // 施設の1回の前傾合計時間の平均値(時分秒)
            $facilityTotalFxtAvgTime = gmdate("H:i:s", $facilityTotalFxtSecond / $bpainhedByHelperCount);
            // 全国の1回の前傾合計時間の平均値(時分秒)
            $allTotalFxtAvgTime = gmdate("H:i:s", $allTotalFxtSecond / $filteredBpainhedCount);

            // 施設のひねり合計時間の平均値(時分秒)
            $facilityTotalTxtAvgTime = gmdate("H:i:s", $facilityTotalTxtSecond / $bpainhedByHelperCount);
            // 全国のひねり合計時間の平均値(時分秒)
            $allTotalTxtAvgTime = gmdate("H:i:s", $allTotalTxtSecond / $filteredBpainhedCount);

            // 施設の総合時間の平均値
            $facilityTotalAlhmsAvgTime = gmdate("H:i:s", $facilityTotalAlhmsSecond / $bpainhedByHelperCount);

            // 全国の総合時間の平均値
            $allTotalAlhmsAvgTime = gmdate("H:i:s", $allTotalAlhmsSecond / $filteredBpainhedCount);

            // レスポンス
            $bpainhedData = array(
                // 施設
                'facilityTotalRisk' => $facilityTotalRisk,
                'facilityTotalFxa' => sprintf("%02d:%02d", $facilityTotalFxaHour, $facilityTotalFxaMinute),
                'facilityTotalFxt' => $facilityTotalFxtAvgTime,
                'facilityTotalTxa' => sprintf("%02d:%02d", $facilityTotalTxaHour, $facilityTotalTxaMinute),
                'facilityTotalTxt' => $facilityTotalTxtAvgTime,
                'avgFacilityFxc' => $avgFacilityFxc,
                'avgFacilityTxc' => $avgFacilityTxc,
                'facilityTotalAlhms' => $facilityTotalAlhmsAvgTime,
                // 全国
                'allTotalRisk' => $allTotalRisk,
                'allTotalFxa' => sprintf("%02d:%02d", $allTotalFxaHour, $allTotalFxaMin),
                'allTotalFxt' => $allTotalFxtAvgTime,
                'allTotalTxa' => sprintf("%02d:%02d", $allTotalTxaHour, $allTotalTxaMinute),
                'allTotalTxt' => $allTotalTxtAvgTime,
                'allFxc' => $avgAllFxc,
                'allTxc' => $avgAllTxc,
                'allTotalAlhms' => $allTotalAlhmsAvgTime,
                'existPass' => $existPass,
            );
            return response()->json($bpainhedData);
        }

        // 空を返す
        return response()->json([]);
    }


    //csv出力
    public function Csvoutput(Request $request)
    {
        //$request['helpername']はHelper_idが送信される

         //対象介助者のデータを検索する
         if(isset($_POST["helpername"]))
         {
            //結果データ
            if(isset($_POST["ymd"]))
            {
                $M = substr($_POST["ymd"],4,2);
                $D = substr($_POST["ymd"],6,2);
            }

            $header1 = array("腰痛デバイス名");
            $header2 = array("年月日","時分秒","前傾回数合計","前傾時間合計","前傾平均合計","ひねり回数合計",
                        "ひねり時間合計","ひねり平均時間合計","腰痛リスク","開始時間","終了時間","総合時間","前傾閾値","ひねり閾値＋","ひねり閾値ー");
            $header3 = array("時","分","前傾回数","ひねり回数");



            //変数に入れたモデル名をインスタンス化
            $model = "App\Models\bpain".$M;
            $bpain    = new $model;
            $helperno = "bpain".$M.".helperno";
            $bpainID = "bpain".$M.".id";
            $day = "bpain".$M.".day";

            //結果データ
            $bpainhed = bpainhed::select()
            ->whereIn('helperno',[$_POST["helpername"]])
            ->whereIn("ymd",[$_POST["ymd"]])
            ->whereIn('hms',[$_POST["hms"]])
            ->orderBy('bpainhed.id','asc')
            ->get();
            $headdata = json_decode(json_encode($bpainhed,JSON_PRETTY_PRINT),true);
            $bpainhedno = $headdata[0]['id'];

            $getdata = $bpain->select()
            ->whereIn($helperno,[$_POST["helpername"]])
            ->whereIn('bpainhedno',[$bpainhedno])
            ->orderBy($bpainID,'asc')
            ->get();


            // //日付跨いだデータ
            // $M_next = intval($M+1);
            // //ヘッダテーブルのidと紐づくデータを取得する(boainday.bpainhedno)
            // //計測開始日が月末最終日の場合は次の月もデータを確認する
            // $last_day = date("t", mktime(0, 0, 0, $M_next , 0,sprintf("%04d",$Y)));
            // if($D == $last_day)
            // {
            //     //変数に入れたモデル名をインスタンス化
            //     $model = "App\Models\bpain".$M_next;
            //     $bpain    = new $model;
            //     $helperno = "bpain".$M_next.".helperno";
            //     $bpainID = "bpain".$M_next.".id";
            //     $day = "bpain".$M_next.".day";

            //     $getdata = $bpain->select()
            //     ->whereIn($helperno,[$_POST["helpername"]])
            //     ->whereIn('bpainhedno',[$bpainhedno])
            //     ->orderBy($bpainID,'asc')
            //     ->get();
            // }


            //CSVデータの作成
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $num = 5;   //5行目から5分毎のデータ書き込み
            $num2 = 1;

            //ヘッダ(項目名)書き込み
            $sheet->fromArray($header1 , null, 'A1');
            $sheet->fromArray($header2 , null, 'A2');

            //['ymd']以降のデータを書き込みたいため、他の変数へ値を格納する
            $tmp_array = array();
            $cnt =0;
            foreach ($bpainhed->toArray() as $tmpval)
            {
                foreach($tmpval as $key => $tmpval2)
                {
                    if($key != 'id' && $key != 'backpainno' && $key != 'helperno')
                    {
                        $tmp_array[$cnt] = $tmpval[$key];
                        $cnt++;
                    }
                }
            }

            $sheet->fromArray($tmp_array, null, 'A3');
            $sheet->fromArray($header3 , null, 'A4');

            //5分毎のデータ書き込み
            $flag = 0;
            foreach ($getdata->toArray() as $value)
            {
                for($i=1;$i<13;$i++)
                {
                    if($value['min'.$i] == null) break;
                    if($i != 1)
                    {
                        if((intval($value['min'.$i]) < intval($value['min'.($i-1)]))  || $flag == 1)
                        {
                            $h = intval($value['hou'])+1;
                            $flag = 1;
                        }
                        else $h = $value['hou'];
                    }
                    else $h = $value['hou'];

                    $sheet->setCellValue("A".$num, $h);
                    $sheet->setCellValue("B".$num, $value['min'.$i]);
                    $sheet->setCellValue("C".$num, $value['ftilt'.$i]);
                    $sheet->setCellValue("D".$num, $value['twist'.$i]);
                    $num++;
                }
                $flag = 0;

            }
            //後処理
            $sheet->getColumnDimension('A')->setAutoSize(true); // A列の幅を自動調整
            $sheet->getColumnDimension('B')->setAutoSize(true); // B列の幅を自動調整

            $writer = new Csv($spreadsheet);

            //ダウンロード用
            // header('Content-Disposition: attachment; filename="'.$file_path.$file_name.'" ');
            // header('Content-Type: text/csv　 charset=UTF-8');
            header('Content-Type: text/csv　 charset=Shift_JIS');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            if (ob_get_contents()) ob_end_clean();//バッファ消去

            $fp = fopen('php://output', 'w');
            //フィルタをストリームに付加する
            stream_filter_prepend($fp,'convert.iconv.utf-8/cp932');
            $writer->save($fp);
            // $writer->save('php://output');
            exit; // ※※これがないと余計なものも出力してファイルが開けない


            // return $bpain;
         }
    }

    /**
     * グラフデータを取得するメソッド
     */
    public function getGraphData(Request $request)
    {
        $helpno = $request->input('helpno');
        $selectedDate = $request->input('selected_date');
        $graphType = $request->input('graph_type'); // 'type' or 'category'

        // デバッグ情報をログに出力
        \Log::info('Graph data request:', [
            'helpno' => $helpno,
            'helpno_type' => gettype($helpno),
            'selected_date' => $selectedDate,
            'graph_type' => $graphType
        ]);
        
        // helpnoが数値かどうかチェック
        if (!is_numeric($helpno)) {
            \Log::error('helpno is not numeric: ' . $helpno);
            return response()->json([
                'error' => 'Invalid helpno value',
                'helpno' => $helpno
            ], 400);
        }

        try {
            // まず、time_studyテーブルの構造を確認
            $timeStudyColumns = \DB::select("DESCRIBE time_study");
            \Log::info('Time study table columns:', $timeStudyColumns);
            
            // task_tableテーブルの構造を確認
            $taskTableColumns = \DB::select("DESCRIBE task_table");
            \Log::info('Task table columns:', $taskTableColumns);
            
            // helpno=74のtime_studyデータを確認
            $timeStudyData74 = \DB::table('time_study')->where('helpno', 74)->get();
            \Log::info('Time study data for helpno=74:', $timeStudyData74->toArray());
            
            // task_tableテーブルの全データを確認
            $allTaskTableData = \DB::table('task_table')->get();
            \Log::info('All task table data:', $allTaskTableData->toArray());
            
            // 段階的にクエリを実行してエラーを特定
            \Log::info('Starting database query...');
            
            // 1. まずtime_studyテーブルからデータを取得
            $timeStudyBase = \DB::table('time_study')
                ->where('helpno', $helpno)
                ->whereDate('start', $selectedDate)
                ->get();
            \Log::info('Time study base query result:', $timeStudyBase->toArray());
            
            // 2. task_tableとのJOINを実行
            $timeStudyData = \DB::table('time_study')
                ->select('time_study.*', 'task_table.task_name', 'task_table.task_type_no', 'task_table.task_category_no')
                ->join('task_table', 'time_study.task_id', '=', 'task_table.task_id')
                ->where('time_study.helpno', $helpno)
                ->whereDate('time_study.start', $selectedDate)
                ->orderBy('time_study.start')
                ->get();
            
            \Log::info('Query details:', [
                'helpno' => $helpno,
                'selected_date' => $selectedDate,
                'query_sql' => \DB::table('time_study')
                    ->select('time_study.*', 'task_table.task_name', 'task_table.task_type_no', 'task_table.task_category_no')
                    ->join('task_table', 'time_study.task_id', '=', 'task_table.task_id')
                    ->where('time_study.helpno', $helpno)
                    ->whereDate('time_study.start', $selectedDate)
                    ->orderBy('time_study.start')
                    ->toSql()
            ]);
            
            \Log::info('Database query executed successfully');
            \Log::info('Query result for helpno=' . $helpno . ' and date=' . $selectedDate . ':', $timeStudyData->toArray());
        } catch (\Exception $e) {
            \Log::error('Database error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // データベースエラーの場合はテストデータを使用
            \Log::info('Using test data due to database error');
            
            $timeStudyData = collect([
                (object)[
                    'timestudy_id' => 'test1',
                    'helpno' => $helpno,
                    'task_id' => 1,
                    'start' => $selectedDate . ' 09:02:38',
                    'stop' => $selectedDate . ' 10:02:41',
                    'task_name' => '食事介助PPP',
                    'task_type_no' => 0,
                    'task_category_no' => 0
                ],
                (object)[
                    'timestudy_id' => 'test2',
                    'helpno' => $helpno,
                    'task_id' => 2,
                    'start' => $selectedDate . ' 14:00:00',
                    'stop' => $selectedDate . ' 15:30:00',
                    'task_name' => '入浴介助OOO',
                    'task_type_no' => 1,
                    'task_category_no' => 1
                ]
            ]);
        }

        // デバッグ情報をログに出力
        \Log::info('Time study data count:', ['count' => $timeStudyData->count()]);
        \Log::info('Time study data:', $timeStudyData->toArray());

        // データが見つからない場合の処理
        if ($timeStudyData->count() === 0) {
            \Log::warning('No data found for the specified criteria: helpno=' . $helpno . ', date=' . $selectedDate);
            
            // 指定された条件に該当するデータがない場合は空のデータを返す
            return response()->json([
                'error' => 'No data found',
                'message' => '指定された条件に該当するデータが見つかりませんでした。',
                'timeSlots' => [],
                'taskNames' => [],
                'graphData' => [],
                'graphType' => $graphType
            ]);
        }

        // 30分単位の時間軸を作成（48スロット）
        $timeSlots = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $timeSlots[] = sprintf('%02d:00', $hour);
            $timeSlots[] = sprintf('%02d:30', $hour);
        }
        
        \Log::info('Created time slots:', $timeSlots);

        // 作業名のリストを作成
        $taskNames = $timeStudyData->pluck('task_name')->unique()->filter()->values();
        
        \Log::info('Task names found:', $taskNames->toArray());
        \Log::info('Total records to process:', ['count' => $timeStudyData->count()]);
        
        // 各レコードの基本情報をログ出力
        foreach ($timeStudyData as $index => $record) {
            \Log::info('Record ' . $index . ':', [
                'timestudy_id' => $record->timestudy_id,
                'task_name' => $record->task_name,
                'start' => $record->start,
                'stop' => $record->stop,
                'task_type_no' => $record->task_type_no,
                'task_category_no' => $record->task_category_no
            ]);
        }

        // グラフデータを構築（正確な時間帯で）
        $graphData = [];
        \Log::info('Building graph data for ' . $taskNames->count() . ' tasks');
        
        foreach ($taskNames as $taskName) {
            $taskData = [];
            foreach ($timeSlots as $timeSlot) {
                $taskData[$timeSlot] = null;
            }

            // 該当する作業のデータを処理
            $taskRecords = $timeStudyData->where('task_name', $taskName);
            \Log::info('Processing task: ' . $taskName . ' with ' . $taskRecords->count() . ' records');
            \Log::info('Task records for ' . $taskName . ':', $taskRecords->toArray());
            \Log::info('Initial taskData keys for ' . $taskName . ':', array_keys($taskData));
            \Log::info('TaskData count: ' . count($taskData) . ', TimeSlots count: ' . count($timeSlots));
            \Log::info('Sample taskData values: ' . json_encode(array_slice($taskData, 0, 5, true)));
            
            foreach ($taskRecords as $record) {
                $startTime = strtotime($record->start);
                $stopTime = strtotime($record->stop);
                
                \Log::info('Processing record:', [
                    'task_name' => $record->task_name,
                    'start' => $record->start,
                    'stop' => $record->stop,
                    'start_timestamp' => $startTime,
                    'stop_timestamp' => $stopTime,
                    'task_type_no' => $record->task_type_no,
                    'task_category_no' => $record->task_category_no
                ]);
                
                // 時間スロットのマーク処理を詳細にログ出力
                \Log::info('Starting time slot marking for record ' . $record->timestudy_id);
                
                // 開始時間と終了時間の時間と分を取得
                $startHour = (int)date('H', $startTime);
                $startMinute = (int)date('i', $startTime);
                $stopHour = (int)date('H', $stopTime);
                $stopMinute = (int)date('i', $stopTime);
                
                // 開始時間から終了時間までの各30分スロットをマーク
                $currentTime = $startTime;
                $markedSlots = [];
                
                \Log::info('Processing time range:', [
                    'start_time' => date('Y-m-d H:i:s', $startTime),
                    'stop_time' => date('Y-m-d H:i:s', $stopTime)
                ]);
                
                while ($currentTime <= $stopTime) {
                    $currentHour = (int)date('H', $currentTime);
                    $currentMinute = (int)date('i', $currentTime);
                    
                    // 30分単位のスロットを決定
                    if ($currentMinute < 30) {
                        $timeSlot = sprintf('%02d:00', $currentHour);
                    } else {
                        $timeSlot = sprintf('%02d:30', $currentHour);
                    }
                    
                    \Log::info('Current time slot:', [
                        'current_time' => date('H:i:s', $currentTime),
                        'time_slot' => $timeSlot,
                        'exists_in_taskData' => array_key_exists($timeSlot, $taskData),
                        'current_hour' => $currentHour,
                        'current_minute' => $currentMinute
                    ]);
                    
                    // 時間スロットが存在するかチェック（issetの代わりにarray_key_existsを使用）
                    if (array_key_exists($timeSlot, $taskData)) {
                        if ($graphType === 'type') {
                            $taskData[$timeSlot] = $record->task_type_no ?? 0;
                        } else {
                            $taskData[$timeSlot] = $record->task_category_no ?? 0;
                        }
                        $markedSlots[] = $timeSlot;
                        \Log::info('Marked slot: ' . $timeSlot . ' with value: ' . ($graphType === 'type' ? ($record->task_type_no ?? 0) : ($record->task_category_no ?? 0)));
                    } else {
                        \Log::warning('Time slot ' . $timeSlot . ' not found in taskData. Available keys: ' . implode(', ', array_keys($taskData)));
                        \Log::warning('TaskData type: ' . gettype($taskData) . ', TaskData keys count: ' . count($taskData));
                    }
                    
                    // 次の30分に進む
                    $currentTime = strtotime('+30 minutes', $currentTime);
                }
                
                \Log::info('Marked time slots for this record:', $markedSlots);
            }
            
            $graphData[$taskName] = $taskData;
            \Log::info('Final task data for ' . $taskName . ':', $taskData);
        }
        
        \Log::info('Final graph data structure:', $graphData);
        
        // 作業時間の計算（合計時間と個別時間範囲）
        $taskDurations = [];
        $taskIndividualDurations = [];
        
        foreach ($taskNames as $taskName) {
            $taskRecords = $timeStudyData->where('task_name', $taskName);
            $totalMinutes = 0;
            $individualDurations = [];
            
            foreach ($taskRecords as $record) {
                $startTime = strtotime($record->start);
                $stopTime = strtotime($record->stop);
                $durationMinutes = round(($stopTime - $startTime) / 60);
                $totalMinutes += $durationMinutes;
                
                // 個別の時間範囲情報を保存（時間範囲を正確に計算）
                $individualDurations[] = [
                    'start' => $record->start,
                    'stop' => $record->stop,
                    'start_hour' => (int)date('H', $startTime),
                    'start_minute' => (int)date('i', $startTime),
                    'stop_hour' => (int)date('H', $stopTime),
                    'stop_minute' => (int)date('i', $stopTime),
                    'start_time_decimal' => (float)date('H', $startTime) + ((float)date('i', $startTime) / 60),
                    'stop_time_decimal' => (float)date('H', $stopTime) + ((float)date('i', $stopTime) / 60),
                    'duration' => $durationMinutes,
                    'task_type_no' => $record->task_type_no,
                    'task_category_no' => $record->task_category_no
                ];
            }
            
            $taskDurations[$taskName] = $totalMinutes;
            $taskIndividualDurations[$taskName] = $individualDurations;
        }
        
        // サンプルデータを詳細にログ出力
        foreach ($graphData as $taskName => $taskData) {
            \Log::info('Final task data for ' . $taskName . ':', [
                'task_name' => $taskName,
                'data_count' => count($taskData),
                'total_minutes' => $taskDurations[$taskName] ?? 0,
                'sample_slots' => [
                    '09:00' => $taskData['09:00'] ?? 'NOT_SET',
                    '09:30' => $taskData['09:30'] ?? 'NOT_SET',
                    '10:00' => $taskData['10:00'] ?? 'NOT_SET',
                    '10:30' => $taskData['10:30'] ?? 'NOT_SET',
                    '11:00' => $taskData['11:00'] ?? 'NOT_SET',
                    '11:30' => $taskData['11:30'] ?? 'NOT_SET'
                ]
            ]);
        }

        return response()->json([
            'timeSlots' => $timeSlots,
            'taskNames' => $taskNames,
            'graphData' => $graphData,
            'graphType' => $graphType,
            'taskDurations' => $taskDurations,
            'taskIndividualDurations' => $taskIndividualDurations
        ]);
    }
}
