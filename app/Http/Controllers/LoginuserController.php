<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use \App\Rules\AlphaNumHalf;


class LoginuserController extends Controller
{

    public function del(Request $request)
    {
        //all 配列として受け取る
        $value = User::all();

        //ログインユーザ以外を選択していること
        if(isset($_POST["name"]))
        {
            if($_POST["name"] == "del")
            {
                if(isset($_POST["data"]))
                {
                    //save() ->updated_atのカラムが更新されない
                    //update() ->updated_atのカラムが更新される
                    echo User::where('id',$_POST["data"])->update(['delflag'=>1,'upduserno'=> Auth::user()->id]);
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
        //削除フラグdelflagが0の値のみ取得
        $value = User::orderBy('id','asc')
        ->whereNotIn('delflag',[1])
        ->get();
        $page = 'loginuser';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        $data = '';
        return view($page, compact('title' ,'page','group','value','data'));
    }

    public function add_index(Request $request)
    {
        $page = 'loginuser_add';
        $title = Common::$title[$page];
        $group = Common::$group[$page];

        //施設名と作業者名
        $data = "";
        return view($page, compact('title' ,'page','group','data'));
    }

    public function fix_index(Request $request)
    {        
        $page = 'loginuser_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        $data = "";
        if(isset($_POST["userid"]))
        {
            $adddata = User::whereIn('id',[$_POST["userid"]])
            ->whereNotIn('delflag',[1])
            ->get();
            
            if(isset($_POST["addmess"]))
            {
                $addmess = $_POST["addmess"];
                return view($page, compact('title' ,'page','group','adddata','data','addmess'));
            } 
            else return view($page, compact('title' ,'page','group','adddata','data'));
        }
        else return view($page, compact('title' ,'page','group','data'));        
    }

    
    public function UserAdd(Request $request)
    {
        // ユーザ作成時の重複確認
        $exists = User::select('users.username')
        // 削除されていない施設の中での重複チェックの処理を追加
        ->where('delflag', '=', 0)
        ->get();

        $exist_mes = array();
        $exist_mes['username'] = Common::$erralr_user;
        foreach ($exists as $exist) {
            if($exist['username'] == $request['username'])
            {
                return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
            };
        };

        //施設ユーザのとき施設Noが登録されていなかったらエラー
        if(isset($_POST['authority']))
        {
            if($_POST['authority'] == 3 || $_POST['authority'] == "3")
            {
                if(isset($_POST['facilityno']))
                {
                    if($_POST['facilityno'] == 0 || $_POST['facilityno'] == "" || $_POST['facilityno'] == null)
                    {
                        return json_encode("facilerr");
                    }
                }
                else return json_encode("facilerr");
            }
        }
        

        $rulus_obj = Common::user_rulus();
        $rulus = json_decode(json_encode($rulus_obj), true);
        $validator = Validator::make($request->all(),$rulus, Common::$message_);
        if($validator->fails())
        {
            return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
        }

        try
        {
            // abort(404, 'Not Found');
            $insertid = Common::create_user($request->all());
            // $this->create($request->all());
            $adddata = User::whereIn('id',[$insertid])->get();
        }
        catch(\Throwable $e)
        // catch(IlluminateDatabaseQueryException $e)
        {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 7)
            {
                //**既に登録されているか調べる***********************************************************/
                // $exist = User::select()
                // ->whereIn('username',[$_POST["username"]])
                // ->exists();
                $exist_mes = array();
                $exist_mes['username'] = Common::$erralr_user;
                // if($exist != null)
                // {
                    // return json_encode(array('errors' => "already"),JSON_UNESCAPED_UNICODE);
                    return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
                // }
                //************************************************************************************/
            }
            return json_encode("error");
        }
        return $insertid;
    }

    //修正
    public function Update(array $data)
    {
        if(isset($data['facilityno'])) $facilityno = $data['facilityno'];
        else $facilityno = 0;
        
        User::where('id',$data["id"])->update([
            'username' => $data['username'],
            'pass' => Hash::make($data['pass']),
            'authority' => $data['authority'],
            'facilityno' => $facilityno,
            'upduserno'  => Auth::user()->id,
            ]);
    } 

    public function UserFix(Request $request)
    {
        // ユーザ修正時の重複確認
        $exist = User::select()
        ->whereIn('username',[$_POST["username"]])
        ->whereNotIn('id', [$_POST["id"]])
        // 削除されていないログインユーザの中での重複チェックの処理を追加
        ->where('delflag', '=', 0)
        ->exists();

        //**既に登録されているか調べる***********************************************************/
        
        $exist_mes = array();
        $exist_mes['username'] = Common::$erralr_user;  
        if($exist != null)
        {
            return json_encode(array('errors' => $exist_mes),JSON_UNESCAPED_UNICODE);
        }

        $page = 'loginuser_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page]; 

        //施設ユーザのとき施設Noが登録されていなかったらエラー
        if(isset($_POST['authority']))
        {
            if($_POST['authority'] == 3 || $_POST['authority'] == "3")
            {
                if(isset($_POST['facilityno']))
                {
                    if($_POST['facilityno'] == 0 || $_POST['facilityno'] == "" || $_POST['facilityno'] == null)
                    {
                        return json_encode("facilerr");
                    }
                }
                else return json_encode("facilerr");
            }
        }
        $insertid = $request['id'];

        // $validator = Validator::make($request->all(),Common::$rulus_user, Common::$message_)->validate();        
        // Common::user_validator($request);
        $rulus_obj = Common::user_rulus();
        $rulus = json_decode(json_encode($rulus_obj), true);
        $validator = Validator::make($request->all(),$rulus, Common::$message_);
        if($validator->fails())
        {
            return json_encode(array('errors' => $validator->getMessageBag()->toArray()),JSON_UNESCAPED_UNICODE);
        }
        try
        {
            $this->Update($request->all());            
            $adddata = User::whereIn('id',[$request['id']])->get();
        }
        catch(\Throwable $e)
        {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 7)
            {
                //修正中のuserno以外で該当する名前があるか調べる
                //**既に登録されているか調べる***********************************************************/
                // $exist = User::select()
                // ->whereIn('username',[$_POST["username"]])
                // ->whereNotIn('id', [$_POST["id"]])
                // ->exists();

                $exist_mes = array();
                $exist_mes['username'] = Common::$erralr_user;
                // if($exist != null)
                // {
                    return redirect()->back()->withErrors($exist_mes)->withInput();
                // }
                //************************************************************************************/
            }

            $data = "";
            $fixerror = "error";
            $adddata = User::whereIn('id',[$_POST['id']])->get();
            // return view($page, compact('title' ,'page','group','adddata','data','fixerror'));
            return json_encode("error");
        }
        $data = "";
        $fixmess = "修正しました。";
        // return view($page, compact('title' ,'page','group','adddata','data','fixmess'));
        return $insertid;
    }

    public function cxl_UserFix(Request $request)
    {
        $page = 'loginuser_fix';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        if(isset($_POST["data"]))
        {
            $adddata = User::whereIn('id',[$_POST["data"]])->get();
  
            // echo $test[0]['id'];
            return json_encode($adddata,JSON_PRETTY_PRINT);
            
        }
        else return 0;      
    }

}
