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

        {{-- 期間指定 --}}
        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-end mb-3">
          <div class="col-sm-3">
            <label class="form-label">開始日</label>
            <input type="date" name="start_date" class="form-control" value="{{ $start }}">
          </div>
          <div class="col-sm-3">
            <label class="form-label">終了日</label>
            <input type="date" name="end_date" class="form-control" value="{{ $end }}">
          </div>
          <div class="col-sm-3">
            <button class="btn btn-primary w-100" type="submit">更新</button>
          </div>
          <div class="col-sm-3 text-end">
            <div class="small text-muted">集計期間：{{ $start }} 〜 {{ $end }}</div>
          </div>
        </form>

        {{-- 指標カード --}}
        <div class="row g-3 mb-4">
          <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
              <div class="card-body">
                <div class="stat-title">全国ご利用事業者数</div>
                <div class="stat-value">{{ number_format($facilityCount) }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
              <div class="card-body">
                <div class="stat-title">ご利用作業者数</div>
                <div class="stat-value">{{ number_format($helperCount) }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
              <div class="card-body">
                <div class="stat-title">男性</div>
                <div class="stat-value text-primary">{{ number_format($sexCounts['male'] ?? 0) }}</div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
              <div class="card-body">
                <div class="stat-title">女性</div>
                <div class="stat-value text-danger">{{ number_format($sexCounts['female'] ?? 0) }}</div>
              </div>
            </div>
          </div>
        </div>

        {{-- グラフ列 --}}
        <div class="row g-3 mb-4">
          {{-- 年齢構成（円グラフ） --}}
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">年齢構成</h5>
                <canvas id="agePie"></canvas>
              </div>
            </div>
          </div>

          {{-- 利用プラン比率（プレースホルダ） --}}
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">利用プラン比率</h5>
                <div class="text-muted">準備中</div>
                {{-- 実装時は下記のように Chart.js を使ってください --}}
                {{-- <canvas id="planPie"></canvas> --}}
              </div>
            </div>
          </div>
        </div>

        {{-- 都道府県別データ --}}
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">都道府県別データ</h5>
            <div class="table-responsive">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="white-space:nowrap;">都道府県</th>
                    <th class="text-end" style="white-space:nowrap;">人数</th>
                    <th class="text-end" style="white-space:nowrap;">平均年齢</th>
                    <th class="text-end" style="white-space:nowrap;">平均勤務時間</th>
                    <th class="text-end" style="white-space:nowrap;">平均残業時間</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($prefRows as $r)
                    <tr>
                      <td>{{ $r['pref'] }}</td>
                      <td class="text-end">{{ number_format($r['people']) }}</td>
                      <td class="text-end">{{ is_null($r['avg_age']) ? '-' : number_format($r['avg_age'], 1) }}</td>
                      <td class="text-end">{{ number_format($r['avg_work'], 1) }} h</td>
                      <td class="text-end">{{ number_format($r['avg_over'], 1) }} h</td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="text-center text-muted">データがありません</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            <div class="small text-muted">※ 平均勤務/残業は「期間内の各作業者の値」を平均化した概算です（1日標準8時間換算）。</div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  // 年齢円グラフ
  const ageCtx = document.getElementById('agePie');
  if (ageCtx) {
    const ageLabels = @json(array_keys($ageBuckets));
    const ageData   = @json(array_values($ageBuckets));

    new Chart(ageCtx, {
      type: 'doughnut',
      data: {
        labels: ageLabels,
        datasets: [{
          data: ageData
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' },
          title: { display: false }
        },
        cutout: '55%'
      }
    });
  }

  // 利用プラン比率（実装時の参考）
  // const planCtx = document.getElementById('planPie');
  // if (planCtx) {
  //   const plan = @json($planRatio);
  //   new Chart(planCtx, {
  //     type: 'doughnut',
  //     data: { labels: plan.labels, datasets: [{ data: plan.data }] },
  //     options: { responsive: true, plugins: { legend: { position:'bottom' } } }
  //   });
  // }
});
</script>

<style>
.stat-card .stat-title{ font-size: 12px; color:#6c757d; font-weight:700; }
.stat-card .stat-value{ font-size: 28px; font-weight:800; letter-spacing: .5px; }
.table td, .table th{ vertical-align: middle; }
</style>
@endsection
