{{-- resources/views/time_summary.blade.php --}}
@extends('layouts.parent')
@php
  $title = 'Time Study サマリー';
  $page  = 'time_summary';
  $group = 'time_summary';   // パンくずや戻るの判定で使っているなら適宜
@endphp

@section('content')
<div class="allcont">
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <h2>Time Study サマリー</h2>
        {{-- ここにサマリーUI/グラフ等を実装していきます --}}
        <p class="text-muted">
          ここに「年月選択＋複数月同時表示」などのサマリー機能を追加していきます。<br>
          facilityno: {{ $facilityno ?? '未指定' }}
        </p>
        {{-- 条件フォーム --}}
        <div class="card mb-4">
          <div class="card-body">
            <form id="monthly-form" class="row g-3" onsubmit="return false;">
              <div class="col-md-4">
                <label for="helpno-input" class="form-label">作業者ID（helpno）</label>
                <input type="number" id="helpno-input" class="form-control"
                       value="{{ request('helpno') ?? '' }}"
                       placeholder="例: 123">
                <div class="form-text">※このサマリーは作業者単位で集計します</div>
              </div>

              <div class="col-md-8">
                <label class="form-label d-block">対象年月</label>

                <div id="month-rows" class="d-flex flex-column gap-2">
                  {{-- 1行目（初期） --}}
                  <div class="month-row">
                    <input type="month" class="form-control month-input" value="{{ now()->format('Y-m') }}">
                    <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="removeMonthRow(this)" title="この年月を削除" disabled>－</button>
                  </div>
                </div>

                <div class="mt-2">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-month">＋ 年月を追加</button>
                </div>
              </div>

              <div class="col-12 mt-2">
                <button type="button" class="btn btn-secondary" id="btn-run">集計</button>
              </div>
            </form>
            <div id="req-error" class="alert alert-danger mt-3" style="display:none;"></div>
          </div>
        </div>

        {{-- 結果：月ごとの横棒グラフ（複数表示） --}}
        <div id="monthly-results" class="d-flex flex-column gap-4"></div>

      </div>
    </div>
  </div>
</div>

<script>
/** =========================
 *  UI：年月行 追加/削除
 * ========================= */
document.getElementById('btn-add-month')?.addEventListener('click', () => {
  const wrap = document.getElementById('month-rows');
  const row  = document.createElement('div');
  row.className = 'month-row';

  const input = document.createElement('input');
  input.type  = 'month';
  input.className = 'form-control month-input';
  input.value = new Date().toISOString().slice(0,7); // YYYY-MM

  const del = document.createElement('button');
  del.type = 'button';
  del.className = 'btn btn-outline-danger btn-sm ms-2';
  del.textContent = '－';
  del.title = 'この年月を削除';
  del.onclick = () => removeMonthRow(del);

  row.appendChild(input);
  row.appendChild(del);
  wrap.appendChild(row);

  // 1行しか無い場合の削除無効化を更新
  updateRemoveButtons();
});

function removeMonthRow(btn){
  const wrap = document.getElementById('month-rows');
  if (wrap.children.length <= 1) return; // 最低1行
  btn.closest('.month-row')?.remove();
  updateRemoveButtons();
}
function updateRemoveButtons(){
  const rows = [...document.querySelectorAll('#month-rows .month-row')];
  const canRemove = rows.length > 1;
  rows.forEach(r => {
    const b = r.querySelector('button');
    if (b) b.disabled = !canRemove;
  });
}

/** =========================
 *  集計リクエスト
 *  期待エンドポイント: POST /time_study/monthly
 *  期待リクエスト: { helpno: <number>, months: ["2025-07","2025-08", ...] }
 *  期待レスポンス例:
 *  {
 *    "months": ["2025-07","2025-08"],
 *    "byMonth": {
 *       "2025-07": { "directMin": 7200, "indirectMin": 3000, "otherMin": 1800, "totalMin": 12000 },
 *       "2025-08": { "directMin": 4800, "indirectMin": 4800, "otherMin": 3000 } // totalMin 無くてもOK（合計は前3つの和）
 *    }
 *  }
 * ========================= */
document.getElementById('btn-run')?.addEventListener('click', onRun);

async function onRun(){
  const errBox = document.getElementById('req-error');
  errBox.style.display = 'none';
  errBox.textContent = '';

  const helpno = (document.getElementById('helpno-input')?.value || '').trim();
  const months = [...document.querySelectorAll('.month-input')].map(i => i.value).filter(Boolean);

  if (!helpno)  return showReqError('作業者ID（helpno）を入力してください。');
  if (!months.length) return showReqError('対象の年月を1つ以上選択してください。');

  let res;
  try{
    res = await fetchJSON(`{{ url('/time_study/monthly') }}`, { helpno, months });
  }catch(e){
    return showReqError('集計に失敗しました：' + e.message + '（コンソール参照）');
  }
  renderMonthlyBars(res);
}

function showReqError(msg){
  const errBox = document.getElementById('req-error');
  errBox.textContent = msg;
  errBox.style.display = 'block';
}

/** =========================
 *  fetchJSON（共通）
 * ========================= */
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

  if (!resp.ok) { console.error(raw); throw new Error('HTTP ' + resp.status); }
  if (!ct.includes('application/json')) { console.error(raw); throw new Error('Server returned non-JSON'); }
  return JSON.parse(raw);
}

/** =========================
 *  描画：月ごとの横方向スタック棒
 *  ・direct/indirect/other の比率で横幅を按分
 *  ・右側に合計・残業を表示
 *  ・残業は以下優先で表示:
 *     1) API が "overtimeMin" を返していればそれを利用
 *     2) 返していなければ totalMin - 月標準(=160h=9600分) を超過した分を簡易算出（負なら0）
 * ========================= */
function renderMonthlyBars(data){
  const mount = document.getElementById('monthly-results');
  mount.innerHTML = '';

  const months = data?.months || [];
  const byMonth = data?.byMonth || {};

  if (!months.length){
    mount.innerHTML = '<div class="text-muted">該当データがありません。</div>';
    return;
  }

  months.forEach((ym) => {
    const rec = byMonth[ym] || {};
    const d = +rec.directMin   || 0;
    const i = +rec.indirectMin || 0;
    const o = +rec.otherMin    || 0;
    const total = +rec.totalMin || (d + i + o);
    const stdMonthMin = 160 * 60; // 160h=月標準（仮）
    const overtimeMin = typeof rec.overtimeMin !== 'undefined'
                        ? (+rec.overtimeMin || 0)
                        : Math.max(0, total - stdMonthMin);

    // 比率→幅（%）
    const sumForPct = Math.max(1, d + i + o);
    const wD = (d / sumForPct) * 100;
    const wI = (i / sumForPct) * 100;
    const wO = (o / sumForPct) * 100;

    // 表示用
    const ymObj = new Date(ym + '-01T00:00:00');
    const title = (ymObj.getMonth() + 1) + '月';
    const toH = (m) => (m/60).toFixed(0) + 'h';

    // 要素組み立て
    const card = document.createElement('div');
    card.className = 'tsm-item';

    card.innerHTML = `
      <div class="tsm-head"><span class="tsm-badge">${title}</span></div>
      <div class="tsm-bar">
        <div class="seg seg-direct"   style="width:${wD}%">
          ${d ? `<span class="seg-label">直接業務　${toH(d)}</span>` : ''}
        </div>
        <div class="seg seg-indirect" style="width:${wI}%">
          ${i ? `<span class="seg-label">間接業務</span>` : ''}
        </div>
        <div class="seg seg-other"    style="width:${wO}%">
          ${o ? `<span class="seg-label">その他${toH(o)}</span>` : ''}
        </div>
      </div>
      <div class="tsm-tail">
        <span>合計：${toH(total)}</span>
        <span class="ms-3">残業：${toH(overtimeMin)}</span>
      </div>
    `;
    mount.appendChild(card);
  });
}
</script>

<style>
/* ========== 入力UI ========== */
.month-row{
  display:flex;
  align-items:center;
  max-width: 320px;
}
#btn-add-month{ vertical-align: middle; }

/* ========== 結果表示（1カード=1ヶ月） ========== */
.tsm-item{
  padding: 10px 6px 6px 6px;
}
.tsm-head{ margin-bottom: 8px; }
.tsm-badge{
  display:inline-block;
  border:2px solid #34495e;
  padding: 4px 14px;
  border-radius: 6px;
  background:#f5fbff;
  font-weight:700;
  letter-spacing:.5px;
}

/* 棒グラフ */
.tsm-bar{
  display:flex;
  width: 100%;
  max-width: 760px;
  min-height: 36px;
  border: 3px solid #131313;
  border-radius: 4px;
  overflow: hidden;
  background:#fff;
}
.seg{
  display:flex; align-items:center;
  position: relative;
  min-width: 0;       /* ラベル折返し用 */
  font-weight:600;
  border-right: 2px solid #131313;
}
.seg:last-child{ border-right: none; }

/* セグメント色（画像の雰囲気に寄せる） */
.seg-direct   { background:#f9d8c8; }
.seg-indirect { background:#cfe9fb; }
.seg-other    { background:#e7f3d8; }

/* ラベル（中に見やすく） */
.seg-label{
  display:inline-block;
  padding: 2px 8px;
  white-space: nowrap;
  font-size: 14px;
}

/* 右側の合計・残業 */
.tsm-tail{
  margin-top: 8px;
  font-weight:700;
}

/* スマホ時の詰め */
@media (max-width: 576px){
  .seg-label{ font-size: 12px; padding: 2px 6px; }
  .tsm-badge{ padding: 3px 10px; }
  .tsm-bar{ max-width: 100%; }
}
</style>
@endsection
