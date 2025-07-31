@extends('layouts.parent')

@section('content')
<!-- Chart.js本体CDN（公式） -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>


<div class="allcont">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>介助者データ表示</h2>
                <!-- 基本情報表示 -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>施設名:</strong> <span id="facility-name">{{ isset($data2[0]) ? $data2[0]['facility'] : '未選択' }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>作業者名:</strong> <span id="helper-name">{{ isset($data2[0]) ? $data2[0]['helpername'] : '未選択' }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>作業者ID:</strong> <span id="helper-id">{{ isset($data2[0]) ? $data2[0]['Helper_id'] : '未選択' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 条件選択 -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="graph-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="selected-date">年月日:</label>
                                    <input type="date" id="selected-date" name="selected-date" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="graph-type">表示タイプ:</label>
                                    <select id="graph-type" name="graph-type" class="form-control">
                                        <option value="type">介護種別</option>
                                        <option value="category">カテゴリ</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">確定</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- グラフ表示エリア -->
                <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">作業時間表</h5>
                            <div id="graph-legend" style="margin-bottom:8px;"></div>
                            <div id="graph-error" class="alert alert-danger" style="display: none;"></div>
                            <div id="graph-container" style="height: 600px; overflow-x: auto;">
                                <div id="timeTableArea" style="width:100%;"></div>
                            </div>
                            <!-- ↓miniグラフはこの外！ -->
                            <div id="mini-graph-area" style="min-width:320px; margin-top: 24px;"></div>
                        </div>
                    </div>
            </div>
        
        </div>
    </div>
</div>

<script>
let timeGraph = null;

// "2025-07-18 09:02:38" → 9.04
function toDecimalTime(datetimeStr) {
    const time = datetimeStr.split(' ')[1].split(':');
    return parseInt(time[0], 10) + parseInt(time[1], 10) / 60 + parseInt(time[2], 10)/3600;
}

// ページロード時に初期描画
window.addEventListener('DOMContentLoaded', function() {
    const graphType = document.getElementById('graph-type').value;
    renderLegend(graphType);
});

// さらに、select（表示タイプ）を変更した瞬間にも反映
document.getElementById('graph-type').addEventListener('change', function() {
    renderLegend(this.value);
});


// グラフ描画
function createTimeTable(data) {
    const graphType = document.getElementById('graph-type').value;
    renderLegend(graphType);
    const colorMapType = {
        0: "rgba(255, 165, 0, 0.8)",   // オレンジ
        1: "rgba(135, 206, 235, 0.8)", // 水色
        2: "rgba(200,200,200,0.8)"     // グレー
    };
    const colorMapCategory = {
        0: "rgba(255,70,70,0.8)",      // 赤
        1: "rgba(180,80,255,0.8)",     // 紫
        2: "rgba(200,200,200,0.8)"     // グレー
    };

    let html = `<table class="time-table"><thead><tr><th>作業名</th>`;
    for(let h=0; h<24; h++) html += `<th>${h}:00</th>`;
    html += '</tr></thead><tbody>';

    for(const task of data.taskNames) {
        html += `<tr><td>${task}</td>`;
        const intervals = data.taskIndividualDurations[task] || [];
        for(let h=0; h<24; h++) {
            let cellContent = '';
            let isStopCell = false;
            let minutes = 0;
            let dTarget = null;
            for(const d of intervals) {
                const s = toDecimalTime(d.start);
                const e = toDecimalTime(d.stop);
                if (s < h+1 && e > h) {
                    dTarget = d;
                    if (e > h && e <= h+1) {
                        isStopCell = true;
                        minutes = Math.round((e - s) * 60);
                    }
                    break;
                }
            }
            if (dTarget) {
                const s = toDecimalTime(dTarget.start);
                const e = toDecimalTime(dTarget.stop);
                let left = 0, width = 100;
                if (s > h) left = (s-h)*100;
                if (e < h+1) width = (e-h)*100 - left;
                else width = 100 - left;
                // ---- 色を切り替え ----
                let color = "rgba(200,200,200,0.7)";
                if (graphType === "type") color = colorMapType[dTarget.task_type_no] || color;
                if (graphType === "category") color = colorMapCategory[dTarget.task_category_no] || color;
                let minutesHtml = '';
                if (isStopCell) {
                    minutesHtml = `<span style="
                        position:absolute;
                        top:2px;
                        left:calc(${left + width}% + 2px);
                        font-size:13px;
                        color:#222;
                        background:transparent;
                        border:none;
                        padding:0 2px;
                        white-space:nowrap;
                        z-index:2;
                    ">${minutes}</span>`;
                }
                cellContent = `
                    <div style="position:relative;width:100%;height:100%;">
                        <div style="position:absolute;top:0;left:${left}%;width:${width}%;height:100%;background:${color};border-radius:0;"></div>
                        ${minutesHtml}
                    </div>
                `;
            }
            html += `<td style="position:relative;width:32px;height:26px;padding:0;">${cellContent}</td>`;
        }
        html += '</tr>';
    }
    html += '</tbody></table>';
    document.getElementById('timeTableArea').innerHTML = html;
    console.log('drawMiniGraph呼び出し', data);
    drawMiniGraph(data); 
}


function renderLegend(graphType) {
    const legendEl = document.getElementById('graph-legend');
    let html = '';
    if (graphType === "type") {
        html = `
            <span class="legend-box" style="background:rgba(255,165,0,0.8);"></span>直接
            <span class="legend-box" style="background:rgba(135,206,235,0.8);margin-left:24px;"></span>間接
            <span class="legend-box" style="background:rgba(200,200,200,0.8);margin-left:24px;"></span>その他
        `;
    } else if (graphType === "category") {
        html = `
            <span class="legend-box" style="background:rgba(255,70,70,0.8);"></span>肉体的負担
            <span class="legend-box" style="background:rgba(180,80,255,0.8);margin-left:24px;"></span>精神的負担
            <span class="legend-box" style="background:rgba(200,200,200,0.8);margin-left:24px;"></span>その他
        `;
    }
    console.log(html); // ←デバッグ用
    legendEl.innerHTML = html;
}

// 確定ボタンでデータ取得しグラフ描画
document.getElementById('graph-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // 既存のグラフ・エラーをクリア
    if (timeGraph) { timeGraph.destroy(); timeGraph = null; }
    document.getElementById('graph-error').style.display = 'none';
    document.getElementById('graph-error').textContent = '';

    const selectedDate = document.getElementById('selected-date').value;
    const graphType = document.getElementById('graph-type').value;
    const helpno = document.getElementById('helper-id').textContent.trim();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!helpno || helpno === '未選択') {
        alert('作業者IDが取得できません。ページを再読み込みしてください。');
        return;
    }
    if (!selectedDate) {
        alert('年月日を選択してください。');
        return;
    }
    if (!csrfToken) {
        alert('CSRFトークンが見つかりません。');
        return;
    }

    document.getElementById('graph-error').style.display = 'block';
    document.getElementById('graph-error').textContent = 'データを取得中...';
    document.getElementById('graph-error').style.color = '#0066cc';

    fetch('/get_graph_data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            helpno: helpno,
            selected_date: selectedDate,
            graph_type: graphType
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('graph-error').style.display = 'none'; // エラー表示消す
        createTimeTable(data);
    })
    .catch(error => {
        document.getElementById('graph-error').textContent = 'データの取得に失敗しました。: ' + error.message;
        document.getElementById('graph-error').style.display = 'block';
        document.getElementById('graph-error').style.color = '#dc3545';
    });
});



// 既存関数を「createTimeTable」に差し替え
function toDecimalTime(datetimeStr) {
    // "2025-07-18 09:02:38" → 9.04
    const time = datetimeStr.split(' ')[1].split(':');
    return parseInt(time[0], 10) + parseInt(time[1], 10) / 60 + parseInt(time[2], 10)/3600;
}

// ミニグラフ描画
function drawMiniGraph(data) {
    const graphType = document.getElementById('graph-type').value;
    const target = document.getElementById('mini-graph-area');
    if (!target) {
        alert('mini-graph-areaが見つかりません！');
        return;
    }
    // 色・ラベル設定
    let meta = {};
    if (graphType === "type") {
        meta = {
            title: "介護種別別勤務時間",
            items: [
                { key: 0, color: "#f7b98b", dot: "#f19545", label: "直接業務" },
                { key: 1, color: "#8ed6f6", dot: "#55b5ec", label: "間接業務" },
                { key: 2, color: "#bcbcbc", dot: "#888", label: "その他業務" },
            ]
        };
    } else {
        meta = {
            title: "カテゴリ別勤務時間",
            items: [
                { key: 0, color: "#f6999a", dot: "#e45757", label: "肉体的負担業務" },
                { key: 1, color: "#cfb0f7", dot: "#a15be7", label: "精神的負担業務" },
                { key: 2, color: "#bcbcbc", dot: "#888", label: "その他業務" },
            ]
        };
    }

    // 合計計算
    const totalMinutes = [0,0,0];
    for (const task of data.taskNames) {
        const intervals = data.taskIndividualDurations[task] || [];
        for (const d of intervals) {
            let no = graphType === "type" ? d.task_type_no : d.task_category_no;
            let min = Math.round((toDecimalTime(d.stop) - toDecimalTime(d.start)) * 60);
            totalMinutes[no] += min;
        }
    }

    // 最大値で横棒長さを決定
    const maxMinutes = Math.max(...totalMinutes, 1);

    // HTML組み立て
    let html = `
        <div style="border:2px solid #111; border-radius:18px; padding:16px 20px; background:#fff;">
        <div style="font-weight:bold; margin-bottom:16px;">${meta.title}</div>
    `;
    meta.items.forEach((item, i) => {
        // 時間表記（例: 2時間35分、0は"00分"とする）
        let min = totalMinutes[item.key] || 0;
        let h = Math.floor(min/60);
        let m = min%60;
        let timeLabel = (h ? h+"時間" : "") + (("0"+m).slice(-2)) + "分";
        let barLen = Math.round((min/maxMinutes)*150); // 150px最大幅

        html += `
        <div style="display:flex;align-items:center;margin-bottom:12px;">
          <span style="display:inline-block;width:16px;height:16px;border-radius:50%;background:${item.dot};margin-right:10px;"></span>
          <span style="width:90px;">${item.label}</span>
          <span style="width:70px;text-align:right;">${timeLabel}</span>
          <div style="height:18px;width:${barLen}px;background:${item.color};border-radius:9px;margin-left:20px;"></div>
        </div>
        `;
    });
    html += `</div>`;

    target.innerHTML = html;
}



</script>

<style>
.card {
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.card-title {
    color: #333;
    font-weight: bold;
}
#graph-container {
    position: relative;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px;
    min-height: 600px;
    overflow-x: auto;
}

.time-table {
  border-collapse: collapse;   /* 線を繋げる */
  width: 100%;
}
.time-table th, .time-table td {
  border: 1px solid #aaa;
  padding: 0;              /* 余白なしでセル全体を色塗り */
  background: #fff;
  border-radius: 0 !important; /* ←角丸を消す */
  box-sizing: border-box;
}
.time-table .cell-colored {
  background: #fa8 !important;  /* 色塗り */
  border-radius: 0 !important;  /* ←角丸禁止 */
}
.time-table th {
  background: #f0f0fa;
  font-weight: bold;
}
.time-table tr td:first-child {
  background: #f5f5f5;
  font-weight: bold;
  min-width: 90px;
}

/* ---- 色塗りセル ---- */
.cell-colored {
  background: #fa8 !important;
  border-radius: 0 !important;
}
.cell-colored2 {
  background: #8cf !important;
  border-radius: 0 !important;
}
.cell-colored3 {
  background: #8f8 !important;
  border-radius: 0 !important;
}

/* ---- 部分塗り（例: 30分だけ塗りたい場合）---- */
.cell-half {
  width: 100%;
  height: 100%;
  background: linear-gradient(to right, #fa8 50%, #fff 50%);
  border-radius: 0 !important;
}
.cell-third {
  width: 100%;
  height: 100%;
  background: linear-gradient(to right, #fa8 33.33%, #fff 33.33%);
  border-radius: 0 !important;
}

.legend-box {
    display: inline-block;
    width: 40px;
    height: 16px;
    border-radius: 0;
    vertical-align: middle;
    margin-right: 4px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

</style>
@endsection
