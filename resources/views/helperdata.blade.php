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
                        <div id="graph-error" class="alert alert-danger" style="display: none;"></div>
                        <div id="graph-container" style="height: 600px; overflow-x: auto;">
                        <div id="timeTableArea"></div>
                        </div>
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

// グラフ描画
function createTimeGraph(data) {
    const canvas = document.getElementById('timeGraph');
    const ctx = canvas.getContext('2d');
    if (timeGraph && typeof timeGraph.destroy === "function") {
        timeGraph.destroy();
        timeGraph = null;
        canvas.width = canvas.width;
    }

    const colors = [
        "rgba(255, 165, 0, 0.8)",
        "rgba(135, 206, 235, 0.8)",
        "rgba(34, 139, 34, 0.8)"
    ];

    // 区間ごとラベル方式
    let labels = [];
    let dataValues = [];
    let baseValues = [];
    let bgColors = [];
    let borderColors = [];
    let borderWidths = [];

    data.taskNames.forEach((task, idx) => {
        const durations = data.taskIndividualDurations[task];
        if (!durations) return;
        durations.forEach((d) => {
            labels.push(task); // ← 各区間ごとにラベル。作業名が重複してOK
            const start = toDecimalTime(d.start);
            const stop = toDecimalTime(d.stop);
            dataValues.push(stop - start);
            baseValues.push(start);
            bgColors.push(colors[d.task_type_no] || "rgba(150,150,150,0.7)");
            borderColors.push(colors[d.task_type_no] || "rgba(150,150,150,1)");
            borderWidths.push(16);
        });
    });

    timeGraph = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "作業",
                data: dataValues,
                backgroundColor: bgColors,
                borderColor: borderColors,
                borderWidth: borderWidths,
                base: baseValues, // 棒の開始位置
                barPercentage: 0.9,
                categoryPercentage: 1.0,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: false,
            maintainAspectRatio: false,
            scales: {
                y: {
                    title: { display: false },
                    ticks: {
                        font: { size: 16 },
                        color: "#222"
                    }
                },
                x: {
                    min: 0,
                    max: 24,
                    title: { display: true, text: "時間", font: { size: 18 } },
                    ticks: {
                        stepSize: 1,
                        font: { size: 16 },
                        callback: v => `${String(v).padStart(2, '0')}:00`
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const base = ctx.dataset.base[ctx.dataIndex];
                            const val = ctx.raw;
                            if (!val || val === 0) return '';
                            return `${ctx.label}: ${base.toFixed(2)}時～${(base + val).toFixed(2)}時`;
                        }
                    }
                }
            }
        }
    });
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

// 作業時間データからセル塗り分け表を生成
function createTimeTable(data) {
    const hours = Array.from({length:24}, (_,i) => i);
    const colors = [
        "rgba(255, 165, 0, 0.8)",    // オレンジ
        "rgba(135, 206, 235, 0.8)",  // 水色
        "rgba(34, 139, 34, 0.8)",    // 緑
    ];

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
                    // stopがこのセル内
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
                const color = colors[dTarget.task_type_no] || "rgba(150,150,150,0.7)";
                let minutesHtml = '';
                if (isStopCell) {
                    // バーの右端ぴったりに、透明で表示
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
}





// 既存関数を「createTimeTable」に差し替え
function toDecimalTime(datetimeStr) {
    // "2025-07-18 09:02:38" → 9.04
    const time = datetimeStr.split(' ')[1].split(':');
    return parseInt(time[0], 10) + parseInt(time[1], 10) / 60 + parseInt(time[2], 10)/3600;
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



</style>
@endsection
