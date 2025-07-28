@extends('layouts.parent')

@section('content')
<script src="/js/task.js"></script>

<div class="allcont">
<a id="a_task_add" href="{{ url('/task_add') }}"> <img src="image/img_add.png" class="img_style" alt="作業内容追加"  border="0"> </a>

<input type="image" id="btn_deltask"  src="image/img_del.png" alt="作業内容削除" onclick="del_check_task(targetID,this.id)" border="0">

<form id="a_task_fix" action = '/task_fix'  method = "post" onsubmit = "return Idcheck_task(targetID)">
    @csrf
    <input id="targetid_task" type="hidden" name="id" value="">
    <input type="image" class="img_style" src="image/img_fix.png" alt="作業内容修正" border="0">
</form>

<!-- テーブルヘッダ -->
<img id = "img_task_tb" src="image/img_task_tb.png" alt="" >

<table id="table_task">
    <tbody class="scrollBody">
    @if(isset($tasks))
        @if(count($tasks)<=0)
            <!--  配列の総アイテム数が12未満 -->
            @for($i=0;$i<12;$i++)
                <tr><td class="task_id"></td><td class="task_name"></td><td class="task_type_no"></td>
                    <td class="task_category_no"></td></tr>  
            @endfor
        @else    
            @foreach($tasks as $task)
                <tr data-task-id="{{$task['task_id']}}"><td class="task_id">{{$loop->iteration}}</td>
                    <td class="task_name">{{$task['task_name']}}</td>
                    <td class="task_type_no">
                        @if($task['task_type_no'] == 0)
                            直接
                        @elseif($task['task_type_no'] == 1)
                            間接
                        @elseif($task['task_type_no'] == 2)
                            その他
                        @else
                            {{$task['task_type_no']}}
                        @endif
                    </td>
                    <td class="task_category_no">
                        @if($task['task_category_no'] == 0)
                            肉体的負担
                        @elseif($task['task_category_no'] == 1)
                            精神的負担
                        @elseif($task['task_category_no'] == 2)
                            その他
                        @else
                            {{$task['task_category_no']}}
                        @endif
                    </td></tr>  
                <!--  最後のループ -->
                @if(($loop->last))
                    @if ($loop->count < 12)
                        <!--  配列の総アイテム数が12未満 -->
                        @for($i=$loop->count;$i<12;$i++)
                        <tr><td class="task_id"></td><td class="task_name"></td><td class="task_type_no"></td> 
                            <td class="task_category_no"></td></tr>  
                        @endfor
                    @endif
                @endif
            @endforeach
        @endif
    @else
        <!--  配列の総アイテム数が12未満 -->
        @for($i=0;$i<12;$i++)
        <tr><td class="task_id"></td><td class="task_name"></td><td class="task_type_no"></td> 
            <td class="task_category_no"></td></tr>    
        @endfor
    @endif
    </tbody>
</table>

<!-- CSV取り込みモーダル -->
<span id="pop_csvimport" style="visibility: collapse;">
    <center><nobr id="lb_csvimport">CSV取り込み</nobr></center>
    <form id="form_csvimport" method="POST" enctype="multipart/form-data" onsubmit="return false;">
        @csrf
        <div style="margin:10px 0;">
            <label for="csvimport_helpername" style="display:inline-block; margin-right:8px;">作業者</label>
            <select id="csvimport_helpername" name="helpername" required style="min-width:120px;">
                @if(isset($helpers))
                    @foreach($helpers as $helper)
                        <option value="{{ $helper['helper_id'] }}">{{ $helper['helpername'] }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div style="margin:10px 0;">
            <label for="csvimport_file" style="display:inline-block; padding:8px 20px; background:#3b82f6; color:white; border-radius:4px; cursor:pointer;">
                ファイルを選択
            </label>
            <input type="file" id="csvimport_file" name="csv_file" accept=".csv" style="display:none;" onchange="showSelectedFileName(this)">
            <span id="csv_filename" style="margin-left:10px; color:#333;"></span>
        </div>
        <div style="margin-top:10px; text-align:center;">
            <button id="btn_csvimport_start" onclick="startCsvImport()" style="
                display:inline-block; padding:8px 20px; border:2px solid #2563eb; color:#2563eb; border-radius:4px;
                background:#fff; cursor:pointer; margin-right:10px;">
                取り込み開始
            </button>
            <button id="btn_csvimport_cancel" onclick="closeCsvImportModal()" style="
                display:inline-block; padding:8px 20px; border:2px solid #f44336; color:#f44336; border-radius:4px;
                background:#fff; cursor:pointer;">
                キャンセル
            </button>
        </div>
    </form>
</span>

<!-- 確認モーダル -->
<span id="pop_csvimport_confirm" style="visibility: collapse;">
    <center><nobr id="lb_csvimport_confirm">作業内容データを取り込みます。よろしいですか。</nobr></center>
    <div style="margin-top:10px; text-align:center;">
        <button id="btn_csvimport_yes" onclick="confirmCsvImport()" style="
            display:inline-block; padding:8px 20px; border:2px solid #4CAF50; color:#4CAF50; border-radius:4px;
            background:#fff; cursor:pointer; margin-right:10px;">
            はい
        </button>
        <button id="btn_csvimport_no" onclick="cancelCsvImport()" style="
            display:inline-block; padding:8px 20px; border:2px solid #f44336; color:#f44336; border-radius:4px;
            background:#fff; cursor:pointer;">
            いいえ
        </button>
    </div>
</span>

<script>
function showCsvImportModal() {
    document.getElementById('pop_csvimport').style.visibility = 'visible';
    document.getElementById('pop_alert_back').style.visibility = 'visible';
}

function closeCsvImportModal() {
    document.getElementById('pop_csvimport').style.visibility = 'collapse';
    document.getElementById('pop_alert_back').style.visibility = 'collapse';
}

function showSelectedFileName(input) {
    const fileName = input.files[0] ? input.files[0].name : '';
    document.getElementById('csv_filename').innerText = fileName;
}

function startCsvImport() {
    if (!document.getElementById('csvimport_file').files[0]) {
        alert('CSVファイルを選択してください。');
        return;
    }
    
    if (!document.getElementById('csvimport_helpername').value) {
        alert('作業者を選択してください。');
        return;
    }
    
    // 確認モーダルを表示
    document.getElementById('pop_csvimport').style.visibility = 'collapse';
    document.getElementById('pop_csvimport_confirm').style.visibility = 'visible';
}

function confirmCsvImport() {
    var formdata = new FormData();
    
    formdata.append('csv_file', document.getElementById('csvimport_file').files[0]);
    formdata.append('helpername', document.getElementById('csvimport_helpername').value);
    
    // CSRFトークン取得
    var token = document.querySelector('meta[name="csrf-token"]').content;
    formdata.append('_token', token);
    
    var url = "/task_csv_import";
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('X-CSRF-Token', token);
    
    xhr.responseType = 'json';
    xhr.send(formdata);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                var response = this.response;
                if (response.success) {
                    closeCsvImportModal();
                    document.getElementById('pop_csvimport_confirm').style.visibility = 'collapse';
                    alert('CSVファイルの取り込みが成功しました。');
                    location.reload(); // ページをリロードして最新データを表示
                } else {
                    alert(response.message);
                }
            } else {
                alert('エラーが発生しました。');
            }
        }
    };
}

function cancelCsvImport() {
    document.getElementById('pop_csvimport_confirm').style.visibility = 'collapse';
    document.getElementById('pop_csvimport').style.visibility = 'visible';
}
</script>
@endsection 