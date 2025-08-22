<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Group;
use App\Library\Common;

class GroupController extends Controller
{
    // グループ一覧
    public function index(Request $request)
    {
        // facilityno を GET/POST どちらからでも受け取る
        $facilityno = $request->query('facilityno') ?? $request->input('facilityno');

        if (empty($facilityno)) {
            // facilityno が無い時はメニューへ返す（お好みで変更可）
            return redirect('/facility');
        }

        // 施設名（画面の見出し用）
        $facility = Facility::where('id', $facilityno)->where('delflag', '<>', 1)->first();
        $facilityname = $facility ? $facility->facility : '';

        // 選択施設に紐づくグループ一覧
        $groups = Group::select('group_id', 'group_name', 'facilityno')
            ->where('facilityno', $facilityno)
            ->orderBy('group_id', 'asc')
            ->get();

        // （共通レイアウトで使っているなら）
        $page  = 'group';
        $title = Common::$title[$page]  ?? 'グループ一覧';
        $group = Common::$group[$page]  ?? '';

        return view('group', [
            'title'        => $title,
            'page'         => $page,
            'group'        => $group,
            'groups'       => $groups,
            'facilityno'   => $facilityno,
            'facilityname' => $facilityname,
            'data'         => [],           // ★ 追加：parent.blade が参照するため
        ]);
    }
}
