<?php

namespace App\Library;
use App\Models\User;
use App\Models\Facility;
use App\Models\Wearable;
use App\Models\Helper;
use App\Models\BackPain;
use App\Models\Meauser;
use \App\Library\Common;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use \App\Rules\AlphaNumHalf;
use \App\Rules\AlphaNumHalf_mail;
use \App\Rules\space;

class Common
{
    static public $debug_path = '/storage/app/debug/debug.txt';
    //publicディレクトリの完全パスpublic_path();
    //プロジェクトの完全パスbase_path()

    // backpain\storage\app\public
    // static public $Picture_path = '/FileWherehouse/Picture';

    static public $Picture_path = 'FileWherehouse/Picture/';
    static public $public = 'public/';
    static public $pub_str_path = '/public/';

    //Excelディレクトリ
    static public $Excel_path = 'FileWherehouse/Excel/';
    //調査票元型
    static public $InvestigationFile = 'Investigation.xlsx';


    //Excelフォーマット
    static public $BasicExcel_path = 'BasicExcel/';
    static public $BaseCurrentFile = 'BaseCurrentFile.xlsx';
    static public $BaseIntroFile = 'BaseIntroFile.xlsx';

    //施設コストファイル
    static public $CurrentFile = 'CurrentFile.xlsx';
    static public $CurrentFile_tmp = 'CurrentFile_tmp.xlsx';
    static public $IntroFile = 'IntroFile.xlsx';
    static public $IntroFile_tmp = 'IntroFile_tmp.xlsx';
    static public $sheetName_data = "データ";
    static public $sheetName_current = "現状コストⅠ";
    static public $sheetName_intro = "導入コスト";
    static public $sheetIndex = 1;


    //
    static public $erralr_user = "このユーザ名は既に登録されています";
    static public $erralr_device = "このセンサー名は既に登録されています";
    static public $erralr_faci = "この施設名は既に登録されています";
    static public $erralr_helper = "この介助者名は既に登録されています";

    static public $title = array(
        "login" => "ログイン画面",
        "mainmenu" => "メニュー",
        "loginuser" => "ログインユーザ一覧",
        "loginuser_add" => "ログインユーザ追加",
        "loginuser_fix" => "ログインユーザ修正",
        "wearable" => "心拍センサー一覧",
        "wearable_add" => "心拍センサー追加",
        "wearable_fix" => "心拍センサー修正",
        "risksensor"=> "リスクデバイス一覧",
        "risksensor_add"=> "リスクデバイス追加",
        "risksensor_fix"=> "リスクデバイス修正",
        "facility"=> "施設一覧",
        "facility_add"=> "施設情報追加",
        "facility_fix"=> "施設情報修正",
        "cost_ctrl"=> "コストデータ管理",
        "helper"=> "介助者一覧",
        "helper_add"=> "介助者追加",
        "helper_fix"=> "介助者修正",
        "helperdata"=> "介助者データ表示",
        "comparison"=> "介助者データ比較",
        "averdata"=> "平均データ表示",
        "facilityinput"=> "施設情報入力",
        "costregist"=> "現状コスト/導入コスト登録",
    );

    static public $group = array(
        "login" => "login",
        "mainmenu" => "mainmenu",
        "loginuser" => "mainmenu",
        "loginuser_add" => "loginuser",
        "loginuser_fix" => "loginuser",
        "wearable" => "mainmenu",
        "wearable_add" => "wearable",
        "wearable_fix" => "wearable",
        "risksensor"=> "mainmenu",
        "risksensor_add"=> "risksensor",
        "risksensor_fix"=> "risksensor",
        "facility"=> "mainmenu",
        "facility_add"=> "facility",
        "facility_fix"=> "facility",
        "cost_ctrl"=> "facility",
        "helper"=> "facility",
        "helper_add"=> "helper",
        "helper_fix"=> "helper",
        "helperdata"=> "helper",
        "comparison"=> "helperdata",
        "averdata"=> "mainmenu",
        "facilityinput"=> "mainmenu",
        "costregist"=> "mainmenu",
    );



    static public $rulus_ = [
        'helpername' => ['required','min:1','string','max:50','alpha_dash'],
        'wearableno' => ['nullable','min:1','integer','max:10'],
        'facilityno' => ['integer','min:1','max:10'],
        'position' => ['string','max:1','nullable'],
        'backpainno' =>['nullable','integer','min:1','max:10'],
        'age' => ['string','max:3','nullable'],
        'sex' => ['string','max:1','nullable'],
        'jobfrom' => ['string','max:5'],
        'jobto' =>['string','max:5'],
        'measufrom' => ['string','max:10'],
        'measuto' => ['string','max:10'],
        // 'img1' => [
            // //nullでもOK
            // 'nullable',
            // // アップロードされたファイルであること
            // 'file',
            // // 画像ファイルであること
            // 'image',
            // // MIMEタイプを指定
            // 'mimes:jpeg,jpg',
        // ],
        // 'age' => 'integer | between:0,150',
        // 'sex' => ['max:1', 'regex:/^[男|女]+$/u'],

      ];



    //オブジェクトルールはコントローラ側で処理(new ~)
    //全角半角英数字　日本語不可
    static public $alpha_fullhalf_ja = 'regex:/^[Ａ-Ｚａ-ｚa-zA-Z0-9]+$/';
    //半角英数 日本語不可
    static public $alpha_half = 'regex:/^[a-zA-Z0-9]+$/';
    //半角英数 日本語不可 メール記号のみ可能
    // static public $alpha_half_mail = 'regex:/^[a-zA-Z0-9\._-@]+$/';
    static public $alpha_half_mail = 'regex:/^[a-zA-Z0-9\.\_\@\-]+$/';
    //半角数字のみ
    static public $num_half = 'regex:/^[0-9]+$/';
    //全角半角英数字　日本語可
    //alpha_num
    // static public $alpha_all = 'regex:/^[Ａ-Ｚａ-ｚa-zA-Z0-9０-９ぁ-んァ-ヶｦ-ﾟ一-龠\x20　\ー-‐－]+$/u';
    static public $alpha_all = 'regex:/^[Ａ-Ｚａ-ｚa-zA-Z0-9０-９ぁ-んァ-ヶｦ-ﾟ一-龠\x20　\ー\－\−\-\‐]+$/u';
    //小数点
    static public $num_real = 'regex:/^([1-9][0-9]{0,9}|0)(\.[0-9]{0,1})?$/';




    static public function login_validator($request)
    {
        $rulus = [
            // 'username' => ['required','string', 'max:20',new AlphaNumHalf],
            // 'pass' => ['required', 'string',new AlphaNumHalf],
            'username' => ['required','string', 'max:20',Common::$alpha_half],
            'pass' => ['required', 'string',Common::$alpha_half],
        ];
        $validator = Validator::make($request->all(),$rulus, Common::$message_)->validate();
    }


    static public function user_validator($request)
    {
        $rulus = Common::user_rulus();
        $validator = Validator::make($request->all(),$rulus, Common::$message_)->validate();
    }

    static public function user_rulus()
    {
        $rulus = [
            // 'username' => ['required','string', 'max:20',new AlphaNumHalf],
            // 'pass' => ['required', 'string', 'confirmed',new AlphaNumHalf],
            'username' => ['required','string', 'max:20',Common::$alpha_half],
            'pass' => ['required', 'string', 'confirmed',Common::$alpha_half],
            'authority' => ['string','max:1'],
            'facilityno' => ['integer', 'digits_between:0,10'],
        ];
        return $rulus;
    }


    static public function wearable_validator($request)
    {
        $rulus = Common::wearable_rulus();
        $validator = Validator::make($request->all(),$rulus, Common::$message_)->validate();
    }


    static public function wearable_rulus()
    {
        $rulus = [
            // 'devicename' => ['required','string', 'max:20',new AlphaNumHalf],
            // 'clientid' => ['nullable','string', 'max:20','alpha_dash',new AlphaNumHalf],
            // 'clientsc' => ['nullable','string', 'max:40','alpha_dash',new AlphaNumHalf],
            // 'auth' => ['nullable','string', 'max:60','alpha_dash',new AlphaNumHalf],
            'devicename' => ['required','string', 'max:20',Common::$alpha_half],
            'userid' => ['nullable','string', 'max:40',Common::$alpha_half_mail],
            'passwd' => ['nullable','string', 'max:40',Common::$alpha_half],
            'clientid' => ['nullable','string', 'max:20'],
            'clientsc' => ['nullable','string', 'max:40'],
            'auth' => ['nullable','string', 'max:60'],
        ];
        return $rulus;
    }


    static public function helper_validator($request)
    {
        $rulus = Common::helper_rulus();
        $validator = Validator::make($request->all(),$rulus, Common::$message_)->validate();
    }
    static public function helper_rulus()
    {
        $rulus = [
            // 'helpername' => ['required','string','max:20','alpha_num'],
            'helpername' => ['required','string','max:20',Common::$alpha_all,new space],
            'wearableno' => ['nullable','integer','digits_between:0,10'],
            'facilityno' => ['nullable','integer','digits_between:0,10'],
            'position' => ['required','string','max:1'],
            'backpainno' => ['nullable','integer','digits_between:0,10'],
            'age' => ['required','integer'],
            'sex' => ['required','string','max:1'],
            'pic1' => ['nullable','string','max:1'],
            'pic2' => ['nullable','string','max:1'],
            'pic3' => ['nullable','string','max:1'],
            'pic4' => ['nullable','string','max:1'],
            'pic5' => ['nullable','string','max:1'],
            'jobfrom_h' => ['nullable','string','max:2'],
            'jobfrom_m' => ['nullable','string','max:2'],
            'jobto_h' => ['nullable','string','max:2'],
            'jobto_m' => ['nullable','string','max:2'],

            'measufrom' => ['nullable','string','max:10'],
            'measuto' => ['nullable','string','max:10'],
            'img1' => [
            //nullでもOK
            'nullable',
            // アップロードされたファイルであること
            'file',
            // 画像ファイルであること
            'image',
            // MIMEタイプを指定
            'mimes:jpeg,jpg,png,pdf',
            ],
            'img2' => ['nullable', 'file','image','mimes:jpeg,jpg'],
            'img3' => ['nullable', 'file','image','mimes:jpeg,jpg'],
            'img4' => ['nullable', 'file','image','mimes:jpeg,jpg'],
            'img5' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        ];
        return $rulus;
    }


    static public function facility_validator($request)
    {
        $rulus = Common::facility_rulus();
        $validator = Validator::make($request->all(),$rulus, Common::$message_)->validate();
    }
    static public function facility_rulus()
    {
        $rulus = [
        // 'facility' => ['required','string', 'max:10','regex:/^[Ａ-Ｚａ-ｚa-zA-Z0-9]+$/'],
        // 'tel' => ['nullable','string', 'max:16',new AlphaNumHalf],
        // 'mail' => ['nullable','string', 'max:40','regex:/^[a-zA-Z0-9\._-]*@+$/'],
        'facility' => ['required','string', 'max:20',Common::$alpha_all,new space],
        'pass' => ['nullable','string', 'max:2'],
        'address' => ['nullable','string', 'max:100',Common::$alpha_all,new space],
        'mail' => ['nullable','string', 'max:40',new AlphaNumHalf_mail],
        'tel' => ['nullable','string', 'max:20',Common::$num_half],
        //2021.05.18 追加
        'url' => ['nullable','string', 'max:256'],

        'item1' => ['nullable','integer'],
        'item2' => ['nullable','integer'],
        'item3' => ['nullable','integer'],
        'item4' => ['nullable','integer'],
        'item5' => ['nullable','integer'],
        'item6' => ['nullable','integer'],
        'item7' => ['nullable','integer'],
        'item8' => ['nullable','integer'],
        'item9' => ['nullable','integer'],

        'item10' => ['nullable','integer'],
        'item11' => ['nullable','integer'],
        'item12' => ['nullable','integer'],
        'item13' => ['nullable','integer'],
        'item14' => ['nullable','integer'],
        'item15' => ['nullable','integer'],
        'item16' => ['nullable','integer'],
        'item17' => ['nullable',Common::$num_real],
        'item18' => ['nullable','integer'],
        'item19' => ['nullable','integer'],

        'item20' => ['nullable','integer'],
        'item21' => ['nullable',Common::$num_real],
        'item22' => ['nullable','integer'],
        'item23' => ['nullable','integer'],
        'item24' => ['nullable','integer'],
        'item25' => ['nullable',Common::$num_real],
        'item26' => ['nullable','integer'],
        'item27' => ['nullable','integer'],
        'item28' => ['nullable','integer'],
        'item29' => ['nullable',Common::$num_real],

        'item30' => ['nullable','integer'],
        'item31' => ['nullable','integer'],
        'item32' => ['nullable','integer'],
        'item33' => ['nullable','integer'],
        'item34' => ['nullable','integer'],
        'item35' => ['nullable','integer'],
        'item36' => ['nullable','integer'],
        'item37' => ['nullable','integer'],
        'item38' => ['nullable','integer'],
        'item39' => ['nullable','integer'],
//小数点
        'item40' => ['nullable',Common::$num_real],
        'item41' => ['nullable','integer'],
        'item42' => ['nullable','integer'],
        'item43' => ['nullable',Common::$num_real],
        'item44' => ['nullable','integer'],
        'item45' => ['nullable','integer'],
        'item46' => ['nullable',Common::$num_real],
        'item47' => ['nullable','integer'],
        'item48' => ['nullable',Common::$num_real],
        'item49' => ['nullable','integer'],

        'item50' => ['nullable','integer'],
        'item51' => ['nullable','integer'],
        'item52' => ['nullable','integer'],
        'item53' => ['nullable','integer'],
        'item54' => ['nullable','integer'],
        'item55' => ['nullable','integer'],
        'item56' => ['nullable','integer'],
        'item57' => ['nullable','integer'],
        'item58' => ['nullable','integer'],
        'item59' => ['nullable','integer'],

        'item60' => ['nullable','integer'],
        'item61' => ['nullable','integer'],
        'item62' => ['nullable','integer'],
        'item63' => ['nullable','integer'],
        'item64' => ['nullable','integer'],
        'item65' => ['nullable','integer'],
        'item66' => ['nullable',Common::$num_real],
        'item67' => ['nullable','integer'],
        'item68' => ['nullable','integer'],
        'item69' => ['nullable','integer'],
        'item70' => ['nullable','integer'],
        'item71' => ['nullable','integer'],
        'item72' => ['nullable','integer'],

        'pic1' => ['nullable','integer'],
        'pic2' => ['nullable','integer'],
        'pic3' => ['nullable','integer'],
        'pic4' => ['nullable','integer'],
        'pic5' => ['nullable','integer'],
        'pic6' => ['nullable','integer'],
        'pic7' => ['nullable','integer'],
        'pic8' => ['nullable','integer'],
        'pic9' => ['nullable','integer'],
        'pic10' => ['nullable','integer'],
        'pic11' => ['nullable','integer'],
        'pic12' => ['nullable','integer'],
        'pic13' => ['nullable','integer'],
        'pic14' => ['nullable','integer'],
        'pic15' => ['nullable','integer'],
        'pic16' => ['nullable','integer'],
        'pic17' => ['nullable','integer'],
        'pic18' => ['nullable','integer'],
        'pic19' => ['nullable','integer'],
        'pic20' => ['nullable','integer'],
        'img1' => [
            //nullでもOK
            'nullable',
            // アップロードされたファイルであること
            'file',
            // 画像ファイルであること
            'image',
            // MIMEタイプを指定
            'mimes:jpeg,jpg',
            ],
        'img2' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img3' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img4' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img5' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img6' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img7' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img8' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img9' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img10' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img11' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img12' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img13' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img14' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img15' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img16' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img17' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img18' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img19' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        'img20' => ['nullable', 'file','image','mimes:jpeg,jpg'],
        ];
        return $rulus;
    }

    static public function risk_validator($request)
    {
        $rulus = Common::risk_rulus();
        $validator = Validator::make($request->all(),$rulus, Common::$message_risk)->validate();
    }
    static public function risk_rulus()
    {
        $rulus = [
            // 'devicename' => ['required','string','max:3',new AlphaNumHalf],
            'devicename' => ['required','string','max:3',Common::$num_half],
        ];
        return $rulus;
    }

    static public $message_risk = [
        'devicename.required'=> 'デバイス名を入力してください',
        'devicename.max'=> '3文字以内で入力してください',
        'devicename.regex' => '半角数字で入力してください',
    ];


    static public $message_ = [

        //ログインユーザ
        'username.required' => 'ユーザ名を入力してください',
        'username.max' => '20文字以内で入力してください',
        'username.regex' => '半角英数字で入力してください',
        'pass.required' => 'パスワードを入力してください',
        'pass.confirmed' => 'パスワードが一致しません',
        'pass.regex' => '半角英数字で入力してください',
        'authority.max' => '正しく選択してください',
        'facilityno.required' => '正しく選択してください',
        'facilityno.string' => '正しく選択してください',

        //ウェアラブルデバイス
        'devicename.required'=> 'センサー名を入力してください',
        'devicename.max'=> '20文字以内で入力してください',
        'devicename.regex' => '半角英数字で入力してください',
        'userid.max'=> '40文字以内で入力してください',
        'userid.regex' => '半角英数字で入力してください',
        'passwd.max'=> '40文字以内で入力してください',
        'passwd.regex' => '半角英数字で入力してください',
        'clientid.max' => '20文字以内で入力してください',
        'clientsc.max' => '40文字以内で入力してください',
        'auth.max' => '60文字以内で入力してください',



        //介助者
        'helpername.required' => '介助者名を入力してください',
        'helpername.string' => '英数字で名前を入力してください',
        'helpername.max' => '20文字以内で入力してください',
        // 'helpername.alpha_num' => '全角半角英数字もしくは日本語で入力してください',
        'helpername.regex' => '全角半角英数字もしくは日本語で入力してください',

        'wearableno.integer' => '数字で入力してください',
        'wearableno.digits_between' => '10文字以内で登録してください',

        'facilityno.integer' => '数字で入力してください',
        'facilityno.digits_between' => '10文字以内で登録してください',

        'position.string' => '半角英数字で入力してください',
        'position.max' => '1文字以内で登録してください',
        'position.required' => '役職を入力してください',

        'backpainno.integer' => '数字で入力してください',
        'backpainno.digits_between' => '10文字以内で登録してください',

        'age.integer' => '半角数字で入力してください',
        'age.digits_between' => '3文字以内で登録してください',
        'age.required' => '年齢を入力してください',

        'sex.string' => '半角英数字で入力してください',
        'sex.max' => '1文字以内で登録してください',
        'sex.required' => '性別を入力してください',


        'jobfrom.string' => '半角英数字で入力してください',
        'jobfrom.max' => '5文字以内で登録してください',
        'jobto.string' => '半角英数字で入力してください',
        'jobto.max' => '5文字以内で登録してください',
        'measufrom.string' => '半角英数字で入力してください',
        'measufrom.max' => '10文字以内で登録してください',
        'measuto.string' => '半角英数字で入力してください',
        'measuto.max' => '10文字以内で登録してください',

        'img1.file' => '画像ファイルを登録してください',
        'img1.image' => '画像ファイルを登録してください',
        'img1.mimes' => 'ファイルの拡張子が異なります',
        'img2.file' => '画像ファイルを登録してください',
        'img2.image' => '画像ファイルを登録してください',
        'img2.mimes' => 'ファイルの拡張子が異なります',
        'img3.file' => '画像ファイルを登録してください',
        'img3.image' => '画像ファイルを登録してください',
        'img3.mimes' => 'ファイルの拡張子が異なります',
        'img4.file' => '画像ファイルを登録してください',
        'img4.image' => '画像ファイルを登録してください',
        'img4.mimes' => 'ファイルの拡張子が異なります',
        'img5.file' => '画像ファイルを登録してください',
        'img5.image' => '画像ファイルを登録してください',
        'img5.mimes' => 'ファイルの拡張子が異なります',
        // 'age.numeric' => '整数で入力してください',
        // 'age.between' => '0～150で入力してください',
        // 'sex.regex' => '男か女で入力してください',



        // Facility
        'facility.required'=> '入力してください',
        'facility.max'=> '20文字以内で入力してください',
        // 'facility.alpha_num' => '全角半角英数字もしくは日本語で入力してください',
        'facility.regex' => '全角半角英数字もしくは日本語で入力してください',
        // 'pass.max'=> '入力してください',
        'address.max' => '100文字以内で入力してください',
        // 'address.alpha_num' => '全角半角英数字もしくは日本語で入力してください',
        'address.regex' => '全角半角英数字もしくは日本語で入力してください',
        'tel.max' => '20文字以内で入力してください',
        'tel.regex' => '半角数字で入力してください',

        'mail.max' => '40文字以内で入力してください',
        'mail.regex' => '40文字以内で入力してください',

        //2021.05.18追加
        'url.max' => '256文字以内で入力してください',

        'item1.integer' => '半角数字で入力してください',
        'item2.integer' => '半角数字で入力してください',
        'item3.integer' => '半角数字で入力してください',
        'item4.integer' => '半角数字で入力してください',
        'item5.integer' => '半角数字で入力してください',
        'item6.integer' => '半角数字で入力してください',
        'item7.integer' => '半角数字で入力してください',
        'item8.integer' => '半角数字で入力してください',
        'item9.integer' => '半角数字で入力してください',
        'item10.integer' => '半角数字で入力してください',
        'item11.integer' => '半角数字で入力してください',
        'item12.integer' => '半角数字で入力してください',
        'item13.integer' => '半角数字で入力してください',
        'item14.integer' => '半角数字で入力してください',
        'item15.integer' => '半角数字で入力してください',
        'item16.integer' => '半角数字で入力してください',
        'item17.regex' => '小数点以下1桁までの数字で入力してください',
        'item18.integer' => '半角数字で入力してください',
        'item19.integer' => '半角数字で入力してください',
        'item20.integer' => '半角数字で入力してください',
        'item21.regex' => '小数点以下1桁までの数字で入力してください',
        'item22.integer' => '半角数字で入力してください',
        'item23.integer' => '半角数字で入力してください',
        'item24.integer' => '半角数字で入力してください',
        'item25.regex' => '小数点以下1桁までの数字で入力してください',
        'item26.integer' => '半角数字で入力してください',
        'item27.integer' => '半角数字で入力してください',
        'item28.integer' => '半角数字で入力してください',
        'item29.regex' => '小数点以下1桁までの数字で入力してください',
        'item30.integer' => '半角数字で入力してください',
        'item31.integer' => '半角数字で入力してください',
        'item32.integer' => '半角数字で入力してください',
        'item33.integer' => '半角数字で入力してください',
        'item34.integer' => '半角数字で入力してください',
        'item35.integer' => '半角数字で入力してください',
        'item36.integer' => '半角数字で入力してください',
        'item37.integer' => '半角数字で入力してください',
        'item38.integer' => '半角数字で入力してください',
        'item39.integer' => '半角数字で入力してください',
        'item40.regex' => '小数点以下1桁までの数字で入力してください',
        'item41.integer' => '半角数字で入力してください',
        'item42.integer' => '半角数字で入力してください',
        'item43.regex' => '小数点以下1桁までの数字で入力してください',
        'item44.integer' => '半角数字で入力してください',
        'item45.integer' => '半角数字で入力してください',
        'item46.regex' => '小数点以下1桁までの数字で入力してください',
        'item47.integer' => '半角数字で入力してください',
        'item48.regex' => '小数点以下1桁までの数字で入力してください',
        'item49.integer' => '半角数字で入力してください',
        'item50.integer' => '半角数字で入力してください',
        'item51.integer' => '半角数字で入力してください',
        'item52.integer' => '半角数字で入力してください',
        'item53.integer' => '半角数字で入力してください',
        'item54.integer' => '半角数字で入力してください',
        'item55.integer' => '半角数字で入力してください',
        'item56.integer' => '半角数字で入力してください',
        'item57.integer' => '半角数字で入力してください',
        'item58.integer' => '半角数字で入力してください',
        'item59.integer' => '半角数字で入力してください',
        'item60.integer' => '半角数字で入力してください',
        'item61.integer' => '半角数字で入力してください',
        'item62.integer' => '半角数字で入力してください',
        'item63.integer' => '半角数字で入力してください',
        'item64.integer' => '半角数字で入力してください',
        'item65.integer' => '半角数字で入力してください',
        'item66.regex' => '小数点以下1桁までの数字で入力してください',
        'item67.integer' => '半角数字で入力してください',
        'item68.integer' => '半角数字で入力してください',
        'item69.integer' => '半角数字で入力してください',
        'item70.integer' => '半角数字で入力してください',
        'item71.integer' => '半角数字で入力してください',
        'item72.integer' => '半角数字で入力してください',
        'pic1.integer' => '半角数字で入力してください',
        'pic2.integer' => '半角数字で入力してください',
        'pic3.integer' => '半角数字で入力してください',
        'pic4.integer' => '半角数字で入力してください',
        'pic5.integer' => '半角数字で入力してください',
        'pic6.integer' => '半角数字で入力してください',
        'pic7.integer' => '半角数字で入力してください',
        'pic8.integer' => '半角数字で入力してください',
        'pic9.integer' => '半角数字で入力してください',
        'pic10.integer' => '半角数字で入力してください',
        'pic11.integer' => '半角数字で入力してください',
        'pic12.integer' => '半角数字で入力してください',
        'pic13.integer' => '半角数字で入力してください',
        'pic14.integer' => '半角数字で入力してください',
        'pic15.integer' => '半角数字で入力してください',
        'pic16.integer' => '半角数字で入力してください',
        'pic17.integer' => '半角数字で入力してください',
        'pic18.integer' => '半角数字で入力してください',
        'pic19.integer' => '半角数字で入力してください',
        'pic20.integer' => '半角数字で入力してください',
        'img1.file' => '画像ファイルを登録してください',
        'img1.image' => '画像ファイルを登録してください',
        'img1.mimes' => 'ファイルの拡張子が異なります',
        'img2.file' => '画像ファイルを登録してください',
        'img2.image' => '画像ファイルを登録してください',
        'img2.mimes' => 'ファイルの拡張子が異なります',
        'img3.file' => '画像ファイルを登録してください',
        'img3.image' => '画像ファイルを登録してください',
        'img3.mimes' => 'ファイルの拡張子が異なります',
        'img4.file' => '画像ファイルを登録してください',
        'img4.image' => '画像ファイルを登録してください',
        'img4.mimes' => 'ファイルの拡張子が異なります',
        'img5.file' => '画像ファイルを登録してください',
        'img5.image' => '画像ファイルを登録してください',
        'img5.mimes' => 'ファイルの拡張子が異なります',

        'img6.file' => '画像ファイルを登録してください',
        'img6.image' => '画像ファイルを登録してください',
        'img6.mimes' => 'ファイルの拡張子が異なります',
        'img7.file' => '画像ファイルを登録してください',
        'img7.image' => '画像ファイルを登録してください',
        'img7.mimes' => 'ファイルの拡張子が異なります',
        'img8.file' => '画像ファイルを登録してください',
        'img8.image' => '画像ファイルを登録してください',
        'img8.mimes' => 'ファイルの拡張子が異なります',
        'img9.file' => '画像ファイルを登録してください',
        'img9.image' => '画像ファイルを登録してください',
        'img9.mimes' => 'ファイルの拡張子が異なります',
        'img10.file' => '画像ファイルを登録してください',
        'img10.image' => '画像ファイルを登録してください',
        'img10.mimes' => 'ファイルの拡張子が異なります',

        'img11.file' => '画像ファイルを登録してください',
        'img11.image' => '画像ファイルを登録してください',
        'img11.mimes' => 'ファイルの拡張子が異なります',
        'img12.file' => '画像ファイルを登録してください',
        'img12.image' => '画像ファイルを登録してください',
        'img12.mimes' => 'ファイルの拡張子が異なります',
        'img13.file' => '画像ファイルを登録してください',
        'img13.image' => '画像ファイルを登録してください',
        'img13.mimes' => 'ファイルの拡張子が異なります',
        'img14.file' => '画像ファイルを登録してください',
        'img14.image' => '画像ファイルを登録してください',
        'img14.mimes' => 'ファイルの拡張子が異なります',
        'img15.file' => '画像ファイルを登録してください',
        'img15.image' => '画像ファイルを登録してください',
        'img15.mimes' => 'ファイルの拡張子が異なります',

        'img16.file' => '画像ファイルを登録してください',
        'img16.image' => '画像ファイルを登録してください',
        'img16.mimes' => 'ファイルの拡張子が異なります',
        'img17.file' => '画像ファイルを登録してください',
        'img17.image' => '画像ファイルを登録してください',
        'img17.mimes' => 'ファイルの拡張子が異なります',
        'img18.file' => '画像ファイルを登録してください',
        'img18.image' => '画像ファイルを登録してください',
        'img18.mimes' => 'ファイルの拡張子が異なります',
        'img19.file' => '画像ファイルを登録してください',
        'img19.image' => '画像ファイルを登録してください',
        'img19.mimes' => 'ファイルの拡張子が異なります',
        'img20.file' => '画像ファイルを登録してください',
        'img20.image' => '画像ファイルを登録してください',
        'img20.mimes' => 'ファイルの拡張子が異なります',

      ];


      static public function create_user(array $data)
    {

        if(isset($data['facilityno']))
        {
            $facilityno  = intval($data['facilityno']);
        }
        else $facilityno  = 0;

        return User::insertGetId([
            'username' => $data['username'],
            'pass' => Hash::make($data['pass']),
            'authority' => $data['authority'],
            'facilityno' => $facilityno,
            'insdatetime' => now(),
            'upddatetime' => now(),
            'delflag' => '0',
            'insuserno' => Auth::user()->id,
            'upduserno' => 0,
        ]);
    }


    static public function create_wearable(array $data,$pass)
    {
        // return Wearable::create([
            return Wearable::insertGetId([
            'devicename' => $data['devicename'],
            'clientid' => $data['clientid'],
            'clientsc' => $data['clientsc'],
            'auth' => $data['auth'],

            'userid' => $data['userid'],
            'passwd' => $pass,

            'insdatetime' => now(),
            'upddatetime' => now(),

            'delflag' => '0',
            'insuserno' => Auth::user()->id,
            'upduserno' => 0,
        ]);
    }

    static public function create_facility(array $data)
    {
        $item = array();
        //2021.05.18
        for($i=1;$i<73;$i++)
        {
            if(isset($data['item'.$i]))$item[$i] = $data['item'.$i];
            else $item[$i] = 0;
        }

        // return Facility::create([
            return Facility::insertGetId([
            'facility' => $data['facility'],
            'pass' => $data['pass'],
            'address' => $data['address'],
            'tel' => $data['tel'],
            'mail' => $data['mail'],

            'insdatetime' => now(),
            'upddatetime' => now(),

            'item1' => $item[1],
            'item2' => $item[2],
            'item3' => $item[3],
            'item4' => $item[4],
            'item5' => $item[5],
            'item6' => $item[6],
            'item7' => $item[7],
            'item8' => $item[8],
            'item9' => $item[9],

            'item10' => $item[10],
            'item11' => $item[11],
            'item12' => $item[12],
            'item13' => $item[13],
            'item14' => $item[14],
            'item15' => $item[15],
            'item16' => $item[16],
            'item17' => $item[17],
            'item18' => $item[18],
            'item19' => $item[19],

            'item20' => $item[20],
            'item21' => $item[21],
            'item22' => $item[22],
            'item23' => $item[23],
            'item24' => $item[24],
            'item25' => $item[25],
            'item26' => $item[26],
            'item27' => $item[27],
            'item28' => $item[28],
            'item29' => $item[29],

            'item30' => $item[30],
            'item31' => $item[31],
            'item32' => $item[32],
            'item33' => $item[33],
            'item34' => $item[34],
            'item35' => $item[35],
            'item36' => $item[36],
            'item37' => $item[37],
            'item38' => $item[38],
            'item39' => $item[39],

            'item40' => $item[40],
            'item41' => $item[41],
            'item42' => $item[42],
            'item43' => $item[43],
            'item44' => $item[44],
            'item45' => $item[45],
            'item46' => $item[46],
            'item47' => $item[47],
            'item48' => $item[48],
            'item49' => $item[49],

            'item50' => $item[50],
            'item51' => $item[51],
            'item52' => $item[52],
            'item53' => $item[53],
            'item54' => $item[54],
            'item55' => $item[55],
            'item56' => $item[56],
            'item57' => $item[57],
            'item58' => $item[58],
            'item59' => $item[59],

            'item60' => $item[60],
            'item61' => $item[61],
            'item62' => $item[62],
            'item63' => $item[63],
            'item64' => $item[64],
            'item65' => $item[65],
            'item66' => $item[66],
            'item67' => $item[67],
            'item68' => $item[68],
            'item69' => $item[69],
            'item70' => $item[70],
            'item71' => $item[71],
            'item72' => $item[72],

            'pic1' => 0,
            'pic2' => 0,
            'pic3' => 0,
            'pic4' => 0,
            'pic5' => 0,
            'pic6' => 0,
            'pic7' => 0,
            'pic8' => 0,
            'pic9' => 0,
            'pic10' => 0,
            'pic11' => 0,
            'pic12' => 0,
            'pic13' => 0,
            'pic14' => 0,
            'pic15' => 0,
            'pic16' => 0,
            'pic17' => 0,
            'pic18' => 0,
            'pic19' => 0,
            'pic20' => 0,

            'delflag' => '0',
            'insuserno' => Auth::user()->id,

            //2021.05.18 追加
            'questurl' => $data['url'],
        ]);
    }


    static public function create_risksensor(array $data)
    {
        // return Helper::create([
            return BackPain::insertGetId([
            'devicename' => $data['devicename'],

            'delflag' => '0',
            'insuserno' => Auth::user()->id,
            'insdatetime' => now(),
            'upddatetime' => now(),
        ]);
    }

    static public function create_helper(array $data)
    {

        if(isset($data['wearableno'])) $wearableno = $data['wearableno'];
        else $wearableno = 0;

        if(isset($data['backpainno'])) $backpainno = $data['backpainno'];
        else $backpainno = 0;

        if(isset($data['age'])) $age = $data['age'];
        else $age = 0;

        if(isset($data['sex'])) $sex = $data['sex'];
        else $sex = 0;

        if(isset($data['jobfrom_h']) && isset($data['jobfrom_m'])) $jobfrom = $data['jobfrom_h'].$data['jobfrom_m'];
        else $jobfrom = "";

        if(isset($data['jobto_h']) && isset($data['jobto_m'])) $jobto = $data['jobto_h'].$jobto = $data['jobto_m'];
        else $jobto = "";

        if(isset($data['measufrom'])) $measufrom = str_replace("-","",$data['measufrom']);
        else $measufrom = "";

        if(isset($data['measuto'])) $measuto = str_replace("-","",$data['measuto']);
        else $measuto = "";


        // ??
        // return Helper::create([
            return Helper::insertGetId([
            'helpername' => $data['helpername'],
            'wearableno' => $wearableno,
            'facilityno' => $data['facilityno'],
            'position' => $data['position'],
            'backpainno' => $backpainno,
            'age' => $age,
            'sex' => $sex,

            'jobfrom' => $jobfrom,
            'jobto' => $jobto,
            'measufrom' => $measufrom,
            'measuto' => $measuto,

            'insdatetime' => now(),
            'upddatetime' => now(),

            'pic1' => 0,
            'pic2' => 0,
            'pic3' => 0,
            'pic4' => 0,
            'pic5' => 0,
            'delflag' => '0',
            'insuserno' => Auth::user()->id,
        ]);
    }

    static public function create_measure(array $data,$date,$id)
    {
            if(isset($data['wearableno'])) $wearableno = $data['wearableno'];
            else $wearableno = 0;

            if(isset($data['backpainno'])) $backpainno = $data['backpainno'];
            else $backpainno = 0;

            return Meauser::insertGetId([
            'helperno' => $id,
            'ymd' => $date,
            'wearableno' => $wearableno,
            'backpainno' => $backpainno,

        ]);
    }


    //修正
    static public function Update_wearable(array $data,$pass)
    {
        Wearable::where('id',$data["id"])->update([
            'devicename' => $data['devicename'],
            'clientid' => $data['clientid'],
            'clientsc' => $data['clientsc'],
            'auth' => $data['auth'],

            'userid' => $data['userid'],
            'passwd' => $pass,

            'upddatetime' => now(),
            'upduserno'  => Auth::user()->id,

            ]);
    }

    //修正
    static public function Update_facility(array $data)
    {

        $item = array();
        for($i=1;$i<73;$i++)
        {
            if(isset($data['item'.$i]))$item[$i] = $data['item'.$i];
            else $item[$i] = 0;
        }

        Facility::where('id',$data["id"])->update([
            'facility' => $data['facility'],
            'pass' => $data['pass'],
            'address' => $data['address'],
            'tel' => $data['tel'],
            'mail' => $data['mail'],

            'item1' => $item[1],
            'item2' => $item[2],
            'item3' => $item[3],
            'item4' => $item[4],
            'item5' => $item[5],
            'item6' => $item[6],
            'item7' => $item[7],
            'item8' => $item[8],
            'item9' => $item[9],

            'item10' => $item[10],
            'item11' => $item[11],
            'item12' => $item[12],
            'item13' => $item[13],
            'item14' => $item[14],
            'item15' => $item[15],
            'item16' => $item[16],
            'item17' => $item[17],
            'item18' => $item[18],
            'item19' => $item[19],

            'item20' => $item[20],
            'item21' => $item[21],
            'item22' => $item[22],
            'item23' => $item[23],
            'item24' => $item[24],
            'item25' => $item[25],
            'item26' => $item[26],
            'item27' => $item[27],
            'item28' => $item[28],
            'item29' => $item[29],

            'item30' => $item[30],
            'item31' => $item[31],
            'item32' => $item[32],
            'item33' => $item[33],
            'item34' => $item[34],
            'item35' => $item[35],
            'item36' => $item[36],
            'item37' => $item[37],
            'item38' => $item[38],
            'item39' => $item[39],

            'item40' => $item[40],
            'item41' => $item[41],
            'item42' => $item[42],
            'item43' => $item[43],
            'item44' => $item[44],
            'item45' => $item[45],
            'item46' => $item[46],
            'item47' => $item[47],
            'item48' => $item[48],
            'item49' => $item[49],

            'item50' => $item[50],
            'item51' => $item[51],
            'item52' => $item[52],
            'item53' => $item[53],
            'item54' => $item[54],
            'item55' => $item[55],
            'item56' => $item[56],
            'item57' => $item[57],
            'item58' => $item[58],
            'item59' => $item[59],

            'item60' => $item[60],
            'item61' => $item[61],
            'item62' => $item[62],
            'item63' => $item[63],
            'item64' => $item[64],
            'item65' => $item[65],
            'item66' => $item[66],
            'item67' => $item[67],
            'item68' => $item[68],
            'item69' => $item[69],
            'item70' => $item[70],
            'item71' => $item[71],
            'item72' => $item[72],

            'pic1' => 0,
            'pic2' => 0,
            'pic3' => 0,
            'pic4' => 0,
            'pic5' => 0,
            'pic6' => 0,
            'pic7' => 0,
            'pic8' => 0,
            'pic9' => 0,
            'pic10' => 0,
            'pic11' => 0,
            'pic12' => 0,
            'pic13' => 0,
            'pic14' => 0,
            'pic15' => 0,
            'pic16' => 0,
            'pic17' => 0,
            'pic18' => 0,
            'pic19' => 0,
            'pic20' => 0,

            'upddatetime' => now(),
            'upduserno'  => Auth::user()->id,

            //2021.05.18 追加
            'questurl' => $data['url'],
        ]);
    }

    //修正
    static public function Update_risksensor(array $data)
    {
        BackPain::where('id',$data["id"])->update([
        'devicename' => $data['devicename'],

        'upduserno' => Auth::user()->id,
        'upddatetime' => now(),
        ]);
    }

     //修正
     static public function Update_helper(array $data)
    {
        if(isset($data['wearableno'])) $wearableno = $data['wearableno'];
        else $wearableno = 0;

        if(isset($data['backpainno'])) $backpainno = $data['backpainno'];
        else $backpainno = 0;

        if(isset($data['jobfrom_h']) && isset($data['jobfrom_m'])) $jobfrom = $data['jobfrom_h'].$data['jobfrom_m'];
        else $jobfrom = "";

        if(isset($data['jobto_h']) && isset($data['jobto_m'])) $jobto = $data['jobto_h'].$jobto = $data['jobto_m'];
        else $jobto = "";

        if(isset($data['measufrom'])) $measufrom = str_replace("-","",$data['measufrom']);
        else $measufrom = "";


        if(isset($data['measuto'])) $measuto = str_replace("-","",$data['measuto']);
        else $measuto = "";

        if(isset($data['age'])) $age = $data['age'];
        else $age = "";

        if(isset($data['sex'])) $sex = $data['sex'];
        else $sex = "";

         Helper::where('id',$data["id"])->update([
         'helpername' => $data['helpername'],
         'wearableno' => $wearableno,
         'facilityno' => $data['facility'],
         'position' => $data['position'],
         'backpainno' => $backpainno,
         'age' => $age,
         'sex' => $sex ,

         'jobfrom' => $jobfrom,
         'jobto' => $jobto,
         'measufrom' => $measufrom,
         'measuto' => $measuto,

         'pic1' => 0,
         'pic2' => 0,
         'pic3' => 0,
         'pic4' => 0,
         'pic5' => 0,
         'upduserno' => Auth::user()->id,
         'upddatetime' => now(),
         ]);
    }
}
