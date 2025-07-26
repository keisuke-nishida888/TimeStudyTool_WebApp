@extends('layouts.parent')

@section('content')
<script src="/js/task_fix.js"></script>

<div class="allcont">
    <form method="POST" action="/task_fixctrl" id="form_taskfix" name="form_taskfix" autocomplete="off">
        @csrf
        <input type="hidden" name="task_id" value="{{ $task->task_id ?? '' }}">
        
        <div class="container">
            <table class="tb">
                <tr>
                    <td class="lb">作業名</td>
                    <td>
                        <input type="text" name="task_name" value="{{ old('task_name', $task->task_name ?? '') }}" required>
                    </td>
                </tr>
                <tr>
                    <td class="lb">介護種別</td>
                    <td>
                        <select name="task_type_no" required>
                            <option value="">選択してください</option>
                            <option value="0" {{ old('task_type_no', $task->task_type_no ?? '') == 0 ? 'selected' : '' }}>直接</option>
                            <option value="1" {{ old('task_type_no', $task->task_type_no ?? '') == 1 ? 'selected' : '' }}>間接</option>
                            <option value="2" {{ old('task_type_no', $task->task_type_no ?? '') == 2 ? 'selected' : '' }}>その他</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="lb">カテゴリ</td>
                    <td>
                        <select name="task_category_no" required>
                            <option value="">選択してください</option>
                            <option value="0" {{ old('task_category_no', $task->task_category_no ?? '') == 0 ? 'selected' : '' }}>肉体的負担</option>
                            <option value="1" {{ old('task_category_no', $task->task_category_no ?? '') == 1 ? 'selected' : '' }}>精神的負担</option>
                            <option value="2" {{ old('task_category_no', $task->task_category_no ?? '') == 2 ? 'selected' : '' }}>その他</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 修正ボタン -->
        <button type="submit" id="btn_fixtask" class="btn_fix">修正</button>
    </form>

    <!-- キャンセルボタン -->
    <button type="button" id="btn_cxl_taskfix" class="btn_cancel">キャンセル</button>
</div>
@endsection 