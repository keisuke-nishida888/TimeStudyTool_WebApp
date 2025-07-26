// 作業内容一覧用のJavaScript

// テーブルの行クリック時の処理
function tr_click_task(trID)
{
    trID.css("background-color","#e49e61");
    trID.mouseover(function(){
        $(this).css("background-color","#CCFFCC") .css("cursor","pointer")
    });
    trID.mouseout(function(){
        $(this).css("background-color","#e49e61") .css("cursor","normal")
    });
}

// 作業内容一覧用のIDチェック関数
function Idcheck_task(targetID) {
    if (targetID == null || targetID == "") {
        alert("行を選択してください。");
        return false;
    }
    return true;
}

// 作業内容削除確認
function del_check_task(targetID, btnID) {
    if (targetID == null || targetID == "") {
        alert("行を選択してください。");
        return false;
    }
    
    if (confirm("選択された作業内容を削除しますか？")) {
        // 削除処理を実行
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/task_delete';
        
        var csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        var idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = targetID;
        
        form.appendChild(csrfToken);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
    return false;
}

// ページ読み込み時の初期化
$(document).ready(function() {
    // テーブルの行クリックイベントを設定
    $('#table_task tbody tr').click(function() {
        // 他の行の背景色をリセット
        $('#table_task tbody tr').css("background-color", "#ffffff");
        
        // クリックされた行の背景色を変更
        $(this).css("background-color", "#e49e61");
        
        // 選択された行のIDを取得
        var taskId = $(this).data('task-id');
        if (taskId) {
            targetID = taskId;
            $('#targetid_task').val(taskId);
        }
    });
    
    // マウスオーバー時の処理
    $('#table_task tbody tr').hover(
        function() {
            if ($(this).css("background-color") !== "rgb(228, 158, 97)") {
                $(this).css("background-color", "#CCFFCC").css("cursor", "pointer");
            }
        },
        function() {
            if ($(this).css("background-color") !== "rgb(228, 158, 97)") {
                $(this).css("background-color", "#ffffff").css("cursor", "normal");
            }
        }
    );
}); 