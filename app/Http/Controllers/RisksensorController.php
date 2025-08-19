<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Facility;
use App\Models\Wearable;
use App\Models\Helper;
use App\Models\BackPain;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RisksensorController extends Controller
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
                   
                    echo BackPain::where('id',$_POST["data"])->update(['delflag'=>1,'upduserno'=> Auth::user()->id]);
                    // echo $user->destroy($_POST["data"]);
                }
                
                return ;
            }                
        }
    }

    
    //
    
    public function index(Request $request)
    {
        //whereNotInは配列型で
        //削除フラグdelflagが0の値のみ取得(昇順)
        $value = BackPain::orderBy('id','asc')->whereNotIn('delflag',[1])->get();
        //作業者管理マスタの腰痛デバイスが一致するレコードから作業者名と施設Noを取得する
        //施設Noは施設管理マスタのNoから取得
        // $getdata = BackPain::select()
        //     ->orderBy('backpain.id','asc')
        //     ->whereNotIn('backpain.delflag',[1])
        //     ->get();
        $getdata = BackPain::select('backpain.id as backpain_id','backpain.devicename','helper.helpername','facility.facility')
            ->leftJoin('helper', function ($join) {
                $today = date("Ymd");
                $join->on('helper.backpainno', '=', 'backpain.id')
                    ->whereNotIn('helper.delflag',[1])
                    ->where('helper.measufrom', '<=', $today)
                    ->where('helper.measuto', '>=', $today);
            })
            ->leftJoin('facility', function ($join) {
                $join->on('facility.id', '=', 'helper.facilityno')
                ->whereNotIn('facility.delflag',[1]);
            })
            ->orderBy('backpain.id','asc')
            ->whereNotIn('backpain.delflag',[1])
            ->get();


        
        $data_arr = array();
        $arr = $getdata->toArray();
        $cnt = 0;
        for($i=0;$i<count($arr);$i++)
        {            
            $val = $arr[$i]['backpain_id'];
            if($i != 0 && $i != 1)
            {
                $key = array_search($val, array_column($data_arr, 'backpain_id'), true);
                if($key === false || $key === "" )
                {                    
                    $data_arr[$cnt] = $arr[$i];
                    $cnt++;
                }
            }
            else if($i == 1)
            {
                if($data_arr[0]['backpain_id'] != $val)
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



        $page = 'risksensor';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        
        return view($page, compact('title' ,'page','group','value','data'));
    }


    public function add_index(Request $request)
    {
        $data = "";
        $page = 'risksensor_add';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        return view($page, compact('title' ,'page','group','data'));   
    }

    public function fix_index(Request $request)
    {
        if($request->isMethod('POST'))
        {
            //腰痛センサ情報
            // $getdata = BackPain::select()
            //     ->whereIn('backpain.id',[$_POST["id"]])
            //     ->whereNotIn('backpain.delflag',[1])
            //     ->orderBy('backpain.id','asc')
            //     ->get();

            $getdata = BackPain::select('backpain.id as id','backpain.devicename','helper.helpername','facility.facility')
                ->whereIn('backpain.id',[$_POST["id"]])
                ->leftJoin('helper', function ($join) {
                    $today = date("Ymd");
                    $join->on('helper.backpainno', '=', 'backpain.id')
                        ->whereNotIn('helper.delflag',[1])
                        ->where('helper.measufrom', '<=', $today)
                        ->where('helper.measuto', '>=', $today);
                })
                // ->leftjoin('facility','facility.id','=','helper.facilityno')
                ->leftJoin('facility', function ($join) {
                    $join->on('facility.id', '=', 'helper.facilityno')
                    ->whereNotIn('facility.delflag',[1]);
                })
                ->whereNotIn('backpain.delflag',[1])
                ->orderBy('backpain.id','asc')
                ->get();

                

            $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

        }
        else if($request->isMethod('GET'))
        {
            $data = "";
            $helper = "";
        }
        else
        {
            $data = "";
            $helper = "";
        }
        

        $page = 'risksensor_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        if(isset($_POST["id"]))
        {
            if(isset($_POST["addmess"]))
            {
                $addmess = $_POST["addmess"];
                return view($page, compact('title' ,'page','group','data','addmess'));
            }
            else return view($page, compact('title' ,'page','group','data'));
        }
        else return view($page, compact('title' ,'page','group','data')); 
    }

    
    

    //追加
    public function RisksensorAdd(Request $request)
    {

        //**既に登録されているか調べる***********************************************************/
        $exist = BackPain::select()
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
        // Common::risk_validator($request);
        $rulus_obj = Common::risk_rulus();
        $rulus = json_decode(json_encode($rulus_obj), true);
        $validator = Validator::make($request->all(),$rulus, Common::$message_risk);
        if($validator->fails())
        {
            return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
        }
        try
        {
            if($request->isMethod('POST'))
            {
                $insertid = Common::create_risksensor($request->all());
            }
            
          
        }
        catch(\Throwable $e)
        {
            return "error";
          
        }
        return $insertid;
    }

    

    public function RisksensorFix(Request $request)
    {
        //修正中のuserno以外で該当する名前があるか調べる
        //**既に登録されているか調べる***********************************************************/
        $exist = BackPain::select()
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
        // $this->validator($request->all())->validate();
        // $validator = Validator::make($request->all(),Common::$rulus_risk, Common::$message_)->validate();
        // Common::risk_validator($request);
        $rulus_obj = Common::risk_rulus();
        $rulus = json_decode(json_encode($rulus_obj), true);
        $validator = Validator::make($request->all(),$rulus, Common::$message_risk);
        if($validator->fails())
        {
            return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
        }
        try
        {
            if($request->isMethod('POST'))
            {
                Common::Update_risksensor($request->all());
                
                $getdata = BackPain::select('backpain.id as id','backpain.devicename','helper.helpername','facility.facility')
                    ->whereIn('backpain.id',[$_POST["id"]])
                    ->leftJoin('helper', function ($join) {
                        $today = date("Ymd");
                        $join->on('helper.backpainno', '=', 'backpain.id')
                            ->whereNotIn('helper.delflag',[1])
                            ->where('helper.measufrom', '<=', $today)
                            ->where('helper.measuto', '>=', $today);
                    })
                    // ->leftjoin('facility','facility.id','=','helper.facilityno')
                    ->leftJoin('facility', function ($join) {
                        $join->on('facility.id', '=', 'helper.facilityno')
                        ->whereNotIn('facility.delflag',[1]);
                    })
                    ->whereNotIn('backpain.delflag',[1])
                    ->orderBy('backpain.id','asc')
                    ->get();

                $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
                
            }
        }
        catch(\Throwable $e)
        {
            if($request->isMethod('POST'))
            {
                //腰痛センサ情報
                $getdata = BackPain::select('backpain.id as id','backpain.devicename','helper.helpername','facility.facility')
                    ->whereIn('backpain.id',[$_POST["id"]])
                    ->leftJoin('helper', function ($join) {
                        $today = date("Ymd");
                        $join->on('helper.backpainno', '=', 'backpain.id')
                            ->whereNotIn('helper.delflag',[1])
                            ->where('helper.measufrom', '<=', $today)
                            ->where('helper.measuto', '>=', $today);
                    })
                    // ->leftjoin('facility','facility.id','=','helper.facilityno')
                    ->leftJoin('facility', function ($join) {
                        $join->on('facility.id', '=', 'helper.facilityno')
                        ->whereNotIn('facility.delflag',[1]);
                    })
                    ->whereNotIn('backpain.delflag',[1])
                    ->orderBy('backpain.id','asc')
                    ->get();

                $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

                //作業者情報
                //施設情報
                // $tmp = Helper::select('helper.id as Helper_id','helper.helpername','helper.facilityno','helper.delflag','facility.facility')
                //     ->whereIn('helper.id',[$data[0]["helperno"]])
                //     ->join('facility','facility.id','=','helper.facilityno')
                //     ->whereNotIn('helper.delflag',[1])
                //     ->whereNotIn('facility.delflag',[1])
                //     ->orderBy('helper.id','asc')
                //     ->get();

                // $helper = json_decode(json_encode($tmp,JSON_PRETTY_PRINT),true);
            }
            else if($request->isMethod('GET'))
            {
                $data = "";
                $helper = "";
            }
            else
            {
                $data = "";
                $helper = "";
            }
            
            $fixerror = "error";
            $page = 'risksensor_fix';
            $title = Common::$title[$page];
            $group = Common::$group[$page];
            return "error";
            // return view($page, compact('title' ,'page','group','data','fixerror'));
        }

        $page = 'risksensor_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        $fixmess = "修正しました。";
        // return view($page, compact('title' ,'page','group','data','fixmess'));
        return $insertid;
    }

    //修正ページ　フォームリセット
    public function cxl_RisksensorFix(Request $request)
    {
        if(isset($_POST["data"]))
        {
            //backpain.helpernoがない場合は取得しない
            $getdata = BackPain::select()
            ->whereIn('backpain.id',[$_POST["data"]])
            ->whereNotIn('backpain.delflag',[1])
            ->leftJoin('helper','helper.id','=','backpain.helperno')
            ->leftJoin('facility','facility.id','=','helper.facilityno')
            ->orderBy('backpain.id','asc')
            ->get();

            $adddata = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
            return $adddata;
            
        }
        else return 0;      
    }

}
