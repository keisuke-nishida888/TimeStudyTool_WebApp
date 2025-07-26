<?php
use \App\Library\Common;
//　メインメニュー
Breadcrumbs::for('mainmenu', function ($trail) {
    $trail->push('メニュー', url('mainmenu'));
});


// 　メインメニュー > ログインユーザ一覧
Breadcrumbs::for('loginuser', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('ログインユーザ一覧', url('loginuser'));
});

    // 　メインメニュー > ログインユーザ一覧 >  ログインユーザ追加
    Breadcrumbs::for('loginuser_add', function ($trail) {
        $trail->parent('loginuser');
        $trail->push('ログインユーザ追加',url('loginuser_add'));
    });

    // 　メインメニュー > ログインユーザ一覧 >  ログインユーザ修正
    Breadcrumbs::for('loginuser_fix', function ($trail) {
        $trail->parent('loginuser');
        $trail->push('ログインユーザ修正',url('loginuser_fix'));
    });

// ----------------------------------------------------------------------------------->

// 　メインメニュー > ウェアラブルデバイス一覧
Breadcrumbs::for('wearable', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('心拍センサー一覧', url('wearable'));
});

    // 　メインメニュー > ウェアラブルデバイス一覧 >  ウェアラブルデバイス追加
    Breadcrumbs::for('wearable_add', function ($trail) {
        $trail->parent('wearable');
        $trail->push('心拍センサー追加',url('wearable_add'));
    });

    // 　メインメニュー > ウェアラブルデバイス一覧 >  ウェアラブルデバイス修正
    Breadcrumbs::for('wearable_fix', function ($trail) {
        $trail->parent('wearable');
        $trail->push('心拍センサー修正',url('wearable_fix'));
    });

// ----------------------------------------------------------------------------------->

// 　メインメニュー > リスクデバイス一覧
Breadcrumbs::for('risksensor', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('リスクデバイス一覧', url('risksensor'));
});

// 　メインメニュー > リスクデバイス一覧 >  リスクデバイス追加
Breadcrumbs::for('risksensor_add', function ($trail) {
    $trail->parent('risksensor');
    $trail->push('リスクデバイス追加',url('risksensor_add'));
});

// 　メインメニュー > リスクデバイス一覧 >  リスクデバイス修正
Breadcrumbs::for('risksensor_fix', function ($trail) {
    $trail->parent('risksensor');
    $trail->push('リスクデバイス修正',url('risksensor_fix'));
});


// ----------------------------------------------------------------------------------->

    // 　メインメニュー > 施設一覧
    Breadcrumbs::for('facility', function ($trail) {
        $trail->parent('mainmenu');
        $trail->push('施設一覧', url('facility'));
    });

    // 　メインメニュー > 施設一覧 >  施設情報追加
    Breadcrumbs::for('facility_add', function ($trail) {
        $trail->parent('facility');
        $trail->push('施設情報追加',url('facility_add'));
    });


    // 　メインメニュー > 施設一覧 >  施設情報修正
    Breadcrumbs::for('facility_fix', function ($trail) {
        $trail->parent('facility');
        $trail->push('施設情報修正',url('facility_fix'));
    });

    // 　メインメニュー > 施設一覧 >  コストデータ管理
    Breadcrumbs::for('cost_ctrl', function ($trail) {
        $trail->parent('facility');
        $trail->push('コストデータ管理',url('cost_ctrl'));
    });

    // 　メインメニュー > 施設一覧 >  作業内容一覧
    Breadcrumbs::for('task', function ($trail) {
        $trail->parent('facility');
        $trail->push('作業内容一覧',url('task'));
    });


    // 　メインメニュー > 施設一覧 >  介助者一覧
    // Breadcrumbs::for('helper', function ($trail) {
    //     $trail->parent('facility');
    //     $trail->push('介助者一覧',url('helper'));
    // });

    Breadcrumbs::for('helper', function ($trail, $facilityno=null) // <-- Implicit binding(auto injection)  
    {       
        if(!isset($facilityno))
        {
            $param = $_SERVER['HTTP_REFERER'];
            $tmp = [];
            if(isset($param))
            {
                //parse_url でURLを分解してパラメータのみ取得する
                parse_str(parse_url($param, PHP_URL_QUERY), $query);
                if(isset($query))
                {
                    if(isset($query['facilityno']))
                    {
                        $val_url = $query['facilityno'];
                        $facilityno = "?facilityno=".$val_url;
                    } 
                    else $facilityno = "?facilityno="."0";
                }
                else $facilityno = "?facilityno="."0";
            }
            else $facilityno = "?facilityno="."0";
        } 
        else $facilityno = "?facilityno=".$facilityno; 
            
        $trail->parent('facility');
        $trail->push('介助者一覧', route('helper', ['id' => $facilityno]));
    });


// ------------------------------------------->
    // 　メインメニュー > 施設一覧 >  介助者一覧 >  介助者追加
    Breadcrumbs::for('helper_add', function ($trail, $facilityno=null) {
        $trail->parent('helper', $facilityno);
        $trail->push('介助者追加',url('helper_add'));
    });

    // 　メインメニュー > 施設一覧 >  介助者一覧 >  介助者修正
    Breadcrumbs::for('helper_fix', function ($trail, $facilityno=null) {
        $trail->parent('helper', $facilityno);
        $trail->push('介助者修正',url('helper_fix'));
    });

    // 　メインメニュー > 施設一覧 >  介助者一覧 >  介助者データ表示
    Breadcrumbs::for('helperdata', function ($trail, $facilityno=null) {
        $trail->parent('helper', $facilityno);
        $trail->push('介助者データ表示',url('helperdata'));
    });

    // 　メインメニュー > 施設一覧 >  介助者一覧 >  介助者データ表示 >  介助者データ表示
    Breadcrumbs::for('comparison', function ($trail, $facilityno=null) {

        $trail->parent('helperdata', $facilityno);
        $trail->push('介助者データ比較',url('comparison'));
    });
// ----------------------------------------------------------------------------------->

// 　メインメニュー > 平均データ表示
    Breadcrumbs::for('averdata', function ($trail) {
        $trail->parent('mainmenu');
        $trail->push('平均データ表示', url('averdata'));
    });

// ----------------------------------------------------------------------------------->

// 　メインメニュー > 施設情報入力
    Breadcrumbs::for('facilityinput', function ($trail) {
        $trail->parent('mainmenu');
        $trail->push('施設情報入力', url('facilityinput'));
    });

// ----------------------------------------------------------------------------------->

// 　メインメニュー > 現状コスト/導入コスト登録
    Breadcrumbs::for('costregist', function ($trail) {
        $trail->parent('mainmenu');
        $trail->push('現状コスト/導入コスト登録', url('costregist'));
    });

// ----------------------------------------------------------------------------------->

// 施設ユーザでログイン
// ----------------------------------------------------------------------------------->

// 　メインメニュー >  介助者一覧
    Breadcrumbs::for('helper_facil', function ($trail, $facilityno=null) {
        if(!isset($facilityno))
        {
            $param = $_SERVER['HTTP_REFERER'] ?? '';
            $tmp = [];
            if(isset($param))
            {
                //parse_url でURLを分解してパラメータのみ取得する
                parse_str(parse_url($param, PHP_URL_QUERY), $query);
                if(isset($query))
                {
                    if(isset($query['facilityno']))
                    {
                        $val_url = $query['facilityno'];
                        $facilityno = "?facilityno=".$val_url;
                    } 
                    else $facilityno = "?facilityno="."0";
                }
                else $facilityno = "?facilityno="."0";
            }
            else $facilityno = "?facilityno="."0";
           
        } 
        else $facilityno = "?facilityno=".$facilityno; 

        $trail->parent('mainmenu');
        $trail->push('介助者一覧', route('helper', ['id' => $facilityno]));
    });

    // 　メインメニュー > 施設一覧 >  介助者一覧 >  介助者追加
    Breadcrumbs::for('helper_add_facil', function ($trail, $facilityno=null) {
        $trail->parent('helper_facil', $facilityno);
        $trail->push('介助者追加',url('helper_add'));
    });

    // 　メインメニュー > 施設一覧 >  介助者一覧 >  介助者修正
    Breadcrumbs::for('helper_fix_facil', function ($trail, $facilityno=null) {
        $trail->parent('helper_facil', $facilityno);
        $trail->push('介助者修正',url('helper_fix'));
    });

    // 　メインメニュー > 施設一覧 >  介助者一覧 >  介助者データ表示
    Breadcrumbs::for('helperdata_facil', function ($trail, $facilityno=null) {
        $trail->parent('helper_facil',$facilityno);
        $trail->push('介助者データ表示',url('helperdata'));
    });

    // 　メインメニュー > 施設一覧 >  介助者一覧 >  介助者データ表示 >  介助者データ表示
    Breadcrumbs::for('comparison_facil', function ($trail, $facilityno=null) {
        $trail->parent('helperdata_facil' ,$facilityno);
        $trail->push('介助者データ比較',url('comparison'));
    });
// ----------------------------------------------------------------------------------->

