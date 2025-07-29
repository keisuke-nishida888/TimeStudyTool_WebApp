@extends('layouts.parent')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                        <h5 class="card-title">作業時間グラフ（ガントチャート）</h5>
                        <div id="graph-error" class="alert alert-danger" style="display: none;"></div>
                        <div id="graph-container" style="height: 600px; overflow-x: auto;">
                            <canvas id="timeGraph" width="2400" height="600"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let timeGraph = null;

document.getElementById('graph-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // 既存のグラフとエラーメッセージをクリア
    if (timeGraph) {
        timeGraph.destroy();
        timeGraph = null;
    }
    document.getElementById('graph-error').style.display = 'none';
    document.getElementById('graph-error').textContent = '';
    
    const selectedDate = document.getElementById('selected-date').value;
    const graphType = document.getElementById('graph-type').value;
    const helpno = document.getElementById('helper-id').textContent.trim();
    
    console.log('Form submitted:', {
        selectedDate: selectedDate,
        graphType: graphType,
        helpno: helpno
    });
    
    if (!helpno || helpno === '未選択') {
        alert('作業者IDが取得できません。ページを再読み込みしてください。');
        return;
    }
    
    if (!selectedDate) {
        alert('年月日を選択してください。');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('CSRFトークンが見つかりません。');
        return;
    }
    
    // ローディング状態を表示
    document.getElementById('graph-error').style.display = 'block';
    document.getElementById('graph-error').textContent = 'データを取得中...';
    document.getElementById('graph-error').style.color = '#0066cc';
    
    // グラフデータを取得
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
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Received data:', data);
        console.log('Data structure check:');
        console.log('- timeSlots:', data.timeSlots);
        console.log('- taskNames:', data.taskNames);
        console.log('- graphData:', data.graphData);
        console.log('- graphType:', data.graphType);
        
        if (data.error) {
            document.getElementById('graph-error').textContent = data.message || 'データの取得に失敗しました。';
            document.getElementById('graph-error').style.display = 'block';
            document.getElementById('graph-error').style.color = '#dc3545';
            return;
        }
        
        if (!data || !data.taskNames || data.taskNames.length === 0) {
            document.getElementById('graph-error').textContent = '指定された日付にデータが見つかりませんでした。';
            document.getElementById('graph-error').style.display = 'block';
            document.getElementById('graph-error').style.color = '#dc3545';
            return;
        }
        
        if (!data.taskIndividualDurations) {
            document.getElementById('graph-error').textContent = '時間データが取得できませんでした。';
            document.getElementById('graph-error').style.display = 'block';
            document.getElementById('graph-error').style.color = '#dc3545';
            return;
        }
        
        createTimeGraph(data);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('graph-error').textContent = 'データの取得に失敗しました。: ' + error.message;
        document.getElementById('graph-error').style.display = 'block';
        document.getElementById('graph-error').style.color = '#dc3545';
    });
});

function createTimeGraph(data) {
    console.log('Creating gantt chart with data:', data);
    
    const canvas = document.getElementById('timeGraph');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }
    
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        console.error('Could not get 2D context');
        return;
    }
    
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        alert('Chart.jsライブラリが読み込まれていません。');
        return;
    }
    
    // 既存のグラフを破棄
    if (timeGraph) {
        timeGraph.destroy();
    }
    
    // 色の定義
    const typeColors = {
        0: 'rgba(255, 165, 0, 0.8)', // オレンジ色
        1: 'rgba(135, 206, 235, 0.8)', // 水色
        2: 'rgba(34, 139, 34, 0.8)' // 緑色
    };
    
    const categoryColors = {
        0: 'rgba(255, 0, 0, 0.8)', // 赤色
        1: 'rgba(255, 255, 0, 0.8)', // 黄色
        2: 'rgba(128, 0, 128, 0.8)' // 紫色
    };
    
    // ガントチャート用のデータセットを作成（実際の開始・終了時間を表示）
    const datasets = [];
    console.log('Creating gantt chart datasets with actual start/stop times');
    
    // 各作業名について個別のデータセットを作成
    data.taskNames.forEach((taskName, taskIndex) => {
        const individualDurations = data.taskIndividualDurations ? data.taskIndividualDurations[taskName] : [];
        
        if (individualDurations && individualDurations.length > 0) {
            // 各時間範囲について個別のデータセットを作成
            individualDurations.forEach((duration, durationIndex) => {
                const startTime = duration.start_time_decimal;
                const stopTime = duration.stop_time_decimal;
                const durationMinutes = duration.duration;
                
                // 色を決定
                let barColor = 'rgba(100, 100, 100, 0.7)';
                if (data.graphType === 'type') {
                    switch(duration.task_type_no) {
                        case 0: barColor = 'rgba(255, 165, 0, 0.7)'; break; // オレンジ
                        case 1: barColor = 'rgba(173, 216, 230, 0.7)'; break; // 水色
                        case 2: barColor = 'rgba(0, 128, 0, 0.7)'; break; // 緑
                    }
                } else {
                    switch(duration.task_category_no) {
                        case 0: barColor = 'rgba(255, 0, 0, 0.7)'; break; // 赤
                        case 1: barColor = 'rgba(255, 255, 0, 0.7)'; break; // 黄色
                        case 2: barColor = 'rgba(147, 112, 219, 0.7)'; break; // 紫
                    }
                }
                
                // 実際の開始・終了時間を直接使用（ガントチャート形式）
                // 各時間帯での作業時間を計算
                const dataPoints = [];
                for (let hour = 0; hour < 24; hour++) {
                    const hourStart = hour;
                    const hourEnd = hour + 1;
                    
                    // この時間帯が作業時間範囲内かチェック
                    if (startTime < hourEnd && stopTime > hourStart) {
                        // 重複部分の時間を計算
                        const overlapStart = Math.max(startTime, hourStart);
                        const overlapEnd = Math.min(stopTime, hourEnd);
                        const overlapDuration = overlapEnd - overlapStart;
                        
                        // この時間帯での作業時間（分単位）
                        dataPoints.push(overlapDuration * 60);
                    } else {
                        dataPoints.push(null);
                    }
                }
                
                datasets.push({
                    label: `${taskName} (${duration.start_hour}:${duration.start_minute.toString().padStart(2, '0')}-${duration.stop_hour}:${duration.stop_minute.toString().padStart(2, '0')})`,
                    data: dataPoints,
                    backgroundColor: barColor,
                    borderColor: barColor.replace('0.7', '1'),
                    borderWidth: 1,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9,
                    startTime: startTime,
                    stopTime: stopTime,
                    duration: durationMinutes,
                    // 実際の時間範囲情報を追加
                    actualStartTime: startTime,
                    actualStopTime: stopTime,
                    actualDuration: durationMinutes
                });
            });
        }
    });
    
    console.log('Final datasets:', datasets);
    
    if (datasets.length === 0) {
        console.error('No datasets created');
        document.getElementById('graph-error').textContent = 'グラフデータが作成できませんでした。';
        document.getElementById('graph-error').style.display = 'block';
        return;
    }
    
    // 24時間のラベルを作成
    const timeLabels = [];
    for (let hour = 0; hour < 24; hour++) {
        timeLabels.push(`${hour.toString().padStart(2, '0')}:00`);
    }
    
    timeGraph = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.taskNames, // 作業名をラベルとして使用
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // 横棒グラフにする（縦軸に作業名、横軸に時間）
            animation: {
                duration: 1000
            },
            layout: {
                padding: {
                    right: 50 // 時間表示のための余白
                }
            },
            plugins: {
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.x;
                            if (value === null) return 'データなし';
                            const taskName = context.chart.data.labels[context.dataIndex];
                            const timeSlot = context.dataset.label;
                            return `${taskName} (${timeSlot}): ${value}分`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: '時間'
                    },
                    min: 0,
                    max: 24, // 24時間表示
                    grid: {
                        color: 'rgba(200, 200, 200, 0.3)', // 薄いグレーのグリッド線
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1,
                        callback: function(value, index, values) {
                            return `${value.toString().padStart(2, '0')}:00`;
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: '作業名'
                    },
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(200, 200, 200, 0.3)', // 薄いグレーのグリッド線
                        drawBorder: false
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: data.graphType === 'type' ? '介護種別別作業時間（ガントチャート）' : 'カテゴリ別作業時間（ガントチャート）'
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.x;
                            if (value === null) return 'データなし';
                            const taskName = context.chart.data.labels[context.dataIndex];
                            const timeSlot = context.dataset.label;
                            return `${taskName} (${timeSlot}): ${value}分`;
                        }
                    }
                },
                // カスタムプラグイン：横棒グラフ用の時間表示
                customDurationPlugin: {
                    id: 'customDurationPlugin',
                    afterDraw: function(chart) {
                        const ctx = chart.ctx;
                        const datasets = chart.data.datasets;
                        
                        datasets.forEach((dataset, datasetIndex) => {
                            // 各データポイントの値を表示
                            dataset.data.forEach((value, dataIndex) => {
                                if (value !== null && value > 0) {
                                    const xAxis = chart.scales.x;
                                    const yAxis = chart.scales.y;
                                    
                                    const xPos = xAxis.getPixelForValue(value);
                                    const yPos = yAxis.getPixelForValue(dataIndex);
                                    
                                    // テキストを描画
                                    ctx.save();
                                    ctx.font = 'bold 10px Arial';
                                    ctx.fillStyle = '#333';
                                    ctx.textAlign = 'left';
                                    ctx.textBaseline = 'middle';
                                    
                                    // バーの右端に時間を表示
                                    ctx.fillText(value.toString() + '分', xPos + 5, yPos);
                                    ctx.restore();
                                }
                            });
                        });
                    }
                }
            }
        }
    });
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
}

#timeGraph {
    max-height: 600px;
    width: 100% !important;
    height: 100% !important;
}
</style>
@endsection





