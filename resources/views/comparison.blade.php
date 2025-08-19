@extends('layouts.parent')

@section('content')
<script src="/js/helperdata.js"></script>

<div class="allcont">
<input type="image" id="btn_comparison_pre"  src="image/img_datadisp.png" alt="データ表示" onclick="VisibleChange(this.id)" border="0">


{{-- <nobr id="txt_wearable1" style="visibility:Hidden;">※心拍データは登録されていません</nobr>
<nobr id="txt_wearable2" style="visibility:Hidden;">※心拍データは登録されていません</nobr> --}}

<div id="total_disp1">
    <div id="risk_div">
        <img id="risk_back1" src="image/img_risk.png">
        <nobr id="disp_title1">腰痛リスク</nobr>
        <nobr id="total_risk" class="risk_val1"> -</nobr>
        <nobr id="disp_unit1">%</nobr>
    </div>

    <div id="avetm_for_div1">
        <nobr class="text_style2">1回の前傾平均時間</nobr>
        <nobr id="total_avetm_for" class="tm_val1"> -</nobr>
    </div>

    <div id="alltm_for_div1">
        <nobr class="text_style2">前傾合計時間</nobr>
        <nobr id="total_alltm_for" class="tm_val1"> -</nobr>
    </div>


    <div id="avetm_tw_div1">
        <nobr class="text_style2">1回のひねり平均時間</nobr>
        <nobr id="total_avetm_tw" class="tm_val1"> -</nobr>
    </div>

    <div id="alltm_tw_div1">
        <nobr class="text_style2">ひねり合計時間</nobr>
        <nobr id="total_alltm_tw" class="tm_val1"> -</nobr>
    </div>

    
    <div id="cnt_for_div1">
        <img id="totalcnt_back1" src="image/img_totalcnt.png">
        <nobr class="dispcnt_title1">前傾回数</nobr>
        <nobr id="total_cnt_for" class="dispcnt_val1"> -</nobr>
        <nob class="dispcnt_unit1">回</nobr>
    </div>

    <div id="cnt_tw_div1">
        <img id="totalcnt_tw_back1" src="image/img_totalcnt_tw.png">
        <nobr class="dispcnt_title1">前傾中のひねり回数</nobr>
        <nobr id="total_cnt_tw" class="dispcnt_val1"> -</nobr>
        <nobr class="dispcnt_unit1">回</nobr>
    </div>

    <div class="starttm_div1">
        <nobr>開始時間</nobr>
        <nobr id="total_starttm" class="totaltm_val1"> -</nobr>
    </div>

    <div class="endtm_div1">
        <nobr>終了時間</nobr>
        <nobr id="total_endtm" class="totaltm_val1"> -</nobr>
    </div>

    <div class="alltm_div1">
        <nobr>総合時間</nobr>
        <nobr id="total_alltm" class="totaltm_val1"> -</nobr>
    </div>

</div>


<div id="total_disp2">
    <div id="risk_div">
        <img id="risk_back1" src="image/img_risk.png">
        <nobr id="disp_title1">腰痛リスク</nobr>
        <nobr id="total_risk2" class="risk_val1"> -</nobr>
        <nobr id="disp_unit1">%</nobr>
    </div>
    <div id="avetm_for_div1">
        <nobr>1回の前傾平均時間</nobr>
        <nobr id="total_avetm_for2" class="tm_val1"> -</nobr>
    </div>

    <div id="alltm_for_div1">
        <nobr>前傾合計時間</nobr>
        <nobr id="total_alltm_for2" class="tm_val1"> -</nobr>
    </div>


    <div id="avetm_tw_div1">
        <nobr>1回のひねり平均時間</nobr>
        <nobr id="total_avetm_tw2" class="tm_val1"> -</nobr>
    </div>

    <div id="alltm_tw_div1">
        <nobr>ひねり合計時間</nobr>
        <nobr id="total_alltm_tw2" class="tm_val1"> -</nobr>
    </div>

    <div id="cnt_for_div1">
        <img id="totalcnt_back1" src="image/img_totalcnt.png">
        <nobr class="dispcnt_title1">前傾回数</nobr>
        <nobr id="total_cnt_for2" class="dispcnt_val1"> -</nobr>
        <nob class="dispcnt_unit1">回</nobr>
    </div>

    <div id="cnt_tw_div1">
        <img id="totalcnt_tw_back1" src="image/img_totalcnt_tw.png">
        <nobr class="dispcnt_title1">前傾中のひねり回数</nobr>
        <nobr id="total_cnt_tw2" class="dispcnt_val1"> -</nobr>
        <nobr class="dispcnt_unit1">回</nobr>
    </div>


    <div class="starttm_div1">
        <nobr>開始時間</nobr>
        <nobr id="total_starttm2" class="totaltm_val1"> -</nobr>
    </div>

    <div class="endtm_div1">
        <nobr>終了時間</nobr>
        <nobr id="total_endtm2" class="totaltm_val1"> -</nobr>
    </div>

    <div class="alltm_div1">
        <nobr>総合時間</nobr>
        <nobr id="total_alltm2" class="totaltm_val1"> -</nobr>
    </div>
</div>


<!-- <div class="chartWrapper2">
        <div class="chartAreaWrapper3">
            <div class="chartAreaWrapper4"> -->
                <div id="chart2">
                <canvas id="myChart2"></canvas>
                </div>
            <!-- </div>
        </div>           -->
    <canvas id="myChartAxis2" height="300" width="0"></canvas>
    <!-- </div> -->
    <canvas id="myChartAxis2_2" height="300" width="0"></canvas>

    <!-- <div class="chartWrapper3">
        <div class="chartAreaWrapper5">
            <div class="chartAreaWrapper6"> -->
            <div id="chart3">
                <canvas id="myChart3"></canvas>
                </div>
            <!-- </div>
        </div>           -->
    <canvas id="myChartAxis3" height="300" width="0"></canvas>
    <!-- </div> -->
    <nobr id="lb_time1">時間</nobr>

    <canvas id="myChartAxis3_2" height="300" width="0"></canvas>
    <nobr id="lb_time2">時間</nobr>
    
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

<div class="container3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">       

                    <form id = "form_datadisp2" method="POST" action="helperdata_dispctrl" name = "form_datadisp2" >    
                    @csrf                

                    
                        <!-- 施設名 -->
                        <div class="form-group row">
                            <label for="facility">{{ __('施設名') }}</label>
                            <div>
                            @if(isset($data2))
                                @foreach($data2 as $val)
                                    <input  type="text" value={{$val['facility']}} disabled>
                                    <input id="facility" type="hidden" class="form-control @error('facility') is-invalid @endif" name="facility" value={{$val['facility']}}>
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
                        <!-- 作業者名 -->
                        <div class="form-group row">
                            <label for="helpername">{{ __('作業者名') }}</label>

                            <div>
                                @if(isset($data2))
                                    @foreach($data2 as $val)
                                        <input  type="text" value={{$val['helpername']}} disabled>
                                        <input id="helpername" type="hidden" class="form-control @error('helpername') is-invalid @endif" name="helpername" value={{$val['Helper_id']}}>
                                    @endforeach
                                @else
                                    <input  type="text" value="" disabled>
                                    <input id="helpername" type="hidden" class="form-control @error('helpername') is-invalid @endif" name="helpername" value="">
                    
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
                                    <select  id="ymd" class="form-control @error('ymd') is-invalid @endif" name="ymd" value="{{ old('ymd') }}" autocomplete="name" onchange="change('ymd')" autofocus>
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
                                    <select  id="hms" class="form-control @error('hms') is-invalid @endif" name="hms" value="{{ old('hms') }}" autocomplete="name" autofocus>
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
            <input id="btn_datadisp"  type="image" src="image/img_yes.png" alt="データ表示" border="0">
            
    </form>

    <div class="div3">

        <form id = "form_datadisp3" method="POST" action="helperdata_dispctrl2" name = "form_datadisp3" >    
        @csrf                 
            <!-- 年月日 -->
            <!-- 年月日② -->
            <div>
                <label for="ymd2">{{ __('年月日') }}</label>

                <div class="col-md-6">
                    <select  id="ymd2" class="form-control @error('ymd2') is-invalid @endif" name="ymd2" value="{{ old('ymd2') }}" autocomplete="name" onchange="change('ymd2')" autofocus>
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
                    @error('ymd2')
                        <span class="invalid-feedback validate" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <!-- 時間範囲 -->
            <!-- 時間範囲② -->
            <div>
                <label for="hms2">{{ __('時間') }}</label>

                <div class="col-md-6">
                    <select  id="hms2" class="form-control @error('hms2') is-invalid @endif" name="hms2" value="{{ old('hms2') }}" autocomplete="name" autofocus>
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
                    @error('hms2')
                        <span class="invalid-feedback validate" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @endif
                </div>
            </div>

        </form>
    </div>


</div>
@endsection



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
        var formdata = new FormData(document.getElementById("form_datadisp"));

        // XMLHttpRequestによるアップロード処理
        var url = "csvoutput";
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);

        xhr.responseType = 'arraybuffer'; //arraybuffer型のレスポンスを受け付ける
        xhr.dataType = 'binary';
        xhr.send(formdata);
        xhr.onreadystatechange = function()
        {
            if (xhr.readyState == 4 && xhr.status == 200)
            {
                var response_data = this.response;
                var uint8_array = new Uint8Array(response_data);
                //エラーの場合
                if(uint8_array.length == 2)
                {
                    var text_decoder = new TextDecoder("utf-8");
                    var str = text_decoder.decode(Uint8Array.from(uint8_array).buffer);
                    alert("エラー:" + str);
                    if(str == "-1")
                    {
                        alert("ダウンロードするファイルがありません。");
                    }
                    return ;
                }
                // rtn : -1/エラー ,-2/アップロード失敗,-3/拡張子がxlslのファイルを選択してください,-4/ファイルを選択してください
                var downloadData = new Blob([uint8_array], {type: "text/csv;"});
                // var filename = formdata.get('fname') + "xlsx";
                var filename = "tttt.csv";
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
                    alert("ダウンロードしました。");
                }

            }
        };

    }

    
    //グラフ

  </script>
