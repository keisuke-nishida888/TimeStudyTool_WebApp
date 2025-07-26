// 作業内容追加画面用のJavaScript

$(document).ready(function() {
    console.log('task_add.js が読み込まれました');
    
    // フォーム送信時のバリデーション
    $('#form_taskadd').submit(function(e) {
        console.log('フォーム送信が実行されました');
        var taskName = $('input[name="task_name"]').val().trim();
        var taskType = $('select[name="task_type_no"]').val();
        var taskCategory = $('select[name="task_category_no"]').val();
        
        if (!taskName) {
            alert('作業名を入力してください。');
            e.preventDefault();
            return false;
        }
        
        if (taskType === '') {
            alert('介護種別を選択してください。');
            e.preventDefault();
            return false;
        }
        
        if (taskCategory === '') {
            alert('カテゴリを選択してください。');
            e.preventDefault();
            return false;
        }
        
        console.log('バリデーション成功、フォームを送信します');
        return true;
    });

    // 追加ボタンのクリックイベント
    $('#btn_addtask').on('click', function(e) {
        console.log('追加ボタンがクリックされました');
        e.preventDefault();
        
        // フォームを手動で送信
        $('#form_taskadd').submit();
    });

    // キャンセルボタンのクリックイベント
    $('#btn_cxl_taskadd').on('click', function(e) {
        console.log('キャンセルボタンがクリックされました');
        e.preventDefault();
        window.location.href = '/task';
    });

    // セレクトボックスの選択確認
    $('select[name="task_type_no"]').change(function() {
        console.log('介護種別が選択されました:', $(this).val());
    });

    $('select[name="task_category_no"]').change(function() {
        console.log('カテゴリが選択されました:', $(this).val());
    });
    
    // ボタンの存在確認
    console.log('追加ボタン要素:', $('#btn_addtask').length);
    console.log('キャンセルボタン要素:', $('#btn_cxl_taskadd').length);
}); 