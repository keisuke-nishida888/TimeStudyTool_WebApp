@extends('layouts.parent')

@section('content')
<script src="/js/helperdata.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart.jsのグローバル設定
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart !== 'undefined') {
            try {
                Chart.defaults.font.family = 'Arial, sans-serif';
                Chart.defaults.font.size = 12;
            } catch (error) {
                console.warn('Chart.js configuration error:', error);
            }
        }
    });
</script>

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
                        <h5 class="card-title">作業時間グラフ</h5>
                        <div id="graph-container" style="height: 600px; overflow-x: auto;">
                            <canvas id="timeGraph" width="2400" height="600"></canvas>
                        </div>
                        <div id="graph-legend" class="graph-legend" style="display: none;">
                            <h6>凡例</h6>
                            <div id="legend-content"></div>
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
    
    const selectedDate = document.getElementById('selected-date').value;
    const graphType = document.getElementById('graph-type').value;
    const helpno = '{{ isset($data2[0]) ? $data2[0]["Helper_id"] : "" }}';
    
    console.log('Form submitted:', {
        selectedDate: selectedDate,
        graphType: graphType,
        helpno: helpno,
        helpnoType: typeof helpno,
        helpnoLength: helpno ? helpno.length : 0
    });
    
    // data2の内容をデバッグ出力
    console.log('data2 from server:', @json($data2));
    
    // helpnoが空でないかチェック
    if (!helpno || helpno.trim() === '') {
        console.error('helpno is empty or invalid');
        alert('作業者IDが取得できません。ページを再読み込みしてください。');
        return;
    }
    
    if (!selectedDate) {
        alert('年月日を選択してください。');
        return;
    }
    
    if (!helpno) {
        alert('作業者が選択されていません。');
        return;
    }
    
    // CSRFトークンを取得
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('CSRFトークンが見つかりません。');
        return;
    }
    
    console.log('Sending request with:', {
        helpno: helpno,
        selected_date: selectedDate,
        graph_type: graphType,
        csrf_token: csrfToken
    });
    
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
        console.log('Response status:', response.status);
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
        
        // グラフデータの詳細を確認
        if (data.graphData) {
            console.log('Graph data keys:', Object.keys(data.graphData));
            Object.keys(data.graphData).forEach(taskName => {
                console.log('Task data for ' + taskName + ':', data.graphData[taskName]);
                console.log('Task data type:', typeof data.graphData[taskName]);
                if (data.graphData[taskName]) {
                    console.log('Task data keys:', Object.keys(data.graphData[taskName]));
                    console.log('Sample values:', {
                        '09:00': data.graphData[taskName]['09:00'],
                        '09:30': data.graphData[taskName]['09:30'],
                        '10:00': data.graphData[taskName]['10:00'],
                        '10:30': data.graphData[taskName]['10:30']
                    });
                }
            });
        }
        
        // エラーレスポンスの処理
        if (data.error) {
            alert(data.message || 'エラーが発生しました。');
            return;
        }
        
        if (!data || !data.taskNames || data.taskNames.length === 0) {
            alert('指定された日付にデータが見つかりませんでした。');
            return;
        }
        
        console.log('Task names:', data.taskNames);
        console.log('Graph data:', data.graphData);
        
        // 各作業のデータを詳細にログ出力
        data.taskNames.forEach(taskName => {
            console.log('Task data for ' + taskName + ':', data.graphData[taskName]);
        });
        
        createTimeGraph(data);
        createLegend(data);
    })
    .catch(error => {
        console.error('Error:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        
        // エラーレスポンスの詳細を表示
        if (error.response) {
            error.response.json().then(data => {
                console.error('Server error response:', data);
                alert('サーバーエラー: ' + (data.message || error.message));
            }).catch(() => {
                alert('データの取得に失敗しました。: ' + error.message);
            });
        } else {
            alert('データの取得に失敗しました。: ' + error.message);
        }
    });
});

function createTimeGraph(data) {
    console.log('Creating time graph with data:', data);
    
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
    
    // Chart.jsが利用可能かチェック
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
    
    const colors = data.graphType === 'type' ? typeColors : categoryColors;
    
    // 30分単位の時間軸のデータを作成（48スロット）
    const timeLabels = [];
    for (let i = 0; i < 24; i++) {
        timeLabels.push(`${i.toString().padStart(2, '0')}:00`);
        timeLabels.push(`${i.toString().padStart(2, '0')}:30`);
    }
    
    // 横棒グラフ用のデータセットを作成
    const datasets = [];
    console.log('Creating datasets for task names:', data.taskNames);
    console.log('Graph data structure:', data.graphData);
    
    data.taskNames.forEach((taskName) => {
        const taskData = data.graphData[taskName];
        const taskDuration = data.taskDurations ? data.taskDurations[taskName] : 0;
        console.log('Task data for ' + taskName + ':', taskData);
        console.log('Task duration for ' + taskName + ':', taskDuration + ' minutes');
        
        // 横棒グラフ用：各作業名に対して1つのデータポイントを作成
        // 作業時間を分単位で表現
        const dataPoints = [taskDuration]; // 作業時間を1つの値として設定
        
        console.log('Data points for ' + taskName + ':', dataPoints);
        
        // データがあるかチェック
        const hasData = taskDuration > 0;
        console.log('Has data for ' + taskName + ':', hasData);
        
        if (hasData) {
            // 作業の色を決定（最初の非null値を使用）
            let taskColor = 'rgba(100, 100, 100, 0.7)'; // デフォルト色
            for (let timeSlot in taskData) {
                if (taskData[timeSlot] !== null && taskData[timeSlot] !== undefined) {
                    taskColor = colors[taskData[timeSlot]] || taskColor;
                    break;
                }
            }
            
            datasets.push({
                label: taskName,
                data: dataPoints,
                backgroundColor: taskColor,
                borderColor: taskColor.replace('0.7', '1'),
                borderWidth: 1,
                barPercentage: 0.8,
                categoryPercentage: 0.9
            });
            console.log('Added dataset for ' + taskName + ' with color: ' + taskColor);
        } else {
            console.log('No data found for ' + taskName);
        }
    });
    
    console.log('Final datasets:', datasets);
    console.log('Number of datasets:', datasets.length);
    console.log('Time labels for chart:', timeLabels);
    
    if (datasets.length === 0) {
        console.error('No datasets created');
        alert('グラフデータが作成できませんでした。');
        return;
    }
    
    timeGraph = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['作業時間'], // 横棒グラフ用のラベル
            datasets: datasets
        },
        options: {
            indexAxis: 'y', // 横棒グラフにする
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: '作業時間（分）'
                    },
                    ticks: {
                        callback: function(value, index, values) {
                            return value + '分';
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: '作業名'
                    },
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: data.graphType === 'type' ? '介護種別別作業時間（30分単位）' : 'カテゴリ別作業時間（30分単位）'
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
                            
                            const typeLabels = ['種別A', '種別B', '種別C'];
                            const categoryLabels = ['カテゴリA', 'カテゴリB', 'カテゴリC'];
                            const labels = data.graphType === 'type' ? typeLabels : categoryLabels;
                            
                            return `${context.dataset.label}: ${labels[value] || '未分類'}`;
                        }
                    }
                },
                // カスタムプラグイン：作業時間を表示
                customDurationPlugin: {
                    id: 'customDurationPlugin',
                    afterDraw: function(chart) {
                        const ctx = chart.ctx;
                        const datasets = chart.data.datasets;
                        
                        datasets.forEach((dataset, datasetIndex) => {
                            const taskName = dataset.label;
                            const taskDuration = data.taskDurations ? data.taskDurations[taskName] : 0;
                            
                            if (taskDuration > 0) {
                                // バーの終点位置を計算
                                const xAxis = chart.scales.x;
                                const yAxis = chart.scales.y;
                                
                                const xPos = xAxis.getPixelForValue(taskDuration);
                                const yPos = yAxis.getPixelForValue(datasetIndex);
                                
                                // テキストを描画
                                ctx.save();
                                ctx.font = 'bold 12px Arial';
                                ctx.fillStyle = '#333';
                                ctx.textAlign = 'left';
                                ctx.textBaseline = 'middle';
                                
                                // バーの終点に少しオフセットを加えて表示
                                ctx.fillText(`${taskDuration}分`, xPos + 8, yPos);
                                ctx.restore();
                            }
                        });
                    }
                }
            }
        }
    });
}

function createLegend(data) {
    const legendContainer = document.getElementById('legend-content');
    const legendDiv = document.getElementById('graph-legend');
    
    if (!legendContainer || !legendDiv) return;
    
    // 凡例をクリア
    legendContainer.innerHTML = '';
    
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
    
    const colors = data.graphType === 'type' ? typeColors : categoryColors;
    const labels = data.graphType === 'type' 
        ? ['種別A', '種別B', '種別C']
        : ['カテゴリA', 'カテゴリB', 'カテゴリC'];
    
    // 凡例アイテムを作成
    Object.keys(colors).forEach(key => {
        const legendItem = document.createElement('div');
        legendItem.className = 'legend-item';
        
        const colorBox = document.createElement('span');
        colorBox.className = 'legend-color';
        colorBox.style.backgroundColor = colors[key];
        
        const label = document.createElement('span');
        label.textContent = labels[key] || `未分類(${key})`;
        
        legendItem.appendChild(colorBox);
        legendItem.appendChild(label);
        legendContainer.appendChild(legendItem);
    });
    
    // 凡例を表示
    legendDiv.style.display = 'block';
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

.graph-legend {
    margin-top: 20px;
    padding: 10px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.legend-item {
    display: inline-block;
    margin-right: 20px;
    margin-bottom: 10px;
}

.legend-color {
    display: inline-block;
    width: 20px;
    height: 20px;
    margin-right: 5px;
    border: 1px solid #ccc;
}

.form-control {
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>
@endsection


