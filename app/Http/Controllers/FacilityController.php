<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;
use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Helper;
use Illuminate\Support\Facades\Validator;
use \App\Rules\space;
use \App\Rules\AlphaNumHalf_mail;

class FacilityController extends Controller
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

                    //介助者のデータがあるか調べる

                   
                    $helper_exist = Helper::select()
                    ->whereIn('helper.facilityno',[$_POST["data"]])
                    ->whereNotIn('delflag',[1])
                    ->exists();
                    

                    if($helper_exist != null)
                    {
                        $rtn = Helper::select()
                        ->whereIn('helper.facilityno',[$_POST["data"]])
                        ->update(['delflag'=>1,'upduserno'=> Auth::user()->id]);
                        if($rtn != 1)
                        {
                            echo -1;
                            return ;
                        }
                    }
                    $rtn2 =  Facility::where('id',$_POST["data"])->update(['delflag'=>1,'upduserno'=> Auth::user()->id]);
                    if($rtn2 != 1)
                    {
                        echo -1;
                        return ;
                    }
                    else echo 1;
                  
                    // echo $user->destroy($_POST["data"]);
                }
                
                return ;
            }                
        }
    }

    
    //
    public function index(Request $request)
    {
        //施設
        $Facility = new Facility;
        //削除フラグdelflagが0の値のみ取得
        $facili = $Facility->whereNotIn('delflag',[1])->get();        
        $facilitydata = json_decode($facili,true);
        $sort3=array();
        $data = "";

        foreach ((array)$facilitydata as $key => $value)
        {
            $sort3[$key] = $value['id'];
        }
        array_multisort($sort3, SORT_ASC, $facilitydata);

        $value = array();
        $value = $facilitydata;

        $page = 'facility';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        return view($page, compact('title' ,'page','group','value','data'));
    }

    public function add_index(Request $request)
    {
        $page = 'facility_add';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        $data = "";
        return view($page, compact('title' ,'page','group','data'));
    }

    public function fix_index(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $adddata = Facility::whereIn('id',[$_POST["id"]])->get();
        }
        else if($request->isMethod('GET'))
        {
            $adddata = "";
        }
        else
        {
             //施設
            $Facility = new Facility;
            //削除フラグdelflagが0の値のみ取得
            $facili = $Facility->whereNotIn('delflag',[1])->get();        
            $facilitydata = json_decode($facili,true);
            $sort3=array();
            $data = "";

            foreach ((array)$facilitydata as $key => $value)
            {
                $sort3[$key] = $value['id'];
            }
            array_multisort($sort3, SORT_ASC, $facilitydata);

            $value = array();
            $value = $facilitydata;

            $page = 'facility';
            $title = Common::$title[$page];
            $group = Common::$group[$page];
            if(isset($_POST["addmess"]))
            {
                $addmess = $_POST["addmess"];
                return view($page, compact('title' ,'page','group','value','data','addmess'));
            }
            else return view($page, compact('title' ,'page','group','value','data'));

        }
        
        $page = 'facility_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        $data = "";
        if(isset($_POST["addmess"]))
        {
            $addmess = $_POST["addmess"];
            return view($page, compact('title' ,'page','group','adddata','data','addmess'));
        }
        else return view($page, compact('title' ,'page','group','adddata','data'));
       
    }



 

    public function facilityAdd(Request $request)
    {
        //**既に登録されているか調べる***********************************************************/
        // 施設登録時の重複確認
        $exists = Facility::select('facility.facility')
        // 削除されていない施設の中での重複チェックの処理を追加
        ->where('delflag', '=', 0)
        ->get();    
        $exist_mes = array();
        $exist_mes['facility'] = Common::$erralr_faci;
        foreach ($exists as $exist) {
            if($exist['facility'] == $request['facility'])
            {
                return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
            };
        };
        //************************************************************************************/

    

        //->validate();をつけたら元のページへリダイレクトする
        // Common::facility_validator($request);
        $rulus_obj = Common::facility_rulus();
        $rulus = json_decode(json_encode($rulus_obj), true);

        $tmp1 = $rulus['facility'];
        array_push($tmp1,new space);
        $rulus['facility'] = $tmp1;
        
        $tmp2 = $rulus['address'];
        array_push($tmp2,new space);
        $rulus['address'] = $tmp2;

        $tmp3 = $rulus['mail'];
        array_push($tmp3,new AlphaNumHalf_mail);
        $rulus['mail'] = $tmp3;
        
        $validator = Validator::make($request->all(),$rulus, Common::$message_);
        if($validator->fails())
        {
            return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
        }

        try
        {
            if($request->isMethod('POST'))
            {
                $insertid = Common::create_facility($request->all());

                //介助者管理マスタNO_番号で登録
                //ファイルが存在しているかを判定
                //チェック入っている場合は削除する
                for($i=1;$i<21;$i++)
                {
                    $idname = "img".$i;
                    $fname = "S".$insertid."_".$i.".jpg";
                    if($request->hasFile($idname))
                    {
                        //サーバへ画像ファイルをアップロード
                        $path = $request->file($idname)->storeAs(Common::$public.Common::$Picture_path,$fname);
                        //アップロードに成功したか確認
                        if ($request->file($idname)->isValid())
                        {
                            //
                        }
                        //アップロード失敗     
                    }
                }
            }
        }
        catch(\Throwable $e)
        {
            return json_encode("error");
            // $fixerror = "error";
            // $page = 'facility_add';
            // $title = Common::$title[$page];
            // $group = Common::$group[$page];
            // $data = "";
            // return view($page, compact('title' ,'page','group','data','fixerror'));
        }
        return $insertid;
        // $data = "";
        // $page = 'facility_fix';
        // $title = Common::$title[$page];
        // $group = Common::$group[$page];
        // $addmess = "追加しました。";
        // $adddata = Facility::whereIn('id',[$insertid])->get();
        // // 二重送信対策
        // // $request->session()->regenerateToken();
        // return view($page, compact('title' ,'page','group','adddata','data','addmess'));
    }



 
    public function FacilityFix(Request $request)
    {
        // 削除されていない施設の中での重複チェックの処理を追加
        $exist = Facility::select()
        ->whereIn('facility',[$_POST["facility"]])
        ->whereNotIn('id', [$_POST["id"]])
        ->where('delflag', '=', 0)
        ->exists();

        //修正中のuserno以外で該当する名前があるか調べる
        //**既に登録されているか調べる***********************************************************/
        // 施設修正時の重複確認

        $exist_mes = array();
        $exist_mes['facility'] = Common::$erralr_faci;
        if($exist != null)
        {
            return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
        }
        //************************************************************************************/

        // $this->validator($request->all())->validate();
        // $this->Update($request->all());
        //->validate();をつけたら元のページへリダイレクトする
        // $validator = Validator::make($request->all(),Common::$rulus_facility, Common::$message_)->validate();
        // Common::facility_validator($request);
        $rulus_obj = Common::facility_rulus();
        $rulus = json_decode(json_encode($rulus_obj), true);

        $tmp1 = $rulus['facility'];
        array_push($tmp1,new space);
        $rulus['facility'] = $tmp1;
        
        $tmp2 = $rulus['address'];
        array_push($tmp2,new space);
        $rulus['address'] = $tmp2;

        $tmp3 = $rulus['mail'];
        array_push($tmp3,new AlphaNumHalf_mail);
        $rulus['mail'] = $tmp3;

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
                Common::Update_facility($request->all());
            }
        }
        catch(\Throwable $e)
        {
            if($request->isMethod('POST'))
            {
                $adddata = Facility::whereIn('id',[$_POST["id"]])->get();
            }
            else if($request->isMethod('GET'))
            {
                $adddata = "";
            }
            else
            {
                //施設
                $Facility = new Facility;
                //削除フラグdelflagが0の値のみ取得
                $facili = $Facility->whereNotIn('delflag',[1])->get();        
                $facilitydata = json_decode($facili,true);
                $sort3=array();
                $data = "";

                foreach ((array)$facilitydata as $key => $value)
                {
                    $sort3[$key] = $value['id'];
                }
                array_multisort($sort3, SORT_ASC, $facilitydata);

                $value = array();
                $value = $facilitydata;

                $page = 'facility';
                $title = Common::$title[$page];
                $group = Common::$group[$page];
                // return view($page, compact('title' ,'page','group','value','data'));
                return json_encode("error");
            }
            $fixerror = "error";
            $page = 'facility_fix';
            $title = Common::$title[$page];
            $group = Common::$group[$page];
            $data = "";
                
            // return view($page, compact('title' ,'page','group','adddata','data','fixerror'));
            return json_encode("error");
        }
        $data = "";
        $page = 'facility_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page]; 
        $fixmess = "修正しました。";
        $adddata = Facility::whereIn('id',[$request['id']])->get();
        // return view($page, compact('title' ,'page','group','adddata','data','fixmess'));
        return $insertid;
    }

    public function cxl_FacilityFix(Request $request)
    {
        $page = 'facility_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        if(isset($_POST["data"]))
        {
            $adddata = Facility::whereIn('id',[$_POST["data"]])->get();

            // echo $test[0]['id'];
            return json_encode($adddata,JSON_PRETTY_PRINT);
        }
        else return 0;      
    }

}
