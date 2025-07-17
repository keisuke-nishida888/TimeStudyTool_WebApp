<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Facility;
use App\Models\Wearable;
use App\Models\Helper;
use \App\Library\Common;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class WearableController extends Controller
{
    public function del(Request $request)
    {
        //all 配列として受け取る
        $value = Wearable::all();

        //ログインユーザ以外を選択していること
        if(isset($_POST["name"]))
        {
            if($_POST["name"] == "del")
            {

                if(isset($_POST["data"]))
                {
                    //save() ->updated_atのカラムが更新されない
                    //update() ->updated_atのカラムが更新される
                    echo Wearable::where('id',$_POST["data"])->update(['delflag'=>1,'upduserno'=> Auth::user()->id]);
                    // echo $user->destroy($_POST["data"]);
                }
                
                return ;
            }                
        }
    }
    
    //
    public function index(Request $request)
    {        
        $getdata = Wearable::select('wearable.id as wearable_id','wearable.devicename')     
        // $getdata = Wearable::select('wearable.id as wearable_id','wearable.devicename','helper.helpername','facility.facility')        
        // ->leftJoin('helper', function ($join) {
        //     $today = date("Ymd");
        //     $join->on('helper.wearableno', '=', 'wearable.id')
        //         ->whereNotIn('helper.delflag',[1])
        //         ->where('helper.measufrom', '<=', $today)
        //         ->where('helper.measuto', '>=', $today);
        // })
        // ->leftJoin('facility', function ($join) {
        //     $join->on('facility.id', '=', 'helper.facilityno')
        //     ->whereNotIn('facility.delflag',[1]);
        // })
        ->orderBy('wearable.id','asc')
        ->whereNotIn('wearable.delflag',[1])
        ->get();


        $data_arr = array();
        $arr = $getdata->toArray();
        $cnt = 0;
        for($i=0;$i<count($arr);$i++)
        {            
            $val = $arr[$i]['wearable_id'];
            if($i != 0 && $i != 1)
            {
                $key = array_search($val, array_column($data_arr, 'wearable_id'), true);
                if($key === false || $key === "" )
                {
                    $data_arr[$cnt] = $arr[$i];
                    $cnt++;
                }
            }
            else if($i == 1)
            {
                if($data_arr[0]['wearable_id'] != $val)
                {
                    $data_arr[$cnt] = $arr[$i];
                    $cnt++;
                }
            }
            else
            {
                $data_arr[$cnt] = $arr[$i];
                $cnt++;
            }
        }
        $data = json_decode(json_encode($data_arr,JSON_PRETTY_PRINT),true);

        
        $page = 'wearable';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        
        return view($page, compact('title' ,'page','group','data'));

    }

    public function add_index(Request $request)
    {
        $page = 'wearable_add';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        $data = "";
        return view($page, compact('title' ,'page','group','data'));
    }

    public function fix_index(Request $request)
    {
        if($request->isMethod('POST'))
        {
            //介助者管理マスタのウェアラブルデバイスが一致するレコードから介助者名と施設Noを取得する
            //施設Noは施設管理マスタのNoから取得
            // $getdata = Wearable::select()        
            //     ->whereIn('wearable.id',[$_POST["id"]])
            //     ->whereNotIn('wearable.delflag',[1])
            //     ->orderBy('wearable.id','asc')
            //     ->get();
            // $adddata = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

            $getdata = Wearable::select('wearable.id as id','wearable.devicename','wearable.userid','wearable.passwd','wearable.clientid','wearable.clientsc','wearable.auth','helper.helpername','facility.facility')        
                ->whereIn('wearable.id',[$_POST["id"]])
                ->leftJoin('helper', function ($join) {
                    $today = date("Ymd");
                    $join->on('helper.wearableno', '=', 'wearable.id')
                        ->whereNotIn('helper.delflag',[1])
                        ->where('helper.measufrom', '<=', $today)
                        ->where('helper.measuto', '>=', $today);
                })
                ->leftJoin('facility', function ($join) {
                    $join->on('facility.id', '=', 'helper.facilityno')
                    ->whereNotIn('facility.delflag',[1]);
                })
                // ->leftjoin('facility','facility.id','=','helper.facilityno')
                ->whereNotIn('wearable.delflag',[1])
                ->orderBy('wearable.id','asc')
                ->get();
            $adddata = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
            

        }
        else if($request->isMethod('GET'))
        {
                $value = "";
                $data = "";
                $adddata = "";
        }
        else
        {
            //whereNotInは配列型で
                //削除フラグdelflagが0の値のみ取得(昇順)
                $value = Wearable::orderBy('id','asc')->whereNotIn('delflag',[1])->get();
                $getdata = Wearable::select('wearable.id as wearable_id','wearable.devicename','helper.helpername','facility.facility')        
                    ->leftJoin('helper', function ($join) {
                        $today = date("Ymd");
                        $join->on('helper.wearableno', '=', 'wearable.id')
                            ->where('helper.measufrom', '<=', $today)
                            ->where('helper.measuto', '>=', $today);
                    })
                    // ->leftjoin('facility','facility.id','=','helper.facilityno')
                    ->leftJoin('facility', function ($join) {
                        $join->on('facility.id', '=', 'helper.facilityno')
                        ->whereNotIn('facility.delflag',[1]);
                    })
                    ->orderBy('wearable.id','asc')
                    ->whereNotIn('wearable.delflag',[1])
                    ->get();

                $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
                $tmp = Helper::select('helper.id as Helper_id','helper.helpername','helper.facilityno','helper.delflag','facility.facility')
                    ->leftjoin('facility','facility.id','=','helper.facilityno')
                    ->whereNotIn('helper.delflag',[1])
                    ->orderBy('helper.id','asc')
                    ->get();
                $helper = json_decode(json_encode($tmp,JSON_PRETTY_PRINT),true);
                

                $page = 'wearable';
                $title = Common::$title[$page];
                $group = Common::$group[$page];
                
                return view($page, compact('title' ,'page','group','value','data','helper'));
        }

        $page = 'wearable_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        $data = "";
        //※※html側の値の受取方がUserとは異なるため注意(json化しているため)
        if(isset($_POST["addmess"]))
        {
            $addmess = $_POST["addmess"];
            return view($page, compact('title' ,'page','group','adddata','data','addmess'));
        } 
        else return view($page, compact('title' ,'page','group','adddata','data'));
    }




    public function WearableAdd(Request $request)
    {
        if($request->isMethod('POST'))
        {
            //**既に登録されているか調べる***********************************************************/
            $exist = Wearable::select()
            ->whereIn('devicename',[$_POST["devicename"]])
            ->exists();

            $exist_mes = array();
            $exist_mes['devicename'] = Common::$erralr_device;
            if($exist != null)
            {
                return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
            }
            //************************************************************************************/

            //->validate();をつけたら元のページへリダイレクトする
            // Common::wearable_validator($request);
            $rulus_obj = Common::wearable_rulus();
            $rulus = json_decode(json_encode($rulus_obj), true);
            $validator = Validator::make($request->all(),$rulus, Common::$message_);
            

            if($validator->fails())
            {
                return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
            }
            try
            {
                //ハッシュ値を生成
                // if(isset($_POST['passwd'])) $pass = Hash::make($_POST['passwd']);
                // else $pass = "";
                $pass = $_POST['passwd'];

                // $this->validator($request->all())->validate();
                // $this->create($request->all(),$pass);
                $insertid = Common::create_wearable($request->all(),$pass);
            }
            catch(\Throwable $e)
            {
                return json_encode("error");
            }
            return $insertid;
        }
        else return json_encode("error");
 
        
    }


    public function WearableFix(Request $request)
    {        
        if($request->isMethod('POST'))
        {

            //修正中のuserno以外で該当する名前があるか調べる
            //**既に登録されているか調べる***********************************************************/
            $exist = Wearable::select()
            ->whereIn('devicename',[$_POST["devicename"]])
            ->whereNotIn('id', [$_POST["id"]])
            ->exists();

            $exist_mes = array();
            $exist_mes['devicename'] = Common::$erralr_device;
            if($exist != null)
            {
                return redirect()->back()->withErrors($exist_mes)->withInput();
            }
            //************************************************************************************/

            $insertid = $_POST["id"];
            //->validate();をつけたら元のページへリダイレクトする
            // $validator = Validator::make($request->all(),Common::$rulus_wearable, Common::$message_)->validate();
            // Common::wearable_validator($request);
            $rulus_obj = Common::wearable_rulus();
            $rulus = json_decode(json_encode($rulus_obj), true);
            $validator = Validator::make($request->all(),$rulus, Common::$message_);
            if($validator->fails())
            {
                return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
            }
            try
            {
                //ハッシュ値を生成
                // if(isset($_POST['passwd'])) $pass = Hash::make($_POST['passwd']);
                // else $pass = "";
                $pass = $_POST['passwd'];
                Common::Update_wearable($request->all(),$pass);

                $getdata = Wearable::select('wearable.id as id','wearable.devicename','wearable.userid','wearable.passwd','wearable.clientid','wearable.clientsc','wearable.auth')     
                // $getdata = Wearable::select('wearable.id as id','wearable.devicename','wearable.userid','wearable.passwd','wearable.clientid','wearable.clientsc','wearable.auth','helper.helpername','facility.facility')     
                ->whereIn('wearable.id',[$_POST["id"]])
                // ->leftJoin('helper', function ($join) {
                //     $today = date("Ymd");
                //     $join->on('helper.wearableno', '=', 'wearable.id')
                //         ->whereNotIn('helper.delflag',[1])
                //         ->where('helper.measufrom', '<=', $today)
                //         ->where('helper.measuto', '>=', $today);
                // })
                // ->leftJoin('facility', function ($join) {
                //     $join->on('facility.id', '=', 'helper.facilityno')
                //     ->whereNotIn('facility.delflag',[1]);
                // })
                ->whereNotIn('wearable.delflag',[1])
                ->orderBy('wearable.id','asc')
                ->get();
                $adddata = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
            }
            catch(\Throwable $e)
            {
                if($request->isMethod('POST'))
                {
                    $getdata = Wearable::select('wearable.id as id','wearable.devicename','wearable.userid','wearable.passwd','wearable.clientid','wearable.clientsc','wearable.auth')      
                    // $getdata = Wearable::select('wearable.id as id','wearable.devicename','wearable.userid','wearable.passwd','wearable.clientid','wearable.clientsc','wearable.auth','helper.helpername','facility.facility')      
                    ->whereIn('wearable.id',[$_POST["id"]])
                    // ->leftJoin('helper', function ($join) {
                    //     $today = date("Ymd");
                    //     $join->on('helper.wearableno', '=', 'wearable.id')
                    //         ->whereNotIn('helper.delflag',[1])
                    //         ->where('helper.measufrom', '<=', $today)
                    //         ->where('helper.measuto', '>=', $today);
                    // })
                    // ->leftJoin('facility', function ($join) {
                    //     $join->on('facility.id', '=', 'helper.facilityno')
                    //     ->whereNotIn('facility.delflag',[1]);
                    // })
                    ->whereNotIn('wearable.delflag',[1])
                    ->orderBy('wearable.id','asc')
                    ->get();
                    $adddata = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
        
                }
        
                $page = 'wearable_fix';
                $title = Common::$title[$page];
                $group = Common::$group[$page];
                $data = "";
                $fixerror = "error";
                // return view($page, compact('title' ,'page','group','adddata','data','fixerror'));
                return json_encode("error");
            }

            $page = 'wearable_fix';
            $title = Common::$title[$page];
            $group = Common::$group[$page]; 

            $data = "";
            $fixmess = "修正しました。";
            return $insertid;
            // return view($page, compact('title' ,'page','group','adddata','data','fixmess'));
        }
    }

    //修正ページ　フォームリセット
    public function cxl_WearableFix(Request $request)
    {
        if(isset($_POST["data"]))
        {
            $getdata = Wearable::select()        
            // ->join('helper','helper.wearableno','=','wearable.id')
            // ->join('facility','facility.id','=','helper.facilityno')
            ->whereNotIn('wearable.delflag',[1])
            ->whereIn('wearable.id',[$_POST["data"]])
            ->orderBy('wearable.id','asc')
            ->get();
            $adddata = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
            return $adddata;
            
        }
        else return 0;      
    }

}
