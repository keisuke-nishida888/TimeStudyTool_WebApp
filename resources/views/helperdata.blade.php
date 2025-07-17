@extends('layouts.parent')

@section('content')
<script src="/js/helperdata.js"></script>


<div class="allcont">

<div id="vertical_items">

<input type="image" id="btn_datadisp_pre"  src="image/img_datadisp.png" alt="データ表示" onclick="VisibleChange(this.id)" border="0">


<!-- CSV出力ボタン -->
{{-- <input type="image" id="btn_csvoutput_pre"  src="image/img_csvoutput.png" alt="CSV出力" onclick="VisibleChange(this.id)" border="0">
<input type="image" id="btn_csvoutput"  src="image/img_yes.png" alt="CSV出力" onclick="csvoutput()" style="visibility:Hidden;" border="0"> --}}
<div id="a_average_data">
    <button id="btn_average_data" class="average_data" onclick="averageDataDisp({{$data2[0]['facilityno']}})">平均値表示切替</button>
</div>
{{-- <div id="a_csvoutput_helper_list">
    <button id="btn_csvoutput_helper_list" class="csvoutput_helper_list" onclick="helperListCsvOutput({{$facilityno}})">CSV出力　介助者一覧</button>
</div> --}}

{{-- <nobr id="txt_wearable" style="visibility:Hidden;">※心拍データは登録されていません</nobr> --}}



@if(isset($data2[0]['Helper_id']) && isset($data2[0]['facilityno']))
    @if($data2[0]['Helper_id'] != "" && $data2[0]['facilityno'] != "")
        <form id="a_comparison" action = '/comparison?helperno={{$data2[0]['Helper_id']}}&facilityno={{$data2[0]['facilityno']}}' method = "post">
    @else
        <form id="a_comparison" action = '/comparison'  method = "post">
    @endif
@else
    <form id="a_comparison" action = '/comparison'  method = "post">
@endif
@csrf
    @if(isset($data2[0]['Helper_id']))
        <input id="targetid2" type="hidden" name="id" value="{{$data2[0]['Helper_id']}}">
    @else
        <input id="targetid2" type="hidden" name="id" value="0">
    @endif
    <input type="image" class="img_style5" src="image/img_comparison.png" alt="比較画面" border="0">
</form>

</div>

<p id="wearable_error"></p>

{{-- 施設/全国平均が表示される際は非表示 --}}
<div id="total_disp">
    <div id="risk_div">
        <img id="risk_back" src="image/img_risk.png">
        <nobr id="disp_title" class="text_style">腰痛リスク</nobr>
        <nobr id="total_risk" class="risk_val"> -</nobr>
        <nobr id="disp_unit">%</nobr>
    </div>

    <div id="avetm_for_div">
        <nobr id="avetm_for_title" class="text_style">1回の前傾平均時間</nobr>
        <nobr id="total_avetm_for" class="tm_val"> -</nobr>
    </div>

    <div id="alltm_for_div">
        <nobr id="alltm_for_title" class="text_style">前傾合計時間</nobr>
        <nobr id="total_alltm_for" class="tm_val"> -</nobr>
    </div>


    <div id="avetm_tw_div">
        <nobr class="text_style">1回のひねり平均時間</nobr>
        <nobr id="total_avetm_tw" class="tm_val"> -</nobr>
    </div>

    <div id="alltm_tw_div">
        <nobr class="text_style">ひねり合計時間</nobr>
        <nobr id="total_alltm_tw" class="tm_val"> -</nobr>
    </div>


    <div id="cnt_for_div">
        <img id="totalcnt_back" src="image/img_totalcnt.png">
        <nobr class="dispcnt_title">前傾回数</nobr>
        <nobr id="total_cnt_for" class="dispcnt_val"> -</nobr>
        <nobr class="dispcnt_unit">回</nobr>
    </div>

    <div id="cnt_tw_div">
        <img id="totalcnt_tw_back" src="image/img_totalcnt_tw.png">
        <nobr class="dispcnt_title">前傾中のひねり回数</nobr>
        <nobr id="total_cnt_tw" class="dispcnt_val"> -</nobr>
        <nobr class="dispcnt_unit">回</nobr>
    </div>


    <div id="starttm_div">
        <nobr class="text_style">開始時間</nobr>
        <nobr id="total_starttm" class="totaltm_val"> -</nobr>
    </div>

    <div id="endtm_div">
        <nobr class="text_style">終了時間</nobr>
        <nobr id="total_endtm" class="totaltm_val"> -</nobr>
    </div>

    <div id="alltm_div">
        <nobr class="text_style">総合時間</nobr>
        <nobr id="total_alltm" class="totaltm_val"> -</nobr>
    </div>

</div>

{{-- 施設/全国平均が表示される際に表示 --}}
<div id="average_total_disp">
    <div id="average_risk_div">
        <img id="average_risk_back" src="image/img_risk.png">
        <nobr id="disp_title1" class="text_style">腰痛リスク(個人)</nobr>
        <nobr id="average_total_risk" class="risk_val1"> -</nobr>
        <nobr id="average_disp_unit">%</nobr>
    </div>

    <div id="avetm_for_div1">
        <nobr class="text_style2">1回の前傾平均時間</nobr>
        <nobr id="average_total_avetm_for" class="tm_val1"> -</nobr>
    </div>

    <div id="alltm_for_div1">
        <nobr class="text_style2">前傾合計時間</nobr>
        <nobr id="average_total_alltm_for" class="tm_val1"> -</nobr>
    </div>


    <div id="avetm_tw_div1">
        <nobr class="text_style2">1回のひねり平均時間</nobr>
        <nobr id="average_total_avetm_tw" class="tm_val1"> -</nobr>
    </div>

    <div id="alltm_tw_div1">
        <nobr class="text_style2">ひねり合計時間</nobr>
        <nobr id="average_total_alltm_tw" class="tm_val1"> -</nobr>
    </div>


    <div id="cnt_for_div1">
        <img id="average_totalcnt_back" src="image/img_totalcnt.png">
        <nobr class="dispcnt_title1">前傾回数</nobr>
        <nobr id="average_total_cnt_for" class="dispcnt_val1"> -</nobr>
        <nob class="dispcnt_unit1">回</nobr>
    </div>

    <div id="cnt_tw_div1">
        <img id="average_totalcnt_tw_back1" src="image/img_totalcnt_tw.png">
        <nobr class="dispcnt_title1">前傾中のひねり回数</nobr>
        <nobr id="average_total_cnt_tw" class="dispcnt_val1"> -</nobr>
        <nobr class="dispcnt_unit1">回</nobr>
    </div>


    <div id="average_starttm_div">
        <nobr>開始時間</nobr>
        <nobr id="average_total_starttm" class="totaltm_val1"> -</nobr>
    </div>

    <div id="average_endtm_div">
        <nobr>終了時間</nobr>
        <nobr id="average_total_endtm" class="totaltm_val1"> -</nobr>
    </div>

    <div id="average_alltm_div">
        <nobr>総合時間</nobr>
        <nobr id="average_total_alltm" class="totaltm_val1"> -</nobr>
    </div>

</div>

<div id="average_content">
    <div id="facility_average_disp">
        <div class="facility_average_title">【施設平均】</div>
        <div id="facility_average_risk_div">
            <img id="facility_average_risk_back" src="image/img_risk.png">
            <nobr id="facility_average_disp_title" class="text_style">腰痛リスク</nobr>
            <nobr id="facility_average_risk" class="risk_val1"> -</nobr>
            <nobr id="facility_average_disp_unit">%</nobr>
        </div>

        <div id="facility_average_avetm_for_div">
            <nobr class="text_style2">1回の前傾平均時間</nobr>
            <nobr id="facility_average_avetm_for" class="tm_val1"> -</nobr>
        </div>

        <div id="facility_average_alltm_for_div">
            <nobr class="text_style2">前傾合計時間</nobr>
            <nobr id="facility_average_alltm_for" class="tm_val1"> -</nobr>
        </div>

        <div id="facility_average_avetm_tw_div">
            <nobr class="text_style2">1回のひねり平均時間</nobr>
            <nobr id="facility_average_avetm_tw" class="tm_val1"> -</nobr>
        </div>

        <div id="facility_average_alltm_tw_div">
            <nobr class="text_style2">ひねり合計時間</nobr>
            <nobr id="facility_average_alltm_tw" class="tm_val1"> -</nobr>
        </div>


        <div id="facility_average_cnt_for_div">
            <img id="facility_average_totalcnt_back" src="image/img_totalcnt.png">
            <nobr class="dispcnt_title1">前傾回数</nobr>
            <nobr id="facility_average_cnt_for" class="dispcnt_val1"> -</nobr>
            <nob class="dispcnt_unit1">回</nobr>
        </div>

        <div id="facility_average_cnt_tw_div">
            <img id="facility_average_totalcnt_tw_back" src="image/img_totalcnt_tw.png">
            <nobr class="dispcnt_title1">前傾中のひねり回数</nobr>
            <nobr id="facility_average_cnt_tw" class="dispcnt_val1"> -</nobr>
            <nobr class="dispcnt_unit1">回</nobr>
        </div>

        <div id="facility_average_alltm_div">
            <nobr>総合時間</nobr>
            <nobr id="facility_average_alltm" class="totaltm_val1"> -</nobr>
        </div>

    </div>

    <div id="all_average_disp">
        <div class="all_average_title">【全国平均】</div>
                <select id="allAverageDatas"
                onChange="allDataAverage({{$data2[0]['facilityno']}})"
                class="facility_average_disp_select">
                <option disabled selected>選択して下さい。</option>
                <option value="0">全国</option>
                @foreach($code as $valcode)
                    @if($valcode['codeno']==2)
                        <option value={{$valcode['value']}}>{{$valcode['selectname']}}</option>
                    @endif
                @endforeach
                </select>
        <div id="all_average_risk_div">
            <img id="all_average_risk_back" src="image/img_risk.png">
            <nobr id="all_average_disp_title" class="text_style">腰痛リスク</nobr>
            <nobr id="all_average_risk" class="risk_val1"> -</nobr>
            <nobr id="all_average_disp_unit">%</nobr>
        </div>

        <div id="all_average_avetm_for_div">
            <nobr class="text_style2">1回の前傾平均時間</nobr>
            <nobr id="all_average_avetm_for" class="tm_val1"> -</nobr>
        </div>

        <div id="all_average_alltm_for_div">
            <nobr class="text_style2">前傾合計時間</nobr>
            <nobr id="all_average_alltm_for" class="tm_val1"> -</nobr>
        </div>

        <div id="all_average_avetm_tw_div">
            <nobr class="text_style2">1回のひねり平均時間</nobr>
            <nobr id="all_average_avetm_tw" class="tm_val1"> -</nobr>
        </div>

        <div id="all_average_alltm_tw_div">
            <nobr class="text_style2">ひねり合計時間</nobr>
            <nobr id="all_average_alltm_tw" class="tm_val1"> -</nobr>
        </div>


        <div id="all_average_cnt_for_div">
            <img id="all_average_totalcnt_back" src="image/img_totalcnt.png">
            <nobr class="dispcnt_title1">前傾回数</nobr>
            <nobr id="all_average_cnt_for" class="dispcnt_val1"> -</nobr>
            <nob class="dispcnt_unit1">回</nobr>
        </div>

        <div id="all_average_cnt_tw_div">
            <img id="all_average_totalcnt_tw_back" src="image/img_totalcnt_tw.png">
            <nobr class="dispcnt_title1">前傾中のひねり回数</nobr>
            <nobr id="all_average_cnt_tw" class="dispcnt_val1"> -</nobr>
            <nobr class="dispcnt_unit1">回</nobr>
        </div>

        <div id="all_average_alltm_div">
            <nobr >総合時間</nobr>
            <nobr id="all_average_alltm" class="totaltm_val1"> -</nobr>
        </div>

    </div>
</div>

<div id="span">
<select id="span_h" name="span_h" value="">
    @for($i=0;$i<13;$i++)
            <option value={{$i}}>{{$i}}</option>
    @endfor
</select>
時間
<select id="span_m" name="span_m" value="">
    @for($i=0;$i<60;$i++)
            @if(0<$i && $i<5)
                @continue
            @endif
            @if($i == 10)
                <option value={{$i}} selected>{{$i}}</option>
            @elseif(($i % 5) == 0)
                <option value={{$i}}>{{$i}}</option>
            @endif
    @endfor
</select>
分間隔で表示する
</div>

<form id = "form_datadisp1" method="POST" action="helperdata_dispctrl" name = "form_datadisp1" >
@csrf


        <div class="container2">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <!-- 施設名 -->
                        <div class="form-group row">
                            <label for="facility">{{ __('施設名') }}</label>
                            <div>
                            @if(isset($data2))
                                @foreach($data2 as $val)
                                    <input  type="text" value={{$val['facility']}} disabled>
                                    <input id="facility" type="hidden" name="facility" value={{$val['facility']}}>
                                @endforeach
                            @else
                                <input  type="text" value="" disabled>
                                <input id="facility" type="hidden" class="form-control @error('facility') is-invalid @endif" name="facility" value="">
                            @endif

                                @error('facility')
                                    <span class="invalid-feedback validate" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- 介助者名 -->
                        <div class="form-group row">
                            <label for="helpername">{{ __('介助者名') }}</label>

                            <div>
                                @if(isset($data2))
                                    @foreach($data2 as $val)
                                        <input  type="text" value={{$val['helpername']}} disabled>
                                        <input id="helpername" type="hidden" name="helpername" value={{$val['Helper_id']}}>
                                    @endforeach
                                @else
                                    <input  type="text" value="" disabled>
                                    <input id="helpername" type="hidden" name="helpername" value="">

                                @endif

                                @error('id')
                                    <span class="invalid-feedback validate" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="div2">
                            <!-- 年月日 -->
                            <!-- 年月日① -->
                            <div>
                                <label for="hms">{{ __('年月日') }}</label>

                                <div class="col-md-6">
                                    <select  id="ymd" name="ymd" value="{{ old('ymd') }}" onchange="change('ymd')" autofocus>
                                    @foreach($ymdGroupData as $val)
                                        <option value={{$val['ymd']}}>
                                            <?php
                                            $Y = substr($val['ymd'], 0, 4);
                                            $M = substr($val['ymd'], 4, 2);
                                            $D = substr($val['ymd'], 6, 2);
                                            echo $Y."年".$M."月".$D."日";
                                            ?>
                                        </option>
                                    @endforeach

                                    </select>
                                    @error('ymd')
                                        <span class="invalid-feedback validate" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- 時間範囲 -->
                            <!-- 時間範囲① -->
                            <div>
                                <label for="hms">{{ __('時間') }}</label>

                                <div class="col-md-6">
                                    <select  id="hms" name="hms" value="{{ old('hms') }}">
                                        @foreach($data as $val)
                                            @if($data[0]['ymd'] == $val['ymd'])
                                                <option value={{$val['hms']}}>
                                                    <?php
                                                    $H = substr($val['hms'], 0, 2);
                                                    $M = substr($val['hms'], 2, 2);
                                                    echo $H.":".$M;
                                                    ?>
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('hms')
                                        <span class="invalid-feedback validate" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    <!-- データ表示ボタン -->
        <input id="btn_datadisp" type="image" src="image/img_yes.png" alt="データ表示" border="0">

</form>


    <!-- <div class="chartWrapper">
        <div class="chartAreaWrapper">
            <div class="chartAreaWrapper2"> -->
                <div class="chart">
                <canvas id="myChart"></canvas>
                </div>
            <!-- </div>
        </div>           -->
    <canvas id="myChartAxis" height="526" width="0"></canvas>
    <!-- </div> -->
    <canvas id="myChartAxis1_2" height="526" width="0"></canvas>

<!-- Time Study Tool Chart -->


<div id="movableChart"
     style="
        position:absolute;
        left:50%; top:auto; bottom:0;
        transform:translateX(-50%);
        width:800px; height:150px;
        background:#fff;
        z-index:100;
        box-shadow:0 -4px 16px rgba(0,0,0,0.1);
        border-radius:16px 16px 0 0;
        padding:0;
        margin-bottom:8px;
        text-align:center;
        overflow:visible;
     ">
    <div id="chartHeader"
         style="cursor:move; user-select:none; background:#eee; border-radius:16px 16px 0 0; padding:10px 0 6px 0;">
        <span style="font-size:1.1em;">Time Study Tool データ</span>
    </div>
    <canvas id="timeStudyChart" width="360" height="180" style="margin:12px 0 0 0; background:#f7f7f7; border-radius:8px;"></canvas>
    <!-- Resize handles（右・下・右下）-->
    <div id="resizeRight"  style="position:absolute; right:0; top:10px; width:10px; height:70%; cursor:e-resize;"></div>
    <div id="resizeBottom" style="position:absolute; left:10px; bottom:0; width:calc(100% - 20px); height:10px; cursor:s-resize;"></div>
    <div id="resizeCorner" style="position:absolute; right:0; bottom:0; width:18px; height:18px; cursor:nwse-resize; background:rgba(180,180,180,0.2); border-radius:4px;"></div>
</div>

<!-- Time Study Tool Chart js-->
<script>
(function(){
    const chartDiv = document.getElementById("movableChart");
    const header = document.getElementById("chartHeader");
    const canvas = document.getElementById("timeStudyChart");
    const resizeRight = document.getElementById("resizeRight");
    const resizeBottom = document.getElementById("resizeBottom");
    const resizeCorner = document.getElementById("resizeCorner");

    // ドラッグ移動
    let isMoving = false, moveOffset = {x:0, y:0};

    header.addEventListener('mousedown', function(e){
        isMoving = true;
        moveOffset.x = e.clientX - chartDiv.getBoundingClientRect().left;
        moveOffset.y = e.clientY - chartDiv.getBoundingClientRect().top;
        chartDiv.style.transition = "none";
        document.body.style.userSelect = "none";
    });

    document.addEventListener('mousemove', function(e){
        if(isMoving){
            chartDiv.style.left = (e.clientX - moveOffset.x) + "px";
            chartDiv.style.top  = (e.clientY - moveOffset.y) + "px";
            chartDiv.style.bottom = "auto";
            chartDiv.style.transform = "none";
        }
        if(isResizingR){
            const minW = 220;
            let newW = Math.max(minW, e.clientX - chartDiv.getBoundingClientRect().left);
            chartDiv.style.width = newW + "px";
            canvas.width = newW - 40;
        }
        if(isResizingB){
            const minH = 100;
            let newH = Math.max(minH, e.clientY - chartDiv.getBoundingClientRect().top);
            chartDiv.style.height = newH + "px";
            canvas.height = newH - 60;
        }
        if(isResizingC){
            // 右下斜め
            const minW = 220, minH = 100;
            let newW = Math.max(minW, e.clientX - chartDiv.getBoundingClientRect().left);
            let newH = Math.max(minH, e.clientY - chartDiv.getBoundingClientRect().top);
            chartDiv.style.width = newW + "px";
            chartDiv.style.height = newH + "px";
            canvas.width = newW - 40;
            canvas.height = newH - 60;
        }
    });

    document.addEventListener('mouseup', function(e){
        isMoving = false;
        isResizingR = false;
        isResizingB = false;
        isResizingC = false;
        document.body.style.userSelect = "";
    });

    // 右リサイズ
    let isResizingR = false;
    resizeRight.addEventListener('mousedown', function(e){
        isResizingR = true;
        document.body.style.userSelect = "none";
        e.stopPropagation();
    });

    // 下リサイズ
    let isResizingB = false;
    resizeBottom.addEventListener('mousedown', function(e){
        isResizingB = true;
        document.body.style.userSelect = "none";
        e.stopPropagation();
    });

    // 右下角リサイズ（斜め）
    let isResizingC = false;
    resizeCorner.addEventListener('mousedown', function(e){
        isResizingC = true;
        document.body.style.userSelect = "none";
        e.stopPropagation();
    });

    document.addEventListener('DOMContentLoaded', function() {
    var timeStudyData = @json($timeStudyData ?? []);
    if (timeStudyData.length > 0) {
        var ctx = document.getElementById('timeStudyChart').getContext('2d');

        var labels = [];
        var durations = [];
        var taskNames = [];

        timeStudyData.forEach(function(item) {
            var startTime = new Date(item.start);
            var stopTime = new Date(item.stop);
            var duration = (stopTime - startTime) / 60000; // 分単位

            // 「開始時間 作業名」の組み合わせ
            var label = startTime.toLocaleTimeString('ja-JP', { hour: '2-digit', minute: '2-digit' }) + " " + item.task_name;
            labels.push(label);

            durations.push(duration);
            taskNames.push(item.task_name);
        });

        var timeStudyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels, // ←X軸に「時間＋作業名」
                datasets: [{
                    label: '作業時間（分）',
                    data: durations,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true },
                        scaleLabel: {
                            display: true,
                            labelString: '時間（分）'
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: '開始時間＋作業名'
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var index = tooltipItem.index;
                            return [
                                '作業: ' + taskNames[index],
                                '時間: ' + durations[index] + '分'
                            ];
                        }
                    }
                }
            }
        });
    }
});


})();
</script>




</div>
@endsection

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var timeStudyData = @json($timeStudyData ?? []);
        if (timeStudyData.length > 0) {
            var ctx = document.getElementById('timeStudyChart').getContext('2d');

            var labels = [];
            var durations = [];
            var taskNames = [];

            timeStudyData.forEach(function(item) {
                var startTime = new Date(item.start);
                var stopTime = new Date(item.stop);
                var duration = (stopTime - startTime) / 60000; // 分単位に変換

                labels.push(startTime.toLocaleTimeString('ja-JP', { hour: '2-digit', minute: '2-digit' }));
                durations.push(duration);
                taskNames.push(item.task_name);
            });

            var timeStudyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '作業時間（分）',
                        data: durations,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            },
                            scaleLabel: {
                                display: true,
                                labelString: '時間（分）'
                            }
                        }],
                        xAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: '開始時間'
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var index = tooltipItem.index;
                                return [
                                    '作業: ' + taskNames[index],
                                    '時間: ' + durations[index] + '分'
                                ];
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<script type="text/javascript">



    //コンボボックスの可変
    function change(name)
    {
        switch(name)
        {
            case "ymd":

            var obj = document.getElementById('hms');
            //valueの値に応じて時間のコンボボックス切り替える
            if(@json($data)!= 'undefined')
            {
                var val = @json($data);

                //時間のコンボボックスを削除
                while(obj.lastChild)
                {
                    obj.removeChild(obj.lastChild);
                }

                Object.keys(val).forEach(function (key)
                {
                    //日付に含まれる時刻か判断
                    if(val[key]['ymd'] == document.getElementById('ymd').value)
                    {
                        let option = document.createElement('option');
                        option.value = val[key]['hms'];
                        option.innerHTML = val[key]['hms'].substr(0,2) + ':'
                                        + val[key]['hms'].substr(2,2);
                        obj.appendChild(option);
                    }
                });
            }
            break;
            case "ymd2":

            var obj = document.getElementById('hms2');
            //valueの値に応じて時間のコンボボックス切り替える
            if(@json($data)!= 'undefined')
            {
                var val = @json($data);


                //時間のコンボボックスを削除
                while(obj.lastChild)
                {
                    obj.removeChild(obj.lastChild);
                }

                Object.keys(val).forEach(function (key)
                {
                    //日付に含まれる時刻か判断
                    if(val[key]['ymd'] == document.getElementById('ymd2').value)
                    {
                        let option = document.createElement('option');
                        option.value = val[key]['hms'];
                        option.innerHTML = val[key]['hms'].substr(0,2) + ':'
                                        + val[key]['hms'].substr(2,2);
                        obj.appendChild(option);
                    }
                });
            }
            break;

            default :
            break;
        }
    }

    //csv出力
    function csvoutput()
    {
        // フォームデータを取得
        var formdata = new FormData(document.getElementById("form_datadisp1"));
        if(formdata.get('ymd') == "" || formdata.get('ymd') == null)
        {
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById("btn_yes").style.visibility = 'collapse';
            document.getElementById("btn_csvoutput").style.visibility = 'collapse';
            Ctrl_pop("error","visible",23);
            return ;
        }
        if(formdata.get('hms') == "" || formdata.get('hms') == null)
        {
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById("btn_yes").style.visibility = 'collapse';
            document.getElementById("btn_csvoutput").style.visibility = 'collapse';
            Ctrl_pop("error","visible",24);
            return ;
        }


        // XMLHttpRequestによるアップロード処理
        var url = "csvoutput";
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);

        xhr.responseType = 'arraybuffer'; //arraybuffer型のレスポンスを受け付ける
        xhr.dataType = 'binary';
        xhr.send(formdata);

        //ファイル名用のデータ取得
        //ファイル名は、「デバイス名（腰痛リスクに付ける固有の名前）_年月日.csv」
        var devname = "";
        var date = "";
        var flag=0;
        var total = @json($data);
        Object.keys(total).forEach(function (key)
        {
            if(flag == 0)
            {
                if(total[key]['ymd'] == formdata.get('ymd'))
                {
                    if(total[key]['hms'].trim() == formdata.get('hms').trim())
                    {
                        devname = total[key]['backpainno'];
                        date = total[key]['ymd'];
                        flag = 1;
                    }
                }
            }
        });
        try
        {
            xhr.onreadystatechange = function()
            {
                if (xhr.readyState == 4 && xhr.status == 200)
                {
                    var response_data = this.response;
                    var uint8_array = new Uint8Array(response_data);
                    //エラーの場合
                    if(uint8_array.length == 2)
                    {
                        var text_decoder = new TextDecoder("Shift_JIS");
                        var str = text_decoder.decode(Uint8Array.from(uint8_array).buffer);
                        if(str == "-1")
                        {
                            document.getElementById('pop_alert_back').style.visibility = 'collapse';
                            document.getElementById("btn_yes").style.visibility = 'collapse';
                            document.getElementById("btn_csvoutput").style.visibility = 'collapse';
                            Ctrl_pop("error","visible",17);
                            return ;
                        }
                        return ;
                    }
                    // rtn : -1/エラー ,-2/アップロード失敗,-3/拡張子がxlslのファイルを選択してください,-4/ファイルを選択してください
                    var downloadData = new Blob([uint8_array], {type: "text/csv;"});
                    // var filename = formdata.get('fname') + "xlsx";
                    //ファイル名は、「デバイス名（腰痛リスクに付ける固有の名前）_年月日.csv」


                    var filename = devname + "_" +  date + ".csv";
                    //ファイルのダウンロードにはブラウザ毎に処理を分ける
                    if(window.navigator.msSaveBlob)
                    { // for IE
                        window.navigator.msSaveBlob(downloadData, filename);
                    }
                    else
                    {
                        //レスポンスからBlobオブジェクト＆URLの生成
                        var downloadUrl  = (window.URL || window.webkitURL).createObjectURL(downloadData);
                        var link = document.createElement("a");
                        //URLをセット
                        link.href = downloadUrl;
                        // //ダウンロードさせるファイル名の生成
                        link.download = filename;
                        // //クリックイベント発火
                        link.click();

                        link.remove();
                        URL.revokeObjectURL(downloadData);
                        Ctrl_pop('','collapse','');
                    }

                }
            };
        }
        catch{alert();}
    }
    //各施設の全国データの表示
    function allDataAverage(facilityno) {
        // XMLHttpRequestによるアップロード処理
        var url = "/averagedata";
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.responseType = 'json'; //JSON型のレスポンスを受け付ける
        xhr.dataType = 'binary';

        // パラメータ設定
        var param = "facilityno=" + facilityno;

        xhr.send(param);
        try
        {
            xhr.onreadystatechange = function()
            {
                if (xhr.readyState == 4 && xhr.status == 200)
                {
                    var response_data = this.response;
                    const allAverageData = document.getElementById('allAverageDatas');
                    for (const existPassRisk of response_data['existPass']) {
                        const allTotalRisk = document.getElementById('all_average_risk');
                        const allTotalFxa = document.getElementById('all_average_avetm_for');
                        const allFxc = document.getElementById('all_average_cnt_for');
                        const allTxc = document.getElementById('all_average_cnt_tw');
                        const allTotalFxt = document.getElementById('all_average_alltm_for');
                        const allTotalTxa = document.getElementById('all_average_avetm_tw');
                        const allTotalTxt = document.getElementById('all_average_alltm_tw');
                        const allTotalAlhms = document.getElementById('all_average_alltm');
                        // 全国の平均値を各要素に反映
                        if (allAverageData.value === '0') {
                            allTotalRisk.innerHTML = response_data['allTotalRisk'];
                            allTotalFxa.innerHTML = response_data['allTotalFxa'];
                            allFxc.innerHTML = response_data['allFxc'];
                            allTxc.innerHTML = response_data['allTxc'];
                            allTotalFxt.innerHTML = response_data['allTotalFxt'];
                            allTotalTxa.innerHTML = response_data['allTotalTxa'];
                            allTotalTxt.innerHTML = response_data['allTotalTxt'];
                            allTotalAlhms.innerHTML = response_data['allTotalAlhms'];
                            return
                        // 各施設区分の全国の平均値を表示
                        // 特別養護老人ホームの平均
                        } else if (allAverageData.value === '1' && existPassRisk[8] === '1') {
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 老人保健施設の平均
                        } else if (allAverageData.value === '2' && existPassRisk[8] === '2'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 高齢者通所施設の平均
                        } else if (allAverageData.value === '3' && existPassRisk[8] === '3'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 身体障がい者入所施設の平均
                        } else if (allAverageData.value === '4' && existPassRisk[8] === '4'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 身体障がい者通所施設の平均
                        } else if (allAverageData.value === '5' && existPassRisk[8] === '5'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 回復期病院の平均
                        } else if (allAverageData.value === '6' && existPassRisk[8] === '6'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 急性期病院の平均
                        } else if (allAverageData.value === '7' && existPassRisk[8] === '7'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 介護医療院の平均
                        } else if (allAverageData.value === '8' && existPassRisk[8] === '8'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // その他の平均
                        } else if (allAverageData.value === '9' && existPassRisk[8] === '9'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 有料老人ホームの平均
                        } else if (allAverageData.value === '10' && existPassRisk[8] === '10'){
                            allTotalRisk.innerHTML = existPassRisk[0];
                            allTotalFxa.innerHTML = existPassRisk[1];
                            allFxc.innerHTML = existPassRisk[2];
                            allTxc.innerHTML = existPassRisk[3];
                            allTotalFxt.innerHTML = existPassRisk[4];
                            allTotalTxa.innerHTML = existPassRisk[5];
                            allTotalTxt.innerHTML = existPassRisk[6];
                            allTotalAlhms.innerHTML = existPassRisk[7];
                            return
                        // 何も存在しない時は'0'を表示
                        } else {
                            allTotalRisk.innerHTML = '0';
                            allTotalFxa.innerHTML = '00:00';
                            allFxc.innerHTML = '0';
                            allTxc.innerHTML = '0';
                            allTotalFxt.innerHTML = '00:00';
                            allTotalTxa.innerHTML = '00:00';
                            allTotalTxt.innerHTML = '00:00';
                            allTotalAlhms.innerHTML = '00:00';
                        }
                    }
                }
            };
        }
        catch{alert();}
    }

    // 施設データ表示
    function averageDataDisp(facilityno)
    {
        // XMLHttpRequestによるアップロード処理
        var url = "/averagedata";
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.responseType = 'json'; //JSON型のレスポンスを受け付ける
        xhr.dataType = 'binary';

        // パラメータ設定
        var param = "facilityno=" + facilityno;

        xhr.send(param);
        try
        {
            xhr.onreadystatechange = function()
            {
                if (xhr.readyState == 4 && xhr.status == 200)
                {
                    var response_data = this.response;
                    if (Object.keys(response_data).length > 0) {
                        // 施設の平均値を各要素に反映
                        document.getElementById('facility_average_risk').innerHTML = response_data['facilityTotalRisk'];
                        document.getElementById('facility_average_avetm_for').innerHTML = response_data['facilityTotalFxa'];
                        document.getElementById('facility_average_alltm_for').innerHTML = response_data['facilityTotalFxt'];
                        document.getElementById('facility_average_avetm_tw').innerHTML = response_data['facilityTotalTxa'];
                        document.getElementById('facility_average_alltm_tw').innerHTML = response_data['facilityTotalTxt'];
                        document.getElementById('facility_average_cnt_for').innerHTML = response_data['avgFacilityFxc'];
                        document.getElementById('facility_average_cnt_tw').innerHTML = response_data['avgFacilityTxc'];
                        document.getElementById('facility_average_alltm').innerHTML = response_data['facilityTotalAlhms'];
                    }
                }

                // 施設/全国平均データを表示するコンテンツを表示/非表示、切り替え
                var contentVisibility = document.getElementById('average_content').style.visibility;
                if (contentVisibility == 'collapse' || contentVisibility == '') {
                    document.getElementById('average_content').style.visibility = 'visible';
                    document.getElementById('average_total_disp').style.visibility = 'visible';
                    document.getElementById('total_disp').style.visibility = 'collapse';
                    // totalDispSmall();
                } else {
                    document.getElementById('average_content').style.visibility = 'collapse';
                    document.getElementById('average_total_disp').style.visibility = 'collapse';
                    document.getElementById('total_disp').style.visibility = 'visible';
                    // totalDispNormal();
                }
            };
        }
        catch{alert();}

    }

    // function totalDispNormal()
    // {
    //     // リスク関連
    //     document.getElementById('average_total_disp').id = 'total_disp';
    //     document.getElementById('average_risk_div').id = 'risk_div';
    //     document.getElementById('average_risk_back').id = 'risk_back';
    //     document.getElementById('disp_title1').id = 'disp_title';
    //     document.getElementById('total_risk').classList.remove("risk_val1");
    //     document.getElementById('average_disp_unit').id = 'disp_unit';

    //     document.getElementById('avetm_for_div1').id = 'avetm_for_div';
    //     document.getElementById('avetm_for_title').classList.remove('text_style2');
    //     document.getElementById('total_avetm_for').classList.remove('tm_val1');

    //     document.getElementById('alltm_for_div1').id = 'alltm_for_div';
    //     document.getElementById('alltm_for_title').classList.remove('text_style2');
    //     document.getElementById('total_alltm_for').classList.remove('tm_val1');
    // }

    // function totalDispSmall()
    // {
    //     // リスク関連
    //     document.getElementById('total_disp').id = 'average_total_disp';
    //     document.getElementById('risk_div').id = 'average_risk_div';
    //     document.getElementById('risk_back').id = 'average_risk_back';
    //     document.getElementById('disp_title').id = 'disp_title1';
    //     document.getElementById('total_risk').classList.add("risk_val1");
    //     document.getElementById('disp_unit').id = 'average_disp_unit';

    //     // 1回の前傾平均時間
    //     document.getElementById('avetm_for_div').id = 'avetm_for_div1';
    //     document.getElementById('avetm_for_title').classList.add('text_style2');
    //     document.getElementById('total_avetm_for').classList.add('tm_val1');

    //     // 前傾合計時間
    //     document.getElementById('alltm_for_div').id = 'alltm_for_div1';
    //     document.getElementById('alltm_for_title').classList.add('text_style2');
    //     document.getElementById('total_alltm_for').classList.add('tm_val1');

    // }


  </script>
