<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeStudyController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\HelperdataController;

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

   

    //施設一覧
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
        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index')->withoutMiddleware('authroot');


    //作業名一覧
    Route::match(['get','post'], '/task', [TaskController::class, 'index'])->name('tasks.index');
        //削除
        Route::post('/task_delete',   [TaskController::class, 'del'])->name('tasks.delete');
        //追加
        Route::get('/task_add',       [TaskController::class, 'add_index'])->name('tasks.create');
        Route::post('/task_addctrl', [\App\Http\Controllers\TaskController::class, 'Taskadd']);
        //修正
        Route::get('/task_fix',       [TaskController::class, 'fix_index'])->name('tasks.edit');
        Route::post('/task_fix',      [TaskController::class, 'fix_index']); // 必要なら残す
        Route::post('/task_fixctrl',  [TaskController::class, 'TaskFix'])->name('tasks.update');
        Route::post('/cxl_taskfix',   [TaskController::class, 'cxl_TaskFix'])->name('tasks.edit.cancel');
    

     //グループ一覧
     Route::get('/groups',        [GroupController::class, 'index'])->name('groups.index');
        //追加
        Route::get('/group_add',     [GroupController::class, 'add_index'])->name('groups.add');
        Route::post('/group_addctrl',[GroupController::class, 'store'])->name('groups.store');
        //修正
        Route::get('/group_fix',     [GroupController::class, 'fix_index'])->name('groups.edit');
        Route::post('/group_fixctrl',[GroupController::class, 'update'])->name('groups.update');
        // グループ削除
        Route::post('/group_del', [App\Http\Controllers\GroupController::class, 'del'])
        ->name('groups.delete');


    //作業者一覧
    Route::match(['get','post'], '/helper', [HelperController::class, 'index'])->name('helper.index');
        //削除
        Route::post('/helper_del','App\Http\Controllers\HelperController@del');
        //追加
        Route::match(['get','post'], '/helper_add', [HelperController::class, 'add_index'])->name('helper.add.index');
        Route::post('/helper_addctrl', [HelperController::class, 'HelperAdd'])->name('helper.add.store');
        //修正
        Route::get('/helper_fix', 'App\Http\Controllers\HelperController@fix_index');
        Route::post('/helper_fix', 'App\Http\Controllers\HelperController@fix_index');
        //Time Study CSV取込
        Route::post('/task_csv_import', [HelperController::class, 'csvImport'])->name('tasks.csv_import');
        // TimeStudy CSV 出力
        Route::post('/time_study_csvoutput', [App\Http\Controllers\HelperController::class, 'TimeStudyCsvOutput'])->name('time_study.csvoutput');
        // TimeStudy CSV 出力（期間指定）
        Route::post('/time_study_csvoutput', [App\Http\Controllers\HelperController::class, 'timeStudyCsvOutput']);
       

    //作業者データ表示 ※※作業者一覧と順番入れ替えないこと※※
    Route::get('/helperdata', 'App\Http\Controllers\HelperdataController@index');
    Route::post('/helperdata', 'App\Http\Controllers\HelperdataController@index');
    //データ表示
    Route::post('/helperdata_dispctrl', 'App\Http\Controllers\HelperdataController@Helperdata_disp');
    Route::post('/helperdata_dispctrl2', 'App\Http\Controllers\HelperdataController@Helperdata_disp2');
    Route::post('/Wearabledata_disp', 'App\Http\Controllers\HelperdataController@Wearabledata_disp');
    Route::post('/Wearabledata_disp2', 'App\Http\Controllers\HelperdataController@Wearabledata_disp2');
    // グラフデータ取得
    Route::post('/get_graph_data', 'App\Http\Controllers\HelperdataController@getGraphData');
    // 施設/全国平均データ表示
    Route::post('/averagedata', 'App\Http\Controllers\HelperdataController@Averagedata_disp');
    Route::post('/comparison', 'App\Http\Controllers\HelperdataController@comparison');

    //フォームリセット
    Route::post('/cxl_helperfix', 'App\Http\Controllers\HelperController@cxl_HelperFix');
    //修正処理
    Route::post('/helper_fixctrl', 'App\Http\Controllers\HelperController@HelperFix');
    //作業者一覧CSV出力
    Route::post('/helper_list_csvoutput', 'App\Http\Controllers\HelperController@HelperListCsvOutput');
    //作業者データCSV出力
    Route::post('/helper_data_csvoutput', 'App\Http\Controllers\HelperController@HelperDataCsvOutput');


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


    Route::post('/time_study/summary', [HelperdataController::class, 'summary'])
    ->name('time_study.summary');

  
});


//ログイン関連
//vender/laravel/ui/src/AuthRouteMethods.php
//vender/Providers/RouteServiceProvider.php
Auth::routes();
//エラーでるため前Ver.と違うところ、注意('App\Http\Controllers\HomeController@index')
Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
Route::post('/home', 'App\Http\Controllers\HomeController@index')->name('home');

