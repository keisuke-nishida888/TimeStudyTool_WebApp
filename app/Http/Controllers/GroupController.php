<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Group;
use App\Library\Common;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

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
            'data'         => [], 
        ]);
    }

    public function add_index(Request $request)
    {
        $facilityno = $request->query('facilityno') ?? $request->input('facilityno');
        if (empty($facilityno)) {
            // referer から拾うフォールバック（任意）
            if (!empty($_SERVER['HTTP_REFERER'])) {
                parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY) ?? '', $q);
                $facilityno = $q['facilityno'] ?? null;
            }
        }
        if (empty($facilityno)) {
            abort(400, 'facilityno is required');
        }
    
        $facility = Facility::where('id', $facilityno)->where('delflag','!=',1)->firstOrFail();
    
        $page  = 'group_add';
        $title = \App\Library\Common::$title[$page] ?? 'グループ名追加';
        $group = \App\Library\Common::$group[$page] ?? 'groups'; // ← 戻る先（/groups）に合わせる
    
        return view('group_add', [
            'title'      => $title,
            'page'       => $page,
            'group'      => $group,
            'facilityno' => $facilityno,
            'facility'   => $facility,
            'data'       => [], // ★ 追加：parent.blade が参照するため
        ]);
    }

    public function store(Request $request)
    {
    $facilityno = (int)$request->input('facilityno');

    $uniqueRule = Rule::unique('groups','group_name')
        ->where(function($q) use ($facilityno){
            $q->where('facilityno', $facilityno);
            if (Schema::hasColumn('groups','delflag')) {
                $q->where('delflag', 0);
            }
        });

    $request->validate([
        'facilityno'  => ['required','integer'],
        'group_name'  => ['required','string','max:100', $uniqueRule],
    ]);

    Group::create([
        'facilityno' => $facilityno,
        'group_name' => $request->input('group_name'),
        'delflag'    => Schema::hasColumn('groups','delflag') ? 0 : null,
    ]);

    return redirect()->route('groups.index', ['facilityno' => $facilityno])
                     ->with('status','グループを追加しました。');
    }
    
    
    public function fixGroup(Request $request)
    {
        $facilityno = (int)($request->query('facilityno') ?? $request->input('facilityno'));
        $groupno    = (int)($request->query('groupno')    ?? $request->input('groupno'));
    
        if (empty($facilityno) || empty($groupno)) {
            abort(400, 'facilityno & groupno are required');
        }
    
        // 施設確認（未削除）
        $facility = Facility::where('id', $facilityno)->where('delflag', '<>', 1)->firstOrFail();
    
        // 対象グループ
        $groupRow = Group::where('facilityno', $facilityno)
            ->where('group_id', $groupno)
            ->firstOrFail();
    
        // 画面用
        $page  = 'group_fix';
        $title = \App\Library\Common::$title[$page] ?? 'グループ修正';
        $group = 'groups'; // ← 戻る先は一覧
    
        return view('group_fix', [
            'title'      => $title,
            'page'       => $page,
            'group'      => $group,
            'facilityno' => $facilityno,
            'facility'   => $facility,
            'groupRow'   => $groupRow,
            'data'       => [], // parent.blade が参照するため
        ]);
    }
    

    public function fix_index(Request $request)
    {
        $facilityno = (int) ($request->query('facilityno') ?? $request->input('facilityno'));
        $groupno    = (int) ($request->query('groupno')    ?? $request->input('groupno'));

        abort_if(empty($facilityno) || empty($groupno), 400, 'facilityno & groupno are required');

        $facility = Facility::where('id', $facilityno)->where('delflag','!=',1)->firstOrFail();
        $groupRow = Group::where('facilityno', $facilityno)->where('group_id', $groupno)->firstOrFail();

        $page  = 'group_fix';
        $title = \App\Library\Common::$title[$page] ?? 'グループ修正';
        $group = \App\Library\Common::$group[$page] ?? 'groups';

        // ★ ここで 'data' を空配列で渡す（parent.blade が参照するため）
        return view('group_fix', [
            'title'      => $title,
            'page'       => $page,
            'group'      => $group,
            'facilityno' => $facilityno,
            'groupno'    => $groupno,
            'facility'   => $facility,
            'groupRow'   => $groupRow,
            'data'       => [],   // ← 追加
        ]);
    }


    public function update(Request $request)
    {
        $facilityno = (int) $request->input('facilityno');
        $groupno    = (int) $request->input('groupno');

        // バリデーション（施設内で同名禁止）
        $unique = Rule::unique('groups','group_name')
            ->ignore($groupno, 'group_id')
            ->where(function($q) use ($facilityno){
                $q->where('facilityno', $facilityno);
                if (Schema::hasColumn('groups','delflag')) {
                    $q->where('delflag', 0);
                }
            });

        $request->validate([
            'facilityno' => ['required','integer'],
            'groupno'    => ['required','integer'],
            'group_name' => ['required','string','max:100', $unique],
        ]);

        $row = Group::where('facilityno', $facilityno)
                    ->where('group_id',  $groupno)
                    ->firstOrFail();

        $row->group_name = $request->input('group_name');
        $row->save();

        return redirect()->to('/groups?facilityno='.$facilityno)
                        ->with('status','グループを更新しました。');
    }

    public function del(Request $request)
    {
        $facilityno = (int) $request->input('facilityno');
        $groupno    = (int) $request->input('groupno');

        if (empty($facilityno) || empty($groupno)) {
            abort(400, 'facilityno & groupno are required');
        }

        // 対象グループの取得（delflag がある場合は未削除のみ）
        $row = Group::where('facilityno', $facilityno)
            ->where('group_id', $groupno)
            ->when(Schema::hasColumn('groups', 'delflag'), function ($q) {
                $q->where('delflag', '!=', 1);
            })
            ->firstOrFail();

        // --- 必要なら：このグループの作業者の groupno を外す（任意）
        // Helper::where('facilityno', $facilityno)->where('groupno', $groupno)->update(['groupno' => null]);

        // 論理削除 or 物理削除
        if (Schema::hasColumn('groups', 'delflag')) {
            $row->delflag = 1;
            $row->save();
        } else {
            $row->delete();
        }

        return redirect()->route('groups.index', ['facilityno' => $facilityno])
            ->with('status', 'グループを削除しました。');
    }


}
