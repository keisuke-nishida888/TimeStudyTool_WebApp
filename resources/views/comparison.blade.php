{{-- resources/views/comparison.blade.php --}}
@extends('layouts.parent')

@section('content')
<div class="allcont">
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <h2>データ比較</h2>

        {{-- ▼ 作業者選択（A/B） --}}
        <form method="GET" action="{{ route('comparison') }}" class="row g-3 mb-4">
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
          {{-- GETでの表示切替が不要なら、このフォームは送信しなくてOK（選択だけ使います） --}}
        </form>

        {{-- ▼ 期間集計（A/B共通の期間で取得） --}}
        <div class="card mb-4">
          <div class="card-body">
            <form id="range-form" class="row g-3">
              <div class="col-md-3">
                <label for="range-start" class="form-label">期間(開始)</label>
                <input type="date" id="range-start" class="form-control" required value="{{ $start ?? '' }}">
              </div>
              <div class="col-md-3">
                <label for="range-end" class="form-label">期間(終了)</label>
                <input type="date" id="range-end" class="form-control" required value="{{ $end ?? '' }}">
              </div>
              <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-secondary w-100">集計</button>
              </div>
            </form>
          </div>
        </div>

        {{-- ▼ 結果表示：A/Bを横に並べて表示（狭い画面では縦積み） --}}
        <div id="matrix-grid" class="matrix-grid" style="display:none;">
          {{-- A --}}
          <div id="task-day-matrix-section-a" class="ts-section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">
                  作業者A：
                  <span id="title-helper-a">未選択</span>
                </h5>
                <div id="taskDayMatrixWrapA">
                  <div id="taskDayMatrixA"></div>
                </div>
              </div>
            </div>
          </div>
          {{-- B --}}
          <div id="task-day-matrix-section-b" class="ts-section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">
                  作業者B：
                  <span id="title-helper-b">未選択</span>
                </h5>
                <div id="taskDayMatrixWrapB">
                  <div id="taskDayMatrixB"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container -->
</div><!-- /.allcont -->

<script>
/* =========================
   定数
========================= */
const URL_SUMMARY = `{{ url('/time_study/summary') }}`;

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
   期間集計 submit（A/B まとめて）
========================= */
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('range-form')?.addEventListener('submit', onRangeFormSubmit);
});

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

  // タイトル表示
  document.getElementById('title-helper-a').textContent = nameA;
  document.getElementById('title-helper-b').textContent = nameB;

  // 先にクリア
  document.getElementById('taskDayMatrixA').innerHTML = '';
  document.getElementById('taskDayMatrixB').innerHTML = '';

  // A/B それぞれ取得（選択されている方だけ）
  const tasks = [];
  if (helperA) tasks.push(fetchJSON(URL_SUMMARY, { helpno: helperA, start_date: start, end_date: end }).then(res => ({who:'A', res})));
  if (helperB) tasks.push(fetchJSON(URL_SUMMARY, { helpno: helperB, start_date: start, end_date: end }).then(res => ({who:'B', res})));

  let results = [];
  try {
    results = await Promise.all(tasks);
  } catch (err) {
    console.error(err);
    return alert('集計データの取得に失敗しました：' + err.message);
  }

  // 表示グリッドを有効化
  document.getElementById('matrix-grid').style.display = 'grid';

  // 反映
  for (const r of results){
    if (r.who === 'A') renderTaskDayMatrixInto('taskDayMatrixA', r.res);
    if (r.who === 'B') renderTaskDayMatrixInto('taskDayMatrixB', r.res);
  }

  // 片方未選択のときでもカードは表示しておく（中身は「データなし」を出す）
  if (!helperA) document.getElementById('taskDayMatrixA').innerHTML = '<div class="alert alert-light">未選択</div>';
  if (!helperB) document.getElementById('taskDayMatrixB').innerHTML = '<div class="alert alert-light">未選択</div>';
}

/* =========================
   表描画（列=各日付、行=作業名、値=分）
   mountId に描画
========================= */
function renderTaskDayMatrixInto(mountId, res) {
  const days = (res?.days || []).map(d => {
    const [y,m,dd] = d.split('-');
    return `${(+m)}/${(+dd)}<br>計測`;
  });

  const groups = [
    { name: '直接業務',   key: 'directByTask'  },
    { name: '間接業務',   key: 'indirectByTask'},
    { name: 'その他業務', key: 'otherByTask'   },
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
        <th class="col-group"></th>
        <th class="col-task">作業名</th>
        ${days.map(d => `<th>${d}</th>`).join('')}
      </tr>
    </thead>
    <tbody>`;

  groups.forEach(g => {
    const entries = Object.entries(dicts[g.key]); // [task, [m1,m2,...]]
    if (!entries.length) return;
    entries.forEach(([task, arr]) => {
      html += `<tr>
        <td class="matrix-group">${g.name}</td>
        <td>${task}</td>
        ${days.map((_,i) => {
          const v = parseInt((arr && arr[i]) || 0, 10) || 0;
          if (v) colTotals[i] += v;
          return `<td>${v || ''}</td>`;
        }).join('')}
      </tr>`;
    });
  });

  html += `</tbody><tfoot><tr>
    <th></th>
    <th>合計</th>
    ${colTotals.map(v => `<th>${v || 0}</th>`).join('')}
  </tr></tfoot></table>`;

  const mount = document.getElementById(mountId);
  if (mount) mount.innerHTML = html || '<div class="alert alert-warning">データなし</div>';
}
</script>

<style>
/* A/B を縦に並べる（常に A の下に B） */
.matrix-grid{
  display: grid;
  grid-template-columns: 1fr;  /* ← 1列固定 */
  gap: 16px;
}

/* マトリクス表 */
#taskDayMatrixWrapA, #taskDayMatrixWrapB { border: 2px solid #333; overflow:auto; }
.table-matrix { border-collapse: collapse; width: 100%; }
.table-matrix th, .table-matrix td { border:2px solid #333; padding:6px 8px; background:#fff; }
.table-matrix thead th { background:#eef1ff; font-weight:700; }
.matrix-group { background:#f7f7f7; font-weight:700; white-space:nowrap; }
.ts-section { margin-top:8px; }
</style>
@endsection
