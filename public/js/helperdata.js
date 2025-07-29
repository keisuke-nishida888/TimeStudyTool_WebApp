// helperdata.js - 介助者データ表示用のJavaScript

// ページ読み込み時の初期化
document.addEventListener('DOMContentLoaded', function() {
    // 今日の日付をデフォルトに設定
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('selected-date');
    if (dateInput && !dateInput.value) {
        dateInput.value = today;
    }
});

// フォーム送信前のバリデーション
function validateGraphForm() {
    const selectedDate = document.getElementById('selected-date').value;
    const graphType = document.getElementById('graph-type').value;
    
    if (!selectedDate) {
        alert('年月日を選択してください。');
        return false;
    }
    
    return true;
}

// エラーハンドリング
function handleError(error) {
    console.error('Error:', error);
    alert('データの取得に失敗しました。');
}

// 成功時の処理
function handleSuccess(data) {
    console.log('Data received:', data);
    // グラフ作成処理は別途実装
}
