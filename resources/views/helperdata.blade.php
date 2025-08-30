@extends('layouts.parent')

@section('content')
<!-- Chart.js本体CDN（公式） -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>


<div class="allcont">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>作業者データ表示</h2>
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
               <div class="allcont">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- ★ ここから：確定後に出すセクション（初期は非表示） -->
                            <div id="time-table-section" style="display:none;">
                            <!-- 横並びラッパー START -->
                            <div style="display: flex; align-items: flex-start;">

                                <!-- グラフ表のカード（左） -->
                                <div style="flex:1;">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">作業時間表</h5>
                                            <div id="graph-legend" style="margin-bottom:8px;"></div>
                                            <div id="graph-error" class="alert alert-danger" style="display: none;"></div>
                                            <div id="graph-container" style="height: 600px; overflow-x: auto;">
                                                <div id="timeTableArea" style="width:100%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- ミニグラフを右側に（カード外） -->
                            <div id="mini-graph-area" style="min-width:320px; margin-left:40px;"></div>

                        </div>
                        <!-- 横並びラッパー END -->
                    </div>
                    <!-- ★ ここまで：確定後に出すセクション -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

            <!-- ▼ 期間指定 集計フォーム（新規） -->
            <div class="card mb-4">
            <div class="card-body">
                <form id="range-form" class="row g-3">
                <div class="col-md-3">
                    <label for="range-start">期間(開始)</label>
                    <input type="date" id="range-start" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="range-end">期間(終了)</label>
                    <input type="date" id="range-end" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-secondary">集計</button>
                </div>
                </form>
            </div>
            </div>

            <!-- ▼ マトリクス表（新規） -->
            <div class="card mb-4" id="matrix-card" style="display:none;">
            <div class="card-body">
                <h5 class="card-title">日別 × 作業名 集計表</h5>
                <div id="task-day-matrix-wrap" class="matrix-scroll">
                <div id="task-day-matrix"></div>
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
    legendEl.innerHTML = html;
}


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

    // ★★「合計」列を追加
    let html = `<table class="time-table"><thead><tr><th>作業名</th>`;
    for(let h=0; h<24; h++) html += `<th>${h}:00</th>`;
    html += `<th>合計</th></tr></thead><tbody>`; // ←ここで合計列

    for(const task of data.taskNames) {
        html += `<tr><td>${task}</td>`;
        const intervals = data.taskIndividualDurations[task] || [];
        let totalMinutes = 0; // ←合計用

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

        // ★★ 合計分数を計算して最後の列に追加
        for(const d of intervals) {
            totalMinutes += Math.round((toDecimalTime(d.stop) - toDecimalTime(d.start)) * 60);
        }
        html += `<td style="background:#f8f8e9;font-weight:bold;min-width:60px;">${totalMinutes}分</td>`;

        html += '</tr>';
    }
    html += '</tbody></table>';
    document.getElementById('timeTableArea').innerHTML = html;
    console.log('drawMiniGraph呼び出し', data);
    drawMiniGraph(data); 
}


// 確定ボタンでデータ取得しグラフ描画
document.getElementById('graph-form').addEventListener('submit', function(e) {
    e.preventDefault();
     // ★ 確定ボタンクリックで表示開始
    const section = document.getElementById('time-table-section');
    if (section) section.style.display = 'block';
    
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
        let min = totalMinutes[item.key] || 0;
        let h = Math.floor(min/60);
        let m = min%60;
        let timeLabel = (h ? h+"時間" : "") + m + "分";
        let barLen = Math.round((min/maxMinutes)*150); // 150px最大幅

        html += `
            <div style="display:flex;align-items:center;margin-bottom:24px;gap:12px;">
            <span style="display:inline-block;width:20px;height:20px;border-radius:50%;background:${item.dot};margin-right:8px;flex-shrink:0;"></span>
            <span style="width:110px;white-space:nowrap;font-size:1.15em;color:#6a6d6d;font-weight:600;">${item.label}</span>
            <span style="width:auto;min-width:100px;white-space:nowrap;font-size:1.2em;font-weight:500;color:#656969;letter-spacing:1px;text-align:right;">${timeLabel}</span>
            <div style="height:24px;width:${barLen}px;background:${item.color};border-radius:12px;margin-left:16px;flex-shrink:0;"></div>
            </div>
        `;
        });
    html += `</div>`;

    target.innerHTML = html;
}

// ---------- 期間集計：送信 ----------
document.addEventListener('DOMContentLoaded', () => {
  const f = document.getElementById('range-form');
  if (f) f.addEventListener('submit', onRangeFormSubmit);
});

async function onRangeFormSubmit(e){
  e.preventDefault();
  const start  = document.getElementById('range-start').value;
  const end    = document.getElementById('range-end').value;
  const helpno = document.getElementById('helper-id')?.textContent.trim();
  if (!helpno || helpno === '未選択') return alert('作業者IDが取得できません。');
  if (!start || !end) return alert('期間を選択してください。');

  let res;
  try{
    res = await fetchJSON(`{{ url('/time_study/summary') }}`, { helpno, start_date:start, end_date:end });
  }catch(err){
    console.error(err);
    alert('集計データの取得に失敗しました：' + err.message);
    return;
  }
  renderTaskDayMatrix(res);
  document.getElementById('matrix-card').style.display = 'block';
}

// ---------- fetchJSON 共通 ----------
async function fetchJSON(url, payload){
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const resp = await fetch(url,{
    method:'POST',
    headers:{
      'Content-Type':'application/json',
      'X-CSRF-TOKEN': csrf,
      'Accept':'application/json'
    },
    credentials:'same-origin',
    body: JSON.stringify(payload||{})
  });
  const raw = await resp.text();
  const ct  = resp.headers.get('content-type')||'';
  if(!resp.ok){ console.error(raw); throw new Error('HTTP '+resp.status); }
  if(!ct.includes('application/json')){ console.error(raw); throw new Error('Server returned non-JSON'); }
  return JSON.parse(raw);
}

// ---------- 表を描画 ----------
function renderTaskDayMatrix(res){
  const el = document.getElementById('task-day-matrix');
  if (!el) return;

  const days = res?.days || [];
  if (!days.length){
    el.innerHTML = '<div class="text-muted">該当データがありません。</div>';
    return;
  }

  const groups = [
    { title:'直接業務',  key:'directByTask',   cls:'bg-direct'   },
    { title:'間接業務',  key:'indirectByTask', cls:'bg-indirect' },
  ];
  if (res.otherByTask && Object.keys(res.otherByTask).length){
    groups.push({ title:'その他業務', key:'otherByTask', cls:'bg-other' });
  }

  // ヘッダ
  let html = '<table class="matrix-table"><thead><tr>';
  html += '<th class="matrix-group-cell"></th><th class="matrix-task-cell">作業名</th>';
  html += days.map(d => {
    const dt = new Date(d.replace(/-/g,'/'));
    return `<th class="matrix-num-cell">${dt.getMonth()+1}/${dt.getDate()} 計測</th>`;
  }).join('');
  html += '</tr></thead><tbody>';

  // 日別合計
  const dayTotals = new Array(days.length).fill(0);

  // 各グループ
  groups.forEach(g => {
    const dict = res[g.key] || {};
    const tasks = Object.keys(dict);
    if (!tasks.length){
      html += `<tr>
                <td class="matrix-group-cell ${g.cls}">`+g.title+`</td>
                <td class="matrix-task-cell text-muted">-</td>
                ${days.map(()=>'<td class="matrix-num-cell"></td>').join('')}
              </tr>`;
      return;
    }

    tasks.forEach((task, i) => {
      const arr = dict[task] || [];
      html += '<tr>';
      if (i===0){
        html += `<td class="matrix-group-cell ${g.cls}" rowspan="${tasks.length}">${g.title}</td>`;
      }
      html += `<td class="matrix-task-cell">${task}</td>`;
      for(let di=0; di<days.length; di++){
        const v = Math.round(arr[di] || 0);
        if (v>0) dayTotals[di] += v;
        html += `<td class="matrix-num-cell">${v ? v : ''}</td>`;
      }
      html += '</tr>';
    });
  });

  // 最下段 合計
  html += `<tr class="matrix-total-row">
             <td class="matrix-group-cell"></td>
             <td class="matrix-task-cell">合計</td>
             ${dayTotals.map(v=>`<td class="matrix-num-cell">${v||''}</td>`).join('')}
           </tr>`;

  html += '</tbody></table>';
  el.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => {
  // ① 残留オーバーレイを確実に無効化
  ['pop_alert_back','pop_alert','policy_check','policy_dailog','pop_csvimport','pop_csvimport_confirm']
    .forEach(id => {
      const el = document.getElementById(id);
      if (el) {
        el.style.display = 'none';
        el.style.visibility = 'hidden';
        el.style.pointerEvents = 'none';
        el.style.zIndex = '-1';
      }
    });

  // ② 期間集計フォームの submit を必ずバインド（重複防止フラグ付き）
  const rangeForm = document.getElementById('range-form');
  if (rangeForm && !rangeForm.__bound) {
    rangeForm.addEventListener('submit', onRangeFormSubmit);
    rangeForm.__bound = true;
  }

  // ③ 1日表示フォームも念のため
  const graphForm = document.getElementById('graph-form');
  if (graphForm && !graphForm.__bound) {
    graphForm.addEventListener('submit', onGraphFormSubmit);
    graphForm.__bound = true;
  }
});

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

#graph-flex-wrap {
  display: flex;
  align-items: flex-start;
}
#mini-graph-area {
  min-width: 3200px;
  margin-left: 40px;
}
.matrix-scroll { overflow-x:auto; }

.matrix-table{
  border-collapse:collapse;
  width:max-content;
  min-width:100%;
  table-layout:fixed;
  font-size:14px;
}
.matrix-table th,.matrix-table td{
  border:1px solid #8a8a8a;
  padding:6px 10px;
  text-align:center;
  white-space:nowrap;
}

.matrix-table thead th{
  background:#fafafa;
  font-weight:700;
}

.matrix-group-cell{
  width:120px;
  font-weight:700;
  color:#c33;
  text-align:center;
  vertical-align:middle;
}
.matrix-task-cell{ width:180px; text-align:left; background:#fff; }
.matrix-num-cell{ width:90px; }

.matrix-total-row .matrix-task-cell,
.matrix-total-row .matrix-num-cell{
  background:#f7f7df;
  font-weight:700;
}

/* グループ帯の色 */
.bg-direct{  background:#f7e4d8; }
.bg-indirect{background:#d7ecf7; }
.bg-other{   background:#e8f4e6; }

/* クリックしたい領域を常に最前面へ */
#graph-form,
#range-form,
#matrix-card,
#time-table-section,
#daily-summary-section {
  position: relative;
  z-index: 9999;
}
#range-form * { pointer-events: auto; }

/* 残りがちなオーバーレイを強制無効化 */
#pop_alert_back,
#pop_alert,
#policy_check,
#policy_dailog,
#pop_csvimport,
#pop_csvimport_confirm {
  display: none !important;
  visibility: hidden !important;
  pointer-events: none !important;
  z-index: -1 !important;
}

</style>
@endsection
