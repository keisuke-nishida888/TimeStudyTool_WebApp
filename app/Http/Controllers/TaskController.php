<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Facility;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use \App\Library\Common;

class TaskController extends Controller
{
    //
    public function index(Request $request)
    {
        // 例：task_tableからデータ取得
        $tasks = Task::select('task_id', 'task_name', 'task_type_no', 'task_category_no')
            ->orderBy('task_id', 'asc')
            ->get();

        $page = 'task';
        $title = Common::$title[$page] ?? '作業内容一覧';
        $group = Common::$group[$page] ?? '';
        $data = ""; // layouts/parent.blade.phpで使用される変数

        return view($page, compact('title', 'page', 'group', 'tasks', 'data'));
    }

    // 削除処理
    public function del(Request $request)
    {
        $task_id = $request->input('id');
        
        if ($task_id) {
            Task::where('task_id', $task_id)->delete();
            return redirect('/task')->with('message', '作業内容を削除しました。');
        }
        
        return redirect('/task')->with('error', '削除に失敗しました。');
    }

    // 追加画面表示
    public function add_index(Request $request)
    {
        $page = 'task_add';
        $title = Common::$title[$page] ?? '作業内容追加';
        $group = Common::$group[$page] ?? '';
        $data = "";

        return view($page, compact('title', 'page', 'group', 'data'));
    }

    // 追加処理
    public function TaskAdd(Request $request)
    {
        // 30件制限チェック
        if (Task::count() >= 30) {
            return redirect('/task_add')->withErrors(['task_name' => '作業内容は最大30件までです。不要な作業を削除してください。']);
        }

        $request->validate([
            'task_name' => 'required|string|max:255',
            'task_type_no' => 'required|integer|min:0|max:2',
            'task_category_no' => 'required|integer|min:0|max:2',
        ]);

        Task::create([
            'task_name' => $request->task_name,
            'task_type_no' => $request->task_type_no,
            'task_category_no' => $request->task_category_no,
        ]);

        return redirect('/task')->with('message', '作業内容を追加しました。');
    }

    // 修正画面表示
    public function fix_index(Request $request)
    {
        $task_id = $request->input('id');
        $task = null;
        
        if ($task_id) {
            $task = Task::where('task_id', $task_id)->first();
        }

        $page = 'task_fix';
        $title = Common::$title[$page] ?? '作業内容修正';
        $group = Common::$group[$page] ?? '';
        $data = "";

        return view($page, compact('title', 'page', 'group', 'data', 'task'));
    }

    // 修正処理
    public function TaskFix(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'task_name' => 'required|string|max:255',
            'task_type_no' => 'required|integer|min:0|max:2',
            'task_category_no' => 'required|integer|min:0|max:2',
        ]);

        $task = Task::find($request->task_id);
        if ($task) {
            $task->update([
                'task_name' => $request->task_name,
                'task_type_no' => $request->task_type_no,
                'task_category_no' => $request->task_category_no,
            ]);
            return redirect('/task')->with('message', '作業内容を修正しました。');
        }

        return redirect('/task')->with('error', '修正に失敗しました。');
    }

    // 修正キャンセル
    public function cxl_TaskFix(Request $request)
    {
        return redirect('/task');
    }
} 