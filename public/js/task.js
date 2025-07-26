// 作業内容一覧画面用JavaScript

// 行クリック時の処理
function tr_click_task(id) {
    targetID = id;
    // 選択された行の背景色を変更
    var rows = document.querySelectorAll('#table_task tr');
    rows.forEach(function(row) {
        row.style.backgroundColor = '';
    });
    document.getElementById(id).style.backgroundColor = '#e6f3ff';
}

// IDチェック関数
function Idcheck_task(targetID) {
    if (targetID == null || targetID == "") {
        alert("行を選択してください。");
        return false;
    }
    return true;
}

// 削除確認関数
function del_check_task(targetID, btnid) {
    if (targetID == null || targetID == "") {
        alert("行を選択してください。");
        return;
    }
    
    if (confirm("選択された作業内容を削除しますか？")) {
        // 削除処理を実行
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/task_del';
        
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
}

// ページ読み込み時の初期化
document.addEventListener('DOMContentLoaded', function() {
    // テーブルの行にクリックイベントを追加
    var rows = document.querySelectorAll('#table_task tr');
    rows.forEach(function(row, index) {
        if (row.querySelector('.task_id').textContent.trim() !== '') {
            row.id = 'tr_task_' + (index + 1);
            row.onclick = function() {
                tr_click_task(this.id);
            };
        }
    });
}); 