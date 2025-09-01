{{-- resources/views/comparison.blade.php --}}
@extends('layouts.parent')

@section('content')
<div class="allcont">
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <h2>データ比較</h2>

        {{-- ▼ 作業者選択（A/B） --}}
        <form class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label">作業者A</label>
            <select id="helper_a" name="helper_a" class="form-control">
              <option value="">未選択</option>
              @foreach(($helpers ?? []) as $h)
                <option value="{{ $h->id }}" {{ (isset($helperA) && (int)$helperA === (int)$h->id) ? 'selected' : '' }}>
                  {{ $h->helpername }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">作業者B</label>
            <select id="helper_b" name="helper_b" class="form-control">
              <option value="">未選択</option>
              @foreach(($helpers ?? []) as $h)
                <option value="{{ $h->id }}" {{ (isset($helperB) && (int)$helperB === (int)$h->id) ? 'selected' : '' }}>
                  {{ $h->helpername }}
                </option>
              @endforeach
            </select>
          </div>
        </form>

        {{-- ▼ 期間（A/B 共通）＋ 表示切替ボタン --}}
        <div class="card mb-4">
          <div class="card-body">
            <form id="range-form" class="row g-3 align-items-end">
              <div class="col-md-3">
                <label for="range-start" class="form-label">期間(開始)</label>
                <input type="date" id="range-start" class="form-control" required value="{{ $start ?? '' }}">
              </div>
              <div class="col-md-3">
                <label for="range-end" class="form-label">期間(終了)</label>
                <input type="date" id="range-end" class="form-control" required value="{{ $end ?? '' }}">
              </div>

              <div class="col-md-3">
                <button type="submit" class="btn btn-secondary w-100">集計</button>
              </div>

              <div class="col-md-3">
                <div class="btn-group w-100" role="group" aria-label="表示切替">
                  <button type="button" class="btn btn-outline-primary active" id="btn-mode-type" data-mode="type">介護種別</button>
                  <button type="button" class="btn btn-outline-primary" id="btn-mode-category" data-mode="category">カテゴリ</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        {{-- ▼ 結果（A の下に B） --}}
        <div id="result-stack" class="result-stack" style="display:none;">

          {{-- A --}}
          <div class="ts-section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">作業者A：<span id="nameA">未選択</span></h5>
                <div id="tableA"></div>
              </div>
            </div>
          </div>

          {{-- B --}}
          <div class="ts-section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">作業者B：<span id="nameB">未選択</span></h5>
                <div id="tableB"></div>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</div>

<script>
/* =========================
   定数
========================= */
const URL_SUMMARY = `{{ url('/time_study/summary') }}`;

/* =========================
   状態（直近の取得結果と表示モード）
========================= */
let lastResA = null;
let lastResB = null;
let currentMode = 'type'; // 'type' or 'category'

/* =========================
   共通 fetch
========================= */
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
   初期化
========================= */
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('range-form')?.addEventListener('submit', onRangeFormSubmit);

  // 表示切替ボタン
  const btnType = document.getElementById('btn-mode-type');
  const btnCat  = document.getElementById('btn-mode-category');

  btnType?.addEventListener('click', () => {
    currentMode = 'type';
    btnType.classList.add('active');
    btnCat.classList.remove('active');
    rerenderIfCached();
  });

  btnCat?.addEventListener('click', () => {
    currentMode = 'category';
    btnCat.classList.add('active');
    btnType.classList.remove('active');
    rerenderIfCached();
  });
});

function rerenderIfCached(){
  // 直近の取得結果があれば再描画のみ（再フェッチしない）
  if (lastResA) renderMatrix('tableA', lastResA, currentMode);
  if (lastResB) renderMatrix('tableB', lastResB, currentMode);
  if (lastResA || lastResB) document.getElementById('result-stack').style.display = 'block';
}

/* =========================
   期間集計 submit（A/B まとめて）
========================= */
async function onRangeFormSubmit(e){
  e.preventDefault();

  const start = document.getElementById('range-start').value;
  const end   = document.getElementById('range-end').value;

  const selA = document.getElementById('helper_a');
  const selB = document.getElementById('helper_b');
  const helperA = selA?.value || '';
  const helperB = selB?.value || '';
  const nameA   = selA?.options[selA.selectedIndex]?.text?.trim() || '未選択';
  const nameB   = selB?.options[selB.selectedIndex]?.text?.trim() || '未選択';

  if (!start || !end) return alert('期間を選択してください。');
  if (!helperA && !helperB) return alert('作業者A か B を選択してください。');

  // タイトル更新
  document.getElementById('nameA').textContent = nameA;
  document.getElementById('nameB').textContent = nameB;

  // 初期化
  document.getElementById('tableA').innerHTML = '';
  document.getElementById('tableB').innerHTML = '';
  lastResA = null; lastResB = null;

  // 並列取得
  const jobs = [];
  if (helperA) jobs.push(fetchJSON(URL_SUMMARY, { helpno: helperA, start_date: start, end_date: end }).then(res => ({who:'A', res})));
  if (helperB) jobs.push(fetchJSON(URL_SUMMARY, { helpno: helperB, start_date: start, end_date: end }).then(res => ({who:'B', res})));

  let results = [];
  try {
    results = await Promise.all(jobs);
  } catch (err) {
    console.error(err);
    return alert('集計データの取得に失敗しました：' + err.message);
  }

  // 表示
  document.getElementById('result-stack').style.display = 'block';
  for (const r of results){
    if (r.who === 'A') { lastResA = r.res; renderMatrix('tableA', r.res, currentMode); }
    if (r.who === 'B') { lastResB = r.res; renderMatrix('tableB', r.res, currentMode); }
  }
  if (!helperA) document.getElementById('tableA').innerHTML = '<div class="alert alert-light">未選択</div>';
  if (!helperB) document.getElementById('tableB').innerHTML = '<div class="alert alert-light">未選択</div>';
}

/* =========================
   列＝日付 / 行＝3分類 のマトリクス
   mode: 'type' or 'category'
========================= */
function renderMatrix(mountId, res, mode){
  const days = res?.days || [];
  if (!Array.isArray(days) || days.length === 0){
    const mount = document.getElementById(mountId);
    if (mount) mount.innerHTML = '<div class="alert alert-warning">データなし</div>';
    return;
  }

  // データセット切替
  let rows, colors;
  if (mode === 'category'){
    rows = [
      { key: 'physical', label: '肉体的負担',  arr: res?.physicalTotals || [],        className: 'row-physical',  dot: '#ff4d4f' },
      { key: 'mental',   label: '精神的負担',  arr: res?.mentalTotals || [],          className: 'row-mental',    dot: '#8a63d2' },
      { key: 'other',    label: 'その他',      arr: res?.otherTotalsCategory || [],   className: 'row-gray',      dot: '#bfbfbf' },
    ];
  } else {
    rows = [
      { key: 'direct',   label: '直接',        arr: res?.directTotals || [],          className: 'row-direct',    dot: '#ffa500' },
      { key: 'indirect', label: '間接',        arr: res?.indirectTotals || [],        className: 'row-indirect',  dot: '#8fd3ff' },
      { key: 'other',    label: 'その他',      arr: res?.otherTotals || [],           className: 'row-gray',      dot: '#bfbfbf' },
    ];
  }

  const fmtMD = (ymd) => {
    const [y,m,d] = (ymd||'').split('-');
    if (!m || !d) return ymd || '';
    return `${(+m)}/${(+d)}`;
  };
  const sum = arr => (arr||[]).reduce((p,c)=>p+(parseInt(c,10)||0),0);

  // 日合計（列合計）
  const dayTotals = days.map((_, i) =>
    rows.reduce((acc, r) => acc + (parseInt(r.arr[i]||0,10) || 0), 0)
  );

  let html = `<table class="cat-matrix">
    <thead>
      <tr>
        <th class="sticky-left">カテゴリー</th>
        ${days.map(d => `<th>${fmtMD(d)}</th>`).join('')}
        <th>合計</th>
      </tr>
    </thead>
    <tbody>`;

  rows.forEach(r => {
    html += `<tr class="${r.className}">
      <th class="sticky-left">
        <span class="dot" style="background:${r.dot};"></span>${r.label}
      </th>
      ${days.map((_,i)=>`<td class="num">${parseInt(r.arr[i]||0,10)}</td>`).join('')}
      <td class="num">${sum(r.arr)}</td>
    </tr>`;
  });

  html += `</tbody>
    <tfoot>
      <tr>
        <th class="sticky-left">日合計</th>
        ${dayTotals.map(v=>`<th class="num">${v}</th>`).join('')}
        <th class="num">${sum(dayTotals)}</th>
      </tr>
    </tfoot>
  </table>`;

  const mount = document.getElementById(mountId);
  if (mount) mount.innerHTML = html;
}
</script>

<style>
.result-stack{ display:flex; flex-direction:column; gap:16px; }
.ts-section{ margin-top:4px; }

/* 表 */
.cat-matrix{
  width:100%;
  border-collapse:collapse;
  background:#fff;
  border:2px solid #333;
}
.cat-matrix th, .cat-matrix td{
  border:2px solid #333;
  padding:10px 12px;
}
.cat-matrix thead th{
  background:#eef1ff;
  font-weight:700;
  white-space:nowrap;
}
.cat-matrix .sticky-left{ position:sticky; left:0; background:#f9f9f9; z-index:1; }
.cat-matrix .num{ text-align:right; white-space:nowrap; }

/* 行の色味（薄め背景） */
.row-direct  th{ background:#fff5e6; }   /* 直接=オレンジ */
.row-indirect th{ background:#ecf7ff; }  /* 間接=水色 */
.row-gray    th{ background:#f3f3f3; }   /* その他=灰色 */

.row-physical th{ background:#ffeaea; }  /* 肉体=赤の淡色 */
.row-mental   th{ background:#f1eaff; }  /* 精神=紫の淡色 */

.dot{
  display:inline-block;
  width:14px; height:14px;
  border-radius:50%;
  margin-right:8px;
  vertical-align:middle;
}
</style>
@endsection
