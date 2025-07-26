@extends('layouts.parent')

@section('content')
<div class="allcont">
    <h1>作業内容一覧画面</h1>
    <p>TaskControllerが正常に動作しています。</p>
    
    @if(isset($tasks))
        <p>取得したタスク数: {{ count($tasks) }}</p>
        <ul>
        @foreach($tasks as $task)
            <li>{{ $task['task_name'] }} (ID: {{ $task['task_id'] }})</li>
        @endforeach
        </ul>
    @else
        <p>タスクデータが取得できませんでした。</p>
    @endif
    
    @if(isset($codedata))
        <p>コードデータ数: {{ count($codedata) }}</p>
    @else
        <p>コードデータが取得できませんでした。</p>
    @endif
</div>
@endsection 