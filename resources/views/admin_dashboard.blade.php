{{-- resources/views/admin_dashboard.blade.php --}}
@extends('layouts.parent')

@section('content')
<!-- Chart.js（CDN） -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<div class="allcont">
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <h2>管理者ダッシュボード</h2>

        {{-- 指標４つ＋年齢構成（同一行で横並び） --}}
        <div class="row g-3 align-items-stretch mb-4">
          {{-- 全国ご利用事業者数 --}}
          <div class="col-6 col-sm-3 col-lg-2">
            <div class="card stat-card h-100">
              <div class="card-body d-flex flex-column justify-content-center">
                <div class="stat-title">全国ご利用事業者数</div>
                <div class="stat-value">{{ number_format($facilityCount) }}</div>
              </div>
            </div>
          </div>
          {{-- ご利用作業者数 --}}
          <div class="col-6 col-sm-3 col-lg-2">
            <div class="card stat-card h-100">
              <div class="card-body d-flex flex-column justify-content-center">
                <div class="stat-title">ご利用作業者数</div>
                <div class="stat-value">{{ number_format($helperCount) }}</div>
              </div>
            </div>
          </div>
          {{-- 男性 --}}
          <div class="col-6 col-sm-3 col-lg-2">
            <div class="card stat-card h-100">
              <div class="card-body d-flex flex-column justify-content-center">
                <div class="stat-title">男性</div>
                <div class="stat-value text-primary">{{ number_format($sexCounts['male'] ?? 0) }}</div>
              </div>
            </div>
          </div>
          {{-- 女性 --}}
          <div class="col-6 col-sm-3 col-lg-2">
            <div class="card stat-card h-100">
              <div class="card-body d-flex flex-column justify-content-center">
                <div class="stat-title">女性</div>
                <div class="stat-value text-danger">{{ number_format($sexCounts['female'] ?? 0) }}</div>
              </div>
            </div>
          </div>
          {{-- 年齢構成（小さめドーナツ） --}}
          <div class="col-12 col-lg-4">
            <div class="card h-100">
              <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <h6 class="card-title mb-2">年齢構成</h6>
                <div class="chart-sm"><canvas id="agePie" width="220" height="220"></canvas></div>
              </div>
            </div>
          </div>
        </div>

        {{-- 利用プラン比率（プレースホルダ） --}}
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">利用プラン比率</h5>
                <div class="text-muted">準備中</div>
              </div>
            </div>
          </div>
        </div>

        {{-- 都道府県別データ（小さめ表） --}}
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">都道府県別データ</h5>
            <div class="table-responsive">
              <table class="pref-table compact-table">
                <thead>
                  <tr>
                    <th class="head-blue">都道府県</th>
                    <th class="head-blue text-end">施設数</th>
                    <th class="head-blue text-end">人数</th>
                    <th class="head-blue text-end">平均年齢</th>
                    <th class="head-blue text-end">平均勤務</th>
                    <th class="head-blue text-end">平均残業</th>
                    <th class="head-blue text-end">男女比</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($prefRows as $r)
                    @php
                      $fac  = (int)($r['facilities'] ?? ($r['facility_count'] ?? 0));
                      $pp   = (int)($r['people'] ?? ($r['helper_count'] ?? 0));
                      $age  = $r['avg_age']  ?? null;
                      $work = (float)($r['avg_work'] ?? 0);
                      $over = (float)($r['avg_over'] ?? 0);
                      $m    = (int)($r['male'] ?? ($r['male_count'] ?? 0));
                      $f    = (int)($r['female'] ?? ($r['female_count'] ?? 0));
                      $tot  = max(0, $m + $f);
                      $mp   = $tot ? round($m / $tot * 100) : 0;
                      $fp   = $tot ? 100 - $mp : 0;
                    @endphp
                    <tr>
                      <td>{{ $r['pref'] }}</td>
                      <td class="text-end">{{ number_format($fac) }}</td>
                      <td class="text-end">{{ number_format($pp) }}</td>
                      <td class="text-end">{{ is_null($age) ? '-' : number_format((float)$age, 0) }}</td>
                      <td class="text-end">{{ number_format($work, 1) }}</td>
                      <td class="text-end">{{ number_format($over, 1) }}</td>
                      <td class="text-end">{{ $mp }}%:{{ $fp }}%</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-center text-muted">データがありません</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            <div class="small text-muted mt-1">
              ※ 平均勤務/残業は概算（時間）。必要に応じて算出ロジックをご指定ください。
            </div>
          </div>
        </div>

        {{-- ▼▼▼ 都道府県比較（最下部） ▼▼▼ --}}
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">都道府県比較</h5>

            {{-- 入力UI --}}
            <div id="pref-rows" class="d-flex flex-column gap-2 mb-2"></div>
            <div class="d-flex gap-2 mb-3">
              <button type="button" id="btn-add-pref" class="btn btn-outline-primary btn-sm">＋ 追加</button>
              <button type="button" id="btn-compare" class="btn btn-secondary btn-sm">集計</button>
            </div>
            <div id="pref-note" class="text-muted small mb-3">※ 直近30日で集計。最大5都道府県まで選択できます。</div>

            {{-- 結果テーブル --}}
            <div class="table-responsive">
              <table id="pref-compare-table" class="table table-bordered table-sm compact-table">
                <thead class="table-light">
                  <tr>
                    <th>都道府県</th>
                    <th class="text-end">施設数</th>
                    <th class="text-end">人数</th>
                    <th class="text-end">平均年齢</th>
                    <th class="text-end">平均勤務時間</th>
                    <th class="text-end">平均残業時間</th>
                    <th class="text-end">男女比</th>
                  </tr>
                </thead>
                <tbody><tr><td colspan="7" class="text-muted text-center">未集計</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>
        {{-- ▲▲▲ 都道府県比較 ▲▲▲ --}}

        {{-- ▼▼ 施設ダッシュボード（ページ最下部に配置） ▼▼ --}}
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title">施設ダッシュボード</h5>

            @php
              $defaultFacilityId = (int) (Auth::user()->facilityno ?? 0);
            @endphp

            {{-- 入力フォーム --}}
            <form id="fd-form" class="row g-2 align-items-end" onsubmit="return false;">
              <div class="col-12 col-md-3">
                <label class="form-label">期間（開始）</label>
                <input type="date" id="fd-start" class="form-control" required>
              </div>
              <div class="col-12 col-md-3">
                <label class="form-label">期間（終了）</label>
                <input type="date" id="fd-end" class="form-control" required>
              </div>

              {{-- 施設選択（$facilities があればプルダウン、無ければログイン施設固定） --}}
              <div class="col-12 col-md-3">
                <label class="form-label">施設</label>
                @if(!empty($facilities) && count($facilities))
                  <select id="fd-facility" class="form-select form-select-sm">
                    @foreach($facilities as $fc)
                      <option value="{{ $fc->id }}" {{ ($fc->id == $defaultFacilityId) ? 'selected' : '' }}>
                        {{ $fc->facility ?? ('ID:'.$fc->id) }}
                      </option>
                    @endforeach
                  </select>
                @else
                  <input type="hidden" id="fd-facility" value="{{ $defaultFacilityId }}">
                  <div class="form-text">（現在の施設 ID：{{ $defaultFacilityId ?: '未設定' }}）</div>
                @endif
              </div>

              {{-- 施設内作業者（マルチセレクト。施設選択時に自動ロード） --}}
                <div class="col-12 col-md-3">
                <label class="form-label">施設内作業者</label>
                <select id="fd-helpers" class="form-select"></select>
                <div class="form-text small text-muted">施設を選ぶと作業者が表示されます。</div>
                </div>


              <div class="col-12">
                <button type="button" id="fd-run" class="btn btn-secondary">集計</button>
                <span id="fd-msg" class="ms-2 text-muted"></span>
              </div>
            </form>

            {{-- 結果テーブル --}}
            <div class="table-responsive mt-3">
              <table class="table table-bordered table-sm compact-table" id="fd-result">
                <thead class="table-light">
                  <tr>
                    <th>作業名（task_name）</th>
                    <th class="text-end">累計時間（分）</th>
                    <th class="text-end">累計時間（時間）</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td colspan="3" class="text-center text-muted">未集計</td></tr>
                </tbody>
                <tfoot class="table-light d-none" id="fd-tfoot">
                  <tr>
                    <th>合計</th>
                    <th class="text-end" id="fd-total-min">0</th>
                    <th class="text-end" id="fd-total-hr">0.0</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        {{-- ▲▲ 施設ダッシュボード ▲▲ --}}

      </div> {{-- /.col-md-12 --}}
    </div>   {{-- /.row --}}
  </div>     {{-- /.container --}}
</div>       {{-- /.allcont --}}

<script>
/* =========================
   共通 fetch（CSRF対応）
========================= */
async function fetchJSON(url, payload){
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const r = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' },
    credentials: 'same-origin',
    body: JSON.stringify(payload || {})
  });
  const txt = await r.text();
  if (!r.ok) throw new Error(`HTTP ${r.status}: ${txt}`);
  const ct = r.headers.get('content-type') || '';
  if (!ct.includes('application/json')) throw new Error('Non-JSON response');
  return JSON.parse(txt);
}

/* =========================
   初期化
========================= */
document.addEventListener('DOMContentLoaded', function(){
  setupAgeChart();
  setupPrefCompare();
  setupFacilityDashboard();
});

/* =========================
   年齢ドーナツ
========================= */
function setupAgeChart(){
  const ageCtx = document.getElementById('agePie');
  if (!ageCtx) return;
  const ageLabels = @json(array_keys($ageBuckets));
  const ageData   = @json(array_values($ageBuckets));

  new Chart(ageCtx, {
    type: 'doughnut',
    data: { labels: ageLabels, datasets: [{ data: ageData, borderWidth: 1 }] },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      aspectRatio: 1,
      layout: { padding: 0 },
      cutout: '58%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, padding: 8, font: { size: 11 } } },
        tooltip: { bodyFont: { size: 11 }, titleFont: { size: 11 } }
      },
      elements: { arc: { borderWidth: 1 } }
    }
  });
}

/* =========================
   都道府県比較
========================= */
const URL_PREF_COMPARE = `{{ route('admin.pref.compare') }}`;
const PREFS = ['北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];

function setupPrefCompare(){
  addPrefRow(); addPrefRow();  // デフォルト2行
  updateLabels();

  document.getElementById('btn-add-pref')?.addEventListener('click', () => {
    const wrap = document.getElementById('pref-rows');
    if (wrap.children.length >= 5) return;
    addPrefRow(); updateLabels();
  });

  document.getElementById('btn-compare')?.addEventListener('click', runCompare);
}

function addPrefRow(){
  const wrap = document.getElementById('pref-rows');
  const row  = document.createElement('div');
  row.className = 'd-flex align-items-center gap-2 pref-row';

  const label = document.createElement('div');
  label.className = 'text-secondary small pref-label';
  label.style.width = '8.2em';

  const sel = document.createElement('select');
  sel.className = 'form-select form-select-sm pref-select';
  sel.style.maxWidth = '240px';
  sel.innerHTML = `<option value="">未選択</option>` + PREFS.map(p=>`<option value="${p}">${p}</option>`).join('');

  row.appendChild(label);
  row.appendChild(sel);
  wrap.appendChild(row);
}

function updateLabels(){
  [...document.querySelectorAll('.pref-row')].forEach((r, i) => {
    r.querySelector('.pref-label').textContent = `都道府県選択欄${i+1}`;
  });
}

async function runCompare(){
  const prefs = [...document.querySelectorAll('.pref-select')]
    .map(s => s.value).filter(v => v).filter((v,i,arr)=>arr.indexOf(v)===i);
  if (!prefs.length) { alert('都道府県を1つ以上選択してください。'); return; }

  let resp;
  try{
    resp = await fetchJSON(URL_PREF_COMPARE, { prefs });
  }catch(e){
    console.error(e);
    alert('集計に失敗しました：' + e.message);
    return;
  }
  renderCompare(resp?.rows || []);
}

function renderCompare(rows){
  const tbody = document.querySelector('#pref-compare-table tbody');
  if (!tbody) return;
  if (!rows.length){
    tbody.innerHTML = `<tr><td colspan="7" class="text-muted text-center">該当データがありません</td></tr>`;
    return;
  }
  const fmt = (v, d='-') => (v === null || v === undefined || v === '') ? d : v;
  const num = v => Number.isFinite(v) ? v.toLocaleString() : v;

  tbody.innerHTML = rows.map(r => `
    <tr>
      <td>${fmt(r.pref)}</td>
      <td class="text-end">${num(r.facility_count || 0)}</td>
      <td class="text-end">${num(r.people || 0)}</td>
      <td class="text-end">${fmt(r.avg_age ?? '-', '-')}</td>
      <td class="text-end">${fmt(r.avg_work_h ?? '-', '-')}</td>
      <td class="text-end">${fmt(r.avg_over_h ?? '-', '-')}</td>
      <td class="text-end">${fmt(r.ratio || '-')}</td>
    </tr>
  `).join('');
}

/* =========================
   施設ダッシュボード
========================= */
const URL_FAC_HELPERS = `{{ route('admin.facility.helpers') }}`;
const URL_FAC_SUMMARY = `{{ route('admin.facility.task_summary') }}`;

function setupFacilityDashboard(){
  // 既定：今日〜今日
  const today = new Date().toISOString().slice(0,10);
  const $s = document.getElementById('fd-start');
  const $e = document.getElementById('fd-end');
  if ($s && !$s.value) $s.value = today;
  if ($e && !$e.value) $e.value = today;

  // 施設変更で作業者を取得
  const $f = document.getElementById('fd-facility');
  $f?.addEventListener('change', loadFacilityHelpers);
  loadFacilityHelpers(); // 初回ロード

  // 全選択 / 解除
  document.getElementById('fd-select-all')?.addEventListener('click', () => {
    const $h = document.getElementById('fd-helpers');
    [...$h.options].forEach(o => o.selected = true);
  });
  document.getElementById('fd-clear')?.addEventListener('click', () => {
    const $h = document.getElementById('fd-helpers');
    [...$h.options].forEach(o => o.selected = false);
  });

  // 集計
  document.getElementById('fd-run')?.addEventListener('click', runFacilitySummary);
}

async function loadFacilityHelpers(){
  const $f   = document.getElementById('fd-facility');
  const $h   = document.getElementById('fd-helpers');
  const $msg = document.getElementById('fd-msg');
  if (!$h) return;

  let fid = $f?.value;

  // 施設ID未設定時のフォールバック（プルダウンがあれば先頭を選ぶ）
  if ((!fid || fid === '0') && $f && $f.tagName === 'SELECT' && $f.options.length) {
    $f.selectedIndex = 0;
    fid = $f.value;
  }

  if (!fid || fid === '0') {
    $h.innerHTML = '<option value="">（施設を選択してください）</option>';
    $msg.textContent = '施設IDが未設定です。リストから施設を選んでください。';
    return;
  }

  $h.innerHTML = '<option value="">読み込み中...</option>';
  $msg.textContent = '';

  try{
    const data = await fetchJSON(URL_FAC_HELPERS, { facility_id: Number(fid) });
    const helpers = Array.isArray(data.helpers) ? data.helpers : [];

    if (!helpers.length){
      $h.innerHTML = '<option value="">（該当なし）</option>';
      $msg.textContent = 'この施設に作業者が見つかりません。';
      return;
    }

    // 先頭に「全て」を追加（value="__ALL__"）
    const opts = ['<option value="__ALL__">全て</option>'].concat(
      helpers.map(x => {
        const name = x.helpername ?? x.helper_name ?? x.heper_name ?? x.name ?? ('ID:'+x.id);
        return `<option value="${x.id}">${name}</option>`;
      })
    );

    $h.innerHTML = opts.join('');

    // 既定は「全て」を選択
    if ($h.multiple) {
      // 複数選択の場合は「全て」だけ選ばれている状態にする
      [...$h.options].forEach(o => o.selected = (o.value === '__ALL__'));
    } else {
      $h.value = '__ALL__';
    }

  }catch(e){
    console.error(e);
    $h.innerHTML = '<option value="">取得失敗</option>';
    $msg.textContent = '作業者の取得に失敗しました';
  }
}




async function runFacilitySummary(){
  const fid = document.getElementById('fd-facility')?.value;
  const st  = document.getElementById('fd-start')?.value;
  const ed  = document.getElementById('fd-end')?.value;
  const $h  = document.getElementById('fd-helpers');
  const $msg= document.getElementById('fd-msg');

  if (!fid || !st || !ed){
    alert('施設と期間を入力してください。'); return;
  }

  // 「全て」判定とID収集（単一/複数どちらでも動く）
  let useAll = false;
  let hids   = [];

  if ($h) {
    if ($h.multiple) {
      const vals = [...($h.selectedOptions || [])].map(o => o.value);
      useAll = vals.includes('__ALL__') || vals.length === 0; // 0件選択も全員扱い
      if (!useAll) hids = vals;
    } else {
      useAll = ($h.value === '__ALL__' || !$h.value);
      if (!useAll) hids = [$h.value];
    }
  }

  if (!useAll && !hids.length){
    alert('作業者を選択してください。'); return;
  }

  $msg.textContent = '集計中...';

  try{
    const res = await fetchJSON(URL_FAC_SUMMARY, {
      facility_id: fid,
      helper_ids: useAll ? [] : hids,   // ★空配列ならサーバ側で「施設内全員」に展開
      start_date: st,
      end_date: ed
    });
    $msg.textContent = '';
    renderFacilityTable(res?.rows || []);
  }catch(e){
    console.error(e);
    $msg.textContent = '集計に失敗しました';
  }
}



function renderFacilityTable(rows){
  const $tb   = document.querySelector('#fd-result tbody');
  const $tf   = document.getElementById('fd-tfoot');
  const $tmin = document.getElementById('fd-total-min');
  const $thr  = document.getElementById('fd-total-hr');
  if (!$tb) return;

  if (!rows.length){
    $tb.innerHTML = `<tr><td colspan="3" class="text-center text-muted">該当データがありません</td></tr>`;
    $tf?.classList.add('d-none');
    return;
  }

  let totalMin = 0;
  $tb.innerHTML = rows.map(r => {
    const m = +r.minutes || 0;
    totalMin += m;
    const h = (m/60);
    return `<tr>
      <td>${r.task_name || '-'}</td>
      <td class="text-end">${m.toLocaleString()}</td>
      <td class="text-end">${h.toFixed(1)}</td>
    </tr>`;
  }).join('');

  $tmin.textContent = totalMin.toLocaleString();
  $thr.textContent  = (totalMin/60).toFixed(1);
  $tf?.classList.remove('d-none');
}
</script>

<style>
/* 指標カード */
.stat-card .stat-title{ font-size:12px; color:#6c757d; font-weight:700; }
.stat-card .stat-value{ font-size:26px; font-weight:800; letter-spacing:.5px; }

/* 小さい円グラフ */
.chart-sm{ max-width: 220px; margin: 0 auto; }
.chart-sm canvas{ width: 100% !important; height: auto !important; }

/* 都道府県表（小さめ） */
.pref-table{
  width:100%;
  border-collapse: collapse;
  background:#fff;
  table-layout: fixed;
  font-size: 13px;
}
.pref-table th, .pref-table td{
  border: 2px solid #333;
  padding: 6px 8px;
  white-space: nowrap;
}
.pref-table .head-blue{ background: #cfe6f6; font-weight:700; text-align:center; }
.text-end{ text-align:right; }

/* よりコンパクトなテーブル用 */
.compact-table{ font-size:12px; }
.compact-table th, .compact-table td{ padding:.25rem .4rem!important; line-height:1.1; white-space:nowrap; }

/* ▼ 枠線強化：都道府県比較／施設ダッシュボード */
#pref-compare-table,
#fd-result{
  border-collapse: collapse !important;  /* 罫線を一体化 */
  border: 2px solid #333 !important;     /* 外枠 */
  background: #fff;
}

/* 全セルの罫線を太く濃く */
#pref-compare-table th,
#pref-compare-table td,
#fd-result th,
#fd-result td{
  border: 2px solid #333 !important;
}

/* ヘッダ/フッタの見出し感を少し強化（任意） */
#pref-compare-table thead th,
#fd-result thead th,
#fd-result tfoot th{
  background: #eef1ff !important;  /* 見出しの薄い色 */
  font-weight: 700;
}

</style>
@endsection
