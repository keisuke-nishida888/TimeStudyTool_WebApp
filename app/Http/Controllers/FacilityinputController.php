<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;
use Illuminate\Http\Request;
use \App\Rules\space;
use \App\Rules\AlphaNumHalf_mail;
use Illuminate\Support\Facades\Validator;

class FacilityinputController extends Controller
{
    //
    public function index(Request $request)
    {
        //ログインユーザの施設データがない場合は追加、ある場合は修正画面へ
        $facility = Auth::user()->facilityno;

        if(isset($facility)) $target = 1;
        else $target = 0;

        //施設情報
        $getdata = Facility::select()
        ->whereIn('facility.id',[$facility])
        ->whereNotIn('facility.delflag',[1])
        ->get();                
        $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

        //ターゲット
        if(isset($data[0]['id'])) $target = 1;
        else $target = 0;


        $page = 'facilityinput';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        if(isset($_POST["addmess"]))
        {
            $addmess = $_POST["addmess"];
            return view($page, compact('title' ,'page','group', 'data' ,'target','addmess'));
        }
        else return view($page, compact('title' ,'page','group', 'data' ,'target'));

    }


    public function facilityAdd(Request $request)
    {       
        //**既に登録されているか調べる***********************************************************/
       
        $exist = Facility::select()
        ->whereIn('facility',[$_POST["facility"]])
        ->exists();

        $exist_mes = array();
        $exist_mes['facility'] = Common::$erralr_faci;
        if($exist != null)
        {
            return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
        }
           
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
                $userid = Auth::user()->id;
                User::where('id',$userid)->update(['facilityno'=> $insertid]);
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
                        $path = $request->file($idname)->storeAs(Common::$Picture_path,$fname);
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
        }
        return $insertid;
    }


    public function FacilityFix(Request $request)
    {

        //修正中のuserno以外で該当する名前があるか調べる
        //**既に登録されているか調べる***********************************************************/
        $exist = Facility::select()
        ->whereIn('facility',[$_POST["facility"]])
        ->whereNotIn('id', [$_POST["id"]])
        ->exists();

        $exist_mes = array();
        $exist_mes['facility'] = Common::$erralr_faci;
        if($exist != null)
        {
            return redirect()->back()->withErrors($exist_mes)->withInput();
        }
        //************************************************************************************/
        $insertid = $_POST["id"];
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
                //ターゲット
                if(isset($data[0]['id'])) $target = 1;
                else $target = 0;

                $page = 'facilityinput';
                $title = Common::$title[$page];
                $group = Common::$group[$page];
                // return view($page, compact('title' ,'page','group','value','target','data'));
                return json_encode("error");
            }
            $fixerror = "error";
            $page = 'facilityinput';
             //ターゲット
            if(isset($data[0]['id'])) $target = 1;
            else $target = 0;
            $title = Common::$title[$page];
            $group = Common::$group[$page];
            $data = "";
                
            // return view($page, compact('title' ,'page','group','adddata','data','target','fixerror'));
            return json_encode("error");
        }

        //施設情報
        $getdata = Facility::select()
        ->whereIn('facility.id',[$insertid])
        ->whereNotIn('facility.delflag',[1])
        ->get();                
        $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

        $page = 'facilityinput';
        //ターゲット
        if(isset($data[0]['id'])) $target = 1;
        else $target = 0;
        $title = Common::$title[$page];
        $group = Common::$group[$page]; 
        $fixmess = "修正しました。";
        $adddata = Facility::whereIn('id',[$request['id']])->get();
        // return view($page, compact('title' ,'page','group','adddata','data','target','fixmess'));
        return $insertid;
    }



    public function cxl_FacilityFix(Request $request)
    {
        $page = 'facilityinput';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        if(isset($_POST["data"]))
        {
            $adddata = Facility::whereIn('id',[$_POST["data"]])->get();
            return json_encode($adddata,JSON_PRETTY_PRINT);
        }
        else return 0;      
    }

}

