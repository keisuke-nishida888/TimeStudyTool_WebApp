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

} 