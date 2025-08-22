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
use App\Models\Group;
use Illuminate\Support\Facades\DB;



class FacilityController extends Controller
{

    public function del(Request $request)
    {
        if ($request->input('name') === 'del') {
            $facilityId = $request->input('data');
    
            if ($facilityId) {
                // 作業者がいるか
                $helperExist = Helper::where('facilityno', $facilityId)
                    ->where('delflag', '<>', 1)
                    ->exists();
    
                if ($helperExist) {
                    // 何件でも OK（件数は使わない）
                    Helper::where('facilityno', $facilityId)
                        ->update([
                            'delflag'   => 1,
                            'upduserno' => Auth::id(),
                        ]);
                }
    
                // 施設本体（これは1件想定）
                $affected = Facility::where('id', $facilityId)
                    ->update([
                        'delflag'   => 1,
                        'upduserno' => Auth::id(),
                    ]);
    
                echo $affected === 1 ? 1 : -1;
                return;
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
        // POST/GET どちらでも受け取る
        $id = $request->input('id') ?? $request->query('id');
    
        // id が無ければ「施設一覧」に戻す（従来挙動を踏襲）
        if (empty($id)) {
            // 削除フラグが 1 以外（未削除）の施設を id 昇順で取得
            $facilities = Facility::where('delflag', '<>', 1)
                ->orderBy('id', 'asc')
                ->get();
    
            // 既存のビューが配列を期待している場合に合わせる
            $value = $facilities->toArray();
            $data  = "";
    
            $page  = 'facility';
            $title = Common::$title[$page];
            $group = Common::$group[$page];
    
            // 追加メッセージがあれば渡す
            if ($request->filled('addmess')) {
                $addmess = $request->input('addmess');
                return view($page, compact('title', 'page', 'group', 'value', 'data', 'addmess'));
            }
            return view($page, compact('title', 'page', 'group', 'value', 'data'));
        }
    
        // 施設本体
        $adddata = Facility::where('id', $id)->get();
    
        // 施設に紐づく既存グループ
        $groups = Group::where('facilityno', $id)
            ->orderBy('group_id', 'asc')
            ->get();
    
        $page   = 'facility_fix';
        $title  = Common::$title[$page];
        $group  = Common::$group[$page];
        $data   = "";
    
        if ($request->filled('addmess')) {
            $addmess = $request->input('addmess');
            return view('facility_fix', compact('title', 'page', 'group', 'adddata', 'groups', 'data', 'addmess'));
        }
        return view('facility_fix', compact('title', 'page', 'group', 'adddata', 'groups', 'data'));
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
            
                // ▼▼ ここから追記：group_names[] を groups に保存 ▼▼
            $names = (array) $request->input('group_names', []);
            $bulk = [];
            foreach ($names as $name) {
                $name = trim((string)$name);
                if ($name === '') continue; // 未入力はスキップ
                $bulk[] = [
                    'group_name' => $name,
                    'facilityno' => (int) $insertid,
                    // timestamps を使っているなら入れる（Model で $timestamps=false なら省略OK）
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($bulk)) {
                // insert は一括・バリデーション済みなので高速で安全
                Group::insert($bulk);
            }

                //作業者管理マスタNO_番号で登録
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
    // 対象の施設ID・入力施設名を取得
    $id       = (int) $request->input('id');
    $facility = (string) $request->input('facility');

    // 施設名の重複チェック（自分以外＆未削除）
    $exists = Facility::where('facility', $facility)
        ->where('id', '!=', $id)
        ->where('delflag', 0)
        ->exists();

    if ($exists) {
        $exist_mes = ['facility' => Common::$erralr_faci];
        return response()->json(['errors' => $exist_mes], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // バリデーション（既存ロジックを踏襲）
    $rulus_obj = Common::facility_rulus();
    $rulus = json_decode(json_encode($rulus_obj), true);

    $tmp1 = $rulus['facility'];
    array_push($tmp1, new space);
    $rulus['facility'] = $tmp1;

    $tmp2 = $rulus['address'];
    array_push($tmp2, new space);
    $rulus['address'] = $tmp2;

    $tmp3 = $rulus['mail'];
    array_push($tmp3, new AlphaNumHalf_mail);
    $rulus['mail'] = $tmp3;

    $validator = Validator::make($request->all(), $rulus, Common::$message_);
        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->getMessageBag()->toArray()],
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        try {
            if ($request->isMethod('POST')) {
    
                $insertid = (int)$request->input('id'); // 施設ID
                Common::Update_facility($request->all()); // 施設本体を更新
    
                // ★ ここからグループ更新/追加（既存上書き＋新規追加）
                $existingIds   = $request->input('group_ids', []);     // 既存のgroup_id[]
                $existingNames = $request->input('group_names', []);   // 対になるgroup_name[]
                $newNames      = $request->input('new_group_names', []); // 新規追加分
    
                DB::transaction(function () use ($insertid, $existingIds, $existingNames, $newNames) {
                    // 既存の上書き
                    foreach ((array)$existingIds as $i => $gid) {
                        $name = isset($existingNames[$i]) ? trim($existingNames[$i]) : '';
                        if ($name === '') {
                            // 空にされたら更新しない（削除したい運用ならここで delete に切替可）
                            continue;
                        }
                        Group::where('group_id', $gid)
                             ->where('facilityno', $insertid)
                             ->update(['group_name' => $name]);
                    }
    
                    // 新規の追加
                    foreach ((array)$newNames as $name) {
                        $name = trim($name);
                        if ($name === '') continue;
                        Group::create([
                            'group_name' => $name,
                            'facilityno' => $insertid,
                        ]);
                    }
                });
    
                return $insertid; // 既存の戻り値仕様に合わせて施設ID返却
            }
        } catch (\Throwable $e) {
            // …（既存のエラーハンドリングのまま）…
            return json_encode("error");
        }
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


