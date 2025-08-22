<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;
use Illuminate\Http\Request;
use App\Models\Helper;
use App\Models\Wearable;
use App\Models\Facility;
use App\Models\BackPain;
use App\Models\Meauser;
use App\Models\bpainhed;
use \App\Rules\space;
use Illuminate\Support\Facades\Validator;

//PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
// 既存の use 群の下あたりに追加
use App\Models\Group;                 // ★ 追加
use Illuminate\Support\Facades\DB;     // ★ 追加（トランザクションに使用）


class HelperController extends Controller
{
    public function del(Request $request)
    {
        //ログインユーザ以外を選択していること
        if(isset($_POST["name"]))
        {
            if($_POST["name"] == "del")
            {
                if(isset($_POST["data"]))
                {
                    //save() ->updated_atのカラムが更新されない
                    //update() ->updated_atのカラムが更新される

                    //該当するMeauserのデータを削除した日含めて先の日付のものは削除する
                    // Meauser::whereIn('helperno',[$_POST["data"]])
                    // ->where('ymd', '>=', [date("Ymd")])
                    // ->delete();

                    echo Helper::where('id',$_POST["data"])->update(['delflag'=>1 ,'upduserno'=> Auth::user()->id]);
                }

                return ;
            }
        }
    }

    // 作業者一覧の共通クエリ（施設必須／groupnoは任意）
    private function helperListQuery($facilityno, $groupno = null)
    {
        $q = Helper::select('helper.id as helper_id', 'helper.*')
            ->where('helper.facilityno', $facilityno)
            ->where('helper.delflag', '!=', 1)
            ->orderBy('helper.id', 'asc');

        if (!empty($groupno)) {
            $q->where('helper.groupno', $groupno);
        }
        return $q;
    }



    // 作業者一覧
    public function index(Request $request)
    {
        $facilityname = '';
        $wearable     = [];
        $data         = [];
        $ymdData      = collect();

        // ★ 追加: グループ番号（施設内のグループ）を受け取る
        $groupno = $request->input('groupno') ?? $request->query('groupno');

        // 施設番号の優先順位:
        // 1) POST:id（施設一覧からの遷移）
        // 2) GET:facilityno（URLパラメータ）
        // 3) 施設ユーザの場合はログインユーザに紐づくfacilityno
        // 4) リファラのクエリから facilityno（パンくず等）
        $facilityno =
            $request->input('id') ??
            $request->query('facilityno') ??
            ((Auth::user()->authority ?? null) == 3 ? (Auth::user()->facilityno ?? null) : null);

        if (empty($facilityno)) {
            // 4) リファラからfacilitynoを拾う（なければ mainmenu へ）
            $ref = $_SERVER['HTTP_REFERER'] ?? '';
            if ($ref) {
                parse_str(parse_url($ref, PHP_URL_QUERY) ?? '', $q);
                if (!empty($q['facilityno'])) {
                    $facilityno = $q['facilityno'];
                }
            }
            // まだ無ければ mainmenu へ（従来動作を踏襲）
            if (empty($facilityno)) {
                $page  = 'mainmenu';
                $title = Common::$title[$page];
                $group = Common::$group[$page];
                $data  = '';
                return view($page, compact('title', 'page', 'group', 'data', 'facilityno'));
            }
        }

        // 施設名の取得（存在チェックと削除フラグ考慮）
        $facility = Facility::where('id', $facilityno)
            ->where('delflag', '!=', 1)
            ->first();
        if ($facility) {
            $facilityname = $facility->facility;
        }

        // 作業者一覧（★groupnoで絞り込み対応）
        $getdata = $this->helperListQuery($facilityno, $groupno)->get();
        $data    = json_decode(json_encode($getdata, JSON_PRETTY_PRINT), true);

        // ウェアラブル（従来どおりビューに渡す）
        $wearable = Wearable::orderBy('id', 'asc')
            ->where('delflag', '!=', 1)
            ->get();
        $wearable = json_decode(json_encode($wearable, JSON_PRETTY_PRINT), true);

        // カレンダーマーキング用（日付一覧）
        // → 画面に出している作業者のみを対象（施設＋必要ならグループで絞り込んだ helper のみ）
        $helperIds = $getdata->pluck('helper_id')->all(); // selectでaliasにしているため helper_id を使う
        if (!empty($helperIds)) {
            $ymdData = bpainhed::join('helper', 'bpainhed.helperno', '=', 'helper.id')
                ->select('bpainhed.ymd')
                ->whereIn('bpainhed.helperno', $helperIds)
                ->get();
        }

        // 画面描画
        $page  = 'helper';
        $title = Common::$title[$page];
        $group = Common::$group[$page];

        return view($page, compact(
            'title',
            'page',
            'group',
            'data',
            'facilityno',
            'wearable',
            'facilityname',
            'ymdData'
        ));
    }


    public function add_index(Request $request)
    {
        if(isset($_POST["facilityno"]))
        {
            //ウェアラブルデバイス
            // $wearable = Wearable::orderBy('id','asc')
            // ->whereNotIn('delflag',[1])->get();
            //腰痛デバイス
            // $backPain = BackPain::orderBy('id','asc')
            // ->whereNotIn('delflag',[1])->get();

            //施設情報
            $getdata2 = Facility::select()
            ->whereIn('facility.id',[$_POST["facilityno"]])
            ->whereNotIn('facility.delflag',[1])
            ->get();
            $data2 = json_decode(json_encode($getdata2,JSON_PRETTY_PRINT),true);
            $facilityno = $_POST["facilityno"];
        }
        else
        {

            // URLパラメータの部分だけを変数に格納
            $param = $_SERVER['HTTP_REFERER'];
            $tmp = [];
            // parse_strで分解処理
            //parse_url でURLを分解してパラメータのみ取得する
            parse_str(parse_url($param, PHP_URL_QUERY), $query);
            if (isset($query))
            {
                $facilityno = $query['facilityno'];
            }

            //ウェアラブルデバイス
            // $wearable = Wearable::orderBy('id','asc')
            // ->whereNotIn('delflag',[1])->get();
            //腰痛デバイス
            // $backPain = Wearable::orderBy('id','asc')
            // ->whereNotIn('delflag',[1])->get();
            //施設情報
            $getdata2 = Facility::select()
            ->whereIn('facility.id',[$facilityno])
            ->whereNotIn('facility.delflag',[1])
            ->get();
            $data2 = json_decode(json_encode($getdata2,JSON_PRETTY_PRINT),true);
        }
        $data = "";
        $page = 'helper_add';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        // return view($page, compact('title' ,'page','group','data2','wearable','backPain','data'));
        return view($page, compact('title' ,'page','group','data2','data','facilityno' ));
    }

        public function fix_index(Request $request)
    {
        // 既定値
        $data = "";
        $data2 = "";
        $facilityno = $request->query('facilityno') ?? old('facilityno') ?? "";
        $groupName = ''; // ★ 表示用のグループ名

        if ($request->isMethod('POST')) {
            // 作業者情報
            $getdata = Helper::whereIn('id', [$request->input('id')])->get();
            $data = json_decode(json_encode($getdata, JSON_PRETTY_PRINT), true);

            // 施設情報
            $getdata2 = Facility::select()
                ->whereIn('facility.id', [$data[0]["facilityno"]])
                ->whereNotIn('facility.delflag', [1])
                ->get();
            $data2 = json_decode(json_encode($getdata2, JSON_PRETTY_PRINT), true);

            $facilityno = $data[0]["facilityno"];

            // ★ 追加：現在のグループ名を取得（helper.groupno → groups.group_name）
            if (!empty($data[0]['groupno'])) {
                $grp = Group::where('group_id', $data[0]['groupno'])->first();
                if ($grp) {
                    $groupName = $grp->group_name;
                }
            }
        } elseif ($request->isMethod('GET')) {
            // 何も取得しない（空のまま表示）
        } else {
            // 何もしない
        }

        $page = 'helper_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];

        if (isset($_POST["addmess"])) {
            $addmess = $_POST["addmess"];
            return view($page, compact('title', 'page', 'group', 'data', 'data2', 'addmess', 'facilityno', 'groupName'));
        } else {
            return view($page, compact('title', 'page', 'group', 'data', 'data2', 'facilityno', 'groupName'));
        }
    }





    //追加
    public function HelperAdd(Request $request)
    {
        //**既に登録されているか調べる***********************************************************/
        // 作業者登録時の重複確認
        $exists =  Helper::select('helper.helpername','helper.facilityno')
        // 削除されていない作業者の中での重複チェックの処理を追加
        ->where('delflag', '=', 0)
        ->get();
        $exist_mes = array();
        $exist_mes['helpername'] = Common::$erralr_helper;
        foreach ($exists as $exist) {
            // 同じ名前で同じ施設内の場合エラーメッセージを表示
            if($exist['helpername'] == $request['helpername'] && $exist['facilityno'] == $request['facilityno'])
            {
                return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
            };
        };

        //************************************************************************************/

        //->validate();をつけたら元のページへリダイレクトする
        $rulus_obj = Common::helper_rulus();
        $rulus = json_decode(json_encode($rulus_obj), true);

        $tmp1 = $rulus['helpername'];
        array_push($tmp1,new space);
        $rulus['helpername'] = $tmp1;
        // ★ 追加：グループ名のバリデーション
        $rulus['group_name'] = ['required','string','max:100'];
        $validator = Validator::make($request->all(),$rulus, Common::$message_);
        if($validator->fails())
        {
            return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
        }
        try
        {
            if($request->isMethod('POST'))
            {
                // ★ 追加：トランザクション開始
            $insertid = DB::transaction(function () use ($request) {

                // ★ 1) 施設 × グループ名 で groups を find-or-create
                $group = Group::firstOrCreate(
                    [
                        'facilityno' => $request->input('facilityno'),
                        'group_name' => $request->input('group_name'),
                    ]
                );

                // ★ 2) helper に保存するため request に groupno を合流
                //     Common::create_helper() が $request->all() を使っている前提
                //     （もし create_helper が個別フィールド指定なら、そちらにも 'groupno' を追加してください）
                $request->merge(['groupno' => $group->group_id]);

                // ★ 3) 既存の作成ロジックをそのまま呼ぶ
                return Common::create_helper($request->all());
            });
        }

            //Meauserアップデート
            //期間の入れ違い対策はjavascriptで行う
        /*
            if(isset($_POST["measufrom"]) && isset($_POST["measuto"]))
            {
                if(mb_strlen($_POST["measufrom"])==10 &&  mb_strlen($_POST["measuto"])==10)
                {
                    $time1 = strtotime($_POST["measufrom"]);
                    $time2 = strtotime($_POST["measuto"]);

                    $from = array();
                    $from = explode("-",$_POST["measufrom"]);
                    $to = array();
                    $to = explode("-",$_POST["measuto"]);
                    $span = ($time2 - $time1) / (60 * 60 * 24);
                    $y = 0;
                    $m = 0;
                    $d = 0;
                    for($i= 0; $i < $span+1; $i++)
                    {
                        $last_day = date("t", mktime(0, 0, 0, $m+1, 0,sprintf("%04d",$y)));
                        if($i == 0)
                        {
                            $y = intval($from[0]);
                            $m = intval($from[1]);
                            $d = intval($from[2]);
                        }
                        else
                        {
                            $d = $d + 1;
                        }
                        if($d > $last_day)
                        {
                            $m = $m + 1;
                            $d = 1;
                            if($m > 12)
                            {
                                $m = 1;
                                $y = $y + 1;
                            }
                        }
                        if(sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d) >= $to[0].$to[1].$to[2])
                        {
                            //デバイスNoとも紐づける
                            if(sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d) == $to[0].$to[1].$to[2])
                            {
                                Common::create_measure($request->all(),sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d),$insertid);
                            }
                            break;
                        }
                        Common::create_measure($request->all(),sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d),$insertid);
                    }
                }
            }
        */



            //作業者情報
            $getdata = Helper::whereIn('id',[$insertid])->get();
            $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

            //ウェアラブルデバイス
            // $wearable = Wearable::orderBy('id','asc')
            //     ->whereNotIn('delflag',[1])->get();
            //腰痛デバイス
            // $backPain = BackPain::orderBy('id','asc')
            //     ->whereNotIn('delflag',[1])->get();

            //施設情報
            $getdata2 = Facility::select()
                ->whereIn('facility.id',[$data[0]["facilityno"]])
                ->whereNotIn('facility.delflag',[1])
                ->get();
            $data2 = json_decode(json_encode($getdata2,JSON_PRETTY_PRINT),true);
        }
        catch(\Throwable $e)
        {
            return json_encode("error");

        }
        return $insertid;

    }


    public function HelperFix(Request $request)
    {
        // 削除されていない作業者の中での重複チェックの処理を追加
        $exist = Helper::select()
        ->whereIn('helpername',[$_POST["helpername"]])
        ->whereNotIn('id',[$_POST["id"]])
        ->whereIn('facilityno',[$_POST["facilityno"]])
        ->where('delflag', '=', 0)
        ->exists();
        //修正中のuserno以外で該当する名前があるか調べる
        //**既に登録されているか調べる***********************************************************/
        // 作業者修正時の重複確認

        $exist_mes = array();
        $exist_mes['helpername'] = Common::$erralr_helper;

        if($exist != null)
        {
            return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
        }
        //************************************************************************************/

        //->validate();をつけたら元のページへリダイレクトする

        $rulus_obj = Common::helper_rulus();
        $rulus = json_decode(json_encode($rulus_obj), true);

        $tmp1 = $rulus['helpername'];
        array_push($tmp1,new space);
        $rulus['helpername'] = $tmp1;

        $validator = Validator::make($request->all(),$rulus, Common::$message_);
        if($validator->fails())
        {
            return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
        }
        try
        {
            if($request->isMethod('POST'))
            {
                $insertid = $_POST["id"];
                // if(isset($_POST["wearableno"]) && $_POST["wearableno"]!="") $wearableno = $_POST["wearableno"];
                // else $wearableno = 0;
                // if(isset($_POST["backpainno"]) && $_POST["backpainno"]!="") $backpainno = $_POST["backpainno"];
                // else $backpainno = 0;
                // ★ 追加：group_name が送られてくる場合のみ処理
                if ($request->filled('group_name') && $request->filled('facilityno')) {
                    $group = Group::firstOrCreate(
                        [
                            'facilityno' => $request->input('facilityno'),
                            'group_name' => $request->input('group_name'),
                        ]
                    );
                    // Update_helper に渡すため合流
                    $request->merge(['groupno' => $group->group_id]);
                }

                //アップデート
                Common::Update_helper($request->all());
                //Meauserアップデート
                //期間の入れ違い対策はjavascriptで行う
            /*
                if(isset($_POST["measufrom"]) && isset($_POST["measuto"]))
                {
                    if(mb_strlen($_POST["measufrom"])==10 &&  mb_strlen($_POST["measuto"])==10)
                    {
                        //Meauserアップデート
                        //期間の入れ違い対策はjavascriptで行う
                        $time1 = strtotime($_POST["measufrom"]);
                        $time2 = strtotime($_POST["measuto"]);

                        $from = array();
                        $from = explode("-",$_POST["measufrom"]);
                        $to = array();
                        $to = explode("-",$_POST["measuto"]);
                        $span = ($time2 - $time1) / (60 * 60 * 24);

                        $time3 = strtotime($_POST["measufrom_pre"]);
                        $time4 = strtotime($_POST["measuto_pre"]);

                        $from2 = array();
                        $from2 = explode("-",$_POST["measufrom_pre"]);
                        $to2 = array();
                        $to2 = explode("-",$_POST["measuto_pre"]);
                        $span2 = ($time4 - $time3) / (60 * 60 * 24);


                        $y = 0;
                        $m = 0;
                        $d = 0;
                        // 測定期間が一致するか調べる
                        // if($_POST["measufrom"] != $_POST["measufrom_pre"])
                        // {
                        //     //前回の日付よりも今回の日付が後
                        //     if($time3 < $time1)
                        //     {
                        //         //前回設定した日付よりも今回の日付が後の場合は、差分を削除する
                        //         //IDを検索、削除
                        //         Meauser::whereIn('helperno',[$insertid])
                        //         // ->whereIn('wearableno',[$_POST('wearableno')])
                        //         // ->whereIn('backpainno',[$_POSY['backpainno']])
                        //         ->where('ymd', '<', [sprintf("%04d",$from[0]).sprintf("%02d",$from[1]).sprintf("%02d",$from[2])])
                        //         ->delete();
                        //     }
                        // }
                        if($_POST["measuto"] != $_POST["measuto_pre"])
                        {
                            //前回の日付よりも今回の日付が前
                            if($time4 > $time2)
                            {
                                //前回設定した日付よりも今回の日付が前の場合は、差分を削除する
                                //IDを検索、削除
                                Meauser::whereIn('helperno',[$insertid])
                                ->where('ymd', '>=', [date("Ymd")])
                                ->where('ymd', '>', [sprintf("%04d",$to[0]).sprintf("%02d",$to[1]).sprintf("%02d",$to[2])])
                                ->delete();
                            }
                        }


                        //該当するデータがあるか調べる
                        for($i= 0; $i < $span+1; $i++)
                        {
                            $last_day = date("t", mktime(0, 0, 0, $m+1, 0,sprintf("%04d",$y)));
                            if($i == 0)
                            {
                                $y = intval($from[0]);
                                $m = intval($from[1]);
                                $d = intval($from[2]);
                            }
                            else
                            {
                                $d = $d + 1;
                            }
                            if($d > $last_day)
                            {
                                $m = $m + 1;
                                $d = 1;
                                if($m > 12)
                                {
                                    $m = 1;
                                    $y = $y + 1;
                                }
                            }
                            if(sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d) >= $to[0].$to[1].$to[2])
                            {
                                if(sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d) == $to[0].$to[1].$to[2])
                                {
                                    //既にデータがある場合はスキップ
                                    $rtn = Meauser::whereIn('helperno',[$insertid])
                                    ->whereIn('ymd',[sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d)])
                                    ->exists();
                                    if($rtn == false)
                                    {
                                        Common::create_measure($request->all(),sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d),$insertid);
                                    }
                                }
                                break;
                            }
                            //既にデータがある場合はスキップ
                            $rtn = Meauser::whereIn('helperno',[$insertid])
                                    ->whereIn('ymd',[sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d)])
                                    ->exists();
                            //データない場合,レコード作成
                            if($rtn == false)
                            {
                                Common::create_measure($request->all(),sprintf("%04d",$y).sprintf("%02d",$m).sprintf("%02d",$d),$insertid);
                            }
                        }

                        //MeauserのデバイスNoも更新する
                        Meauser::whereIn('helperno',[$insertid])
                        ->update(['wearableno'=> $wearableno]);

                        Meauser::whereIn('helperno',[$insertid])
                        ->update(['backpainno'=> $backpainno]);

                    }
                }
            */
                //ウェアラブルデバイスアップデート
                // Wearable::whereIn('id',[$_POST["wearableno"]])->update(['helperno'=>$insertid]);

                //腰痛デバイスアップデート
                // BackPain::whereIn('id',[$_POST["backpainno"]])->update(['helperno'=>$insertid]);

                //作業者情報
                $getdata = Helper::whereIn('id',[$insertid])->get();
                $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

                //ウェアラブルデバイス
                // $wearable = Wearable::orderBy('id','asc')
                // ->whereNotIn('delflag',[1])->get();
                //腰痛デバイス
                // $backPain = BackPain::orderBy('id','asc')
                // ->whereNotIn('delflag',[1])->get();

                //施設情報
                $getdata2 = Facility::select()
                    ->whereIn('facility.id',[$data[0]["facilityno"]])
                    ->whereNotIn('facility.delflag',[1])
                    ->get();
                $data2 = json_decode(json_encode($getdata2,JSON_PRETTY_PRINT),true);
            }
        }
        catch(\Throwable $e)
        {
            if($request->isMethod('POST'))
            {
                //作業者情報
                $getdata = Helper::whereIn('id',[$_POST["id"]])
                    ->get();
                $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

                //ウェアラブルデバイス
                // $wearable = Wearable::orderBy('id','asc')
                //     ->whereNotIn('delflag',[1])->get();
                //腰痛デバイス
                // $backPain = BackPain::orderBy('id','asc')
                //     ->whereNotIn('delflag',[1])->get();

                //施設情報
                $getdata2 = Facility::select()
                    ->whereIn('facility.id',[$data[0]["facilityno"]])
                    ->whereNotIn('facility.delflag',[1])
                    ->get();
                $data2 = json_decode(json_encode($getdata2,JSON_PRETTY_PRINT),true);
            }
            else if($request->isMethod('GET'))
            {
                $data = "";
                $data2 = "";
                //ウェアラブルデバイス
                // $wearable = Wearable::orderBy('id','asc')
                //     ->whereNotIn('delflag',[1])->get();
                //腰痛デバイス
                // $backPain = BackPain::orderBy('id','asc')
                //     ->whereNotIn('delflag',[1])->get();
            }
            else
            {
                $data = "";
                $data2 = "";
                //ウェアラブルデバイス
                // $wearable = Wearable::orderBy('id','asc')
                //     ->whereNotIn('delflag',[1])->get();
                //腰痛デバイス
                // $backPain = BackPain::orderBy('id','asc')
                //     ->whereNotIn('delflag',[1])->get();
            }
            $fixerror = "error";
            $page = 'helper_fix';
            $title = Common::$title[$page];
            $group = Common::$group[$page];
            return json_encode("error");
        }
        $page = 'helper_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        $fixmess = "修正しました。";
        return $insertid;
    }


    //修正ページ　フォームリセット
    public function cxl_HelperFix(Request $request)
    {
        if(isset($_POST["data"]))
        {
            $getdata = Helper::select()
            ->whereIn('helper.id',[$_POST["data"]])
            ->whereNotIn('helper.delflag',[1])
            ->leftJoin('facility','facility.id','=','helper.facilityno')
            ->orderBy('helper.id','asc')
            ->get();

            $adddata = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
            return $adddata;

        }
        else return 0;
    }

    // 作業者一覧のCSV出力
    public function HelperListCsvOutput(Request $request)
    {
        if ($request->has('facilityno')) {
            // 施設に属している作業者を取得
            $getdata = Helper::select('id', 'helpername')
            ->whereIn('facilityno', [$request->facilityno])
                ->orderBy('helper.id', 'asc')
                ->whereNotIn('helper.delflag', [1])
                ->get();

            // CSVデータの作成
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // ヘッダ(項目名書き込み)
            $header = array("作業者ID", "作業者名");
            $sheet->fromArray($header, null, 'A1');

            $num = 2; // 2行目から作業者情報のデータ書き込み

            foreach ($getdata->toArray() as $value) {
                $sheet->setCellValue("A" . $num, $value['id']);
                $sheet->setCellValue("B" . $num, $value['helpername']);
                $num++;
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
            if (ob_get_contents()) ob_end_clean(); //バッファ消去

            $fp = fopen('php://output', 'w');
            //フィルタをストリームに付加する
            stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932');
            $writer->save($fp);
            // $writer->save('php://output');
            exit; // ※※これがないと余計なものも出力してファイルが開けない

        }
    }

    // 作業者データのCSV出力
    public function HelperDataCsvOutput(Request $request)
    {
        // CSVのHeaderを定義
        $header1 = array("腰痛デバイス名");
        $header2 = array(
           "作業者名", "年月日", "時分秒", "前傾回数合計", "前傾時間合計", "前傾平均合計", "ひねり回数合計",
            "ひねり時間合計", "ひねり平均時間合計", "腰痛リスク", "開始時間", "終了時間", "総合時間", "前傾閾値", "ひねり閾値＋", "ひねり閾値ー"
        );
        // $header3 = array("時", "分", "前傾回数", "ひねり回数");


        // 日付
        if (isset($_POST["st_ymd"]) && isset($_POST["ed_ymd"])) {
            // 区切り文字を削除
            $startDate = str_replace('/', '', $_POST["st_ymd"]);   // 開始日
            $endDate = str_replace('/', '', $_POST["ed_ymd"]);     // 終了日
        }
        // 選択している施設の作業者を探す
        if (isset($_POST['facilityno'])) {
            // 作業者
            $getdata = Helper::whereIn('facilityno', [$_POST['facilityno']])
                ->orderBy('id', 'asc')
                ->whereNotIn('delflag', [1])
                ->pluck('id')
                ->toArray();

            // 各helpernoごとにデータを取得(CSVに加工せずに直接、データを入れるようにするためselectを記載)
            $data = bpainhed::join('helper', 'bpainhed.helperno', 'helper.id')
            ->select('helper.helpername','bpainhed.ymd', 'bpainhed.hms','bpainhed.fxc','bpainhed.fxt','bpainhed.fxa',
                'bpainhed.txc','bpainhed.txt','bpainhed.txa','bpainhed.risk','bpainhed.sthms','bpainhed.edhms',
                'bpainhed.alhms','bpainhed.flim','bpainhed.hplim','bpainhed.hmlim','bpainhed.wearableno')
            ->whereIn('helperno', $getdata)
            ->whereBetween('bpainhed.ymd', [$startDate, $endDate]) // 入力された日付期間内
            ->orderBy('helperno', 'asc')
            ->orderBy('bpainhed.id', 'asc')
            ->get()
            ->groupBy('helpername')
            ->toArray();

            if(empty($data)) {
                //helper.bladeに空の値を渡す
                exit;
            } else {
            // CSVデータの作成
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // ヘッダ(項目名書き込み)
            $sheet->fromArray($header1, null, 'A1');
            $sheet->fromArray($header2, null, 'A2');

            //['ymd']以降のデータを書き込みたいため、他の変数へ値を格納する
            $rowCnt = 0;
            $tmp_array = array(array());
            foreach ($data as $key => $tmpval) {
                foreach ($tmpval as $key2 => $tmpval2) {
                    if ($key2 > 0) {
                        // 同じ作業者で、2個目以降のデータの場合、作業者名を空文字にする
                        $tmpval2['helpername'] = "";
                        $tmp_array[$key][$rowCnt] = $tmpval2;
                        $rowCnt++;
                    } else {
                        $tmp_array[$key][$rowCnt] = $tmpval2;
                        $rowCnt++;
                    }
                }
                $rowCnt = 0;
            }

            $num = 3; // 3行目から作業者データの書き込み
            foreach($tmp_array as $datas) {
                foreach($datas as $data) {
                    $sheet->fromArray($data, null, 'A' . $num);
                    $num++;
                }
            }


            // $sheet->fromArray($header3, null, 'A4');

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
            if (ob_get_contents()) ob_end_clean(); //バッファ消去

            $fp = fopen('php://output', 'w');
            //フィルタをストリームに付加する
            stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932');
            $writer->save($fp);
            // $writer->save('php://output');
            exit; // ※※これがないと余計なものも出力してファイルが開けない
            }
        }
    }

    // CSV取り込み処理（TimeStudyデータ登録）
    public function csvImport(Request $request)
    {
        try {
            \Log::info('CSV取り込み開始');
            
            if (!$request->hasFile('csv_file')) {
                \Log::error('CSVファイルが選択されていません');
                return response()->json(['success' => false, 'message' => 'CSVファイルを選択してください。']);
            }

            $file = $request->file('csv_file');
            if ($file->getClientOriginalExtension() !== 'csv') {
                \Log::error('CSVファイルではありません: ' . $file->getClientOriginalExtension());
                return response()->json(['success' => false, 'message' => 'CSVファイルを選択してください。']);
            }

            $helperId = $request->input('helpername');
            \Log::info('選択された作業者ID: ' . $helperId);

            // CSVファイルを読み込み
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            \Log::info('CSVファイル読み込み完了。行数: ' . count($data));

            // ヘッダー行をスキップ
            array_shift($data);
            \Log::info('ヘッダー行をスキップ。データ行数: ' . count($data));

            $importCount = 0;
            foreach ($data as $row) {
                if (count($row) < 8) {
                    continue; // 不正な行はスキップ
                }

                // CSVの形式: timestudy_id, task_id, task_name, task_type_no, task_category_no, start, stop, helpno
                $timestudyId = trim($row[0]);
                $taskId = trim($row[1]);
                $taskName = trim($row[2]);
                $taskTypeNo = (int)$row[3];
                $taskCategoryNo = (int)$row[4];
                $start = trim($row[5]);
                $stop = trim($row[6]);
                $csvHelpno = (int)$row[7]; // CSVファイルのhelpno

                \Log::info('CSV行データ: ' . json_encode([
                    'timestudy_id' => $timestudyId,
                    'task_id' => $taskId,
                    'task_name' => $taskName,
                    'start' => $start,
                    'stop' => $stop,
                    'helpno' => $csvHelpno
                ]));

                // バリデーション
                if (empty($timestudyId) || empty($taskId) || empty($taskName) || empty($start) || empty($stop)) {
                    \Log::warning('必須項目が空です: ' . json_encode($row));
                    continue; // 必須項目が空の場合はスキップ
                }

                // 日付形式のバリデーション（複数の形式に対応）
                $startDateTime = null;
                $stopDateTime = null;
                
                // 形式1: Y-m-d H:i:s
                $startDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $start);
                $stopDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $stop);
                
                // 形式2: Y-m-d\TH:i:s
                if (!$startDateTime) {
                    $startDateTime = \DateTime::createFromFormat('Y-m-d\TH:i:s', $start);
                }
                if (!$stopDateTime) {
                    $stopDateTime = \DateTime::createFromFormat('Y-m-d\TH:i:s', $stop);
                }
                
                // 形式3: Y-m-d\TH:i:s.u (マイクロ秒付き)
                if (!$startDateTime) {
                    $startDateTime = \DateTime::createFromFormat('Y-m-d\TH:i:s.u', $start);
                }
                if (!$stopDateTime) {
                    $stopDateTime = \DateTime::createFromFormat('Y-m-d\TH:i:s.u', $stop);
                }
                
                // 形式4: Y-m-d
                if (!$startDateTime) {
                    $startDateTime = \DateTime::createFromFormat('Y-m-d', $start);
                }
                if (!$stopDateTime) {
                    $stopDateTime = \DateTime::createFromFormat('Y-m-d', $stop);
                }

                if (!$startDateTime || !$stopDateTime) {
                    \Log::warning('日付変換エラー: start=' . $start . ', stop=' . $stop);
                    continue; // 日付変換エラーはスキップ
                } else {
                    \Log::info('日付変換成功: start=' . $startDateTime->format('Y-m-d H:i:s') . ', stop=' . $stopDateTime->format('Y-m-d H:i:s'));
                }

                // helperテーブルでCSVのhelpnoに対応するidが存在するかチェック
                $helper = \App\Models\Helper::where('id', $csvHelpno)->where('delflag', 0)->first();
                if (!$helper) {
                    \Log::warning('作業者が存在しません: helpno=' . $csvHelpno);
                    continue; // 該当する作業者が存在しない場合はスキップ
                }

                // TimeStudyテーブルに登録（helper.idを使用）
                try {
                    // 日付を正しい形式に変換
                    $startFormatted = $startDateTime->format('Y-m-d H:i:s');
                    $stopFormatted = $stopDateTime->format('Y-m-d H:i:s');
                    
                    $timeStudy = \App\Models\TimeStudy::create([
                        'timestudy_id' => $timestudyId,
                        'helpno' => $helper->id, // helperテーブルのidを使用
                        'task_id' => $taskId,
                        'start' => $startFormatted,
                        'stop' => $stopFormatted,
                    ]);
                    
                    \Log::info('TimeStudy登録成功: ' . json_encode([
                        'timestudy_id' => $timeStudy->timestudy_id,
                        'helpno' => $helper->id,
                        'task_id' => $taskId,
                        'start' => $startFormatted,
                        'stop' => $stopFormatted
                    ]));
                    
                    $importCount++;
                } catch (\Exception $e) {
                    \Log::error('TimeStudy登録エラー: ' . $e->getMessage());
                    \Log::error('登録データ: ' . json_encode([
                        'timestudy_id' => $timestudyId,
                        'helpno' => $helper->id,
                        'task_id' => $taskId,
                        'start' => $start,
                        'stop' => $stop
                    ]));
                    continue;
                }
            }

            return response()->json([
                'success' => true, 
                'message' => $importCount . '件のTimeStudyデータを登録しました。'
            ]);

        } catch (\Exception $e) {
            \Log::error('CSV取り込みエラー: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'CSVファイルの取り込みに失敗しました。']);
        }
    }

    /*
    public function timeStudyCsvUpload(Request $request)
    {
        try {
            if (!$request->hasFile('csv_file')) {
                return response()->json(['success' => false, 'message' => 'CSVファイルを選択してください。']);
            }

            $file = $request->file('csv_file');
            if ($file->getClientOriginalExtension() !== 'csv') {
                return response()->json(['success' => false, 'message' => 'CSVファイルを選択してください。']);
            }

            $helperId = $request->input('helpername');

            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));

            array_shift($data);

            foreach ($data as $row) {
                if (count($row) < 8) {
                    continue; // 不正な行はスキップ
                }

                $startDateTime = \DateTime::createFromFormat('Y/n/j H:i', $row[5]);
                $stopDateTime = \DateTime::createFromFormat('Y/n/j H:i', $row[6]);

                if (!$startDateTime || !$stopDateTime) {
                    continue; // 日付変換エラーはスキップ
                }

                $ymd = $startDateTime->format('Ymd');

                $bpainhed = \App\Models\bpainhed::where('ymd', $ymd)->where('helperno', $helperId)->first();
                $bpainhedno = $bpainhed ? $bpainhed->id : null;

                \App\Models\TimeStudy::create([
                    'bpainhedno' => $bpainhedno,
                    'helpno' => $helperId,
                    'ymd' => $ymd,
                    'year' => (int)$row[4],
                    'start' => $startDateTime->format('Y-m-d H:i:s'),
                    'stop' => $stopDateTime->format('Y-m-d H:i:s'),
                    'task_name' => $row[7],
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }*/
}
