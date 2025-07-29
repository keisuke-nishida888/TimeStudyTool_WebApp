<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeStudyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

 //time study tool　連携
            Route::post('/time_study_csv_upload', [TimeStudyController::class, 'upload']);

//ログインしていないユーザはログイン画面へ
//下記のように書いておくとauthの後にauthrootが行われる
Route::group(['middleware' => ['auth','authroot']], function(){
    Route::get('/', 'App\Http\Controllers\MainmenuController@index');
    //メインメニュー画面
    Route::get('/mainmenu', 'App\Http\Controllers\MainmenuController@index');

    Route::post('/update-flag', 'App\Http\Controllers\MainmenuController@updatePolicyFlag')->name('update.flag');
    //ログインユーザ一覧 -> ログインユーザ一覧
    Route::get('/loginuser', 'App\Http\Controllers\LoginuserController@index');
        //削除
        Route::post('/loginuser_del','App\Http\Controllers\LoginuserController@del');
        //ログインユーザ追加
        Route::get('/loginuser_add', 'App\Http\Controllers\LoginuserController@add_index');
            //追加処理
            Route::post('/loginuser_addctrl','App\Http\Controllers\LoginuserController@UserAdd');
            Route::get('/loginuser_addctrl','App\Http\Controllers\LoginuserController@fix_index');
        //ログインユーザ修正
        Route::get('/loginuser_fix', 'App\Http\Controllers\LoginuserController@fix_index');
        Route::post('/loginuser_fix', 'App\Http\Controllers\LoginuserController@fix_index');
            //フォームリセット
            Route::post('/cxl_userfix', 'App\Http\Controllers\LoginuserController@cxl_UserFix');
            //修正処理
            Route::post('/loginuser_fixctrl', 'App\Http\Controllers\LoginuserController@UserFix');
            Route::get('/loginuser_fixctrl', 'App\Http\Controllers\LoginuserController@fix_index');

    //ウェアラブルデバイス登録 ->ウェアラブルデバイス一覧
    Route::get('/wearable', 'App\Http\Controllers\WearableController@index');
        //削除
        Route::post('/wearable_del','App\Http\Controllers\WearableController@del');
        //ウェアラブルデバイス追加
        Route::get('/wearable_add', 'App\Http\Controllers\WearableController@add_index');
            //追加処理
            Route::post('/wearable_addctrl','App\Http\Controllers\WearableController@WearableAdd');
            Route::get('/wearable_addctrl','App\Http\Controllers\WearableController@fix_index');
        //ウェアラブルデバイス修正
        Route::get('/wearable_fix', 'App\Http\Controllers\WearableController@fix_index');
        Route::post('/wearable_fix', 'App\Http\Controllers\WearableController@fix_index');
            //フォームリセット
            Route::post('/cxl_wearablefix', 'App\Http\Controllers\WearableController@cxl_WearableFix');
            //修正処理
            Route::post('/wearable_fixctrl', 'App\Http\Controllers\WearableController@WearableFix');
            Route::get('/wearable_fixctrl', 'App\Http\Controllers\WearableController@fix_index');

    //リスクデバイス登録->リスクデバイス一覧
    Route::get('/risksensor', 'App\Http\Controllers\RisksensorController@index');
        //削除
        Route::post('/risksensor_del','App\Http\Controllers\RisksensorController@del');
        //リスクデバイス追加
        Route::get('/risksensor_add', 'App\Http\Controllers\RisksensorController@add_index');
        //リスクデバイス修正
        Route::get('/risksensor_fix', 'App\Http\Controllers\RisksensorController@fix_index');
        Route::post('/risksensor_fix', 'App\Http\Controllers\RisksensorController@fix_index');
            //フォームリセット
            Route::post('/cxl_risksensorfix', 'App\Http\Controllers\RisksensorController@cxl_RisksensorFix');
            //追加処理
            Route::post('/risksensor_addctrl','App\Http\Controllers\RisksensorController@RisksensorAdd');
            Route::get('/risksensor_addctrl','App\Http\Controllers\RisksensorController@fix_index');
            //修正処理
            Route::post('/risksensor_fixctrl', 'App\Http\Controllers\RisksensorController@RisksensorFix');
            Route::get('/risksensor_fixctrl', 'App\Http\Controllers\RisksensorController@fix_index');


    //施設一覧->施設一覧-
    Route::get('/facility', 'App\Http\Controllers\FacilityController@index');
        //削除
        Route::post('/facility_del','App\Http\Controllers\FacilityController@del');
        //施設情報追加
        Route::get('/facility_add', 'App\Http\Controllers\FacilityController@add_index');
            //追加処理
            Route::post('/facility_addctrl','App\Http\Controllers\FacilityController@FacilityAdd');
            Route::get('/facility_addctrl','App\Http\Controllers\FacilityController@fix_index');
        //施設情報修正
        Route::get('/facility_fix', 'App\Http\Controllers\FacilityController@fix_index');
        Route::post('/facility_fix', 'App\Http\Controllers\FacilityController@fix_index');
            //フォームリセット
            Route::post('/cxl_facilityfix', 'App\Http\Controllers\FacilityController@cxl_FacilityFix');
            //修正処理
            Route::post('/facility_fixctrl', 'App\Http\Controllers\FacilityController@FacilityFix');
            Route::get('/facility_fixctrl','App\Http\Controllers\FacilityController@fix_index');

        //コストデータ管理
        Route::get('/cost_ctrl', 'App\Http\Controllers\CostController@index');
        Route::post('/cost_ctrl', 'App\Http\Controllers\CostController@index');
            //ダウンロード
            Route::post('/costctrl_download', 'App\Http\Controllers\CostController@Download');
            //
            Route::post('/costctrl_downloadfin', 'App\Http\Controllers\CostController@DownloadFin');
            //アップロード
            Route::post('/costctrl_upload', 'App\Http\Controllers\CostController@Upload');

        //作業内容一覧
        Route::get('/task', 'App\Http\Controllers\TaskController@index');
        Route::post('/task', 'App\Http\Controllers\TaskController@index');
            //削除
            Route::post('/task_delete','App\Http\Controllers\TaskController@del');
            //作業内容追加
            Route::get('/task_add', 'App\Http\Controllers\TaskController@add_index');
            Route::post('/task_add', 'App\Http\Controllers\TaskController@add_index');
                //追加処理
                Route::post('/task_addctrl','App\Http\Controllers\TaskController@TaskAdd');
            //作業内容修正
            Route::get('/task_fix', 'App\Http\Controllers\TaskController@fix_index');
            Route::post('/task_fix', 'App\Http\Controllers\TaskController@fix_index');
                //フォームリセット
                Route::post('/cxl_taskfix', 'App\Http\Controllers\TaskController@cxl_TaskFix');
                //修正処理
                Route::post('/task_fixctrl', 'App\Http\Controllers\TaskController@TaskFix');
            //CSV取り込み
            Route::post('/task_csv_import', 'App\Http\Controllers\HelperController@csvImport');

        //介助者データ表示 ※※介助者一覧と順番入れ替えないこと※※
        Route::get('/helperdata', 'App\Http\Controllers\HelperdataController@index');
        Route::post('/helperdata', 'App\Http\Controllers\HelperdataController@index');
            //データ表示
            Route::post('/helperdata_dispctrl', 'App\Http\Controllers\HelperdataController@Helperdata_disp');
            Route::post('/helperdata_dispctrl2', 'App\Http\Controllers\HelperdataController@Helperdata_disp2');
            Route::post('/Wearabledata_disp', 'App\Http\Controllers\HelperdataController@Wearabledata_disp');
            Route::post('/Wearabledata_disp2', 'App\Http\Controllers\HelperdataController@Wearabledata_disp2');
            //csv出力
            Route::post('/csvoutput', 'App\Http\Controllers\HelperdataController@Csvoutput');
            // グラフデータ取得
            Route::post('/get_graph_data', 'App\Http\Controllers\HelperdataController@getGraphData');
            // 施設/全国平均データ表示
            Route::post('/averagedata', 'App\Http\Controllers\HelperdataController@Averagedata_disp');
        Route::post('/comparison', 'App\Http\Controllers\HelperdataController@comparison');

        //介助者一覧
        // Route::get('/helper', 'App\Http\Controllers\HelperController@index');
        Route::post('/helper', 'App\Http\Controllers\HelperController@index');
            //削除
            Route::post('/helper_del','App\Http\Controllers\HelperController@del');
            //介助者追加
            Route::get('/helper_add', 'App\Http\Controllers\HelperController@add_index');
            Route::post('/helper_add', 'App\Http\Controllers\HelperController@add_index');
                //追加処理
                Route::post('/helper_addctrl','App\Http\Controllers\HelperController@HelperAdd');
                Route::get('/helper_addctrl','App\Http\Controllers\HelperController@fix_index');
            //介助者修正
            Route::get('/helper_fix', 'App\Http\Controllers\HelperController@fix_index');
            Route::post('/helper_fix', 'App\Http\Controllers\HelperController@fix_index');
                //フォームリセット
                Route::post('/cxl_helperfix', 'App\Http\Controllers\HelperController@cxl_HelperFix');
                //修正処理
                Route::post('/helper_fixctrl', 'App\Http\Controllers\HelperController@HelperFix');
            // 介助者一覧CSV出力
            Route::post('/helper_list_csvoutput', 'App\Http\Controllers\HelperController@HelperListCsvOutput');
            // 介助者データCSV出力
            Route::post('/helper_data_csvoutput', 'App\Http\Controllers\HelperController@HelperDataCsvOutput');



        // ※一番最後に持ってくる※※
        Route::name('helper')->get('/helper{id?}', 'App\Http\Controllers\HelperController@index');



    //平均データ表示
    Route::get('/averdata', 'App\Http\Controllers\AverdataController@index');

    //施設情報入力
    Route::get('/facilityinput', 'App\Http\Controllers\FacilityinputController@index');
    Route::post('/facilityinput', 'App\Http\Controllers\FacilityinputController@index');

        //追加処理
        Route::post('/facility_input_addctrl','App\Http\Controllers\FacilityinputController@FacilityAdd');
        Route::get('/facility_input_addctrl','App\Http\Controllers\FacilityinputController@index');
        //フォームリセット
        Route::post('/cxl_facility_inputfix', 'App\Http\Controllers\FacilityinputController@cxl_FacilityFix');
        //修正処理
        Route::post('/facility_input_fixctrl', 'App\Http\Controllers\FacilityinputController@FacilityFix');
        Route::get('/facility_input_fixctrl', 'App\Http\Controllers\FacilityinputController@index');

    //現状コスト/導入コスト登録
    Route::get('/costregist', 'App\Http\Controllers\CostregistController@index');
        //登録
        Route::post('/costregist_regist', 'App\Http\Controllers\CostregistController@regist');


    //戻るボタン、後で削除するかも
    // Route::post('/loginuser','App\Http\Controllers\BackController@index');
    // Route::post('/mainmenu','App\Http\Controllers\BackController@index');
    // Route::post('/wearable','App\Http\Controllers\BackController@index');
    // Route::post('/risksensor','App\Http\Controllers\BackController@index');
    // Route::post('/facility','App\Http\Controllers\BackController@index');
    // Route::post('/averdata','App\Http\Controllers\BackController@index');
    // Route::post('/facilityinput','App\Http\Controllers\BackController@index');
    // Route::post('/costregist','App\Http\Controllers\BackController@index');
    // Route::post('/helper','App\Http\Controllers\BackController@index');



});


//ログイン関連
//vender/laravel/ui/src/AuthRouteMethods.php
//vender/Providers/RouteServiceProvider.php
Auth::routes();
//エラーでるため前Ver.と違うところ、注意('App\Http\Controllers\HomeController@index')
Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
Route::post('/home', 'App\Http\Controllers\HomeController@index')->name('home');

