<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use \App\Library\Common;
use App\Models\CodeTbl;

//PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;



class CostController extends Controller
{
    //
    public function index(Request $request)
    {
        //施設ID
        if(isset($_POST["id"]))
        {
            $getdata = Facility::select()
            ->whereIn('id',[$_POST["id"]])
            ->orderBy('facility.id','asc')
            ->whereNotIn('facility.delflag',[1])
            ->get();

            $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);

        }   
        $page = 'cost_ctrl';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        return view($page, compact('title','page','group','data'));

    }
    //修正
    public function Update($id,$tar)
    {
        if($tar == "currentfile_file")
        {
            Facility::where('id',$id)->update([
                'currentfile' => Common::$CurrentFile,
    
                'upduserno' => Auth::user()->id,
                'upddatetime' => now(),
                ]);
            return 0;
        }
        else if($tar == "introfile_file")
        {
            Facility::where('id',$id)->update([
                'introfile' => Common::$IntroFile,
                
                'upduserno' => Auth::user()->id,
                'upddatetime' => now(),
                ]);
            return 0;
        }
        else return -1;
    } 


    public function Upload(Request $request)
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
                if(isset($_POST['id']))
                {
                    $dir = $_POST['id'];
                    //ディレクトリが存在するか調べる
                    //存在しない場合はディレクトリを作成する
                    if (!file_exists(base_path().Common::$pub_str_path.Common::$Excel_path.$dir))
                    {
                        if (!mkdir(base_path().Common::$pub_str_path.Common::$Excel_path.$dir, 0777, true))
                        {
                            return -6;
                        }
                    }
                    // 正式保存先ファイルパス
                    //$_FILES["CurrentFile_file"]["name"]->現在のファイル名
                    if($target == "currentfile_file") $file_save = base_path().Common::$pub_str_path.Common::$Excel_path.$dir."/".Common::$CurrentFile;
                    else if($target == "introfile_file")$file_save = base_path().Common::$pub_str_path.Common::$Excel_path.$dir."/".Common::$IntroFile;
                    else return -1;
                    // ファイル移動
                    // $result = move_uploaded_file($file_tmp, $file_save);
                    $result = copy($file_tmp, $file_save);
                    if ($result)
                    {
                        $this->Update($_POST['id'],$target);
                        return 0;
                    } 
                    else return -2;
                }
                else return -5;
            }
            else
            {   
                return -3;
            }
        }
        else return -4;
    }

    
    public function Download(Request $request)
    {
        //送信されたフォーム情報を取得
        

        //施設ID
        if(isset($_POST["id"]))
        {
            $dir = $_POST['id'];
            $getdata = Facility::select()
            ->whereIn('id',[$_POST["id"]])
            ->orderBy('facility.id','asc')
            ->whereNotIn('facility.delflag',[1])
            ->get();
            $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
            $path = base_path();

                                 
            //コードテーブル
            $CodeTbl = new CodeTbl;
            $code = $CodeTbl->all();        
            $codedata = json_decode($code,true);
            $sort2=array();
            
            foreach ((array)$codedata as $key => $value)
            {
                $sort2[$key] = $value['dispno'];
            }
            array_multisort($sort2, SORT_ASC, $codedata);


            //施設管理マスタのファイル名が存在するか調べる
            if(isset($_POST['fname']))
            {
                
                // if(isset($data[0][$_POST['fname']]))
                // {
                    if($_POST['fname'] == "currentfile")
                    {
                        // if(trim($data[0][$_POST['fname']]) == Common::$CurrentFile)
                        // {
                            ///FileWherehouse/Excel/XXXXXX/CurrentFile.xlslファイルが存在するか調べる                            
                            //ファイルが存在しない場合
                            if (!file_exists($path.Common::$pub_str_path.Common::$Excel_path.$dir."/".Common::$CurrentFile))
                            {
                                //存在しない場合は、CaseCurrentFile.xlslをコピーして、シート貼りつけを行う
                                //まずディレクトリが存在するか調べる
                                if(!file_exists($path.Common::$pub_str_path.Common::$Excel_path.$dir))
                                {
                                    //存在しない場合はディレクトリを作成する
                                    if(!mkdir($path.Common::$pub_str_path.Common::$Excel_path.$dir, 0777, true))
                                    {
                                        return -6;
                                    }
                                }
                                //BaseCurrentFileがあるか確認する
                                if(!file_exists($path.Common::$pub_str_path.Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseCurrentFile))
                                {
                                    return -1;
                                }
                                //CaseCurrentFile.xlslをコピーして、シート貼りつけを行う
                                if (!copy($path.Common::$pub_str_path.Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseCurrentFile,
                                base_path().Common::$pub_str_path.Common::$Excel_path.$dir."/".Common::$CurrentFile))
                                {
                                    return -6;
                                }


                                //Excel処理
                                $file_path = $path.Common::$pub_str_path.Common::$Excel_path.$dir."/";
                                //既存ファイルの読み込み
                                $reader = new XlsxReader();
                                $spreadsheet = $reader->load($file_path.Common::$CurrentFile);

                                //シートが存在するか確認
                                //存在しない場合は作成
                                if($spreadsheet->sheetNameExists(Common::$sheetName_data) === false)
                                {
                                    $newsheet = new Worksheet($spreadsheet,Common::$sheetName_data); //ここで任意のシートを新規作成
                                    $spreadsheet -> addSheet($newsheet, Common::$sheetIndex); //任意のシート追加。1は挿入する位置
                                }
                                
                                //選択されているシートを取得
                                // $sheet = $spreadsheet->getActiveSheet(1);
                                //2番目のシートを取得。Indexは0スタート
                                // $sheet = $spreadsheet->getSheet(Common::$sheetIndex);
                                $sheet = $spreadsheet->getSheetByName(Common::$sheetName_data);
                                $data_arr = array();
                                $arr = $getdata->toArray();
                                $cnt=0;
                                foreach ($getdata->toArray() as $key => $value)
                                {
                                    foreach ($value as $key => $val)
                                    {
                                        if($key != "currentfile" && $key != "introfile" && $key != "delflag" && !strpos($key,'pic')
                                        && !preg_match('/pic/',$key)
                                            && $key != "insdatetime" && $key != "insuserno" && $key != "upddatetime" && $key != "upduserno" && $key != "questurl")
                                        {
                                                if($key == "pass")
                                                {                                                  
                                                    foreach($codedata as $codedata)
                                                    {
                                                        if($codedata['codeno'] == 2)
                                                        {
                                                            if($codedata['value'] == $val)
                                                            {
                                                                $data_arr[$cnt] = $codedata['selectname'];
                                                            }
                                                        }
                                                    }
                                                }
                                                else if($val == "") $data_arr[$cnt] = 0;
                                                else $data_arr[$cnt] = $val;
                                                $cnt++;
                                        }
                                    }
                                }
                                //item67までの書き込み
                                $sheet->fromArray($data_arr, null, 'A2', true);

                                //アクティブシートの設定
                                //シート名を全て取得
                                $ok_flag = 0;
                                //シートが存在するか確認
                                if($spreadsheet->sheetNameExists(Common::$sheetName_current))
                                {
                                    $spreadsheet->setActiveSheetIndexByName(Common::$sheetName_current);
                                }
                                else
                                {
                                    // シート名の取得
                                    $names = $spreadsheet->getSheetNames();
                                    //名前が含まれているか確認する
                                    for($i = 0;$i < count($names);$i++)
                                    {
                                        if(strpos($names[$i],Common::$sheetName_current) !== false)
                                        {
                                            //'abcd'のなかに'bc'が含まれている場合
                                            $spreadsheet->setActiveSheetIndexByName($names[$i]);
                                            $ok_flag = 1;
                                            break;
                                        }
                                    }
                                    //含まれない
                                    if($ok_flag == 0) $spreadsheet->setActiveSheetIndexByName(Common::$sheetName_data);
                                }

                                //tmpファイルに一旦書き込む(上書きすると画像エラーが出るため)
                                $writer = new XlsxWriter($spreadsheet);
                                $writer->save($file_path.Common::$CurrentFile_tmp);



                                //ダウンロード用
                                header("Content-Description: File Transfer");
                                header('Content-Disposition: attachment; filename="'.$file_path.Common::$CurrentFile_tmp.'" ');
                                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                                header('Content-Transfer-Encoding: binary');
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                header('Expires: 0');
                                if (ob_get_contents()) ob_end_clean();//バッファ消去
                                $writer->save('php://output');
                                exit; // ※※これがないと余計なものも出力してファイルが開けない
                                
                            }
                            //ファイルが存在する場合
                            else
                            {
                                //ファイルにデータを追加する
                                $file_path = $path.Common::$pub_str_path.Common::$Excel_path.$dir."/";
                                
                                //既存ファイルの読み込み
                                $reader = new XlsxReader();
                                $spreadsheet = $reader->load($file_path.Common::$CurrentFile);

                                //シートが存在するか確認
                                //存在しない場合は作成
                                if($spreadsheet->sheetNameExists(Common::$sheetName_data) === false)
                                {
                                    $newsheet = new Worksheet($spreadsheet,Common::$sheetName_data); //ここで任意のシートを新規作成
                                    $spreadsheet -> addSheet($newsheet, Common::$sheetIndex); //任意のシート追加。1は挿入する位置
                                }
                                
                                //選択されているシートを取得
                                // $sheet = $spreadsheet->getActiveSheet(1);
                                //2番目のシートを取得。Indexは0スタート
                                $sheet = $spreadsheet->getSheetByName(Common::$sheetName_data);
                                // $num = 1;                                
                                // foreach ($getdata->toArray() as $key => $value)
                                // {
                                //     foreach ($value as $key => $val)
                                //     {
                                //         $sheet->setCellValue("A".$num, $key);
                                //         $sheet->setCellValue("B".$num, $val);
                                //         $num++;
                                //     }
                                // }
                                $data_arr = array();
                                $arr = $getdata->toArray();
                                $cnt=0;
                                foreach ($getdata->toArray() as $key => $value)
                                {
                                    foreach ($value as $key => $val)
                                    {
                                        if($key != "currentfile" && $key != "introfile" && $key != "delflag" && !strpos($key,'pic')
                                            && !preg_match('/pic/',$key)
                                            && $key != "insdatetime" && $key != "insuserno" && $key != "upddatetime" && $key != "upduserno" && $key != "questurl")
                                        {
                                                if($key == "pass")
                                                {                                                  
                                                    foreach($codedata as $codedata)
                                                    {
                                                        if($codedata['codeno'] == 2)
                                                        {
                                                            if($codedata['value'] == $val)
                                                            {
                                                                $data_arr[$cnt] = $codedata['selectname'];
                                                            }
                                                        }
                                                    }
                                                }
                                                else if($val == "")
                                                {
                                                    $data_arr[$cnt] = 0;
                                                } 
                                                else
                                                {
                                                    $data_arr[$cnt] = $val;
                                                } 
                                                $cnt++;
                                        }
                                    }
                                }
                                //item67までの書き込み
                                $sheet->fromArray($data_arr, null, 'A2', true);


                                //アクティブシートの設定
                                //シート名を全て取得
                                $ok_flag = 0;
                                //シートが存在するか確認
                                if($spreadsheet->sheetNameExists(Common::$sheetName_current))
                                {
                                    $spreadsheet->setActiveSheetIndexByName(Common::$sheetName_current);
                                }
                                else
                                {
                                    // シート名の取得
                                    $names = $spreadsheet->getSheetNames();
                                    //名前が含まれているか確認する
                                    for($i = 0;$i < count($names);$i++)
                                    {
                                        if(strpos($names[$i],Common::$sheetName_current) !== false)
                                        {
                                            //'abcd'のなかに'bc'が含まれている場合
                                            $spreadsheet->setActiveSheetIndexByName($names[$i]);
                                            $ok_flag = 1;
                                            break;
                                        }
                                    }
                                    //含まれない
                                    if($ok_flag == 0) $spreadsheet->setActiveSheetIndexByName(Common::$sheetName_data);
                                }


                                //tmpファイルに一旦書き込む(上書きすると画像エラーが出るため)
                                $writer = new XlsxWriter($spreadsheet);
                                $writer->save($file_path.Common::$CurrentFile_tmp);

                                //ダウンロード用
                                header("Content-Description: File Transfer");
                                header('Content-Disposition: attachment; filename="'.$file_path.Common::$CurrentFile_tmp.'" ');
                                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                                header('Content-Transfer-Encoding: binary');
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                header('Expires: 0');
                                if (ob_get_contents()) ob_end_clean();//バッファ消去
                                $writer->save('php://output');
                                exit; // ※※これがないと余計なものも出力してファイルが開けない
                                // return 0;
                            }


                            //Excelファイルを処理条件に記述したディレクトリからダウンロード
                        // }
                    }
                    else if($_POST['fname'] == "introfile")
                    {
                        // if(trim($data[0][$_POST['fname']]) == Common::$IntroFile)
                        // {

                            ///FileWherehouse/Excel/XXXXXX/IntroFile.xlslファイルが存在するか調べる
                            //ファイルが存在しない場合
                            if (!file_exists($path.Common::$pub_str_path.Common::$Excel_path.$dir."/".Common::$IntroFile))
                            {
                                //存在しない場合は、BaseIntroFile.xlslをコピーして、シート貼りつけを行う
                                //まずディレクトリが存在するか調べる
                                if(!file_exists($path.Common::$pub_str_path.Common::$Excel_path.$dir))
                                {
                                    //存在しない場合はディレクトリを作成する
                                    if(!mkdir($path.Common::$pub_str_path.Common::$Excel_path.$dir, 0777, true))
                                    {
                                        return -6;
                                    }
                                }
                                //BaseIntroFileがあるか確認する
                                if(!file_exists($path.Common::$pub_str_path.Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseIntroFile))
                                {
                                    return -1;
                                }
                                //BaseIntroFile.xlslをコピーして、シート貼りつけを行う
                                if (!copy($path.Common::$pub_str_path.Common::$Excel_path.Common::$BasicExcel_path.Common::$BaseIntroFile,
                                base_path().Common::$pub_str_path.Common::$Excel_path.$dir."/".Common::$IntroFile))
                                {
                                    return -6;
                                }

                                $file_path = $path.Common::$pub_str_path.Common::$Excel_path.$dir."/";
                                //既存ファイルの読み込み
                                $reader = new XlsxReader();
                                $spreadsheet = $reader->load($file_path.Common::$IntroFile);

                                // シート数の取得
                                // $count = $spreadsheet->getSheetCount();

                                //シートが存在するか確認
                                //存在しない場合は作成
                                if($spreadsheet->sheetNameExists(Common::$sheetName_data) === false)
                                {
                                    $newsheet = new Worksheet($spreadsheet,Common::$sheetName_data); //ここで任意のシートを新規作成
                                    $spreadsheet -> addSheet($newsheet, Common::$sheetIndex); //任意のシート追加。1は挿入する位置
                                }
                                
                                //選択されているシートを取得
                                // $sheet = $spreadsheet->getActiveSheet(1);
                                //2番目のシートを取得。Indexは0スタート
                                $sheet = $spreadsheet->getSheetByName(Common::$sheetName_data);
                                // $num = 1;
                                
                                // foreach ($getdata->toArray() as $key => $value)
                                // {
                                //     foreach ($value as $key => $val)
                                //     {
                                //         $sheet->setCellValue("A".$num, $key);
                                //         $sheet->setCellValue("B".$num, $val);
                                //         $num++;
                                //     }
                                // }
                                $data_arr = array();
                                $arr = $getdata->toArray();
                                $cnt=0;
                                foreach ($getdata->toArray() as $key => $value)
                                {
                                    foreach ($value as $key => $val)
                                    {
                                        if($key != "currentfile" && $key != "introfile" && $key != "delflag" && !strpos($key,'pic')
                                        && !preg_match('/pic/',$key)
                                            && $key != "insdatetime" && $key != "insuserno" && $key != "upddatetime" && $key != "upduserno" && $key != "questurl")
                                        {
                                                if($key == "pass")
                                                {                                                  
                                                    foreach($codedata as $codedata)
                                                    {
                                                        if($codedata['codeno'] == 2)
                                                        {
                                                            if($codedata['value'] == $val)
                                                            {
                                                                $data_arr[$cnt] = $codedata['selectname'];
                                                            }
                                                        }
                                                    }
                                                }
                                                else if($val == "") $data_arr[$cnt] = 0;
                                                else $data_arr[$cnt] = $val;
                                                $cnt++;
                                        }
                                    }
                                }
                                //item67までの書き込み
                                $sheet->fromArray($data_arr, null, 'A2', true);


                                //アクティブシートの設定
                                //シート名を全て取得
                                $ok_flag = 0;
                                //シートが存在するか確認
                                if($spreadsheet->sheetNameExists(Common::$sheetName_intro))
                                {
                                    $spreadsheet->setActiveSheetIndexByName(Common::$sheetName_intro);
                                }
                                else
                                {
                                    // シート名の取得
                                    $names = $spreadsheet->getSheetNames();
                                    //名前が含まれているか確認する
                                    for($i = 0;$i < count($names);$i++)
                                    {
                                        if(strpos($names[$i],Common::$sheetName_intro) !== false)
                                        {
                                            //'abcd'のなかに'bc'が含まれている場合
                                            $spreadsheet->setActiveSheetIndexByName($names[$i]);
                                            $ok_flag = 1;
                                            break;
                                        }
                                    }
                                    //含まれない
                                    if($ok_flag == 0) $spreadsheet->setActiveSheetIndexByName(Common::$sheetName_data);
                                }


                                $writer = new XlsxWriter($spreadsheet);
                                $writer->save($file_path.Common::$IntroFile_tmp);
                                //ダウンロード用
                                header("Content-Description: File Transfer");
                                header('Content-Disposition: attachment; filename="'.$file_path.Common::$IntroFile_tmp.'" ');
                                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                                header('Content-Transfer-Encoding: binary');
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                header('Expires: 0');
                                if (ob_get_contents()) ob_end_clean();//バッファ消去
                                $writer->save('php://output');
                                exit; // ※※これがないと余計なものも出力してファイルが開けない
                                
                            }
                            //ファイルが存在する場合
                            else
                            {

                                //ファイルにデータを追加する
                                $file_path = $path.Common::$pub_str_path.Common::$Excel_path.$dir."/";
                                
                                //既存ファイルの読み込み
                                $reader = new XlsxReader();
                                $spreadsheet = $reader->load($file_path.Common::$IntroFile);

                                // シート数の取得
                                // $count = $spreadsheet->getSheetCount();

                                //シートが存在するか確認
                                //存在しない場合は作成
                                if($spreadsheet->sheetNameExists(Common::$sheetName_data) === false)
                                {
                                    $newsheet = new Worksheet($spreadsheet,Common::$sheetName_data); //ここで任意のシートを新規作成
                                    $spreadsheet -> addSheet($newsheet, Common::$sheetIndex); //任意のシート追加。1は挿入する位置
                                }
                                
                                //選択されているシートを取得
                                // $sheet = $spreadsheet->getActiveSheet(1);
                                //2番目のシートを取得。Indexは0スタート
                                $sheet = $spreadsheet->getSheetByName(Common::$sheetName_data);
                                // $num = 1;
                                
                                // foreach ($getdata->toArray() as $key => $value)
                                // {
                                //     foreach ($value as $key => $val)
                                //     {
                                //         $sheet->setCellValue("A".$num, $key);
                                //         $sheet->setCellValue("B".$num, $val);
                                //         $num++;
                                //     }
                                // }



                                


                                $data_arr = array();
                                $arr = $getdata->toArray();
                                $cnt=0;
                                foreach ($getdata->toArray() as $key => $value)
                                {
                                    foreach ($value as $key => $val)
                                    {
                                        if($key != "currentfile" && $key != "introfile" && $key != "delflag" && !strpos($key,'pic')
                                        && !preg_match('/pic/',$key)
                                            && $key != "insdatetime" && $key != "insuserno" && $key != "upddatetime" && $key != "upduserno" && $key != "questurl")
                                        {
                                                if($key == "pass")
                                                {                                                  
                                                    foreach($codedata as $codedata)
                                                    {
                                                        if($codedata['codeno'] == 2)
                                                        {
                                                            if($codedata['value'] == $val)
                                                            {
                                                                $data_arr[$cnt] = $codedata['selectname'];
                                                            }
                                                        }
                                                    }
                                                }
                                                else if($val == "") $data_arr[$cnt] = 0;
                                                else $data_arr[$cnt] = $val;
                                                $cnt++;
                                        }
                                    }
                                }
                                //item67までの書き込み
                                $sheet->fromArray($data_arr, null, 'A2', true);

                                //アクティブシートの設定
                                //シート名を全て取得
                                $ok_flag = 0;
                                //シートが存在するか確認
                                if($spreadsheet->sheetNameExists(Common::$sheetName_intro))
                                {
                                    $spreadsheet->setActiveSheetIndexByName(Common::$sheetName_intro);
                                }
                                else
                                {
                                    // シート名の取得
                                    $names = $spreadsheet->getSheetNames();
                                    //名前が含まれているか確認する
                                    for($i = 0;$i < count($names);$i++)
                                    {
                                        if(strpos($names[$i],Common::$sheetName_intro) !== false)
                                        {
                                            //'abcd'のなかに'bc'が含まれている場合
                                            $spreadsheet->setActiveSheetIndexByName($names[$i]);
                                            $ok_flag = 1;
                                            break;
                                        }
                                    }
                                    //含まれない
                                    if($ok_flag == 0) $spreadsheet->setActiveSheetIndexByName(Common::$sheetName_data);
                                }

                                
                                $writer = new XlsxWriter($spreadsheet);
                                $writer->save($file_path.Common::$IntroFile_tmp);

                                //ダウンロード用
                                header("Content-Description: File Transfer");
                                header('Content-Disposition: attachment; filename="'.$file_path.Common::$IntroFile.'" ');
                                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                                header('Content-Transfer-Encoding: binary');
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                header('Expires: 0');
                                if (ob_get_contents()) ob_end_clean();//バッファ消去
                                $writer->save('php://output');
                                exit; // ※※これがないと余計なものも出力してファイルが開けない
                            }


                            //Excelファイルを処理条件に記述したディレクトリからダウンロード
                        // }
                    }
                    
                // }
                else return -1;

            }
            
        }
        else return -4;
    }


    
    public function DownloadFin(Request $request)
    {
        $dir = $_POST['id'];
        $path = base_path();
        //ファイルにデータを追加する
        $file_path = $path.Common::$pub_str_path.Common::$Excel_path.$dir."/";
        //書き込み完了したら前のファイルを削除後、リネームする
        if($_POST['fname'] == "currentfile")
        {
            if (file_exists($file_path.Common::$CurrentFile_tmp))
            {
                if(rename($file_path.Common::$CurrentFile_tmp,$file_path.Common::$CurrentFile) == false) return -7;
            }            
        }
        else if($_POST['fname'] == "introfile")
        {
            if (file_exists($file_path.Common::$IntroFile_tmp))
            {
                if(rename($file_path.Common::$IntroFile_tmp,$file_path.Common::$IntroFile) == false) return -7;
            }
        }
        return 0;
    }
}


