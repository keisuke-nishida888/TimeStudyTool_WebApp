// 作業内容追加画面用のJavaScript

$(document).ready(function() {
    console.log('group_add.js が読み込まれました');
    
    // フォーム送信時のバリデーション
    $('#form_groupadd').submit(function(e) {
        console.log('フォーム送信が実行されました');
        var groupName = $('input[name="group_name"]').val().trim();
        
        if (!groupName) {
            alert('グループ名を入力してください。');
            e.preventDefault();
            return false;
        }
     
    });

    // 追加ボタンのクリックイベント
    $('#btn_addgroup').on('click', function(e) {
        console.log('追加ボタンがクリックされました');
        e.preventDefault();
        
        // フォームを手動で送信
        $('#form_groupadd').submit();
    });

    // キャンセルボタンのクリックイベント
    $('#btn_cxl_groupadd').on('click', function(e) {
        console.log('キャンセルボタンがクリックされました');
        e.preventDefault();
        window.location.href = '/group';
    });
    
    // ボタンの存在確認
    console.log('追加ボタン要素:', $('#btn_addgroup').length);
    console.log('キャンセルボタン要素:', $('#btn_cxl_groupadd').length);
}); 