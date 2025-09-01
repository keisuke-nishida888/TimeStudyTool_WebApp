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
        {{-- ▲▲▲ 都道府県比較（最下部） ▲▲▲ --}}

      </div>
    </div>
  </div>
</div>

<script>
/* ========== 年齢ドーナツ ========== */
document.addEventListener('DOMContentLoaded', function(){
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
});

/* ========== 都道府県比較 ========== */
const URL_PREF_COMPARE = `{{ route('admin.pref.compare') }}`;
// 47都道府県
const PREFS = ['北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];

document.addEventListener('DOMContentLoaded', () => {
  addPrefRow(); addPrefRow();  // デフォルト2行
  updateLabels();

  document.getElementById('btn-add-pref')?.addEventListener('click', () => {
    const wrap = document.getElementById('pref-rows');
    if (wrap.children.length >= 5) return;
    addPrefRow(); updateLabels();
  });

  document.getElementById('btn-compare')?.addEventListener('click', runCompare);
});

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

async function fetchJSON(url, payload){
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const r = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' },
    credentials: 'same-origin',
    body: JSON.stringify(payload || {})
  });
  const body = await r.text();
  if (!r.ok) throw new Error(`HTTP ${r.status}`);
  return JSON.parse(body || '{}');
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
  font-size: 13px;         /* 小さく */
}
.pref-table th, .pref-table td{
  border: 2px solid #333;
  padding: 6px 8px;        /* コンパクト */
  white-space: nowrap;
}
.pref-table .head-blue{ background: #cfe6f6; font-weight:700; text-align:center; }
.text-end{ text-align:right; }

/* よりコンパクトなテーブル用 */
.compact-table{ font-size:12px; }
.compact-table th, .compact-table td{ padding:.25rem .4rem!important; line-height:1.1; white-space:nowrap; }
</style>
@endsection
