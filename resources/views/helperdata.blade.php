@extends('layouts.parent')

@section('content')
<!-- Chart.js（公式CDN） -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<div class="allcont">
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <h2>作業者データ表示</h2>

        <!-- 基本情報 -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <strong>施設名:</strong>
                <span id="facility-name">{{ isset($data2[0]) ? $data2[0]['facility'] : '未選択' }}</span>
              </div>
              <div class="col-md-6">
                <strong>作業者名:</strong>
                <span id="helper-name">{{ isset($data2[0]) ? $data2[0]['helpername'] : '未選択' }}</span>
              </div>
              <div class="col-md-6">
                <strong>作業者ID:</strong>
                <span id="helper-id">{{ isset($data2[0]) ? $data2[0]['Helper_id'] : '未選択' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 1日表示 条件 -->
        <div class="card mb-4">
          <div class="card-body">
            <form id="graph-form">
              <div class="row">
                <div class="col-md-4">
                  <label for="selected-date">年月日</label>
                  <input type="date" id="selected-date" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="graph-type">表示タイプ</label>
                  <select id="graph-type" class="form-control">
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

        <!-- 1日表示エリア -->
        <div id="time-table-section" class="ts-section" style="display:none;">
          <div style="display:flex; align-items:flex-start;">
            <!-- 左：時間テーブル -->
            <div style="flex:1;">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">作業時間表</h5>
                  <div id="graph-legend" style="margin-bottom:8px;"></div>
                  <div id="graph-error" class="alert alert-danger" style="display:none;"></div>
                  <div id="graph-container">
                    <div id="timeTableArea"></div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 右：ミニグラフ -->
            <div id="mini-graph-area" style="min-width:320px; margin-left:40px;"></div>
          </div>
        </div>

        <!-- ▼ 期間指定 集計フォーム -->
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

        <!-- ▼ 下段：日付 × 作業名 マトリクス -->
        <div id="task-day-matrix-section" class="ts-section" style="display:none;">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">日付 × 作業名 マトリクス</h5>
              <div id="taskDayMatrixWrap">
                <div id="taskDayMatrix"></div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container -->
</div><!-- /.allcont -->

<!-- ▼ 月別 集計（複数月） -->
<div id="monthly-sum-section" class="ts-section">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">月別 集計（複数選択可）</h5>

      <div class="row g-2 align-items-end" id="month-picker-row">
        <div class="col-sm-3">
          <label class="form-label">年月</label>
          <input type="month" class="form-control month-input" />
        </div>
        <div class="col-sm-auto">
          <button type="button" id="btn-add-month" class="btn btn-outline-primary">＋ 追加</button>
        </div>
        <div class="col-sm-auto">
          <button type="button" id="btn-run-monthly" class="btn btn-success">集計</button>
        </div>
      </div>

      <div id="monthly-graphs" class="mt-4"></div>
    </div>
  </div>
</div>

<script>
/* =========================
   定数
========================= */
const URL_GRAPH   = `{{ url('/get_graph_data') }}`;
const URL_SUMMARY = `{{ url('/time_study/summary') }}`;

/* =========================
   状態
========================= */
let timeGraph = null; // 予備（破棄用）
let sumByTypeChart = null, perTaskDirectChart = null, perTaskIndirectChart = null;

/* =========================
   初期化
========================= */
document.addEventListener('DOMContentLoaded', () => {
  // クリックを邪魔する残留オーバーレイを確実に無効化
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

  // 初期凡例
  renderLegend(document.getElementById('graph-type')?.value || 'type');

  // イベント（1回だけ）
  const graphForm = document.getElementById('graph-form');
  if (graphForm) graphForm.addEventListener('submit', onGraphFormSubmit);

  const rangeForm = document.getElementById('range-form');
  if (rangeForm) rangeForm.addEventListener('submit', onRangeFormSubmit);

  // 表示タイプ変更 → 凡例更新
  document.getElementById('graph-type')?.addEventListener('change', function () {
    renderLegend(this.value);
  });
});

/* =========================
   共通ユーティリティ
========================= */
function toDecimalTime(datetimeStr) {
  // "YYYY-MM-DD HH:MM:SS" → 9.5 など
  const time = datetimeStr.split(' ')[1].split(':');
  return parseInt(time[0], 10) + parseInt(time[1], 10) / 60 + parseInt(time[2], 10)/3600;
}

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
    body: JSON.stringify(payload || {})
  });

  const raw = await resp.text();
  const ct  = resp.headers.get('content-type') || '';

  if (!resp.ok) {
    console.error('Server error body:\n', raw);
    throw new Error('HTTP ' + resp.status);
  }
  if (!ct.includes('application/json')) {
    console.error('Non-JSON response:\n', raw);
    throw new Error('Server returned non-JSON');
  }
  return JSON.parse(raw);
}

/* =========================
   1日表示：凡例
========================= */
function renderLegend(graphType) {
  const legendEl = document.getElementById('graph-legend');
  if (!legendEl) return;
  legendEl.innerHTML =
    graphType === 'type'
      ? `<span class="legend-box" style="background:rgba(255,165,0,0.8);"></span>直接
         <span class="legend-box" style="background:rgba(135,206,235,0.8);margin-left:24px;"></span>間接
         <span class="legend-box" style="background:rgba(200,200,200,0.8);margin-left:24px;"></span>その他`
      : `<span class="legend-box" style="background:rgba(255,70,70,0.8);"></span>肉体的負担
         <span class="legend-box" style="background:rgba(180,80,255,0.8);margin-left:24px;"></span>精神的負担
         <span class="legend-box" style="background:rgba(200,200,200,0.8);margin-left:24px;"></span>その他`;
}

/* =========================
   1日表示：submit
========================= */
async function onGraphFormSubmit(e) {
  e.preventDefault();

  const section = document.getElementById('time-table-section');
  if (section) section.style.display = 'block';

  const errBox = document.getElementById('graph-error');
  errBox.style.display = 'block';
  errBox.style.color   = '#0066cc';
  errBox.textContent   = 'データを取得中...';

  const selectedDate = document.getElementById('selected-date').value;
  const graphType    = document.getElementById('graph-type').value;
  const helpno       = document.getElementById('helper-id')?.textContent.trim();

  if (!helpno || helpno === '未選択') { errBox.style.display='none'; return alert('作業者IDが取得できません。'); }
  if (!selectedDate) { errBox.style.display='none'; return alert('年月日を選択してください。'); }

  let data;
  try {
    data = await fetchJSON(URL_GRAPH, { helpno, selected_date: selectedDate, graph_type: graphType });
  } catch (err) {
    errBox.style.color = '#dc3545';
    errBox.textContent = 'データの取得に失敗しました: ' + err.message + '（コンソールを確認）';
    return;
  }

  errBox.style.display = 'none';
  createTimeTable(data);
}

/* =========================
   1日表示：テーブル描画＋ミニグラフ
========================= */
function createTimeTable(data) {
  const graphType = document.getElementById('graph-type')?.value || 'type';
  renderLegend(graphType);

  const colorMapType     = { 0: "rgba(255,165,0,0.8)", 1: "rgba(135,206,235,0.8)", 2: "rgba(200,200,200,0.8)" };
  const colorMapCategory = { 0: "rgba(255,70,70,0.8)", 1: "rgba(180,80,255,0.8)", 2: "rgba(200,200,200,0.8)" };

  let html = `<table class="time-table"><thead><tr><th>作業名</th>`;
  for (let h=0; h<24; h++) html += `<th>${h}:00</th>`;
  html += `<th>合計</th></tr></thead><tbody>`;

  for (const task of data.taskNames) {
    html += `<tr><td>${task}</td>`;
    const intervals = data.taskIndividualDurations[task] || [];
    let totalMinutes = 0;

    for (let h=0; h<24; h++) {
      let cellContent = '';
      let isStopCell = false;
      let minutes = 0;
      let dTarget = null;

      for (const d of intervals) {
        const s = toDecimalTime(d.start);
        const e = toDecimalTime(d.stop);
        if (s < h+1 && e > h) {
          dTarget = d;
          if (e > h && e <= h+1) { isStopCell = true; minutes = Math.round((e - s) * 60); }
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

        let color = "rgba(200,200,200,0.7)";
        if (graphType === "type")     color = colorMapType[dTarget.task_type_no]         || color;
        if (graphType === "category") color = colorMapCategory[dTarget.task_category_no] || color;

        let minutesHtml = '';
        if (isStopCell) {
          minutesHtml = `<span style="position:absolute;top:2px;left:calc(${left + width}% + 2px);font-size:13px;color:#222;">${minutes}</span>`;
        }
        cellContent = `
          <div style="position:relative;width:100%;height:100%;">
            <div style="position:absolute;top:0;left:${left}%;width:${width}%;height:100%;background:${color};"></div>
            ${minutesHtml}
          </div>`;
      }
      html += `<td style="position:relative;width:32px;height:26px;padding:0;">${cellContent}</td>`;
    }

    for (const d of intervals) totalMinutes += Math.round((toDecimalTime(d.stop) - toDecimalTime(d.start)) * 60);
    html += `<td style="background:#f8f8e9;font-weight:bold;min-width:60px;">${totalMinutes}分</td>`;
    html += `</tr>`;
  }

  html += `</tbody></table>`;
  document.getElementById('timeTableArea').innerHTML = html;

  drawMiniGraph(data);
}

function drawMiniGraph(data) {
  const graphType = document.getElementById('graph-type')?.value || 'type';
  const target = document.getElementById('mini-graph-area');
  if (!target) return;

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

  const totalMinutes = [0,0,0];
  for (const task of data.taskNames) {
    const intervals = data.taskIndividualDurations[task] || [];
    for (const d of intervals) {
      const no  = graphType === "type" ? d.task_type_no : d.task_category_no;
      const min = Math.round((toDecimalTime(d.stop) - toDecimalTime(d.start)) * 60);
      totalMinutes[no] += min;
    }
  }
  const maxMinutes = Math.max(...totalMinutes, 1);

  let html = `<div style="border:2px solid #111;border-radius:18px;padding:16px 20px;background:#fff;">
                <div style="font-weight:bold;margin-bottom:16px;">${meta.title}</div>`;
  meta.items.forEach(item => {
    const min = totalMinutes[item.key] || 0;
    const h = Math.floor(min/60), m = min%60;
    const timeLabel = (h ? h+'時間' : '') + m + '分';
    const barLen = Math.round((min/maxMinutes)*150);
    html += `<div style="display:flex;align-items:center;margin-bottom:24px;gap:12px;">
               <span style="width:20px;height:20px;border-radius:50%;background:${item.dot};flex-shrink:0;"></span>
               <span style="width:110px;white-space:nowrap;font-size:1.05em;color:#6a6d6d;font-weight:600;">${item.label}</span>
               <span style="min-width:100px;white-space:nowrap;font-size:1.1em;font-weight:500;color:#656969;text-align:right;">${timeLabel}</span>
               <div style="height:24px;width:${barLen}px;background:${item.color};border-radius:12px;margin-left:16px;flex-shrink:0;"></div>
             </div>`;
  });
  html += `</div>`;
  target.innerHTML = html;
}

/* =========================
   期間集計：submit
========================= */
async function onRangeFormSubmit(e){
  e.preventDefault();

  const start  = document.getElementById('range-start').value;
  const end    = document.getElementById('range-end').value;
  const helpno = document.getElementById('helper-id')?.textContent.trim();

  if (!helpno || helpno === '未選択') return alert('作業者IDが取得できません。');
  if (!start || !end)   return alert('期間を選択してください。');

  let res;
  try{
    res = await fetchJSON(URL_SUMMARY, { helpno, start_date:start, end_date:end });
  }catch(err){
    console.error(err);
    return alert('集計データの取得に失敗しました：' + err.message);
  }

  renderTaskDayMatrix(res);
  document.getElementById('task-day-matrix-section').style.display = 'block';
}

/* =========================
   マトリクス描画（列=各日付、行=作業名、値=分）
========================= */
function renderTaskDayMatrix(res) {
  const days = (res?.days || []).map(d => {
    // "YYYY-MM-DD" → "M/D<br>計測"
    const [y,m,dd] = d.split('-');
    return `${(+m)}/${(+dd)}<br>計測`;
  });

  const groups = [
    { name: '直接業務',   key: 'directByTask',   bgClass: 'bg-direct'   },
    { name: '間接業務',   key: 'indirectByTask', bgClass: 'bg-indirect' },
    { name: 'その他業務', key: 'otherByTask',    bgClass: 'bg-other'    },
  ];

  const dicts = {
    directByTask:   res?.directByTask   || {},
    indirectByTask: res?.indirectByTask || {},
    otherByTask:    res?.otherByTask    || {}
  };

  const colTotals = new Array(days.length).fill(0);
  let html = `<table class="table-matrix">
    <thead>
      <tr>
        <th class="sticky-top sticky-col-1 col-group"></th>
        <th class="sticky-top sticky-col-2 col-task">作業名</th>
        ${days.map(d => `<th class="sticky-top">${d}</th>`).join('')}
      </tr>
    </thead>
    <tbody>`;

  groups.forEach(g => {
    const entries = Object.entries(dicts[g.key]); // [task, [m1,m2,...]]
    if (!entries.length) return;

    entries.forEach(([task, arr]) => {
      html += `<tr>
        <td class="sticky-col-1 matrix-group-cell ${g.bgClass}">${g.name}</td>
        <td class="sticky-col-2">${task}</td>
        ${days.map((_,i) => {
          const v = parseInt((arr && arr[i]) || 0, 10) || 0;
          if (v) colTotals[i] += v;
          return `<td>${v || ''}</td>`;
        }).join('')}
      </tr>`;
    });
  });

  html += `</tbody><tfoot><tr>
    <th class="sticky-col-1"></th>
    <th class="sticky-col-2">合計</th>
    ${colTotals.map(v => `<th>${v || 0}</th>`).join('')}
  </tr></tfoot></table>`;

  const mount = document.getElementById('taskDayMatrix');
  if (mount) mount.innerHTML = html;
}

/* ========= 月別集計 ========= */
const MONTHLY_URL_SUMMARY = `{{ url('/time_study/summary') }}`;
// 残業を出したい場合は所定時間を時間(h)で設定。使わないなら null のまま
const MONTHLY_STANDARD_HOURS = null; // 例: 160 にすると「残業」表示が出ます

// ＋ボタン
document.getElementById('btn-add-month')?.addEventListener('click', () => {
  const wrap = document.getElementById('month-picker-row');
  const col  = document.createElement('div');
  col.className = 'col-sm-3';
  col.innerHTML = `
    <label class="form-label d-sm-none d-block">年月</label>
    <div class="d-flex gap-2">
      <input type="month" class="form-control month-input" />
      <button type="button" class="btn btn-outline-danger btn-month-remove" title="削除">×</button>
    </div>`;
  wrap.insertBefore(col, wrap.querySelector('#btn-add-month').parentElement);
  col.querySelector('.btn-month-remove').addEventListener('click', () => col.remove());
});

// 集計ボタン
document.getElementById('btn-run-monthly')?.addEventListener('click', runMonthlySummary);

async function runMonthlySummary(){
  const helpno = document.getElementById('helper-id')?.textContent.trim();
  if (!helpno || helpno === '未選択') return alert('作業者IDが取得できません。');

  // 入力された年月（重複除去）
  const months = [...document.querySelectorAll('.month-input')]
    .map(i => (i.value || '').trim())
    .filter(Boolean);
  if (!months.length) return alert('年月を入力してください。');

  const uniq = [...new Set(months)];
  // 表示クリア
  const mount = document.getElementById('monthly-graphs');
  mount.innerHTML = '';

  for (const ym of uniq){
    const {start, end, labelJP} = monthToRange(ym); // YYYY-MM -> その月の 1日〜末日
    let res;
    try{
      res = await fetchJSON(MONTHLY_URL_SUMMARY, { helpno, start_date:start, end_date:end });
    }catch(e){
      const err = document.createElement('div');
      err.className = 'alert alert-danger';
      err.textContent = `${labelJP} の集計取得に失敗: ${e.message}`;
      mount.appendChild(err);
      continue;
    }

    // ---- 月合計（分）を算出 ----
    const sum = a => (a||[]).reduce((p,c)=>p+(+c||0),0);
    const directMin   = sum(res?.directTotals);
    const indirectMin = sum(res?.indirectTotals);

    // other は API に total が無い前提：otherByTask を足し合わせる
    let otherMin = 0;
    const otherDict = res?.otherByTask || {};
    Object.keys(otherDict).forEach(task => { otherMin += sum(otherDict[task] || []); });

    const totalMin = directMin + indirectMin + otherMin;

    // ---- 描画 ----
    mount.appendChild(buildMonthlyBar({
      labelJP,
      directMin,
      indirectMin,
      otherMin,
      totalMin,
      overtimeMin: (MONTHLY_STANDARD_HOURS != null)
        ? Math.max(0, totalMin - MONTHLY_STANDARD_HOURS * 60)
        : null
    }));
  }
}

// YYYY-MM -> その月の範囲と表示ラベル
function monthToRange(ym){
  const [y,m] = ym.split('-').map(n=>+n);
  const last = new Date(y, m, 0).getDate(); // 翌月0日=当月末
  return {
    start: `${y}-${String(m).padStart(2,'0')}-01`,
    end:   `${y}-${String(m).padStart(2,'0')}-${String(last).padStart(2,'0')}`,
    labelJP: `${y}年 ${m}月`
  };
}

function fmtHour(min){
  const h = min/60;
  return (Math.round(h*10)/10).toString().replace(/\.0$/,'') + 'h';
}

// 月の横棒グラフ DOM を作る
function buildMonthlyBar({labelJP, directMin, indirectMin, otherMin, totalMin, overtimeMin}){
  const wrap = document.createElement('div');
  wrap.className = 'month-graph';

  // 見出し
  const caption = document.createElement('div');
  caption.className = 'month-caption';
  caption.textContent = labelJP;
  wrap.appendChild(caption);

  // バー本体
  const bar = document.createElement('div');
  bar.className = 'month-bar';
  if (totalMin > 0){
    [
      {cls:'seg-direct',  name:'直接業務',  min:directMin},
      {cls:'seg-indirect',name:'間接業務',  min:indirectMin},
      {cls:'seg-other',   name:'その他',    min:otherMin},
    ].forEach(seg=>{
      if (!seg.min) return;
      const div = document.createElement('div');
      div.className = `month-seg ${seg.cls}`;
      div.style.flexBasis = (seg.min/totalMin*100) + '%';
      div.innerHTML = `<span>${seg.name}&nbsp;&nbsp;${fmtHour(seg.min)}</span>`;
      bar.appendChild(div);
    });
  }else{
    const empty = document.createElement('div');
    empty.className = 'month-empty';
    empty.textContent = 'データなし';
    bar.appendChild(empty);
  }
  wrap.appendChild(bar);

  // 右側テキスト
  const meta = document.createElement('div');
  meta.className = 'month-meta';
  meta.textContent = `合計：${fmtHour(totalMin)}` + (overtimeMin!=null ? `　残業：${fmtHour(overtimeMin)}` : '');
  wrap.appendChild(meta);

  return wrap;
}

document.addEventListener('DOMContentLoaded', () => {
  // 画面の一番メインのカラム（col-md-12）の末尾へ monthly を移動
  const monthly = document.getElementById('monthly-sum-section');
  const mainCol = document.querySelector('.allcont .container .row .col-md-12');
  if (monthly && mainCol && mainCol.lastElementChild !== monthly) {
    mainCol.appendChild(monthly);
  }
});


</script>

<style>
/* 共通 */
.card { margin-bottom:20px; box-shadow:0 2px 4px rgba(0,0,0,0.1); }
.card-title { color:#333; font-weight:bold; }

/* 1日表示：テーブル */
#graph-container {
  position: relative;
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  padding: 10px;
  min-height: 600px;
  overflow-x: auto;
}
.time-table { border-collapse: collapse; width: 100%; }
.time-table th, .time-table td { border:1px solid #aaa; padding:0; background:#fff; box-sizing:border-box; }
.time-table th { background:#f0f0fa; font-weight:bold; }
.time-table tr td:first-child { background:#f5f5f5; font-weight:bold; min-width:90px; }

/* 凡例 */
.legend-box {
  display:inline-block; width:40px; height:16px; border-radius:0;
  vertical-align:middle; margin-right:4px; border:1px solid #ccc; box-sizing:border-box;
}

/* セクションは縦積み（重なり防止） */
.ts-section { position:relative; z-index:1; margin-top:28px; clear:both; }

/* クリックしたい領域を前面に */
#graph-form, #range-form, #time-table-section, #task-day-matrix-section { position:relative; z-index:999; }

/* ===== 罫線をクッキリ表示（上書き） ===== */

/* 集計マトリクス（列=日付 × 行=作業名） */
#taskDayMatrixWrap { border: 2px solid #333 !important; }  /* 外枠も濃く */
.table-matrix { border-collapse: collapse !important; }
.table-matrix th,
.table-matrix td {
  border-color: #333 !important;   /* 線色を濃く */
  border-width: 2px !important;    /* 線を太く */
  border-style: solid !important;
}

/* sticky ヘッダ/左列の接合部も太線で統一 */
.table-matrix thead th.sticky-top { border-bottom-width: 2px !important; }
.table-matrix .sticky-col-1 { border-right-width: 2px !important; }
.table-matrix .sticky-col-2 { border-right-width: 2px !important; }

/* 1日表示の時間テーブルも同様にクッキリ */
#graph-container { border: 2px solid #333 !important; }    /* 外枠 */
.time-table { border-collapse: collapse !important; }
.time-table th,
.time-table td {
  border-color: #333 !important;
  border-width: 2px !important;
  border-style: solid !important;
}

/* 見出しのコントラストも少し上げる（任意） */
.table-matrix thead th.sticky-top { background: #eef1ff !important; }
.time-table th { background: #e9ecef !important; }

/* 交互行で視認性アップ（任意） */
.table-matrix tbody tr:nth-child(odd) td:not(.sticky-col-1):not(.sticky-col-2) {
  background: #fcfcfc !important;
}

/* グループ帯の色 */
.bg-direct  { background:#fde9df !important; }
.bg-indirect{ background:#dff1fb !important; }
.bg-other   { background:#eaf5e5 !important; }

/* 余分な余白をなくす：内容の高さに合わせる */
#graph-container{
  height: auto !important;      /* ← 固定高さを解除 */
  min-height: 0 !important;     /* ← 最小高さを解除 */
  overflow-x: auto;              /* 横スクロールは維持 */
  overflow-y: visible;           /* 縦は内容に合わせて伸縮 */
  padding-bottom: 8px;           /* 下の余白も少しだけに */
}

/* 表の下マージンがあれば消す（Bootstrap対策） */
#timeTableArea table{ margin-bottom: 0 !important; }

/* カード下の余白を少しだけに（必要なら） */
#time-table-section .card{ margin-bottom: 12px !important; }

/* ===== 月別 横棒グラフ ===== */
.month-graph{
  display:flex;
  align-items:center;
  gap:28px;
  margin:28px 0 40px;
}
.month-caption{
  border:3px solid #1e2a35;
  padding:6px 16px;
  font-weight:800;
  font-size:22px;
  color:#1e2a35;
  border-radius:6px;
  min-width:110px;
  text-align:center;
}
.month-bar{
  display:flex;
  align-items:stretch;
  min-width:420px;           /* お好みで */
  max-width:720px;
  width:60%;
  border:3px solid #1e2a35;
  border-radius:4px;
  overflow:hidden;
  background:#fff;
}
.month-seg{
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:700;
  padding:6px 10px;
  border-right:3px solid #1e2a35;
  white-space:nowrap;
}
.month-seg:last-child{ border-right:none; }

/* 色は既存に寄せた柔らかいトーン */
.seg-direct  { background:#fde9df; }
.seg-indirect{ background:#dff1fb; }
.seg-other   { background:#e6f3de; }

.month-empty{
  padding:8px 12px;
  color:#777;
}
.month-meta{
  font-size:18px;
  font-weight:700;
  color:#1e2a35;
  min-width:220px;
}

/* 一番下に積まれて、他と重ならないように */
#monthly-sum-section{
  position: relative;
  z-index: 1;        /* クリックできるが前面に出過ぎない */
  margin-top: 28px;
  clear: both;       /* 左右のフロート/横並びの下に回り込む */
}

/* クリック優先にしたいセクションの z-index をそろえる（上げすぎ注意） */
#graph-form,
#range-form,
#time-table-section,
#task-day-matrix-section,
#monthly-sum-section{
  position: relative;
  z-index: 2;
}

/* 月グラフの入れ物は縦並びに */
#monthly-graphs{
  display: flex;
  flex-direction: column;
  gap: 28px;
}

</style>
@endsection
