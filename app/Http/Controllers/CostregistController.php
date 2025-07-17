<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use \App\Library\Common;
use Illuminate\Support\Facades\Validator;

class CostregistController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = "";
        $page = 'costregist';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        return view($page, compact('title','page','group','data'));
        
    }

    
    public function regist(Request $request)
    {
        //送信されたフォーム情報を取得
        $target = $_POST['fname']."_file";
        /* 拡張子情報の取得・セット */
        if($_FILES[$target]['size'] >0 )
        {
            if($_FILES[$target]['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
            {
                // 一時アップロード先ファイルパス
                $file_tmp  = $_FILES[$target]["tmp_name"];
                // 正式保存先ファイルパス
                //$_FILES["CurrentFile_file"]["name"]->現在のファイル名
                // asset('storage/file.txt');
                // app_path
                // "C:\Users\jibiki\Desktop\backpain\storage\app\FileWherehouse\BasicExcel\\"

                if($target == "currentfile_file") $file_save = base_path().Common::$pub_str_path.Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseCurrentFile;
                else if($target == "introfile_file")$file_save = base_path().Common::$pub_str_path.Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseIntroFile;
                // if($target == "CurrentFile_file") $file_save = Common::$app_path.Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseCurrentFile;
                // else if($target == "IntroFile_file")$file_save = Common::$app_path.Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseIntroFile;
                else return -1;
                
                // if($target == "CurrentFile_file") $file_save = Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseCurrentFile;
                // else if($target == "IntroFile_file")$file_save = Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseIntroFile;
                // else return -1;
                // ファイル移動
                // $result = move_uploaded_file($file_tmp, $file_save);
                $result = copy($file_tmp, $file_save);
                if ( $result === true ) return 0;
                else return -2;
            }
            else
            {   
                return -3;
            }
        }
        else return -4;
    }
}

