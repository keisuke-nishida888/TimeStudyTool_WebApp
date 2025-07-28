<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BackPain;
use App\Models\bpainhed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class ApiController extends Controller
{
    /**
    *　 メール送信機能
    * メール送信設定を返すAPI
     * POST /api/get-mail-setting
     */
    public function getMailSetting(Request $request)
    {
  
        //メール送信情報を返す（ここでは仮フィールド名。実DB構成に合わせて修正）
        return response()->json([
            'from_mail' => 'fa.nishida.keisuke@gmail.com',         // 差出人アドレス
            'smtp_password' => 'rtemponbsxiwzvtg', // SMTPパスワード
            // 必要に応じて宛先やサーバ名等も
        ]);
    }

     /**
    　　*　　作業名同期 
     * 作業名（task_table）一覧を返すAPI
     * GET /api/get-task-table
     */
    public function getTaskTable()
    {
        // task_table から全件取得
        $tasks = DB::table('task_table')
            ->select('task_id', 'task_name', 'task_type_no', 'task_category_no')
            ->orderBy('task_id', 'asc')
            ->get();

        // JSONで返却
        return response()->json($tasks);
    }

     /**
    　　*　　WEB登録
     * TimeStudyデータをAPIで通信する。
     * POST /api/time_study_import
     */
    public function timeStudyImport(Request $request)
{
    $records = $request->input('records', []);
    foreach ($records as $rec) {
        DB::table('time_study')->updateOrInsert(
            ['timestudy_id' => $rec['timestudy_id']], // 主キーで判定
            [
                'task_id' => $rec['task_id'],
                'start' => $rec['start'],
                'stop' => $rec['stop'],
                'helpno' => $rec['helpno'],
                'websent' => 1,                // 受信済みフラグ
                'updated_at' => now(),
            ]
        );
    }
    return response()->json(['result' => 'ok']);
}


    public function store(Request $requestJ)
    {
        $request = $requestJ[0];

        if (!is_array($request)) {
            \Log::error('Invalid JSON data format');
            return response()->json([]);
        }

        if (empty($request['headerData'])) {
            \Log::error('headerDataが存在しない');
            return response()->json([]);
        }

        if (empty($request['recordDatas'])) {
            \Log::error('recordDatasが存在しない');
            return response()->json([]);
        }

        \DB::beginTransaction();
        try {

            $headerData = $request['headerData'];
            $deviceName = $request['deviceName'];

            // 腰痛デバイス情報
            $backPainId = BackPain::where('devicename', $deviceName)
                        ->where('helperno', $headerData['helperno'])
                        ->where('delflag', '<>', 1)
                        ->value('id');

            // データがない場合、作成する
            if(blank($backPainId)) {
                $backPainId = BackPain::insertGetId([
                    'devicename' => $deviceName,
                    'helperno' => $headerData['helperno'],
                    'delflag' => "0"
                ]);
                // 作成したため、bpainhedのbpainhednoを上書き
                $headerData['backpainno'] = $backPainId;
            }

            // 腰痛のヘッダーデータ登録、ID取得
            $headerId = bpainhed::insertGetId($headerData);
            $recordDatas = $request['recordDatas'];

            // 各Noを上書き
            $records = [];
            for ($i = 0; $i < count($recordDatas); $i++) {
                $data = $recordDatas[$i];
                $data['backpainno'] = $backPainId;
                $data['helperno'] = $headerData['helperno'];
                $data['bpainhedno'] = $headerId;
                $records[] = $data;
            }

            // 月を取得
            $month = substr($headerData['ymd'], 4, 2);
            //変数に入れたモデル名をインスタンス化
            $model = "App\Models\bpain" . $month;
            $bpain = new $model;

            // 腰痛のレコードデータ挿入
            $isSuccess = $bpain::insert($records);

            if (!$isSuccess) {
                // 挿入に失敗
                \DB::rollBack();
                return response()->json([]);
            }

            \DB::Commit();
            return response()->json([]);
        } catch(\Exception $e) {
            \DB::rollBack();
            \Log::error($e->getMessage());

            return response()->json([]);
        }
    }
}
