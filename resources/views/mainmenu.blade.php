@extends('layouts.parent')

@section('content')

    <!-- 一般 1-->
    <!-- {{$code[0]['value']}} -->
    <!-- 管理者 2-->
    <!-- {{$code[4]['value']}} -->
    <!-- 施設ユーザ 3-->
    <!-- {{$code[8]['value']}} -->
<div class="allcont">
    @if(Auth::user()->policyflag == 0)
        <span style="visibility: visible;"></span>
            <span id="policy_dailog"  style="visibility: visible;">
                <nobr id="policy_sentence">
                @include('auth.policy')
                <center>
                    <nobr id="policy_title">【プライバシーポリシー】<br><br></nobr>
                    <input type="checkbox" id="policy_check" onclick="activeButton()">
                    <label id="policy_certification">プライバシーポリシーに同意する。</label>
                    <button id="policy_close" onclick="changePolicy()" disabled="false">承諾</button>
                </center>
                </nobr>
            </span>
    @endif

    <!-- 管理者のみ表示 -->
    <!-- ログインユーザ一覧とアンケートサイト管理画面 -->
    @if(Auth::user()->authority == $code[4]['value'])
        <a href="{{ url('/loginuser') }}"> <img id="btn_loginuser" src="image/img_loginuser.png" alt="ログインユーザ一覧"  border="0"> </a>
    @endif



    <!-- ウェアラブルデバイス登録 -->
    <!-- 一般　・・・$code[0]['value'] -->
    <!-- 管理者・・・$code[4]['value'] -->

    <!-- ※※一般ユーザ -->
    @if(Auth::user()->authority == $code[0]['value'] )
        <!-- <a href="{{ url('/wearable') }}"> <img id="btn_wearable_3" src="image/img_wearable.png" alt="心拍センサー登録"  border="0"></a> -->

        <!-- リスクデバイス登録 -->
        <!-- <a href="{{ url('/risksensor') }}"> <img id="btn_risksensor_3" src="image/img_risksensor.png" alt="リスクデバイス登録"  border="0"></a> -->

        <!-- 施設一覧 -->
        <a href="{{ url('/facility') }}"> <img id="btn_facility_3"  src="image/img_facility.png" alt="施設一覧"  border="0"></a>
        <!-- アンケートサイトへのリンク -->
        <a href="https://questant.jp/account/login" target="_blank"><img id="btn_linkquestionary2" src="image/img_linkquestionary.png" alt="アンケートサイト"></a>

    <!-- ※※管理者ユーザ -->
    @elseif(Auth::user()->authority == $code[4]['value'] )
        <!-- <a href="{{ url('/wearable') }}"> <img id="btn_wearable" src="image/img_wearable.png" alt="心拍センサー登録"  border="0"></a> -->

        <!-- リスクデバイス登録 -->
        <!-- <a href="{{ url('/risksensor') }}"> <img id="btn_risksensor" src="image/img_risksensor.png" alt="リスクデバイス登録"  border="0"></a> -->

        <!-- 施設一覧 -->
        <a href="{{ url('/facility') }}"> <img id="btn_facility"  src="image/img_facility.png" alt="施設一覧"  border="0"></a>
        <!-- 平均データ表示 -->
        <!-- <p><a href="{{ url('/averdata') }}"> 平均データ表示</a></p>
        <a href="{{ url('/averdata') }}"> <img src="image/img_wearable.png" alt="平均データ表示" > </a> -->
    @endif


    <!-- 施設ユーザはこのボタンのみ表示 -->
    @if(Auth::user()->authority == $code[8]['value'])
        <!-- 施設情報登録されている場合 -->
        @if(Auth::user()->facilityno != 0 && Auth::user()->facilityno != "" && isset($facilityno) && $facilityno!="")
            <!-- 介助者一覧 -->
            <!-- 施設情報入力 -->
            <a href="{{ url('/facilityinput') }}"> <img id="btn_facilityinput" src="image/img_facilityinput.png" alt="施設情報入力" border="0"> </a>
            <form id="a_helper" action = '/helper'  method = "post">
                @csrf
                <input id="targetid" type="hidden" name="id" value="{{Auth::user()->facilityno}}">
                <input type="image" id="btn_helpermain" src="image/img_helpermain.png" alt="介助者一覧" border="0">
            </form>

            <!-- アンケートサイトへのリンク -->
            <!-- URL登録されている場合 -->
            @if(isset($questurl) && $questurl != "")
                <a href={{$questurl}} target="_blank"><img id="btn_linkquestionary3" src="image/img_linkquestionary.png" alt="アンケートサイト"></a>
            <!-- URL登録されていない場合 ※URL登録されていないためPOPを表示する -->
            @else
            <input id="btn_linkquestionary3" type="image" src="image/img_linkquestionary.png" alt="アンケートサイト" border="0" onclick = "VisibleChange(this.id)">
                <!-- <a href={{$questurl}} target="_blank"><img id="btn_linkquestionary3" src="image/img_linkquestionary.png" alt="アンケートサイト"></a> -->
            @endif

        <!-- 施設情報登録されていない場合 -->
        @else
            <!-- 施設情報入力 -->
            <a  href="{{ url('/facilityinput') }}"> <img id="btn_facilityinput_3" src="image/img_facilityinput.png" alt="施設情報入力" border="0"> </a>
            <!-- アンケートサイトへのリンク ※URL登録されていないためPOPを表示する -->
            <input id="btn_linkquestionary4" type="image" src="image/img_linkquestionary.png" alt="アンケートサイト" border="0" onclick = "VisibleChange(this.id)">
            <!-- <a href="https://questant.jp/account/login" target="_blank"><img id="btn_linkquestionary4" src="image/img_linkquestionary.png" alt="アンケートサイト"></a> -->
        @endif
    @endif
</div>

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function activeButton() {
        if(document.getElementById("policy_close").disabled === false) {
            document.getElementById("policy_close").setAttribute("disabled", true);
		    document.getElementById("policy_close").style.color = "White";
        } else {
            document.getElementById("policy_close").removeAttribute("disabled");
		    document.getElementById("policy_close").style.color = "black";
        }
    }
    // ボタンを押下時のダイアログを閉じる処理
    function changePolicy() {
        document.getElementById('policy_dailog').style.visibility = 'hidden';
        document.getElementById('policy_check').style.visibility = 'hidden';
    }
    // 閉じるボタンを押下されたときにajaxでMainmenuControllerのupdatePolicyFlagの処理を動かす
    $(document).ready(function() {
        $('#policy_close').click(function() {
            var flagValue = 1; // 更新したいフラグの値
            $.ajax({
                url: '{{ route("update.flag") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: { policyflag: flagValue },
                success: function(response) {
                    document.getElementById('policy_dailog').style.visibility = 'hidden';
                    document.getElementById('policy_check').style.visibility = 'hidden';
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });

</script>
