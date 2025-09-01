{{-- resources/views/comparison.blade.php --}}
@extends('layouts.parent')

@section('content')
<div class="allcont">
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <h2>データ比較</h2>

        {{-- ▼ 作業者選択リスト + 追加ボタン --}}
        <div class="card mb-3">
          <div class="card-body">
            <div id="helper-list" class="row g-3 align-items-end">

              {{-- 既定：1行目（削除不可） --}}
              <div class="col-md-4 helper-row" data-fixed="true">
                <label class="form-label helper-label">作業者</label>
                <select name="helper[]" class="form-control helper-select">
                  <option value="">未選択</option>
                  @foreach(($helpers ?? []) as $h)
                    <option value="{{ $h->id }}" {{ (isset($helperA) && (int)$helperA === (int)$h->id) ? 'selected' : '' }}>
                      {{ $h->helpername }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- 既定：2行目（削除不可） --}}
              <div class="col-md-4 helper-row" data-fixed="true">
                <label class="form-label helper-label">作業者</label>
                <select name="helper[]" class="form-control helper-select">
                  <option value="">未選択</option>
                  @foreach(($helpers ?? []) as $h)
                    <option value="{{ $h->id }}" {{ (isset($helperB) && (int)$helperB === (int)$h->id) ? 'selected' : '' }}>
                      {{ $h->helpername }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- 追加ボタン --}}
              <div class="col-md-4">
                <label class="form-label d-block">&nbsp;</label>
                <button type="button" id="btn-add-helper" class="btn btn-outline-primary w-auto">＋ 追加</button>
              </div>
            </div>

            {{-- 追加用オプションの素（非表示・JSでクローン） --}}
            <select id="helper-options-source"
                    class="d-none visually-hidden"
                    hidden
                    aria-hidden="true"
                    tabindex="-1"
                    style="position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">
            <option value="">未選択</option>
            @foreach(($helpers ?? []) as $h)
                <option value="{{ $h->id }}">{{ $h->helpername }}</option>
            @endforeach
            </select>
          </div>
        </div>

        {{-- ▼ 期間＋表示切替 --}}
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

        {{-- ▼ 結果（選んだ人数分だけ下に積む） --}}
        <div id="result-stack" class="result-stack" style="display:none;"></div>

      </div>
    </div>
  </div>
</div>

<script>
const URL_SUMMARY = `{{ url('/time_study/summary') }}`;

let currentMode = 'type'; // 'type' or 'category'
const cachedResults = new Map(); // key=helperId, val=response

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
  if (!resp.ok) { console.error('Server error body:\n', raw); throw new Error('HTTP ' + resp.status); }
  if (!ct.includes('application/json')) { console.error('Non-JSON response:\n', raw); throw new Error('Server returned non-JSON'); }
  return JSON.parse(raw);
}

document.addEventListener('DOMContentLoaded', () => {
  // 初回ラベル連番
  renumberHelperLabels();

  // 追加ボタン
  document.getElementById('btn-add-helper')?.addEventListener('click', addHelperRow);

  // 表示切替
  const btnType = document.getElementById('btn-mode-type');
  const btnCat  = document.getElementById('btn-mode-category');
  btnType?.addEventListener('click', () => { currentMode='type'; btnType.classList.add('active'); btnCat.classList.remove('active'); rerenderAll(); });
  btnCat?.addEventListener('click', () => { currentMode='category'; btnCat.classList.add('active'); btnType.classList.remove('active'); rerenderAll(); });

  // 集計
  document.getElementById('range-form')?.addEventListener('submit', onRangeFormSubmit);

  // 行削除（追加分のみ）
  document.getElementById('helper-list').addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-remove-row');
    if (!btn) return;
    const row = btn.closest('.helper-row');
    if (row?.dataset.fixed === 'true') return; // 既定2行は削除不可
    row.remove();
    renumberHelperLabels();
  });
});

/* 連番付け直し */
function renumberHelperLabels(){
  const rows = document.querySelectorAll('#helper-list .helper-row');
  rows.forEach((row, i) => {
    const label = row.querySelector('.helper-label');
    if (label) label.textContent = '作業者' + (i + 1);
  });
}

/* 行追加 */
function addHelperRow(){
  const list = document.getElementById('helper-list');
  const col  = document.createElement('div');
  col.className = 'col-md-4 helper-row';

  const optionsHTML = [...document.getElementById('helper-options-source').options]
    .map(o => `<option value="${o.value}">${o.text}</option>`).join('');

  col.innerHTML = `
    <label class="form-label helper-label">作業者</label>
    <div class="d-flex gap-2">
      <select name="helper[]" class="form-control helper-select">${optionsHTML}</select>
      <button type="button" class="btn btn-outline-danger btn-remove-row" title="削除">×</button>
    </div>`;
  list.appendChild(col);
  renumberHelperLabels();
}

/* 集計 submit */
async function onRangeFormSubmit(e){
  e.preventDefault();

  const start = document.getElementById('range-start').value;
  const end   = document.getElementById('range-end').value;
  if (!start || !end) return alert('期間を選択してください。');

  // 現在の並び順で作業者IDと見出し用の「作業者N」を取得
  const rows = [...document.querySelectorAll('#helper-list .helper-row')];
  const pairs = rows.map((row, i) => {
    const sel  = row.querySelector('.helper-select');
    const id   = sel?.value || '';
    const name = sel?.options[sel.selectedIndex]?.text?.trim() || '未選択';
    return { id, name, label: `作業者${i+1}` };
  }).filter(p => p.id);

  if (!pairs.length) return alert('作業者を選択してください。');

  // 重複IDは先勝ち（1つだけ表示）
  const seen = new Set();
  const targets = [];
  for (const p of pairs) { if (seen.has(p.id)) continue; seen.add(p.id); targets.push(p); }

  // 表領域初期化
  const stack = document.getElementById('result-stack');
  stack.innerHTML = '';
  stack.style.display = 'block';
  cachedResults.clear();

  // カード作成
  targets.forEach(t => stack.appendChild(buildResultCard(t.id, t.name, t.label)));

  // 取得 → 反映
  try {
    const jobs = targets.map(t =>
      fetchJSON(URL_SUMMARY, { helpno: t.id, start_date: start, end_date: end })
        .then(res => ({id: t.id, res}))
    );
    const results = await Promise.all(jobs);
    for (const {id, res} of results) {
      cachedResults.set(id, res);
      renderMatrixInto(document.getElementById(`table-${id}`), res, currentMode);
    }
  } catch(err) {
    console.error(err);
    alert('集計データの取得に失敗しました：' + err.message);
  }
}

/* カード DOM */
function buildResultCard(helperId, helperName, labelText){
  const wrap = document.createElement('div');
  wrap.className = 'ts-section';
  wrap.innerHTML = `
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">${escapeHtml(labelText)}：<span>${escapeHtml(helperName)}</span></h5>
        <div id="table-${helperId}"><div class="text-muted">読み込み中...</div></div>
      </div>
    </div>`;
  return wrap;
}

function escapeHtml(s){ return (s || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

/* 表示モード切替の再描画 */
function rerenderAll(){
  if (!cachedResults.size) return;
  document.getElementById('result-stack').style.display = 'block';
  for (const [helperId, res] of cachedResults.entries()) {
    const mount = document.getElementById(`table-${helperId}`);
    if (mount) renderMatrixInto(mount, res, currentMode);
  }
}

/* 列＝日付 / 行＝3分類 のマトリクス */
function renderMatrixInto(mount, res, mode){
  const days = res?.days || [];
  if (!Array.isArray(days) || days.length === 0){
    mount.innerHTML = '<div class="alert alert-warning">データなし</div>';
    return;
  }

  let rows;
  if (mode === 'category'){
    rows = [
      { label: '肉体的負担', arr: res?.physicalTotals || [], className: 'row-physical', dot: '#ff4d4f' },
      { label: '精神的負担', arr: res?.mentalTotals   || [], className: 'row-mental',   dot: '#8a63d2' },
      { label: 'その他',     arr: res?.otherTotalsCategory || [], className: 'row-gray', dot: '#bfbfbf' },
    ];
  } else {
    rows = [
      { label: '直接',   arr: res?.directTotals   || [], className: 'row-direct',   dot: '#ffa500' },
      { label: '間接',   arr: res?.indirectTotals || [], className: 'row-indirect', dot: '#8fd3ff' },
      { label: 'その他', arr: res?.otherTotals    || [], className: 'row-gray',     dot: '#bfbfbf' },
    ];
  }

  const fmtMD = (ymd) => {
    const [y,m,d] = (ymd||'').split('-');
    if (!m || !d) return ymd || '';
    return `${(+m)}/${(+d)}`;
  };
  const sum = arr => (arr||[]).reduce((p,c)=>p+(parseInt(c,10)||0),0);

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

  mount.innerHTML = html;
}
</script>

<style>
.result-stack{ display:flex; flex-direction:column; gap:16px; }
.ts-section{ margin-top:4px; }
.btn-remove-row{ padding:6px 10px; }

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

.row-direct   th{ background:#fff5e6; }  /* 直接=オレンジ */
.row-indirect th{ background:#ecf7ff; }  /* 間接=水色 */
.row-gray     th{ background:#f3f3f3; }  /* その他=灰色 */

.row-physical th{ background:#ffeaea; }  /* 肉体=赤の淡色 */
.row-mental   th{ background:#f1eaff; }  /* 精神=紫の淡色 */

.dot{
  display:inline-block;
  width:14px; height:14px;
  border-radius:50%;
  margin-right:8px;
  vertical-align:middle;
}

/* ====== ここからコンパクト化（追記） ====== */
.result-stack{ gap:10px !important; }
.ts-section{ margin-top:0 !important; }

/* カードも少し詰める */
.result-stack .card .card-body{ padding:12px !important; }
.result-stack .card .card-title{ font-size:14px !important; margin:0 0 6px !important; }

/* 表全体を小さく */
.cat-matrix{ 
  font-size:12px !important;      /* 文字小さめ */
  border:1px solid #333 !important;/* 枠線も細く */
}
.cat-matrix th, .cat-matrix td{
  padding:4px 6px !important;      /* 行を細く（余白を縮小） */
  border-width:1px !important;     /* 罫線を細く */
  line-height:1.15 !important;     /* 行高を詰める */
  white-space:nowrap;               /* 折返し抑制（必要なら外してください） */
}
.cat-matrix thead th{
  padding:6px 6px !important;
  background:#eef1ff !important;
  font-weight:700 !important;
}
.cat-matrix tfoot th{ padding:6px 6px !important; }

/* 左列（カテゴリー） */
.cat-matrix .sticky-left{
  position:sticky; left:0;
  background:#f9f9f9 !important;
  z-index:1;
}

/* ドットも小さく */
.cat-matrix .dot{
  width:10px !important;
  height:10px !important;
  margin-right:6px !important;
}

/* 行見出しの淡色背景（既存色は維持しつつ控えめに） */
.row-direct  th{ background:#fff5e6 !important; }   /* 直接=オレンジ薄 */
.row-indirect th{ background:#ecf7ff !important; }  /* 間接=水色薄 */
.row-gray    th{ background:#f3f3f3 !important; }   /* その他=灰 */

.row-physical th{ background:#ffeaea !important; }  /* 肉体=赤薄 */
.row-mental   th{ background:#f1eaff !important; }  /* 精神=紫薄 */
/* ====== ここまで ====== */
</style>
@endsection
