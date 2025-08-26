<?php

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

// ===========================
// メインメニュー
// ===========================
Breadcrumbs::for('mainmenu', function ($trail) {
    $trail->push('メニュー', url('/mainmenu'));
});

// ===========================
// ログインユーザ
// ===========================
Breadcrumbs::for('loginuser', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('ログインユーザ一覧', url('/loginuser'));
});
Breadcrumbs::for('loginuser_add', function ($trail) {
    $trail->parent('loginuser');
    $trail->push('ログインユーザ追加', url('/loginuser_add'));
});
Breadcrumbs::for('loginuser_fix', function ($trail) {
    $trail->parent('loginuser');
    $trail->push('ログインユーザ修正', url('/loginuser_fix'));
});

// ===========================
// ウェアラブル
// ===========================
Breadcrumbs::for('wearable', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('心拍センサー一覧', url('/wearable'));
});
Breadcrumbs::for('wearable_add', function ($trail) {
    $trail->parent('wearable');
    $trail->push('心拍センサー追加', url('/wearable_add'));
});
Breadcrumbs::for('wearable_fix', function ($trail) {
    $trail->parent('wearable');
    $trail->push('心拍センサー修正', url('/wearable_fix'));
});

// ===========================
// リスクデバイス
// ===========================
Breadcrumbs::for('risksensor', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('リスクデバイス一覧', url('/risksensor'));
});
Breadcrumbs::for('risksensor_add', function ($trail) {
    $trail->parent('risksensor');
    $trail->push('リスクデバイス追加', url('/risksensor_add'));
});
Breadcrumbs::for('risksensor_fix', function ($trail) {
    $trail->parent('risksensor');
    $trail->push('リスクデバイス修正', url('/risksensor_fix'));
});

// ===========================
// 施設
// ===========================
Breadcrumbs::for('facility', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('施設一覧', url('/facility'));
});
Breadcrumbs::for('facility_add', function ($trail) {
    $trail->parent('facility');
    $trail->push('施設情報追加', url('/facility_add'));
});
Breadcrumbs::for('facility_fix', function ($trail) {
    $trail->parent('facility');
    $trail->push('施設情報修正', url('/facility_fix'));
});
Breadcrumbs::for('cost_ctrl', function ($trail) {
    $trail->parent('facility');
    $trail->push('コストデータ管理', url('/cost_ctrl'));
});
Breadcrumbs::for('task', function ($trail) {
    $trail->parent('facility');
    $trail->push('作業内容一覧', url('/task'));
});
Breadcrumbs::for('task_add', function ($trail) {
    $trail->parent('task');
    $trail->push('作業内容追加', url('/task_add'));
});
Breadcrumbs::for('task_fix', function ($trail) {
    $trail->parent('task');
    $trail->push('作業内容修正', url('/task_fix'));
});

// ===========================
// 作業者（管理者系・施設一覧配下）
// ===========================
// Helper 一覧（facilityno/groupno は任意引数）
Breadcrumbs::for('helper', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('facility');
    $url = url('/helper');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者一覧', $url);
});
Breadcrumbs::for('helper_add', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('helper', $facilityno, $groupno);
    $url = url('/helper_add');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者追加', $url);
});
Breadcrumbs::for('helper_fix', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('helper', $facilityno, $groupno);
    $url = url('/helper_fix');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者修正', $url);
});
Breadcrumbs::for('helperdata', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('helper', $facilityno, $groupno);
    $url = url('/helperdata');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者データ表示', $url);
});
Breadcrumbs::for('comparison', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('helperdata', $facilityno, $groupno);
    $url = url('/comparison');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者データ比較', $url);
});

// ===========================
// 作業者（施設ユーザ系・メインメニュー配下）
// ===========================
Breadcrumbs::for('helper_facil', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('mainmenu');
    $url = url('/helper');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者一覧', $url);
});
Breadcrumbs::for('helper_add_facil', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('helper_facil', $facilityno, $groupno);
    $url = url('/helper_add');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者追加', $url);
});
Breadcrumbs::for('helper_fix_facil', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('helper_facil', $facilityno, $groupno);
    $url = url('/helper_fix');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者修正', $url);
});
Breadcrumbs::for('helperdata_facil', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('helper_facil', $facilityno, $groupno);
    $url = url('/helperdata');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者データ表示', $url);
});
Breadcrumbs::for('comparison_facil', function ($trail, $facilityno = null, $groupno = null) {
    $trail->parent('helperdata_facil', $facilityno, $groupno);
    $url = url('/comparison');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);
    $trail->push('作業者データ比較', $url);
});

// ===========================
// グループ一覧
// ===========================
Breadcrumbs::for('group', function ($trail, $facilityno = null) {
    $trail->parent('facility');
    $url = url('/groups');
    if (!empty($facilityno)) {
        $url .= '?facilityno=' . urlencode($facilityno);
    }
    $trail->push('グループ一覧', $url);
});
Breadcrumbs::for('group_add', function ($trail, $facilityno = null) {
    $trail->parent('group', $facilityno);
    $url = url('/groups/add');
    if (!empty($facilityno)) {
        $url .= '?facilityno=' . urlencode($facilityno);
    }
    $trail->push('グループ追加', $url);
});

// ===========================
// 平均データ / 施設情報入力 / コスト登録
// ===========================
Breadcrumbs::for('averdata', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('平均データ表示', url('/averdata'));
});
Breadcrumbs::for('facilityinput', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('施設情報入力', url('/facilityinput'));
});
Breadcrumbs::for('costregist', function ($trail) {
    $trail->parent('mainmenu');
    $trail->push('現状コスト/導入コスト登録', url('/costregist'));
});

// ===========================
// グループ修正
// ===========================
Breadcrumbs::for('group_fix', function ($trail, $facilityno = null, $groupno = null) {
    // 親は「グループ一覧」
    $trail->parent('group', $facilityno);

    // 自ページへのURL（クエリ付与）
    $url = url('/group_fix');
    $q = [];
    if (!empty($facilityno)) $q['facilityno'] = $facilityno;
    if (!empty($groupno))    $q['groupno']    = $groupno;
    if ($q) $url .= '?' . http_build_query($q);

    $trail->push('グループ修正', $url);
});