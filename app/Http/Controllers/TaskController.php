<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Facility;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Library\Common;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /** GET/POST 混在・配列対策しつつ int 化 */
    private function intParam(Request $req, string $key, int $default = 0): int
    {
        $v = $req->query($key, $req->input($key, $default));
        if (is_array($v)) $v = reset($v);
        return (int)$v;
    }

    // 一覧表示
    public function index(Request $request)
    {
        $facilityno = $this->intParam($request, 'facilityno', 0);

        $query = Task::select('task_id','task_name','task_type_no','task_category_no','facilityno')
                     ->orderBy('task_id','asc');

        // 施設未指定のときは何も出さない
        if ($facilityno > 0) {
            $query->where('facilityno', $facilityno);
        } else {
            $query->whereRaw('1=0');
        }

        $tasks = $query->get();

        $page  = 'task';
        $title = Common::$title[$page] ?? '作業内容一覧';
        $group = 'task';
        $data  = '';

        return view($page, compact('title','page','group','tasks','data','facilityno'));
    }

    // 削除処理
    public function del(Request $request)
    {
        $facilityno = $this->intParam($request, 'facilityno', (int)(optional(Auth::user())->facilityno ?? 0));
        $task_id    = $this->intParam($request, 'id', 0);

        if ($task_id > 0) {
            // 施設スコープをかけて安全に削除
            $query = Task::where('task_id', $task_id);
            if ($facilityno > 0) {
                $query->where('facilityno', $facilityno);
            }
            $deleted = $query->delete();
        }
        return redirect('/task?facilityno='.$facilityno)->with('error','削除に失敗しました。');
    }

    // 追加画面表示
    public function add_index(Request $request)
    {
        $facilityno = $this->intParam($request, 'facilityno', (int)(optional(Auth::user())->facilityno ?? 0));
        if ($facilityno <= 0 && !empty($_SERVER['HTTP_REFERER'])) {
            parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY) ?? '', $q);
            $facilityno = (int)($q['facilityno'] ?? 0);
        }
        if ($facilityno <= 0) abort(400, 'facilityno is required');

        $facility = Facility::where('id',$facilityno)->where('delflag','!=',1)->firstOrFail();

        $page  = 'task_add';
        $title = Common::$title[$page] ?? '作業名追加';
        $group = 'task';

        return view('task_add', [
            'title'      => $title,
            'page'       => $page,
            'group'      => $group,
            'facilityno' => $facilityno,
            'facility'   => $facility,
            'data'       => [],
        ]);
    }

    // 追加処理（ルートの表記揺れに対応するなら別名メソッドを用意してもOK）
   // 追加処理
public function Taskadd(Request $request)
{
    $validated = $request->validate([
        'facilityno'       => ['required','integer','min:1','exists:facility,id'],
        'task_name'        => ['required','string','max:100'],
        'task_type_no'     => ['required','integer','in:0,1,2'],
        'task_category_no' => ['required','integer','in:0,1,2'],
    ]);

    $facilityno = (int)$validated['facilityno'];

    // 必要なら一意制約（施設×種別×カテゴリ×名称）
    $exists = Task::where('facilityno', $facilityno)
        ->where('task_type_no', (int)$validated['task_type_no'])
        ->where('task_category_no', (int)$validated['task_category_no'])
        ->where('task_name', $validated['task_name'])
        ->exists();
    if ($exists) {
        return back()->withErrors(['task_name'=>'同一の作業名が既に存在します。'])->withInput();
    }

    Task::create([
        'facilityno'       => $facilityno,
        'task_name'        => $validated['task_name'],
        'task_type_no'     => (int)$validated['task_type_no'],
        'task_category_no' => (int)$validated['task_category_no'],
    ]);

    return redirect('/task?facilityno='.$facilityno)->with('status','作業を追加しました。');
}


    // 修正画面表示
    public function fix_index(Request $request)
    {
        $facilityno = $this->intParam($request, 'facilityno', (int)(optional(Auth::user())->facilityno ?? 0));
        $taskId     = $this->intParam($request, 'id', 0);

        $task = null;
        if ($taskId > 0) {
            $q = Task::where('task_id', $taskId);
            if ($facilityno > 0) $q->where('facilityno', $facilityno);
            $task = $q->first();
        }

        $page  = 'task_fix';
        $title = Common::$title[$page] ?? '作業内容修正';
        $group = 'task';
        $data  = '';

        // ★ $task（単数レコード）と $group を正しく渡す
        return view($page, compact('title','page','group','data','task','facilityno'));
    }

    // 修正処理
    public function TaskFix(Request $request)
    {
        $facilityno = $this->intParam($request, 'facilityno', (int)(optional(Auth::user())->facilityno ?? 0));

        $validated = $request->validate([
            'task_id'          => 'required|integer',
            'task_name'        => 'required|string|max:255',
            'task_type_no'     => 'required|integer','in:0,1,2',
            'task_category_no' => 'required|integer','in:0,1,2',
        ]);

        $task = Task::where('task_id', (int)$validated['task_id'])->first();
        if (!$task) {
            return redirect('/task?facilityno='.$facilityno)->with('error','修正対象が見つかりませんでした。');
        }

        $task->task_name        = $validated['task_name'];
        $task->task_type_no     = (int)$request->input('task_type_no');
        $task->task_category_no = (int)$request->input('task_category_no');
        $task->save();

        return redirect('/task?facilityno='.$facilityno)->with('message','作業内容を修正しました。');
    }

    // 修正キャンセル
    public function cxl_TaskFix(Request $request)
    {
        $facilityno = $this->intParam($request, 'facilityno', 0);
        return redirect('/task?facilityno='.$facilityno);
    }
}
