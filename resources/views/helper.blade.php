<script src="{{ asset('/js/jquery-3.3.1.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@extends('layouts.parent')


@section('content')

<div class="allcont">
@if($facilityno != "")
    <form id="a_helper_add" action = '/helper_add?facilityno={{$facilityno}}' method = "post">
@else
    <form id="a_helper_add" action = '/helper_add'  method = "post">
@endif
        @csrf
            <input id="facilityno" type="hidden" name="facilityno" value={{$facilityno}}>
            <input type="image" class="img_style" src="image/img_add.png" alt="介助者追加" border="0">
    </form>



@if($facilityno != "")
    <form id="a_helper_fix" action = '/helper_fix?facilityno={{$facilityno}}' method = "post" onsubmit = "return Idcheck(targetID)">
@else
    <form id="a_helper_fix" action = '/helper_fix'  method = "post"  onsubmit = "return Idcheck(targetID)">
@endif
        @csrf
            <input id="targetid" type="hidden" name="id" value="">
            <input type="image" class="img_style" src="image/img_fix.png" alt="介助者修正" border="0">
    </form>


<input type="image" id="btn_delhelper"  src="image/img_del.png" alt="介助者削除" onclick="del_check(targetID,this.id)" border="0">

{{-- Time Study Tool連携 --}}
<div>
    <button id="btn_timestudytool" class="csvoutput_helper_list" onclick="VisibleChange(this.id)">Time Study Tool連携</button>
</div>

{{-- CSV出力　利用者一覧 --}}
<div id="a_csvoutput_helper_list">
    <button id="btn_csvoutput_helper_list" class="csvoutput_helper_list" onclick="helperListCsvOutput({{$facilityno}})">CSV出力　介助者一覧</button>
</div>

{{-- CSV出力　利用者データ --}}
<div>
    <button id="btn_helperdata_csvoutput_pre" class="csvout_helper_data" onclick="VisibleChange(this.id)">CSV出力　介助者データ</button>
</div>


@if($facilityno != "")
    <form id="a_helperdata" action = '/helperdata?facilityno={{$facilityno}}' method = "post" onsubmit = "return Idcheck(targetID,this.id)">
@else
    <form id="a_helperdata" action = '/helperdata'  method = "post" onsubmit = "return Idcheck(targetID,this.id)">
@endif

@csrf
    <input id="targetid2" type="hidden" name="id" value="">
    <input type="image" class="img_style4" src="image/img_helperdata.png" alt="介助者データ表示" border="0">
</form>


<!-- テーブルヘッダ -->
<img id = "img_helper_tb" src="image/img_helper_tb.png" alt="" >


<table id="table3">
    <tbody class="scrollBody">
    @if(isset($data))
        @if(count($data)<=0)
        <!--  配列の総アイテム数が10未満 -->
            @for($i=0;$i<12;$i++)
                <tr>
                    <td class="id"></td>
                    <td class="helpername"></td>
                    {{-- <td class="wearableno"></td> --}}
                    <td class="position"></td>
                    <td class="age"></td>
                    <td class="sex"></td>
                    {{-- <td class="JobTime"></td> --}}
                </tr>
            @endfor
        @else
            @foreach($data as $val)
                    <td class="id">{{$val['helper_id']}}</td>
                    <td class="helpername">{{$val['helpername']}}</td>
                    {{-- <td class="wearableno">{{$val['devicename']}}</td>                  --}}


                    @if(isset($val['position']))
                        @foreach($code as $valcode)
                            @if($valcode['codeno']==3)
                                    @if($valcode['value'] == $val['position'])
                                        <td class="position">{{$valcode['selectname']}}</td>
                                        @break
                                    @endif
                            @endif
                            @if(($loop->last)) <td class="position"></td>
                            @endif
                        @endforeach
                    @else <td class="position"></td>
                    @endif

                    @if(isset($val['age']))
                        <td class="age">{{$val['age']}}</td>
                    @else
                        <td class="age"></td>
                    @endif

                    @if(isset($val['sex']))
                        @foreach($code as $valcode)
                            @if($valcode['codeno']==4)
                                @if($valcode['value'] == $val['sex'])
                                    <td class="sex">{{$valcode['selectname']}}</td>
                                    @break
                                @endif
                            @endif
                            @if(($loop->last)) <td class="sex"></td>
                            @endif
                        @endforeach
                    @else <td class="sex"></td>
                    @endif

                    {{--
                    @if(isset($val['jobfrom']) && isset($val['jobto']))
                        <td class="JobTime">
                            <?php

                                $jobfrom1 = substr($val['jobfrom'], 0, 2);
                                $jobfrom2 = substr($val['jobfrom'], 2, 2);
                                $jobto1 = substr($val['jobto'], 0, 2);
                                $jobto2 = substr($val['jobto'], 2, 2);
                                echo $jobfrom1.":".$jobfrom2."-".$jobto1.":".$jobto2;

                            ?>
                        </td>
                    @else <td class="JobTime"></td>
                    @endif
                    --}}
                </tr>

                <!--  最後のループ -->
                @if ($loop->last)
                    @if ($loop->count < 12)
                        <!--  配列の総アイテム数が10未満 -->
                        @for($i=$loop->count;$i<12;$i++)
                        <tr>
                            <td class="id"></td>
                            <td class="helpername"></td>
                            {{-- <td class="wearableno"></td> --}}
                            <td class="position"></td>
                            <td class="age"></td>
                            <td class="sex"></td>
                            {{-- <td class="JobTime"></td> --}}
                        </tr>
                        @endfor
                    @endif
                @endif
            @endforeach
        @endif
    @else
        <!--  配列の総アイテム数が10未満 -->
        @for($i=0;$i<12;$i++)
                <tr class="id"></td>
                <td class="helpername"></td>
                {{-- <td class="wearableno"></td> --}}
                <td class="position"></td>
                <td class="age"></td>
                <td class="sex"></td>
                {{-- <td class="JobTime"></td> --}}
                </tr>
        @endfor
    @endif
    </tbody>
</table>
</div>

{{-- 日付選択のポップの上に表示するポップ --}}
<span id="cover_pop_alert">
    <center><nobr id="cover_lb_alert"></nobr></center>
    <input type="image" id="cover_btn_no"  src="image/img_no.png" alt="いいえ" onclick="Ctrl_pop('cover_pop_alert_no','visible','');" border="0">
    <input type="image" id="cover_btn_yes"  src="image/img_yes.png" alt="はい" onclick="helperDataCsvOutput({{$facilityno}})" border="0">
    <p id="error"> 空のデータの為出力出来ません </p>
</span>

{{-- Time Study Tool モーダル --}}
<span id="pop_timestudytool" style="visibility: collapse;">
    <center><nobr id="lb_timestudytool">Time Study Tool連携</nobr></center>
    <form id="form_timestudytool" method="POST" enctype="multipart/form-data" onsubmit="return false;">
        @csrf
        <div style="margin:10px 0;">
            <label for="timestudy_helpername" style="display:inline-block; margin-right:8px;">介助者</label>
            <select id="timestudy_helpername" name="helpername" required style="min-width:120px;">
                @foreach($data as $val)
                    <option value="{{ $val['helper_id'] }}">{{ $val['helpername'] }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin:10px 0;">
            <label for="timestudy_csv" style="display:inline-block; padding:8px 20px; background:#3b82f6; color:white; border-radius:4px; cursor:pointer;">
                CSV選択
            </label>
            <input type="file" id="timestudy_csv" name="csv_file" accept=".csv" style="display:none;" onchange="showSelectedFileName(this)">
            <span id="csv_filename" style="margin-left:10px; color:#333;"></span>
        </div>
        <!-- ↓ここをラベル型ボタンに修正 -->
        <label id="btn_timestudy_upload" onclick="uploadTimeStudyCSV()" style="
            display:inline-block; padding:8px 20px; border:2px solid #2563eb; color:#2563eb; border-radius:4px;
            background:#fff; cursor:pointer; margin-top:8px;">
            CSVファイルをアップロード
        </label>
    </form>
    <div style="margin-top:10px; text-align:center;">
        <button id="btn_cancel" onclick="closeTimeStudyModal();">キャンセル</button>
        <!-- いいえボタンは削除 -->
    </div>
</span>




<script>

function closeTimeStudyModal() {
    document.getElementById('pop_timestudytool').style.visibility = 'collapse';
    const overlay = document.getElementById('pop_alert_back');
    if (overlay) overlay.style.visibility = 'collapse';
}
function showSelectedFileName(input) {
    const fileName = input.files[0] ? input.files[0].name : '';
    document.getElementById('csv_filename').innerText = fileName;
}
function uploadTimeStudyCSV() {
    var formdata = new FormData();

    if (!document.getElementById('timestudy_csv').files[0]) {
        Ctrl_pop("error", "visible", "CSVファイルを選択してください。");
        return;
    }

    formdata.append('csv_file', document.getElementById('timestudy_csv').files[0]);
    formdata.append('helpername', document.getElementById('timestudy_helpername').value);

    // CSRFトークン取得＆FormDataとヘッダー両方にセット
    var token = document.querySelector('meta[name="csrf-token"]').content;
    formdata.append('_token', token);

    var url = "/time_study_csv_upload"; // 必ず絶対パスで指定
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('X-CSRF-Token', token);

    xhr.responseType = 'json';
    xhr.send(formdata);

    try {
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    var response = this.response;
                    if (response.success) {
                        closeTimeStudyModal();
                        Ctrl_pop("error", "visible", "CSVファイルのアップロードが成功しました。");
                    } else {
                        Ctrl_pop("error", "visible", response.message);
                    }
                } else {
                    Ctrl_pop("error", "visible", "エラーが発生しました。");
                }
            }
        };
    } catch(e) {
        alert(e);
    }
}


</script>
{{-- 日付選択できるポップ用 --}}
<span id="pop_custom_alert">
    <div id="pop_custom_alert_text">
        <nobr id="lb_custom_alert_title">利用者データ出力</nobr>
        <nobr id="lb_custom_alert">出力期間</nobr>
        <div id="pop_custom_alert_content">
            <input type="text" name="st_ymd" id="st_ymd" readonly=”readonly” max="{{\Carbon\Carbon::now()->format('Y')}}-12-31">
            <p style="width: 100px"> ~ </p>
            <input type="text" name="ed_ymd" id="ed_ymd" readonly=”readonly”  max="{{\Carbon\Carbon::now()->format('Y')}}-12-31">
        </div>
        <div id="pop_error_custom_alert_content">
            <p id="error_st_ymd">入力されていません</p>
            <p id="error_date"> 開始日が終了日を超えています </p>
            <p id="error_ed_ymd">入力されていません</p>
        </div>
    </div>

    {{-- <input type="image" id="btn_no"  src="image/img_no.png" alt="いいえ" onclick="Ctrl_pop('','collapse','');" border="0"> --}}
    <div id="pop_custom_alert_btns">
        <button id="btn_helperdata_csvout" class="btn_helperdata_csvout" onclick="validateDate()">CSV出力</button>
        <button id="btn_cancel" class="btn_cancel" onclick="Ctrl_pop('pop_custom_alert','collapse','');">キャンセル</button>
    </div>
</span>

@endsection

<script>
    $(function() {
        // HelperControllerから"bpainhed.ymd"の値を取得
        const ymdDatas = @json($ymdData);
        // mapでymdの値のみ取得
        const targetDates = ymdDatas.map(data => data.ymd)
            $('#st_ymd').datepicker({
                // 年選択をプルダウン化
                changeYear: true,
                // 月選択をプルダウン化
                changeMonth: true,
                // 現在日付の5年前～0年後まで選択可能
                yearRange: '-5:+0',
                beforeShowDay: function(date) {
                    const dateString = $.datepicker.formatDate("yymmdd", date);
                    if (targetDates.indexOf(dateString) !== -1) {
                        // 介助者データがある箇所のマーキング
                        return [true, "custom-calendar-data", ""];
                    } else if (date.getDay() === 0){
                        // 日曜日の場合
                        return [true, "custom-calendar-sunday", ""];
                    } else if (date.getDay() === 6){
                        // 土曜日の場合
                        return [true, "custom-calendar-saturday", ""];
                    } else {
                        return [true, ""];
                    }
                }
            });
        $('#ed_ymd').datepicker({
            // 年選択をプルダウン化
            changeYear: true,
            // 月選択をプルダウン化
            changeMonth: true,
            // 現在日付の5年前～0年後まで選択可能
            yearRange: '-5:+0',
            beforeShowDay: function(date) {
                const dateString = $.datepicker.formatDate("yymmdd", date);
                    if (targetDates.indexOf(dateString) !== -1) {
                        // 介助者データがある箇所のマーキング
                        return [true, "custom-calendar-data", ""];
                    } else if (date.getDay() === 0){
                        // 日曜日の場合
                        return [true, "custom-calendar-sunday", ""];
                    } else if (date.getDay() === 6){
                        // 土曜日の場合
                        return [true, "custom-calendar-saturday", ""];
                    } else {
                        return [true, ""];
                    }
            }
        });
        // 日本語化
        $.datepicker.regional['ja'] = {
          closeText: '閉じる',
          prevText: '<前',
          nextText: '次>',
          monthNames: ['1月','2月','3月','4月','5月','6月',
          '7月','8月','9月','10月','11月','12月'],
          monthNamesShort: ['1月','2月','3月','4月','5月','6月',
          '7月','8月','9月','10月','11月','12月'],
          dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
          dayNamesShort: ['日','月','火','水','木','金','土'],
          dayNamesMin: ['日','月','火','水','木','金','土'],
          weekHeader: '週',
          dateFormat: 'yy/mm/dd',
          firstDay: 0,
          isRTL: false,
          showMonthAfterYear: true,
          yearSuffix: '年'};
        $.datepicker.setDefaults($.datepicker.regional['ja']);
    });
    //介助者一覧csv出力
    function helperListCsvOutput(facilityno)
    {
        // XMLHttpRequestによるアップロード処理
        var url = "helper_list_csvoutput";
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.responseType = 'arraybuffer'; //arraybuffer型のレスポンスを受け付ける
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

                    //ファイル名用のデータ取得
                    //ファイル名は、「施設名_介助者データ.csv」
                    var facilityname = @json($facilityname);
                    var filelastname = "介助者データ";
                    var separator = "_";
                    var extension = ".csv"
                    var filename = facilityname + separator +  filelastname + extension;
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

    function validateDate()
    {
        // エラーメッセージたちを非表示
        document.getElementById('error_st_ymd').style.visibility = 'collapse';
        document.getElementById('error_ed_ymd').style.visibility = 'collapse';
        document.getElementById('error_date').style.visibility = 'collapse';
        document.getElementById('error').style.visibility = 'collapse';

        // 出力期間開始日、終了日を取得
        var startDate = document.getElementById('st_ymd').value;
        var endDate = document.getElementById('ed_ymd').value;

        if (startDate == "") {
            // CSV出力開始日未入力
            document.getElementById('error_st_ymd').style.visibility = 'visible';
            return;
        }

        if (endDate == "") {
            // CSV出力終了日未入力
            document.getElementById('error_ed_ymd').style.visibility = 'visible';
            return;
        }

        if (startDate > endDate) {
            // 開始日が終了日より大きい場合
            // ※年-月-日付形式のため、文字列から変換しなくても比較が機能する
            document.getElementById('error_date').style.visibility = 'visible';
            return;
        }

        VisibleChange("btn_helperdata_csvout");
    }

    //介助者データcsv出力
    function helperDataCsvOutput(facilityno)
    {
        // 出力期間開始日、終了日を取得
        var startDate = document.getElementById('st_ymd').value;
        var endDate = document.getElementById('ed_ymd').value;

        // XMLHttpRequestによるアップロード処理
        var url = "helper_data_csvoutput";
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.responseType = 'arraybuffer'; //arraybuffer型のレスポンスを受け付ける
        xhr.dataType = 'binary';

        // パラメータ設定
        var param = "facilityno=" + facilityno + "&st_ymd=" + startDate + "&ed_ymd=" + endDate;
        xhr.send(param);

        try
        {
            xhr.onreadystatechange = function()
            {
                if (xhr.readyState == 4 && xhr.status == 200)
                {
                    var response_data = this.response;
                    // データが空の場合メッセージを表示
                    if(response_data.byteLength == '0') {
                        document.getElementById('error').style.visibility = 'visible';
                        return;
                    }
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

                    //ファイル名用のデータ取得
                    //ファイル名は、「施設名_介助者データ.csv」
                    var facilityname = @json($facilityname);
                    var filelastname = "介助者データ";
                    var separator = "_";
                    var extension = ".csv"
                    var filename = facilityname + separator +  filelastname + extension;
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

    function uploadTimeStudyCSV() {
        var formdata = new FormData();

        if (!document.getElementById('timestudy_csv').files[0]) {
            Ctrl_pop("error", "visible", "CSVファイルを選択してください。");
            return;
        }

        formdata.append('csv_file', document.getElementById('timestudy_csv').files[0]);
        formdata.append('helpername', document.getElementById('timestudy_helpername').value);
        formdata.append('_token', token);


        // XMLHttpRequestによるアップロード処理
        var url = "time_study_csv_upload";
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/time_study_csv_upload', true);
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);

        xhr.responseType = 'json';
        xhr.send(formdata);

        try {
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    console.log("xhr.status:", xhr.status);
            console.log("xhr.response:", xhr.response);
            console.log("xhr.responseText:", xhr.responseText);
                    if (xhr.status == 200) {
                        var response = this.response;
                        if (response.success) {
                            Ctrl_pop('','collapse','');
                            Ctrl_pop("error", "visible", "CSVファイルのアップロードが成功しました。");
                        } else {
                            Ctrl_pop("error", "visible", response.message);
                        }
                    } else {
                        Ctrl_pop("error", "visible", "エラーが発生しました。");
                    }
                }
            };
        } catch(e) {
            alert(e);
        }
    }
</script>
