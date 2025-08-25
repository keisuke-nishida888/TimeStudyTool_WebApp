<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>Time Study Tool</title>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <meta name="robots" content="noindex,nofollow">
        <meta name="googlebot" content="noindex,nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
         <!-- Fonts -->
         <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <!-- Styles -->
        <!-- //assetを使うとpublicフォルダ内のリソースを読み込めるようになる -->
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">
        <link rel="shortcut icon" href="{{ asset('/image/img_logo1.ico') }}">
      <script src="{{ asset('/js/jquery-3.3.1.min.js') }}"></script>

<!-- ✅ jQuery UI 追加（CSS + JS） -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script src="{{ asset('/js/Chart.min.js') }}"></script>

        <meta name="google-site-verification" content="pbXWDZiEXs_5VJ-APYNHphhnpjPiQB_P5VBajcgwhqA" />
    </head>

    <body onload="ini();ini_img();">
<script>
function ini_img() {
    // 未使用関数のため空実装（必要なら後で追加）
}
</script>

    <main class="all">

    <div id = "bar_div">
        <!-- タイトルバー -->
        <img id = "img_bar" src="image/img_bar.png" alt="" >
        <!-- タイトル -->
        <nobr id="title">{{$title}}</nobr>
        <!-- ロゴ -->
        <a  href="{{ url('/mainmenu') }}"> <img id="img_logo" src="image/img_logo3.png" alt="JCLS" border="0"> </a>

        <!-- ログアウト -->
        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <img id="btn_logout" src="image/img_logout.png" alt="ログアウト" >
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        <!-- パンくずリスト -->
        @php
            $__facilityno =
                ($facilityno ?? null)
                ?? data_get($data2 ?? [], '0.facilityno')
                ?? data_get($data2 ?? [], '0.id')
                ?? data_get($data ?? [],  '0.facilityno')
                ?? old('facilityno')
                ?? request('facilityno');

            $__groupno =
                request('groupno')
                ?? data_get($data ?? [], '0.groupno')
                ?? (isset($selectedGroup) ? ($selectedGroup->group_id ?? null) : null);
        @endphp

        <div id="breadcrumbs">
            @if(Auth::user()->authority == $code[8]['value'] && Auth::user()->facilityno && $page == "helper")
                {{ Breadcrumbs::render("helper_facil", $__facilityno, $__groupno) }}
            @elseif(Auth::user()->authority == $code[8]['value'] && Auth::user()->facilityno && $page == "helper_add")
                {{ Breadcrumbs::render("helper_add_facil", $__facilityno, $__groupno) }}
            @elseif(Auth::user()->authority == $code[8]['value'] && Auth::user()->facilityno && $page == "helper_fix")
                {{ Breadcrumbs::render("helper_fix_facil", $__facilityno, $__groupno) }}
            @elseif(Auth::user()->authority == $code[8]['value'] && Auth::user()->facilityno && $page == "helperdata")
                {{ Breadcrumbs::render("helperdata_facil", $__facilityno, $__groupno) }}
            @else
                {{-- 既存その他のページ --}}
                @if(isset($__facilityno))
                    {{ Breadcrumbs::render($page, $__facilityno, $__groupno) }}
                @else
                    {{ Breadcrumbs::render($page) }}
                @endif
            @endif
        </div>

        {{-- 戻る --}}
@php
    // 可能な限り facilityno / groupno を拾う
    $__facilityno =
        ($facilityno ?? null)
        ?? data_get($data2 ?? [], '0.facilityno')
        ?? data_get($data2 ?? [], '0.id')
        ?? data_get($data ?? [],  '0.facilityno')
        ?? old('facilityno')
        ?? request('facilityno');

    $__groupno =
        request('groupno')
        ?? data_get($data ?? [], '0.groupno')
        ?? (isset($selectedGroup) ? ($selectedGroup->group_id ?? null) : null);
@endphp

@if ($group === 'helper')
    {{-- 絶対URL + GET で一覧へ戻す --}}
    <form action="{{ url('/helper') }}" method="get">
        <input type="image" id="btn_back" src="{{ asset('image/img_back.png') }}" alt="戻る" border="0">
        @if(!empty($__facilityno))
            <input type="hidden" name="facilityno" value="{{ $__facilityno }}">
        @endif
        @if(!empty($__groupno))
            <input type="hidden" name="groupno" value="{{ $__groupno }}">
        @endif
    </form>

@elseif ($group === 'helperdata')
    <form action="{{ url('/helperdata') }}" method="get">
        <input type="image" id="btn_back" src="{{ asset('image/img_back.png') }}" alt="戻る" border="0">
        @if(!empty($__facilityno))
            <input type="hidden" name="facilityno" value="{{ $__facilityno }}">
        @endif
        @if(!empty($__groupno))
            <input type="hidden" name="groupno" value="{{ $__groupno }}">
        @endif
        {{-- helperdata で必要なら --}}
        @if(isset($data[0]['id']))
            <input type="hidden" name="helperno" value="{{ $data[0]['id'] }}">
        @endif
    </form>

@else
    @if ($title !== 'メニュー')
        {{-- 他画面も絶対URLに統一 --}}
        <form action="{{ url('/'.$group) }}" method="get">
            <input type="image" id="btn_back" src="{{ asset('image/img_back.png') }}" alt="戻る" border="0">
            @if(!empty($__facilityno))
                <input type="hidden" name="facilityno" value="{{ $__facilityno }}">
            @endif
            @if(!empty($__groupno))
                <input type="hidden" name="groupno" value="{{ $__groupno }}">
            @endif
        </form>
    @endif
@endif
    </div>



        <!-- エラー -->
        @if(isset($adderror))
            <span id="pop_error_back" style="visibility: visible;"></span>
            <span id="pop_error"  style="visibility: visible;">
                <center><nobr id="lb_error">{{$errdata[8]['error']}}</nobr></center>
                <center><input type="image" id="btn_ok" src="image/img_ok.png" alt="OK" onclick="VisibleChange(this.id)" border="0"></center>
            </span>
        @elseif(isset($fixerror))
            <span id="pop_error_back" style="visibility: visible;"></span>
            <span id="pop_error"  style="visibility: visible;">
                <center><nobr id="lb_error">{{$errdata[9]['error']}}</nobr></center>
                <center><input type="image" id="btn_ok" src="image/img_ok.png" alt="OK" onclick="VisibleChange(this.id)" border="0"></center>
            </span>
        @else
            <span id="pop_error_back" style="visibility: collapse;"></span>
            <span id="pop_error"  style="visibility: collapse;">
                <center><nobr id="lb_error"></nobr></center>
                <center><input type="image" id="btn_ok" src="image/img_ok.png" alt="OK" onclick="VisibleChange(this.id)" border="0"></center>
                <center><input type="image" id="btn_reload" src="image/img_ok.png" alt="OK" onclick="location.reload();" border="0"></center>
            </span>
        @endif



        <span id="pop_alert_back"></span>
        <span id="pop_alert">
            <center><nobr id="lb_alert"></nobr></center>
            <input type="image" id="btn_no"  src="image/img_no.png" alt="いいえ" onclick="Ctrl_pop('','collapse','');" border="0">
            <input type="image" id="btn_yes"  src="image/img_yes.png" alt="はい" onclick="" border="0">
        </span>

        <!-- 追加修正後のメッセージ -->
        @if(isset($addmess))
            <p id="addmess" class = "mess">{{$addmess}}</P>
            <p id="fixmess" class = "mess"></P>
        @elseif(isset($fixmess))
            <p id="fixmess" class = "mess">{{$fixmess}}</P>
        @else
            <p id="fixmess" class = "mess"></P>
        @endif

            @yield('content')

        </main>


<script type="text/javascript">
    function ini()
    {
        zoom =Math.round((window.outerWidth / window.innerWidth)*100);

        var coef = -10 * zoom + 3033.7;
        var left = "calc(" +  coef + "px / var(--coef))";
//        if(document.getElementById('btn_adduser_pre'))document.getElementById('btn_adduser_pre').style.left = left;
//        if(document.getElementById('btn_fixuser_pre'))document.getElementById('btn_fixuser_pre').style.left = left;
//        if(document.getElementById('btn_addfacility_pre'))document.getElementById('btn_addfacility_pre').style.left = left;
//        if(document.getElementById('btn_fixfacility_pre'))document.getElementById('btn_fixfacility_pre').style.left = left;
        if(document.getElementById('btn_addwearable_pre'))document.getElementById('btn_addwearable_pre').style.left = left;
        if(document.getElementById('btn_fixwearable_pre'))document.getElementById('btn_fixwearable_pre').style.left = left;
        if(document.getElementById('btn_addrisksensor_pre'))document.getElementById('btn_addrisksensor_pre').style.left = left;
        if(document.getElementById('btn_fixrisksensor_pre'))document.getElementById('btn_fixrisksensor_pre').style.left = left;
//        if(document.getElementById('btn_addhelper_pre'))document.getElementById('btn_addhelper_pre').style.left = left;
//        if(document.getElementById('btn_fixhelper_pre'))document.getElementById('btn_fixhelper_pre').style.left = left;
//        if(document.getElementById('btn_addfacility_input_pre'))document.getElementById('btn_addfacility_input_pre').style.left = left;
//        if(document.getElementById('btn_fixfacility_input_pre'))document.getElementById('btn_fixfacility_input_pre').style.left = left;
//        if(document.getElementById('btn_cxl'))document.getElementById('btn_cxl').style.left = left;
    }


    //y=-10x+3033.7
    var zoom = 100;
    $(function() {
    //ウインドウがリサイズされたら発動
    $(window).resize(function()
    {
        zoom =Math.round((window.outerWidth / window.innerWidth)*100);

        var coef = -10 * zoom + 3033.7;
        var left = "calc(" +  coef + "px / var(--coef))";
        //if(document.getElementById('btn_adduser_pre'))document.getElementById('btn_adduser_pre').style.left = left;
        //if(document.getElementById('btn_fixuser_pre'))document.getElementById('btn_fixuser_pre').style.left = left;
        //if(document.getElementById('btn_addfacility_pre'))document.getElementById('btn_addfacility_pre').style.left = left;
        //if(document.getElementById('btn_fixfacility_pre'))document.getElementById('btn_fixfacility_pre').style.left = left;
        if(document.getElementById('btn_addwearable_pre'))document.getElementById('btn_addwearable_pre').style.left = left;
        if(document.getElementById('btn_fixwearable_pre'))document.getElementById('btn_fixwearable_pre').style.left = left;
        if(document.getElementById('btn_addrisksensor_pre'))document.getElementById('btn_addrisksensor_pre').style.left = left;
        if(document.getElementById('btn_fixrisksensor_pre'))document.getElementById('btn_fixrisksensor_pre').style.left = left;
        //if(document.getElementById('btn_addhelper_pre'))document.getElementById('btn_addhelper_pre').style.left = left;
        //if(document.getElementById('btn_fixhelper_pre'))document.getElementById('btn_fixhelper_pre').style.left = left;
        //if(document.getElementById('btn_addfacility_input_pre'))document.getElementById('btn_addfacility_input_pre').style.left = left;
        //if(document.getElementById('btn_fixfacility_input_pre'))document.getElementById('btn_fixfacility_input_pre').style.left = left;
        //if(document.getElementById('btn_cxl'))document.getElementById('btn_cxl').style.left = left;

        if(document.getElementById(disp))
        {
            var targetElement = document.getElementById( "btn_no" );
            var clientRect = targetElement.getBoundingClientRect();
            // 画面内の位置
            var x = clientRect.left;
            var y = clientRect.top;

            if(disp != "btn_yes")
            {
                document.getElementById(disp).style.top = y + "px";
            }
        }
    });
    });


    // reloadを禁止する方法
    // F5キーによるreloadを禁止する方法
    // document.addEventListener("keydown", function (e)
    // {
    //     if ((e.which || e.keyCode) == 116 )
    //     {
    //         //  alert("リロードは許可されていません。");
    //         //  e.preventDefault();
    //     }
    // });


//   var perfEntries = performance.getEntriesByType("navigation");
//   perfEntries.forEach(function(pe){
//     switch( pe.type )
//     {
//       case 'navigate':
//         console.log('通常のアクセス');
//         break;
//       case 'reload':
//         console.log('更新によるアクセス');
//         alert("リロードは許可されていません。");
//         return false;
//         break;
//       case 'back_forward':
//         console.log('戻るによるアクセス');
//         break;
//       case 'prerender':
//         console.log('レンダリング前');
//         break;
//     }
//   });

    // if(window.performance.navigation.type === 0/* TYPE_NAVIGATE */)
    // {
    //     // 初期表示
    //     // alert("初期表示");
    // }
    // else if(window.performance.navigation.type === 1/* TYPE_RELOAD */)
    // {
    //     // リロード
    //     alert("リロードは許可されていません。");
    //     // return false;
    // }
    // else if(window.performance.navigation.type === 2/* TYPE_BACK_FORWARD */)
    // {
    //     // 履歴から遷移
    //     alert("履歴から遷移");
    // }
    // else
    // {
    //     // その他
    //     alert("その他");
//     // }
//     var timeout;
//     window.addEventListener('beforeunload', function(e)
//     {
//         console.log(window.performance.navigation.type);
//         if(window.performance.navigation.type == 1)
//         {
//             e.preventDefault();
//             console.log("::::");
//         }
//     },  { passive: false });




//     function warning() {
//     timeout = setTimeout(function() {
//         alert('You stayed');
//     }, 1000);
//     // return "You are leaving the page";
// }

// function noTimeout()
// {
//     clearTimeout(timeout);
// }

// // window.onbeforeunload = warning;
// // window.unload = noTimeout;





    //変数
    var targetID="";
    //ID選択チェック
    function Idcheck(id)
    {
        //選択されていない時は選択してくださいPOPを表示
        if(isNaN(parseInt(id,10))==true)
        {
            Ctrl_pop("error","visible",@json($errdata[3]['error']));
            return false;
        }

        document.getElementById('targetid').value  = id;
        if(document.getElementById('targetid2'))document.getElementById('targetid2').value  = id;
        if(document.getElementById('targetid3'))document.getElementById('targetid3').value  = id;
        while(isNaN(parseInt(document.getElementById('targetid').value,10))==true)
        {
            document.getElementById('targetid').value  = id;
            alert("代入失敗");
        }
        if(document.getElementById('targetid2'))
        {
            while(isNaN(parseInt(document.getElementById('targetid2').value,10))==true)
            {
                document.getElementById('targetid2').value  = id;
                alert("代入失敗");
            }
        }
        return true;
    }


    function del_check(tar_id,id)
    {
        if(Idcheck(tar_id) == true) VisibleChange(id);
    }

    var disp = "";
    //関数
    function VisibleChange(id_val)
    {
        var targetElement = document.getElementById( "btn_no" );
        var clientRect = targetElement.getBoundingClientRect();
        // 画面内の位置
        var x = clientRect.left;
        var y = clientRect.top;
        switch(id_val)
        {
            //リンクエラー
            case "btn_linkquestionary3":
                var err_text = (@json($errdata[42]['error']).replace(/\n/g, '<br>'));
                Ctrl_pop("error","visible",err_text);
                break;
            case "btn_linkquestionary4":
                var err_text = (@json($errdata[42]['error']).replace(/\n/g, '<br>'));
                Ctrl_pop("error","visible",err_text);
                break;
            //削除
            case "btn_deluser":
                //ボタンの割付を変更する
                disp = 'btn_yes';
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = send.bind(null,'loginuser_del','del',targetID);
                Ctrl_pop("alert","visible",@json($errdata[5]['error']));
                break;
            case "btn_delwearable":
                disp = 'btn_yes';
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = send.bind(null,'wearable_del','del',targetID);
                Ctrl_pop("alert","visible",@json($errdata[5]['error']));
                break;
            case "btn_delfacility":
                disp = 'btn_yes';
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = send.bind(null,'facility_del','del',targetID);
                Ctrl_pop("alert","visible",@json($errdata[5]['error']));
                break;
            case "btn_delhelper":
                disp = 'btn_yes';
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = send.bind(null,'helper_del','del',targetID);
                Ctrl_pop("alert","visible",@json($errdata[5]['error']));
                break;
            case "btn_timestudytool":
                Ctrl_pop("timestudytool","visible","");
                break;
            case "btn_delrisksensor":
                disp = 'btn_yes';
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = send.bind(null,'risksensor_del','del',targetID);
                Ctrl_pop("alert","visible",@json($errdata[5]['error']));
                break;
            //POP非表示
            case "btn_ok":
                Ctrl_pop("","collapse","");
                break;
            //修正
            case "btn_fixuser_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_fixuser';
                //ボタンの割付を変更する
                document.getElementById('btn_yes').onclick = "";
                Ctrl_pop("alert","visible",35);
                document.getElementById('btn_fixuser').style.top = y + "px";
                document.getElementById('btn_fixuser').style.visibility = 'Visible';
                break;
            case "btn_fixwearable_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_fixwearable';
                Ctrl_pop("alert","visible",35);
                document.getElementById('btn_fixwearable').style.top = y + "px";
                document.getElementById('btn_fixwearable').style.visibility = 'Visible';
                break;
            case "btn_fixfacility_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_fixfacility';
                Ctrl_pop("alert","visible",35);
                document.getElementById('btn_fixfacility').style.top = y + "px";
                document.getElementById('btn_fixfacility').style.visibility = 'Visible';
                break;
            case "btn_fixhelper_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_fixhelper';
                Ctrl_pop("alert","visible",35);
                document.getElementById('btn_fixhelper').style.top = y + "px";
                document.getElementById('btn_fixhelper').style.visibility = 'Visible';
                break;
            case "btn_fixrisksensor_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_fixrisksensor';
                Ctrl_pop("alert","visible",35);
                document.getElementById('btn_fixrisksensor').style.top = y + "px";
                document.getElementById('btn_fixrisksensor').style.visibility = 'Visible';
                break;
            case "btn_fixfacility_input_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_fixfacility_input';
                Ctrl_pop("alert","visible",35);
                document.getElementById('btn_fixfacility_input').style.top = y + "px";
                document.getElementById('btn_fixfacility_input').style.visibility = 'Visible';
                break;

            //追加
            case "btn_adduser_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_adduser';
                Ctrl_pop("alert","visible",34);

                document.getElementById('btn_adduser').style.top = y + "px";
                document.getElementById('btn_adduser').style.visibility = 'Visible';
                break;
            case "btn_addwearable_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_addwearable';
                Ctrl_pop("alert","visible",34);
                document.getElementById('btn_addwearable').style.top = y + "px";
                document.getElementById('btn_addwearable').style.visibility = 'Visible';
                break;

            case "btn_addfacility_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_addfacility';
                Ctrl_pop("alert","visible",34);
                document.getElementById('btn_addfacility').style.top = y + "px";
                document.getElementById('btn_addfacility').style.visibility = 'Visible';
                break;
            case "btn_addfacility_input_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_addfacility_input';
                Ctrl_pop("alert","visible",34);
                document.getElementById('btn_addfacility_input').style.top = y + "px";
                document.getElementById('btn_addfacility_input').style.visibility = 'Visible';
                break;
            case "btn_addhelper_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_addhelper';
                Ctrl_pop("alert","visible",34);

                document.getElementById('btn_addhelper').style.top = y + "px";
                document.getElementById('btn_addhelper').style.visibility = 'Visible';
                break;
            case "btn_addrisksensor_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_addrisksensor';
                Ctrl_pop("alert","visible",34);
                document.getElementById('btn_addrisksensor').style.top = y + "px";
                document.getElementById('btn_addrisksensor').style.visibility = 'Visible';
                break;
            case "btn_addfacility_input_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_addfacility_input';
                Ctrl_pop("alert","visible",34);
                document.getElementById('btn_addfacility_input').style.top = y + "px";
                document.getElementById('btn_addfacility_input').style.visibility = 'Visible';
                break;


            //データ表示
            case "btn_datadisp_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_datadisp';
                Ctrl_pop("alert","visible",36);
                document.getElementById('btn_datadisp').style.top = y + "px";
                document.getElementById('btn_datadisp').style.visibility = 'Visible';
                break;
            //データ表示
            case "btn_comparison_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_datadisp';
                Ctrl_pop("alert","visible",36);
                document.getElementById('btn_datadisp').style.top = y + "px";
                document.getElementById('btn_datadisp').style.visibility = 'Visible';
                break;
            //CSV出力
            case "btn_csvoutput_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_csvoutput';
                Ctrl_pop("alert","visible",37);
                document.getElementById('btn_csvoutput').style.top = y + "px";
                document.getElementById('btn_csvoutput').style.visibility = 'Visible';
                break;

            // 作業者データCSV出力
            case "btn_helperdata_csvoutput_pre":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_helperdata_csvout';
                Ctrl_pop("custom_alert","visible",37);
                // document.getElementById('btn_csvoutput').style.top = y + "px";
                // document.getElementById('btn_csvoutput').style.visibility = 'Visible';
                break;
            // CSV出力　作業者データポップアップ内のCSV出力
            case "btn_helperdata_csvout":
                document.getElementById('btn_yes').style.visibility = 'hidden';
                disp = 'btn_csvoutput';
                Ctrl_pop("cover_pop_alert","visible",37);
                break;

            //フォームリセット
            case "form_useradd":
                document.getElementById('btn_yes').style.visibility = 'Visible';
                //ボタンの割付を変更する
                document.getElementById('btn_yes').onclick = ResetForm.bind(null,id_val);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_userfix":
                //ボタンの割付を変更する
                // document.getElementById('btn_yes').style.visibility = 'Visible';
                // document.getElementById('btn_yes').onclick = send.bind(null,'cxl_userfix','cxl_userfix',document.getElementById('id').value);
                document.getElementById('btn_yes').style.visibility = 'hidden';
                document.getElementById('btn_cxl_do').style.visibility = 'Visible';
                document.getElementById('btn_cxl_do').style.top = y + "px";
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_wearableadd":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = ResetForm.bind(null,id_val);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_wearablefix":
                //ボタンの割付を変更する
                // document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').style.visibility = 'hidden';
                document.getElementById('btn_cxl_do').style.visibility = 'Visible';
                document.getElementById('btn_cxl_do').style.top = y + "px";
                // document.getElementById('btn_yes').onclick = send.bind(null,'cxl_wearablefix','cxl_wearablefix',document.getElementById('id').value);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_facilityadd":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = ResetForm.bind(null,id_val);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_facilityfix":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'hidden';
                document.getElementById('btn_cxl_do').style.visibility = 'Visible';
                document.getElementById('btn_cxl_do').style.top = y + "px";
                // document.getElementById('btn_yes').style.visibility = 'Visible';
                // document.getElementById('btn_yes').onclick = send.bind(null,'cxl_facilityfix','cxl_facilityfix',document.getElementById('id').value);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_facility_inputadd":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = ResetForm.bind(null,id_val);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_facility_inputfix":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'hidden';
                document.getElementById('btn_cxl_do').style.visibility = 'Visible';
                document.getElementById('btn_cxl_do').style.top = y + "px";
                // document.getElementById('btn_yes').style.visibility = 'Visible';
                // document.getElementById('btn_yes').onclick = send.bind(null,'cxl_facility_inputfix','cxl_facility_inputfix',document.getElementById('id').value);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_risksensoradd":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = ResetForm.bind(null,id_val);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_risksensorfix":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'hidden';
                document.getElementById('btn_cxl_do').style.visibility = 'Visible';
                document.getElementById('btn_cxl_do').style.top = y + "px";
                // document.getElementById('btn_yes').onclick = send.bind(null,'cxl_risksensorfix','cxl_risksensorfix',document.getElementById('id').value);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));

                break;
            case "form_helperadd":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'Visible';
                document.getElementById('btn_yes').onclick = ResetForm.bind(null,id_val);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            case "form_helperfix":
                //ボタンの割付を変更する
                document.getElementById('btn_yes').style.visibility = 'hidden';
                document.getElementById('btn_cxl_do').style.visibility = 'Visible';
                document.getElementById('btn_cxl_do').style.top = y + "px";
                // document.getElementById('btn_yes').onclick = send.bind(null,'cxl_helperfix','cxl_helperfix',document.getElementById('id').value);
                Ctrl_pop("alert","visible",@json($errdata[10]['error']));
                break;
            default :
                break;
        }

    }

    function　ResetForm(formneme)
    {
        switch(formneme)
        {
            case "form_useradd":
                document.form_useradd.reset();
                document.getElementById("facilityno_div").style.visibility = 'hidden';
                break;
            case "form_userfix":
                document.form_userfix.reset();
                break;
            case "form_wearableadd":
                document.form_wearableadd.reset();
                break;
            case "form_wearablefix":
                document.form_wearablefix.reset();
                break;
            case "form_facilityadd":
                document.form_facilityadd.reset();
                img_clear();
                break;
            case "form_facilityfix":
                document.form_facilityfix.reset();
                break;
            case "form_risksensoradd":
                document.form_risksensoradd.reset();
                break;
            case "form_risksensorfix":
                document.form_risksensorfix.reset();
                break;
            case "form_helperadd":
                document.form_helperadd.reset();
                img_clear();
                break;
            case "form_helperfix":
                document.form_helperfix.reset();
                break;
            case "form_facility_inputadd":
                document.form_facility_inputadd.reset();
                img_clear();
                break;
        }
        Ctrl_pop('','collapse','')
    }


    //POPと関連ボタンの表示/非表示
    function Ctrl_pop(popname,mode,lb_txt)
    {

        if(mode != "visible")
        {
            document.getElementById('btn_yes').style.visibility = 'collapse';
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById('pop_alert').style.visibility = 'collapse';
            document.getElementById('pop_error_back').style.visibility = 'collapse';
            document.getElementById('pop_error').style.visibility = 'collapse';
            if(document.getElementById("pop_custom_alert") != null) document.getElementById('pop_custom_alert').style.visibility = 'collapse';
            if(document.getElementById("cover_pop_alert") != null) document.getElementById('cover_pop_alert').style.visibility = 'collapse';

            if(document.getElementById("pop_custom_alert") != null) document.getElementById('pop_custom_alert').style.visibility = 'collapse';
            if(document.getElementById("error_st_ymd") != null) document.getElementById('error_st_ymd').style.visibility = 'collapse';
            if(document.getElementById("error_date") != null) document.getElementById('error_date').style.visibility = 'collapse';
            if(document.getElementById("error") != null) document.getElementById('error').style.visibility = 'collapse';
            if(document.getElementById("error_ed_ymd") != null) document.getElementById('error_ed_ymd').style.visibility = 'collapse';
            if(document.getElementById("btn_cxl_do") != null) document.getElementById('btn_cxl_do').style.visibility = 'collapse';
            if(disp != "" && popname != "pop_custom_alert") document.getElementById(disp).style.visibility = 'collapse';
            disp = "";

            if(document.getElementById("btn_adduser") != null) document.getElementById('btn_adduser').style.visibility = 'collapse';
            if(document.getElementById("btn_fixuser") != null) document.getElementById('btn_fixuser').style.visibility = 'collapse';
            return ;
        }

        if(popname == "cover_pop_alert")
        {
            //　「CSV出力　作業者データ」で日付選択POPの上に表示するPOP
            document.getElementById('cover_lb_alert').innerHTML = @json($errdata[37]['error']);
            document.getElementById('pop_alert_back').style.visibility = 'Visible';
            document.getElementById('cover_pop_alert').style.visibility = 'Visible';
            return;
        } else if (popname == "cover_pop_alert_no")
        {
            //　「CSV出力　作業者データ」で日付選択POPの上に表示するPOPを非表示
            document.getElementById('cover_pop_alert').style.visibility = 'collapse';
            return;
        }

        //一旦今表示しているPOPを非表示にする
        document.getElementById('pop_alert_back').style.visibility = 'collapse';
        document.getElementById('pop_alert').style.visibility = 'collapse';
        if(document.getElementById("pop_custom_alert") != null) document.getElementById('pop_custom_alert').style.visibility = 'collapse';
        document.getElementById('pop_error_back').style.visibility = 'collapse';
        document.getElementById('pop_error').style.visibility = 'collapse';
        switch(popname)
        {
            case "error":
                if(lb_txt == 8) document.getElementById('lb_error').innerHTML = @json($errdata[8]['error']);
                else if(lb_txt == 9) document.getElementById('lb_error').innerHTML = @json($errdata[9]['error']);
                else if(lb_txt == 15) document.getElementById('lb_error').innerHTML = @json($errdata[15]['error']);
                else if(lb_txt == 16) document.getElementById('lb_error').innerHTML = @json($errdata[16]['error']);
                else if(lb_txt == 17) document.getElementById('lb_error').innerHTML = @json($errdata[17]['error']);
                else if(lb_txt == 23) document.getElementById('lb_error').innerHTML = @json($errdata[23]['error']);
                else if(lb_txt == 24) document.getElementById('lb_error').innerHTML = @json($errdata[24]['error']);
                else if(lb_txt == 30) document.getElementById('lb_error').innerHTML = @json($errdata[30]['error']);
                else if(lb_txt == 31) document.getElementById('lb_error').innerHTML = @json($errdata[31]['error']);
                // else if(lb_txt == 32) document.getElementById('lb_error').innerHTML = @json($errdata[32]['error']);
                else if(lb_txt == 32)
                {
                    // document.getElementById("txt_wearable").style.visibility = 'visible';
                    break;
                }
                else if(lb_txt == 321)
                {
                    // document.getElementById("txt_wearable1").style.visibility = 'visible';
                    break;
                }
                else if(lb_txt == 322)
                {
                    // document.getElementById("txt_wearable2").style.visibility = 'visible';
                    break;
                }
                else if(lb_txt == 33) document.getElementById('lb_error').innerHTML = @json($errdata[33]['error']);
                else if(lb_txt == 38) document.getElementById('lb_error').innerHTML = @json($errdata[38]['error']);
                else if(lb_txt == 39) document.getElementById('lb_error').innerHTML = @json($errdata[39]['error']);
                else if(lb_txt == 40) document.getElementById('lb_error').innerHTML = @json($errdata[40]['error']);
                else if(lb_txt == 41) document.getElementById('lb_error').innerHTML = @json($errdata[41]['error']);
                else document.getElementById('lb_error').innerHTML = lb_txt;
                document.getElementById('pop_error_back').style.visibility = 'visible';
                document.getElementById('pop_error').style.visibility = 'visible';
                break;
            case "reload":
                document.getElementById('lb_error').innerHTML = lb_txt;
                document.getElementById('btn_ok').style.visibility = 'hidden';
                document.getElementById('pop_error_back').style.visibility = 'visible';
                document.getElementById('pop_error').style.visibility = 'visible';
                document.getElementById('btn_reload').style.visibility = 'visible';
                break;
            case "alert":
                if(lb_txt == 34) document.getElementById('lb_alert').innerHTML = @json($errdata[34]['error']);
                else if(lb_txt == 35) document.getElementById('lb_alert').innerHTML = @json($errdata[35]['error']);
                else if(lb_txt == 36) document.getElementById('lb_alert').innerHTML = @json($errdata[36]['error']);
                else if(lb_txt == 37) document.getElementById('lb_alert').innerHTML = @json($errdata[37]['error']);
                else document.getElementById('lb_alert').innerHTML = lb_txt;
                document.getElementById('pop_alert_back').style.visibility = 'visible';
                document.getElementById('pop_alert').style.visibility = 'visible';
                break;
            case "custom_alert":
                document.getElementById('lb_alert').innerHTML = lb_txt;
                document.getElementById('pop_alert_back').style.visibility = 'visible';
                document.getElementById('pop_custom_alert').style.visibility = 'visible';
                break;
            case "timestudytool":
                document.getElementById('pop_alert_back').style.visibility = 'visible';
                document.getElementById('pop_timestudytool').style.visibility = 'visible';
                break;
            default :
                break;
        }
    }



    //ajaxで送信
    function send(url,name,senddata)
    {
        //選択されていない時は選択してくださいPOPを表示
        if(isNaN(parseInt(senddata,10))==true)
        {
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById("btn_yes").style.visibility = 'collapse';
            Ctrl_pop("error","visible",@json($errdata[3]['error']));
            return ;
        }

        //作業者名が入っている場合、「作業者からウェアラブルデバイスを削除してから削除処理を行ってください」を表示して、処理終了。
        if(url == "wearable_del")
        {

        }

        var xhr = new XMLHttpRequest();
        var requests = "name=" + name + "&data=" + senddata;
        xhr.open('POST', url, true);
        xhr.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);
        xhr.send(requests);
        try
        {
            xhr.onreadystatechange = function()
            {

                    if(this.readyState === 4)
                    {
                        //datas -> ~split("\n")にしたら行で1要素
                        //   recievedata = this.responseText.split(",");        // 全レコードのデータ
                        // console.log(recievedata);
                        //成功
                        if(name == "cxl_userfix")
                        {
                            var obj = JSON.parse(this.responseText || "null");
                            document.getElementById('username').value = (obj[0].username).trim();
                        }
                        else if(name == "cxl_wearablefix")
                        {
                            var obj = JSON.parse(this.responseText || "null");

                            if(obj[0].devicename == ""  ||  obj[0].devicename =="null" ||obj[0].devicename == null || obj[0].devicename == "undefined" ) document.getElementById('devicename').value = "";
                            else document.getElementById('devicename').value = (obj[0].devicename).trim();
                            if(obj[0].userid == ""  ||  obj[0].userid =="null" ||obj[0].userid == null || obj[0].userid == "undefined" ) document.getElementById('userid').value = "";
                            else document.getElementById('userid').value = (obj[0].userid).trim();
                            if(obj[0].passwd == ""  ||  obj[0].passwd =="null" ||obj[0].passwd == null || obj[0].passwd == "undefined" ) document.getElementById('passwd').value = "";
                            else document.getElementById('passwd').value = (obj[0].passwd).trim();
                            if(obj[0].auth == ""  ||  obj[0].auth =="null" ||obj[0].auth == null || obj[0].auth == "undefined" ) document.getElementById('auth').value = "";
                            else document.getElementById('auth').value = (obj[0].auth).trim();
                            if(obj[0].clientid == ""  ||  obj[0].clientid =="null" ||obj[0].clientid == null || obj[0].clientid == "undefined" ) document.getElementById('clientid').value = "";
                            else document.getElementById('clientid').value = (obj[0].clientid).trim();
                            if(obj[0].clientsc == ""  ||  obj[0].clientsc =="null" ||obj[0].clientsc == null || obj[0].clientsc == "undefined" ) document.getElementById('clientsc').value = "";
                            else document.getElementById('clientsc').value = (obj[0].clientsc).trim();
                        }
                        else if(name == "cxl_risksensorfix")
                        {
                            var obj = JSON.parse(this.responseText || "null");
                            if(obj[0].devicename == ""  ||  obj[0].devicename =="null" ||obj[0].devicename == null || obj[0].devicename == "undefined" ) document.getElementById('devicename').value = "";
                            else document.getElementById('devicename').value = (obj[0].devicename).trim();
                            if(obj[0].facility == ""  ||  obj[0].facility =="null" ||obj[0].facility == null || obj[0].facility == "undefined" ) document.getElementById('facility').value = "";
                            else document.getElementById('facility').value = (obj[0].facility).trim();
                            if(obj[0].helpername == ""  ||  obj[0].helpername =="null" ||obj[0].helpername == null || obj[0].helpername == "undefined" ) document.getElementById('helpername').value = "";
                            else document.getElementById('helpername').value = (obj[0].helpername).trim();
                        }
                        else if(name == "cxl_helperfix")
                        {

                            var obj = JSON.parse(this.responseText || "null");
                            // document.getElementById('devicename').value = obj[0].devicename;
                            if(obj[0].facility == ""  ||  obj[0].facility =="null" ||obj[0].facility == null || obj[0].facility == "undefined" ) document.getElementById('facility').value = "";
                            else document.getElementById('facility').value = (obj[0].facility).trim();
                            if(obj[0].helpername == ""  ||  obj[0].helpername =="null" ||obj[0].helpername == null || obj[0].helpername == "undefined" ) document.getElementById('helpername').value = "";
                            else document.getElementById('helpername').value = (obj[0].helpername).trim();
                            if(obj[0].age == ""  ||  obj[0].age =="null" ||obj[0].age == null || obj[0].age == "undefined" ) document.getElementById('age').value = "";
                            else document.getElementById('age').value = (obj[0].age).trim();

                            if(obj[0].position != "" || "null")
                            {
                                // select要素を取得
                                var element = document.getElementById( "position" );
                                // 全てのoption要素を取得
                                var elements = element.options;
                                // 各要素の値を確認
                                var a = "" ;
                                for ( var i=0,l=elements.length; l>i; i++ )
                                {
                                    if(obj[0].position == elements[i].value)
                                    {
                                        elements[i].selected = true;
                                        break;
                                    }
                                }
                            }
                            if(obj[0].sex != "" || "null")
                            {
                                // select要素を取得
                                var element = document.getElementById( "sex" );
                                // 全てのoption要素を取得
                                var elements = element.options;
                                // 各要素の値を確認
                                var a = "" ;
                                for ( var i=0,l=elements.length; l>i; i++ )
                                {
                                    if(obj[0].sex == elements[i].value)
                                    {
                                        elements[i].selected = true;
                                        break;
                                    }
                                }
                            }
                            // if(obj[0].wearableno != "" || "null")
                            // {
                            //     // select要素を取得
                            //     var element = document.getElementById( "wearableno" );
                            //     // 全てのoption要素を取得
                            //     var elements = element.options;
                            //     // 各要素の値を確認
                            //     var a = "" ;
                            //     for ( var i=0,l=elements.length; l>i; i++ )
                            //     {
                            //         if(obj[0].wearableno == elements[i].value)
                            //         {
                            //             elements[i].selected = true;
                            //             break;
                            //         }
                            //     }
                            // }
                            // if(obj[0].backpainno != "" || "null")
                            // {
                            //     // select要素を取得
                            //     var element = document.getElementById( "backpainno" );
                            //     // 全てのoption要素を取得
                            //     var elements = element.options;
                            //     // 各要素の値を確認
                            //     var a = "" ;
                            //     for ( var i=0,l=elements.length; l>i; i++ )
                            //     {
                            //         if(obj[0].backpainno == elements[i].value)
                            //         {
                            //             elements[i].selected = true;
                            //             break;
                            //         }
                            //     }
                            // }
                            // if(obj[0].jobfrom == ""  ||  obj[0].jobfrom =="null" ||obj[0].jobfrom == null || obj[0].jobfrom == "undefined" )
                            // {
                            //     document.getElementById('jobfrom_h').value = "";
                            //     document.getElementById('jobfrom_m').value = "";
                            // }
                            // else
                            // {
                            //     document.getElementById('jobfrom_h').value = obj[0].jobfrom.slice(0, 2);
                            //     document.getElementById('jobfrom_m').value = obj[0].jobfrom.slice(2);
                            // }
                            // if(obj[0].jobto == ""  ||  obj[0].jobto =="null" ||obj[0].jobto == null || obj[0].jobto == "undefined" )
                            // {
                            //     document.getElementById('jobto_h').value = "";
                            //     document.getElementById('jobto_m').value = "";
                            // }
                            // else
                            // {
                            //     document.getElementById('jobto_h').value = obj[0].jobto.slice(0, 2);
                            //     document.getElementById('jobto_m').value = obj[0].jobto.slice(2);
                            // }
                            // if(obj[0].measufrom == ""  ||  obj[0].measufrom =="null" ||obj[0].measufrom == null || obj[0].measufrom == "undefined" ) document.getElementById('measufrom').value = "";
                            // else document.getElementById('measufrom').value = obj[0].measufrom.slice(0, 4) + "-" + obj[0].measufrom.slice(4,6) + "-" + obj[0].measufrom.slice(6);

                            // if(obj[0].measuto == ""  ||  obj[0].measuto =="null" ||obj[0].measuto == null || obj[0].measuto == "undefined" ) document.getElementById('measuto').value = "";
                            // else document.getElementById('measuto').value = obj[0].measuto.slice(0, 4) + "-" + obj[0].measuto.slice(4,6) + "-" + obj[0].measuto.slice(6);


                        }
                        else if(name == "cxl_facility_inputfix" || name == "cxl_facilityfix")
                        {
                            var obj = JSON.parse(this.responseText || "null");
                            if(obj[0].facility == ""  ||  obj[0].facility =="null" ||obj[0].facility == null || obj[0].facility == "undefined" ) document.getElementById('facility').value ="";
                            else document.getElementById('facility').value = obj[0].facility;

                            if(isNaN(obj[0].idname) ||  obj[0].idname == "undefined" || obj[0].idname == "" || obj[0].idname == "null" || obj[0].idname == null)
                            {
                                // select要素を取得
                                var element = document.getElementById( "pass" );
                                // 全てのoption要素を取得
                                var elements = element.options;
                                elements[0].selected = true;
                            }
                            else
                            {
                                // select要素を取得
                                var element = document.getElementById( "pass" );
                                // 全てのoption要素を取得
                                var elements = element.options;
                                // 各要素の値を確認
                                var a = "" ;
                                for ( var i=0,l=elements.length; l>i; i++ )
                                {
                                    if(obj[0].position == elements[i].value)
                                    {
                                        elements[i].selected = true;
                                        break;
                                    }
                                }
                            }

                            if(obj[0].address == ""  ||  obj[0].address =="null" ||obj[0].address == null || obj[0].address == "undefined" ) document.getElementById('address').value = "";
                            else document.getElementById('address').value = (obj[0].address).trim();
                            if(obj[0].tel == ""  || obj[0].tel == "null" || obj[0].tel == null ||  obj[0].tel == "undefined") document.getElementById('tel').value = "";
                            else document.getElementById('tel').value = (obj[0].tel).trim();
                            if(obj[0].mail == ""  ||  obj[0].mail =="null" || obj[0].mail == null || obj[0].mail == "undefined") document.getElementById('mail').value = "";
                            else document.getElementById('mail').value = (obj[0].mail).trim();

                            //2021.05.18 追加
                            if(obj[0].url == ""  ||  obj[0].url =="null" || obj[0].url == null || obj[0].url == "undefined") document.getElementById('url').value = "";
                            else document.getElementById('url').value = (obj[0].url).trim();


                            if(obj[0].item1 == ""  ||  obj[0].item1 =="null" ||obj[0].item1 == null || obj[0].item1 == "undefined" ) document.getElementById("item1").innerText = 0;
                            else document.getElementById("item1").value = obj[0].item1;
                            if(obj[0].item2 == ""  ||  obj[0].item2 =="null" ||obj[0].item2 == null || obj[0].item2 == "undefined" ) document.getElementById("item2").innerText = 0;
                            else document.getElementById("item2").innerText = obj[0].item2;

                            if(obj[0].item3 == ""  ||  obj[0].item3 =="null" ||obj[0].item3 == null || obj[0].item3 == "undefined" ) document.getElementById("item3").innerText = 0;
                            else document.getElementById("item3").innerText = obj[0].item3;

                            if(obj[0].item4 == ""  ||  obj[0].item4 =="null" ||obj[0].item4 == null || obj[0].item4 == "undefined" ) document.getElementById("item4").innerText = 0;
                            else document.getElementById("item4").innerText = obj[0].item4;

                            if(obj[0].item5 == ""  ||  obj[0].item5 =="null" ||obj[0].item5 == null || obj[0].item5 == "undefined" ) document.getElementById("item5").innerText = 0;
                            else document.getElementById("item5").innerText = obj[0].item5;

                            if(obj[0].item6 == ""  ||  obj[0].item6 =="null" ||obj[0].item6 == null || obj[0].item6 == "undefined" ) document.getElementById("item6").innerText = 0;
                            else document.getElementById("item6").innerText = obj[0].item6;

                            if(obj[0].item7 == ""  ||  obj[0].item7 =="null" ||obj[0].item7 == null || obj[0].item7 == "undefined" ) document.getElementById("item7").innerText = 0;
                            else document.getElementById("item7").innerText = obj[0].item7;


                            if(obj[0].item8 == ""  ||  obj[0].item8 =="null" ||obj[0].item8 == null || obj[0].item8 == "undefined" ) document.getElementById("item8").innerText = 0;
                            else document.getElementById("item8").innerText = obj[0].item8;

                            if(obj[0].item9 == ""  ||  obj[0].item9 =="null" ||obj[0].item9 == null || obj[0].item9 == "undefined" ) document.getElementById("item9").innerText = 0;
                            else document.getElementById("item9").innerText = obj[0].item9;

                            if(obj[0].item10 == ""  ||  obj[0].item10 =="null" ||obj[0].item10 == null || obj[0].item10 == "undefined" ) document.getElementById("item10").innerText = 0;
                            else document.getElementById("item10").innerText = obj[0].item10;


                            if(obj[0].item11 == ""  ||  obj[0].item11 =="null" ||obj[0].item11 == null || obj[0].item11 == "undefined" ) document.getElementById("item11").innerText = 0;
                            else document.getElementById("item11").innerText = obj[0].item11;

                            if(obj[0].item12 == ""  ||  obj[0].item12 =="null" ||obj[0].item12 == null || obj[0].item12 == "undefined" ) document.getElementById("item12").innerText = 0;
                            else document.getElementById("item12").innerText = obj[0].item12;


                            if(obj[0].item13 == ""  ||  obj[0].item13 =="null" ||obj[0].item13 == null || obj[0].item13 == "undefined" ) document.getElementById("item13").innerText = 0;
                            else document.getElementById("item13").innerText = obj[0].item13;


                            if(obj[0].item14 == ""  ||  obj[0].item14 =="null" ||obj[0].item14 == null || obj[0].item14 == "undefined" ) document.getElementById("item14").innerText = 0;
                            else document.getElementById("item14").innerText = obj[0].item14;


                            if(obj[0].item15 == ""  ||  obj[0].item15 =="null" ||obj[0].item15 == null || obj[0].item15 == "undefined" ) document.getElementById("item15").innerText = 0;
                            else document.getElementById("item15").innerText = obj[0].item15;


                            if(obj[0].item16 == ""  ||  obj[0].item16 =="null" ||obj[0].item16 == null || obj[0].item16 == "undefined" ) document.getElementById("item16").innerText = 0;
                            else document.getElementById("item16").innerText = obj[0].item16;


                            if(obj[0].item17 == ""  ||  obj[0].item17 =="null" ||obj[0].item17 == null || obj[0].item17 == "undefined" ) document.getElementById("item17").innerText = 0;
                            else document.getElementById("item17").innerText = obj[0].item17;


                            if(obj[0].item18 == ""  ||  obj[0].item18 =="null" ||obj[0].item18 == null || obj[0].item18 == "undefined" ) document.getElementById("item18").innerText = 0;
                            else document.getElementById("item18").innerText = obj[0].item18;


                            if(obj[0].item19 == ""  ||  obj[0].item19 =="null" ||obj[0].item19 == null || obj[0].item19 == "undefined" ) document.getElementById("item19").innerText = 0;
                            else document.getElementById("item19").innerText = obj[0].item19;

                            if(obj[0].item20 == ""  ||  obj[0].item20 =="null" ||obj[0].item20 == null || obj[0].item20 == "undefined" ) document.getElementById("item20").innerText = 0;
                            else document.getElementById("item20").innerText = obj[0].item20;

                            if(obj[0].item21 == ""  ||  obj[0].item21 =="null" ||obj[0].item21 == null || obj[0].item21 == "undefined" ) document.getElementById("item21").innerText = 0;
                            else document.getElementById("item21").innerText = obj[0].item21;

                            if(obj[0].item22 == ""  ||  obj[0].item22 =="null" ||obj[0].item22 == null || obj[0].item22 == "undefined" ) document.getElementById("item22").innerText = 0;
                            else document.getElementById("item22").innerText = obj[0].item22;

                            if(obj[0].item23 == ""  ||  obj[0].item23 =="null" ||obj[0].item23 == null || obj[0].item23 == "undefined" ) document.getElementById("item23").innerText = 0;
                            else document.getElementById("item23").innerText = obj[0].item23;

                            if(obj[0].item24 == ""  ||  obj[0].item24 =="null" ||obj[0].item24 == null || obj[0].item24 == "undefined" ) document.getElementById("item24").innerText = 0;
                            else document.getElementById("item24").innerText = obj[0].item24;

                            if(obj[0].item25 == ""  ||  obj[0].item25 =="null" ||obj[0].item25 == null || obj[0].item25 == "undefined" ) document.getElementById("item25").innerText = 0;
                            else document.getElementById("item25").innerText = obj[0].item25;

                            if(obj[0].item26 == ""  ||  obj[0].item26 =="null" ||obj[0].item26 == null || obj[0].item26 == "undefined" ) document.getElementById("item26").innerText = 0;
                            else document.getElementById("item26").innerText = obj[0].item26;

                            if(obj[0].item27 == ""  ||  obj[0].item27 =="null" ||obj[0].item27 == null || obj[0].item27 == "undefined" ) document.getElementById("item27").innerText = 0;
                            else document.getElementById("item27").innerText = obj[0].item27;

                            if(obj[0].item28 == ""  ||  obj[0].item28 =="null" ||obj[0].item28 == null || obj[0].item28 == "undefined" ) document.getElementById("item28").innerText = 0;
                            else document.getElementById("item28").innerText = obj[0].item28;

                            if(obj[0].item29 == ""  ||  obj[0].item29 =="null" ||obj[0].item29 == null || obj[0].item29 == "undefined" ) document.getElementById("item29").innerText = 0;
                            else document.getElementById("item29").innerText = obj[0].item29;

                            if(obj[0].item30 == ""  ||  obj[0].item30 =="null" ||obj[0].item30 == null || obj[0].item30 == "undefined" ) document.getElementById("item30").innerText = 0;
                            else document.getElementById("item30").innerText = obj[0].item30;


                            if(obj[0].item31 == ""  ||  obj[0].item31 =="null" ||obj[0].item31 == null || obj[0].item31 == "undefined" ) document.getElementById("item31").innerText = 0;
                            else document.getElementById("item31").innerText = obj[0].item31;

                            if(obj[0].item32 == ""  ||  obj[0].item32 =="null" ||obj[0].item32 == null || obj[0].item32 == "undefined" ) document.getElementById("item32").innerText = 0;
                            else document.getElementById("item32").innerText = obj[0].item32;

                            if(obj[0].item33 == ""  ||  obj[0].item33 =="null" ||obj[0].item33 == null || obj[0].item33 == "undefined" ) document.getElementById("item33").innerText = 0;
                            else document.getElementById("item33").innerText = obj[0].item33;

                            if(obj[0].item34 == ""  ||  obj[0].item34 =="null" ||obj[0].item34 == null || obj[0].item34 == "undefined" ) document.getElementById("item34").innerText = 0;
                            else document.getElementById("item34").innerText = obj[0].item34;

                            if(obj[0].item35 == ""  ||  obj[0].item35 =="null" ||obj[0].item35 == null || obj[0].item35 == "undefined" ) document.getElementById("item35").innerText = 0;
                            else document.getElementById("item35").innerText = obj[0].item35;

                            if(obj[0].item36 == ""  ||  obj[0].item36 =="null" ||obj[0].item36 == null || obj[0].item36 == "undefined" ) document.getElementById("item36").innerText = 0;
                            else document.getElementById("item36").innerText = obj[0].item36;

                            if(obj[0].item37 == ""  ||  obj[0].item37 =="null" ||obj[0].item37 == null || obj[0].item37 == "undefined" ) document.getElementById("item37").innerText = 0;
                            else document.getElementById("item37").innerText = obj[0].item37;

                            if(obj[0].item38 == ""  ||  obj[0].item38 =="null" ||obj[0].item38 == null || obj[0].item38 == "undefined" ) document.getElementById("item38").innerText = 0;
                            else document.getElementById("item38").innerText = obj[0].item38;

                            if(obj[0].item39 == ""  ||  obj[0].item39 =="null" ||obj[0].item39 == null || obj[0].item39 == "undefined" ) document.getElementById("item39").innerText = 0;
                            else document.getElementById("item39").innerText = obj[0].item39;

                            if(obj[0].item40 == ""  ||  obj[0].item40 =="null" ||obj[0].item40 == null || obj[0].item40 == "undefined" ) document.getElementById("item40").innerText = 0;
                            else document.getElementById("item40").innerText = obj[0].item40;

                            if(obj[0].item41 == ""  ||  obj[0].item41 =="null" ||obj[0].item41 == null || obj[0].item41 == "undefined" ) document.getElementById("item41").innerText = 0;
                            else document.getElementById("item41").innerText = obj[0].item41;

                            if(obj[0].item42 == ""  ||  obj[0].item42 =="null" ||obj[0].item42 == null || obj[0].item42 == "undefined" ) document.getElementById("item42").innerText = 0;
                            else document.getElementById("item42").innerText = obj[0].item42;

                            if(obj[0].item43 == ""  ||  obj[0].item43 =="null" ||obj[0].item43 == null || obj[0].item43 == "undefined" ) document.getElementById("item43").innerText = 0;
                            else document.getElementById("item43").innerText = obj[0].item43;

                            if(obj[0].item44 == ""  ||  obj[0].item44 =="null" ||obj[0].item44 == null || obj[0].item44 == "undefined" ) document.getElementById("item44").innerText = 0;
                            else document.getElementById("item44").innerText = obj[0].item44;

                            if(obj[0].item45 == ""  ||  obj[0].item45 =="null" ||obj[0].item45 == null || obj[0].item45 == "undefined" ) document.getElementById("item45").innerText = 0;
                            else document.getElementById("item45").innerText = obj[0].item45;

                            if(obj[0].item46 == ""  ||  obj[0].item46 =="null" ||obj[0].item46 == null || obj[0].item46 == "undefined" ) document.getElementById("item46").innerText = 0;
                            else document.getElementById("item46").innerText = obj[0].item46;


                            if(obj[0].item47 == ""  ||  obj[0].item47 =="null" ||obj[0].item47 == null || obj[0].item47 == "undefined" ) document.getElementById("item47").innerText = 0;
                            else document.getElementById("item47").innerText = obj[0].item47;

                            if(obj[0].item48 == ""  ||  obj[0].item48 =="null" ||obj[0].item48 == null || obj[0].item48 == "undefined" ) document.getElementById("item48").innerText = 0;
                            else document.getElementById("item48").innerText = obj[0].item48;

                            if(obj[0].item49 == ""  ||  obj[0].item49 =="null" ||obj[0].item49 == null || obj[0].item49 == "undefined" ) document.getElementById("item49").innerText = 0;
                            else document.getElementById("item49").innerText = obj[0].item49;

                            if(obj[0].item50 == ""  ||  obj[0].item50 =="null" ||obj[0].item50 == null || obj[0].item50 == "undefined" ) document.getElementById("item50").innerText = 0;
                            else document.getElementById("item50").innerText = obj[0].item50;

                            if(obj[0].item51 == ""  ||  obj[0].item51 =="null" ||obj[0].item51 == null || obj[0].item51 == "undefined" ) document.getElementById("item51").innerText = 0;
                            else document.getElementById("item51").innerText = obj[0].item51;

                            if(obj[0].item52 == ""  ||  obj[0].item52 =="null" ||obj[0].item52 == null || obj[0].item52 == "undefined" ) document.getElementById("item52").innerText = 0;
                            else document.getElementById("item52").innerText = obj[0].item52;

                            if(obj[0].item53 == ""  ||  obj[0].item53 =="null" ||obj[0].item53 == null || obj[0].item53 == "undefined" ) document.getElementById("item53").innerText = 0;
                            else document.getElementById("item53").innerText = obj[0].item53;

                            if(obj[0].item54 == ""  ||  obj[0].item54 =="null" ||obj[0].item54 == null || obj[0].item54 == "undefined" ) document.getElementById("item54").innerText = 0;
                            else document.getElementById("item54").innerText = obj[0].item54;

                            if(obj[0].item55 == ""  ||  obj[0].item55 =="null" ||obj[0].item55 == null || obj[0].item55 == "undefined" ) document.getElementById("item55").innerText = 0;
                            else document.getElementById("item55").innerText = obj[0].item55;

                            if(obj[0].item56 == ""  ||  obj[0].item56 =="null" ||obj[0].item56 == null || obj[0].item56 == "undefined" ) document.getElementById("item56").innerText = 0;
                            else document.getElementById("item56").innerText = obj[0].item56;

                            if(obj[0].item57 == ""  ||  obj[0].item57 =="null" ||obj[0].item57 == null || obj[0].item57 == "undefined" ) document.getElementById("item57").innerText = 0;
                            else document.getElementById("item57").innerText = obj[0].item57;

                            if(obj[0].item58 == ""  ||  obj[0].item58 =="null" ||obj[0].item58 == null || obj[0].item58 == "undefined" ) document.getElementById("item58").innerText = 0;
                            else document.getElementById("item58").innerText = obj[0].item58;

                            if(obj[0].item59 == ""  ||  obj[0].item59 =="null" ||obj[0].item59 == null || obj[0].item59 == "undefined" ) document.getElementById("item59").innerText = 0;
                            else document.getElementById("item59").innerText = obj[0].item59;

                            if(obj[0].item60 == ""  ||  obj[0].item60 =="null" ||obj[0].item60 == null || obj[0].item60 == "undefined" ) document.getElementById("item60").innerText = 0;
                            else document.getElementById("item60").innerText = obj[0].item60;

                            if(obj[0].item61 == ""  ||  obj[0].item61 =="null" ||obj[0].item61 == null || obj[0].item61 == "undefined" ) document.getElementById("item61").innerText = 0;
                            else document.getElementById("item61").innerText = obj[0].item61;

                            if(obj[0].item62 == ""  ||  obj[0].item62 =="null" ||obj[0].item62 == null || obj[0].item62 == "undefined" ) document.getElementById("item62").innerText = 0;
                            else document.getElementById("item62").innerText = obj[0].item62;

                            if(obj[0].item63 == ""  ||  obj[0].item63 =="null" ||obj[0].item63 == null || obj[0].item63 == "undefined" ) document.getElementById("item63").innerText = 0;
                            else document.getElementById("item63").innerText = obj[0].item63;

                            if(obj[0].item64 == ""  ||  obj[0].item64 =="null" ||obj[0].item64 == null || obj[0].item64 == "undefined" ) document.getElementById("item64").innerText = 0;
                            else document.getElementById("item64").innerText = obj[0].item64;

                            if(obj[0].item65 == ""  ||  obj[0].item65 =="null" ||obj[0].item65 == null || obj[0].item65 == "undefined" ) document.getElementById("item65").innerText = 0;
                            else document.getElementById("item65").innerText = obj[0].item65;

                            if(obj[0].item66 == ""  ||  obj[0].item66 =="null" ||obj[0].item66 == null || obj[0].item66 == "undefined" ) document.getElementById("item66").innerText = 0;
                            else document.getElementById("item66").innerText = obj[0].item66;

                            if(obj[0].item67 == ""  ||  obj[0].item67 =="null" ||obj[0].item67 == null || obj[0].item67 == "undefined" ) document.getElementById("item67").innerText = 0;
                            else document.getElementById("item67").innerText = obj[0].item67;




                            if(obj[0].item68 == ""  ||  obj[0].item68 =="null" ||obj[0].item68 == null || obj[0].item68 == "undefined" ) document.getElementById("item68").innerText = 0;
                            else document.getElementById("item68").innerText = obj[0].item65;

                            if(obj[0].item69 == ""  ||  obj[0].item69 =="null" ||obj[0].item69 == null || obj[0].item69 == "undefined" ) document.getElementById("item69").innerText = 0;
                            else document.getElementById("item69").innerText = obj[0].item69;

                            if(obj[0].item70 == ""  ||  obj[0].item70 =="null" ||obj[0].item70 == null || obj[0].item70 == "undefined" ) document.getElementById("item70").innerText = 0;
                            else document.getElementById("item70").innerText = obj[0].item70;

                            if(obj[0].item71 == ""  ||  obj[0].item71 =="null" ||obj[0].item71 == null || obj[0].item71 == "undefined" ) document.getElementById("item71").innerText = 0;
                            else document.getElementById("item71").innerText = obj[0].item71;

                            if(obj[0].item72 == ""  ||  obj[0].item72 =="null" ||obj[0].item72 == null || obj[0].item72 == "undefined" ) document.getElementById("item72").innerText = 0;
                            else document.getElementById("item72").innerText = obj[0].item72;







                            // for(var i=1;i<68;i++)
                            // {
                            //     var idname = 'item'+ i;
                            //     if(isNaN(obj[0].idname) ||  obj[0].idname == "undefined" || obj[0].idname == "" || obj[0].idname == "null"|| obj[0].idname == null)document.getElementById(idname).value = "";
                            //     else document.getElementById(idname).value = (obj[0].idname).trim();
                            // }


                            // for(var i=1;i<21;i++)
                            // {
                            //     var idname = 'pic'+ i;
                            //     var idname = 'item'+ i;
                            //     if(isNaN(obj[0].idname) ||  obj[0].idname == "undefined" || obj[0].idname == "" || obj[0].idname == "null" || obj[0].idname == null )document.getElementById(idname).value = "";
                            //     else document.getElementById(idname).value = (obj[0].idname).trim();
                            // }


                        }

                        else
                        {
                            Ctrl_pop('','collapse','');
                            if(this.response == 1)
                            {
                                //削除成功
                                Ctrl_pop("reload","visible",@json($errdata[7]['error']));
                                // var timeoutID = setTimeout("location.reload()",2000);
                                return;
                            }
                            else
                            {
                                Ctrl_pop("error","visible",@json($errdata[6]['error']));
                                return;
                            }
                        }
                        Ctrl_pop('','collapse','');
                    }
                };
        }
        catch{alert();}
    }


    //jquery + ajax フォーム内の値を送信
    $('#form_useradd').submit(function(event)
    {
        event.preventDefault();
        document.getElementById("err_pass").innerText = "";
        document.getElementById("err_username").innerText = "";
        document.getElementById("err_authority").innerText = "";
        document.getElementById("err_facilityno").innerText = "";
        // フォームデータを取得
        var formdata = new FormData(document.getElementById("form_useradd"));

        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.pass != "undefined")
                    {
                        document.getElementById("err_pass").innerText = data.errors.pass;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("pass").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.username != "undefined")
                    {
                        document.getElementById("err_username").innerText = data.errors.username;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("username").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.authority != "undefined")
                    {
                        document.getElementById("err_authority").innerText = data.errors.authority;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("authority").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.facilityno != "undefined")
                    {
                        document.getElementById("err_facilityno").innerText = data.errors.facilityno;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("facilityno").scrollIntoView(true);
                        }
                    }
                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_adduser").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",8);
                }
                else if(data == "facilerr")
                {
                    document.getElementById("err_facilityno").innerText = "施設を登録してください";
                    document.getElementById("facilityno").scrollIntoView(true);
                    Ctrl_pop('','collapse','');
                }
                else
                {
                    while(document.getElementById("targetid").value == "") document.getElementById("targetid").value = data;

                    //submit()でフォームの内容を送信
                    document.fix_index.submit();
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_adduser").style.visibility = 'collapse';
                Ctrl_pop("error","visible",8);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
    $('#form_userfix').submit(function(event)
    {
        event.preventDefault();
        document.getElementById("err_pass").innerText = "";
        document.getElementById("err_username").innerText = "";
        document.getElementById("err_authority").innerText = "";
        document.getElementById("err_facilityno").innerText = "";
        // フォームデータを取得
        var formData = new FormData($(this)[0]);

        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                // return;
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.pass != "undefined")
                    {
                        document.getElementById("err_pass").innerText = data.errors.pass;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_pass").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.username != "undefined")
                    {
                        document.getElementById("err_username").innerText = data.errors.username;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_username").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.authority != "undefined")
                    {
                        document.getElementById("err_authority").innerText = data.errors.authority;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_authority").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.facilityno != "undefined")
                    {
                        document.getElementById("err_facilityno").innerText = data.errors.facilityno;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_facilityno").scrollIntoView(true);
                        }
                    }

                    Ctrl_pop('','collapse','');

                }
                else if(data == "error")
                {
                    document.getElementById("btn_fixuser").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",9);
                }
                else if(data == "facilerr")
                {
                    document.getElementById("err_facilityno").innerText = "施設を登録してください";
                    document.getElementById("facilityno").scrollIntoView(true);
                    Ctrl_pop('','collapse','');
                }
                else
                {
                    if(document.getElementById("addmess")) document.getElementById("addmess").innerText = "";
                    document.getElementById("fixmess").innerText = '修正しました。';
                    Ctrl_pop('','collapse','');
                    window.scrollTo(0, 0);
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_fixuser").style.visibility = 'collapse';
                Ctrl_pop("error","visible",9);
                return ;
            }
        });
    });


    //jquery + ajax フォーム内の値を送信
    $('#form_wearableadd').submit(function(event)
    {
        event.preventDefault();
        document.getElementById("err_devicename").innerText = "";
        document.getElementById("err_userid").innerText = "";
        document.getElementById("err_passwd").innerText = "";
        document.getElementById("err_clientid").innerText = "";
        document.getElementById("err_clientsc").innerText = "";
        document.getElementById("err_auth").innerText = "";

        // フォームデータを取得
        var formdata = new FormData(document.getElementById("form_wearableadd"));

        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                // alert(data);
                // return;
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.devicename != "undefined")
                    {
                        document.getElementById("err_devicename").innerText = data.errors.devicename;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_devicename").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.userid != "undefined")
                    {
                        document.getElementById("err_userid").innerText = data.errors.userid;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_userid").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.passwd != "undefined")
                    {
                        document.getElementById("err_passwd").innerText = data.errors.passwd;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_passwd").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.clientid != "undefined")
                    {
                        document.getElementById("err_clientid").innerText = data.errors.clientid;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_clientid").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.clientsc != "undefined")
                    {
                        document.getElementById("err_clientsc").innerText = data.errors.clientsc;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_clientsc").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.auth != "undefined")
                    {
                        document.getElementById("err_auth").innerText = data.errors.auth;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_auth").scrollIntoView(true);
                        }
                    }
                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_addwearable").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",8);
                }
                else
                {
                    while(document.getElementById("targetid").value == "") document.getElementById("targetid").value = data;

                    //submit()でフォームの内容を送信
                    document.fix_index.submit();
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_addwearable").style.visibility = 'collapse';
                Ctrl_pop("error","visible",8);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
    $('#form_wearablefix').submit(function(event)
    {
        event.preventDefault();
        document.getElementById("err_devicename").innerText = "";
        document.getElementById("err_userid").innerText = "";
        document.getElementById("err_passwd").innerText = "";
        document.getElementById("err_clientid").innerText = "";
        document.getElementById("err_clientsc").innerText = "";
        document.getElementById("err_auth").innerText = "";

        // フォームデータを取得
        var formData = new FormData($(this)[0]);

        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.devicename != "undefined")
                    {
                        document.getElementById("err_devicename").innerText = data.errors.devicename;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_devicename").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.userid != "undefined")
                    {
                        document.getElementById("err_userid").innerText = data.errors.userid;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_userid").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.passwd != "undefined")
                    {
                        document.getElementById("err_passwd").innerText = data.errors.passwd;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_passwd").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.clientid != "undefined")
                    {
                        document.getElementById("err_clientid").innerText = data.errors.clientid;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_clientid").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.clientsc != "undefined")
                    {
                        document.getElementById("err_clientsc").innerText = data.errors.clientsc;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_clientsc").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.auth != "undefined")
                    {
                        document.getElementById("err_auth").innerText = data.errors.auth;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_auth").scrollIntoView(true);
                        }
                    }
                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_fixwearable").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",9);
                }
                else
                {
                    if(document.getElementById("addmess")) document.getElementById("addmess").innerText = "";
                    document.getElementById("fixmess").innerText = '修正しました。';
                    Ctrl_pop('','collapse','');
                    window.scrollTo(0, 0);
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_fixwearable").style.visibility = 'collapse';
                Ctrl_pop("error","visible",9);
                return ;
            }
        });
    });


    //jquery + ajax フォーム内の値を送信
    $('#form_risksensoradd').submit(function(event)
    {
        event.preventDefault();
        document.getElementById("err_devicename").innerText = "";

        // フォームデータを取得
        var formdata = new FormData(document.getElementById("form_risksensoradd"));

        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                // alert(data);
                // return;
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    if(typeof data.errors.devicename != "undefined") document.getElementById("err_devicename").innerText = data.errors.devicename;
                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_addrisksensor").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",8);
                }
                else
                {
                    while(document.getElementById("targetid").value == "") document.getElementById("targetid").value = data;

                    //submit()でフォームの内容を送信
                    document.fix_index.submit();
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_addrisksensor").style.visibility = 'collapse';
                Ctrl_pop("error","visible",8);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
    $('#form_risksensorfix').submit(function(event)
    {
        event.preventDefault();
        document.getElementById("err_devicename").innerText = "";

        // フォームデータを取得
        var formData = new FormData($(this)[0]);

        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                // alert(data);
                // return;
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    if(typeof data.errors.devicename != "undefined") document.getElementById("err_devicename").innerText = data.errors.devicename;
                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_addrisksensor").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",9);
                }
                else
                {
                    if(document.getElementById("addmess")) document.getElementById("addmess").innerText = "";
                    document.getElementById("fixmess").innerText = '修正しました。';
                    Ctrl_pop('','collapse','');
                    window.scrollTo(0, 0);
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_fixrisksensor").style.visibility = 'collapse';
                Ctrl_pop("error","visible",9);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
    $('#form_helperadd').submit(function(event)
    {

        event.preventDefault();
        // if(datechx() == false) return;
        document.getElementById("err_helpername").innerText = "";
        // document.getElementById("err_wearableno").innerText = "";
        document.getElementById("err_position").innerText = "";
        // document.getElementById("err_backpainno").innerText = "";
        document.getElementById("err_age").innerText = "";
        document.getElementById("err_sex").innerText = "";
        // document.getElementById("err_job").innerText = "";
        // document.getElementById("err_measufrom").innerText = "";
        // document.getElementById("err_measuto").innerText = "";

        // フォームデータを取得
        // var formdata = new FormData(document.getElementById("form_helperadd"));
        var formData = new FormData($(this)[0]);
        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            // data: $form.serialize(),
            processData : false,
            contentType : false,
            data:formData,
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.helpername != "undefined")
                    {
                        document.getElementById("err_helpername").innerText = data.errors.helpername;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_helpername").scrollIntoView(true);
                        }
                    }
                    // if(typeof data.errors.wearableno != "undefined") document.getElementById("err_wearableno").innerText = data.errors.wearableno;
                    if(typeof data.errors.position != "undefined")
                    {
                        document.getElementById("err_position").innerText = data.errors.position;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_position").scrollIntoView(true);
                        }
                    }
                    // if(typeof data.errors.backpainno != "undefined") document.getElementById("err_backpainno").innerText = data.errors.backpainno;
                    if(typeof data.errors.age != "undefined")
                    {
                        document.getElementById("err_age").innerText = data.errors.age;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_age").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.sex != "undefined")
                    {
                        document.getElementById("err_sex").innerText = data.errors.sex;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_sex").scrollIntoView(true);
                        }
                    }
                    // if(typeof data.errors.measufrom != "undefined") document.getElementById("err_measufrom").innerText = data.errors.measufrom;
                    // if(typeof data.errors.measuto != "undefined") document.getElementById("err_measuto").innerText = data.errors.measuto;
                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_addhelper").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",8);
                }
                else
                {
                    while(document.getElementById("targetid").value == "") document.getElementById("targetid").value = data;

                    //submit()でフォームの内容を送信
                    document.fix_index.submit();
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_addhelper").style.visibility = 'collapse';
                Ctrl_pop("error","visible",8);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
    $('#form_helperfix').submit(function(event)
    {
        event.preventDefault();
        // if(datechx() == false) return;
        document.getElementById("err_helpername").innerText = "";
        // document.getElementById("err_wearableno").innerText = "";
        document.getElementById("err_position").innerText = "";
        // document.getElementById("err_backpainno").innerText = "";
        document.getElementById("err_age").innerText = "";
        document.getElementById("err_sex").innerText = "";
        // document.getElementById("err_job").innerText = "";
        // document.getElementById("err_measufrom").innerText = "";
        // document.getElementById("err_measuto").innerText = "";

        // フォームデータを取得
        var formData = new FormData($(this)[0]);
        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            // data: $form.serialize(),
            processData : false,
            contentType : false,
            data:formData,
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.helpername != "undefined")
                    {
                        document.getElementById("err_helpername").innerText = data.errors.helpername;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_helpername").scrollIntoView(true);
                        }
                    }
                    // if(typeof data.errors.wearableno != "undefined") document.getElementById("err_wearableno").innerText = data.errors.wearableno;
                    if(typeof data.errors.position != "undefined")
                    {
                        document.getElementById("err_position").innerText = data.errors.position;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_position").scrollIntoView(true);
                        }
                    }
                    // if(typeof data.errors.backpainno != "undefined") document.getElementById("err_backpainno").innerText = data.errors.backpainno;
                    if(typeof data.errors.age != "undefined")
                    {
                        document.getElementById("err_age").innerText = data.errors.age;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_age").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.sex != "undefined")
                    {
                        document.getElementById("err_sex").innerText = data.errors.sex;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_sex").scrollIntoView(true);
                        }
                    }
                    // if(typeof data.errors.measufrom != "undefined") document.getElementById("err_measufrom").innerText = data.errors.measufrom;
                    // if(typeof data.errors.measuto != "undefined") document.getElementById("err_measuto").innerText = data.errors.measuto;
                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_fixhelper").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",9);
                }
                else
                {
                    if(document.getElementById("addmess")) document.getElementById("addmess").innerText = "";
                    document.getElementById("fixmess").innerText = '修正しました。';
                    Ctrl_pop('','collapse','');
                    window.scrollTo(0, 0);
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_fixhelper").style.visibility = 'collapse';
                Ctrl_pop("error","visible",9);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
    $('#form_facilityadd').submit(function(event)
    {
            // 安全クリア関数
        const clear = (id) => {
            const el = document.getElementById(id);
            if (el) el.innerText = '';
        };

        // よく使う基本IDだけ個別に
        ['facility','pass','address','tel','mail','url'].forEach(k => clear('err_' + k));

        // item系は存在するものだけ消す
        for (let i = 1; i <= 72; i++) {
            clear('err_item' + i);
        }

        // フォームデータを取得
        // var formdata = new FormData(document.getElementById("form_facilityadd"));
        var formData = new FormData($(this)[0]);
        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            // data: $form.serialize(),
            processData : false,
            contentType : false,
            data:formData,
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                // alert(data);
                // return;
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.facility != "undefined")
                    {
                        document.getElementById("err_facility").innerText = data.errors.facility;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("facility").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.pass != "undefined")
                    {
                        document.getElementById("err_pass").innerText = data.errors.pass;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("pass").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.address != "undefined")
                    {
                        document.getElementById("err_address").innerText = data.errors.address;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("address").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.tel != "undefined")
                    {
                        document.getElementById("err_tel").innerText = data.errors.tel;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("tel").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.mail != "undefined")
                    {
                        document.getElementById("err_mail").innerText = data.errors.mail;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("mail").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.url != "undefined")
                    {
                        document.getElementById("err_url").innerText = data.errors.url;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("url").scrollIntoView(true);
                        }
                    }


                    if(typeof data.errors.item1 != "undefined")
                    {
                        document.getElementById("err_item1").innerText = data.errors.item1;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item1").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item2 != "undefined")
                    {
                        document.getElementById("err_item2").innerText = data.errors.item2;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item2").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item3 != "undefined")
                    {
                        document.getElementById("err_item3").innerText = data.errors.item3;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item3").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item4 != "undefined")
                    {
                        document.getElementById("err_item4").innerText = data.errors.item4;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item4").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item5 != "undefined")
                    {
                        document.getElementById("err_item5").innerText = data.errors.item5;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item5").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item6 != "undefined")
                    {
                        document.getElementById("err_item6").innerText = data.errors.item6;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item6").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item7 != "undefined")
                    {
                        document.getElementById("err_item7").innerText = data.errors.item7;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item7").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item8 != "undefined")
                    {
                        document.getElementById("err_item8").innerText = data.errors.item8;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item8").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item9 != "undefined")
                    {
                        document.getElementById("err_item9").innerText = data.errors.item9;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item9").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item10 != "undefined")
                    {
                        document.getElementById("err_item10").innerText = data.errors.item10;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item10").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item11 != "undefined")
                    {
                        document.getElementById("err_item11").innerText = data.errors.item11;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item11").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item12 != "undefined")
                    {
                        document.getElementById("err_item12").innerText = data.errors.item12;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item12").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item13 != "undefined")
                    {
                        document.getElementById("err_item13").innerText = data.errors.item13;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item13").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item14 != "undefined")
                    {
                        document.getElementById("err_item14").innerText = data.errors.item14;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item14").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item15 != "undefined")
                    {
                        document.getElementById("err_item15").innerText = data.errors.item15;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item15").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item16 != "undefined")
                    {
                        document.getElementById("err_item16").innerText = data.errors.item16;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item16").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item17 != "undefined")
                    {
                        document.getElementById("err_item17").innerText = data.errors.item17;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item17").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item18 != "undefined")
                    {
                        document.getElementById("err_item18").innerText = data.errors.item18;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item18").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item19 != "undefined")
                    {
                        document.getElementById("err_item19").innerText = data.errors.item19;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item19").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item20 != "undefined")
                    {
                        document.getElementById("err_item20").innerText = data.errors.item20;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item20").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item21 != "undefined")
                    {
                        document.getElementById("err_item21").innerText = data.errors.item21;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item21").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item22 != "undefined")
                    {
                        document.getElementById("err_item22").innerText = data.errors.item22;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item22").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item23 != "undefined")
                    {
                        document.getElementById("err_item23").innerText = data.errors.item23;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item23").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item24 != "undefined")
                    {
                        document.getElementById("err_item24").innerText = data.errors.item24;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item24").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item25 != "undefined")
                    {
                        document.getElementById("err_item25").innerText = data.errors.item25;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item25").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item26 != "undefined")
                    {
                        document.getElementById("err_item26").innerText = data.errors.item26;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item26").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item27 != "undefined")
                    {
                        document.getElementById("err_item27").innerText = data.errors.item27;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item27").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item28 != "undefined")
                    {
                        document.getElementById("err_item28").innerText = data.errors.item28;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item28").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item29 != "undefined")
                    {
                        document.getElementById("err_item29").innerText = data.errors.item29;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item29").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item30 != "undefined")
                    {
                        document.getElementById("err_item30").innerText = data.errors.item30;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item30").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item31 != "undefined")
                    {
                        document.getElementById("err_item31").innerText = data.errors.item31;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item31").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item32 != "undefined")
                    {
                        document.getElementById("err_item32").innerText = data.errors.item32;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item32").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item33 != "undefined")
                    {
                        document.getElementById("err_item33").innerText = data.errors.item33;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item33").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item34 != "undefined")
                    {
                        document.getElementById("err_item34").innerText = data.errors.item34;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item34").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item35 != "undefined")
                    {
                        document.getElementById("err_item35").innerText = data.errors.item35;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item35").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item36 != "undefined")
                    {
                        document.getElementById("err_item36").innerText = data.errors.item36;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item36").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item37 != "undefined")
                    {
                        document.getElementById("err_item37").innerText = data.errors.item37;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item37").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item38 != "undefined")
                    {
                        document.getElementById("err_item38").innerText = data.errors.item38;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item38").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item39 != "undefined")
                    {
                        document.getElementById("err_item39").innerText = data.errors.item39;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item39").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item40 != "undefined")
                    {
                        document.getElementById("err_item40").innerText = data.errors.item40;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item40").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item41 != "undefined")
                    {
                        document.getElementById("err_item41").innerText = data.errors.item41;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item41").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item42 != "undefined")
                    {
                        document.getElementById("err_item42").innerText = data.errors.item42;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item42").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item43 != "undefined")
                    {
                        document.getElementById("err_item43").innerText = data.errors.item43;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item43").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item44 != "undefined")
                    {
                        document.getElementById("err_item44").innerText = data.errors.item44;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item44").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item45 != "undefined")
                    {
                        document.getElementById("err_item45").innerText = data.errors.item45;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item45").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item46 != "undefined")
                    {
                        document.getElementById("err_item46").innerText = data.errors.item46;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item46").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item47 != "undefined")
                    {
                        document.getElementById("err_item47").innerText = data.errors.item47;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item47").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item48 != "undefined")
                    {
                        document.getElementById("err_item48").innerText = data.errors.item48;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item48").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item49 != "undefined")
                    {
                        document.getElementById("err_item49").innerText = data.errors.item49;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item49").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item50 != "undefined")
                    {
                        document.getElementById("err_item50").innerText = data.errors.item50;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item50").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item51 != "undefined")
                    {
                        document.getElementById("err_item51").innerText = data.errors.item51;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item51").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item52 != "undefined")
                    {
                        document.getElementById("err_item52").innerText = data.errors.item52;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item52").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item53 != "undefined")
                    {
                        document.getElementById("err_item53").innerText = data.errors.item53;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item53").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item54 != "undefined")
                    {
                        document.getElementById("err_item54").innerText = data.errors.item54;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item54").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item55 != "undefined")
                    {
                        document.getElementById("err_item55").innerText = data.errors.item55;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item55").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item56 != "undefined")
                    {
                        document.getElementById("err_item56").innerText = data.errors.item56;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item56").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item57 != "undefined")
                    {
                        document.getElementById("err_item57").innerText = data.errors.item57;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item57").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item58 != "undefined")
                    {
                        document.getElementById("err_item58").innerText = data.errors.item58;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item58").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item59 != "undefined")
                    {
                        document.getElementById("err_item59").innerText = data.errors.item59;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item59").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item69 != "undefined")
                    {
                        document.getElementById("err_item69").innerText = data.errors.item69;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item69").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item70 != "undefined")
                    {
                        document.getElementById("err_item70").innerText = data.errors.item70;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item70").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item71 != "undefined")
                    {
                        document.getElementById("err_item71").innerText = data.errors.item71;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item71").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item72 != "undefined")
                    {
                        document.getElementById("err_item72").innerText = data.errors.item72;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item72").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item60 != "undefined")
                    {
                        document.getElementById("err_item60").innerText = data.errors.item60;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item60").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item61 != "undefined")
                    {
                        document.getElementById("err_item61").innerText = data.errors.item61;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item61").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item62 != "undefined")
                    {
                        document.getElementById("err_item62").innerText = data.errors.item62;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item62").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item63 != "undefined")
                    {
                        document.getElementById("err_item63").innerText = data.errors.item63;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item63").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item64 != "undefined")
                    {
                        document.getElementById("err_item64").innerText = data.errors.item64;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item64").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item65 != "undefined")
                    {
                        document.getElementById("err_item65").innerText = data.errors.item65;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item65").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item66 != "undefined")
                    {
                        document.getElementById("err_item66").innerText = data.errors.item66;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item66").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item67 != "undefined")
                    {
                        document.getElementById("err_item67").innerText = data.errors.item67;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item67").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item68 != "undefined")
                    {
                        document.getElementById("err_item68").innerText = data.errors.item68;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item68").scrollIntoView(true);
                        }
                    }

                    Ctrl_pop('','collapse','');
                }

                else if(data == "error")
                {
                    document.getElementById("btn_addfacility").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",8);
                }
                else
                {
                    while(document.getElementById("targetid").value == "") document.getElementById("targetid").value = data;

                    //submit()でフォームの内容を送信
                    document.fix_index.submit();
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_addfacility").style.visibility = 'collapse';
                Ctrl_pop("error","visible",8);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
    $('#form_facilityfix').submit(function(event)
    {
        event.preventDefault();

        const clear = (id) => {
            const el = document.getElementById(id);
            if (el) el.innerText = '';
        };

        // 個別のエラー
        ['err_facility','err_pass','err_address','err_tel','err_mail','err_url']
            .forEach(clear);

        // item系は存在するものだけ
        for (let i = 1; i <= 72; i++) clear(`err_item${i}`);

        // フォームデータを取得
        var formData = new FormData($(this)[0]);
        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            // data: $form.serialize(),
            processData : false,
            contentType : false,
            data:formData,
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                // alert(data);
                // return;
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.facility != "undefined")
                    {
                        document.getElementById("err_facility").innerText = data.errors.facility;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("facility").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.pass != "undefined")
                    {
                        document.getElementById("err_pass").innerText = data.errors.pass;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("pass").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.address != "undefined")
                    {
                        document.getElementById("err_address").innerText = data.errors.address;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("address").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.tel != "undefined")
                    {
                        document.getElementById("err_tel").innerText = data.errors.tel;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("tel").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.mail != "undefined")
                    {
                        document.getElementById("err_mail").innerText = data.errors.mail;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("mail").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.url != "undefined")
                    {
                        document.getElementById("err_url").innerText = data.errors.url;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("url").scrollIntoView(true);
                        }
                    }


                    if(typeof data.errors.item1 != "undefined")
                    {
                        document.getElementById("err_item1").innerText = data.errors.item1;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item1").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item2 != "undefined")
                    {
                        document.getElementById("err_item2").innerText = data.errors.item2;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item2").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item3 != "undefined")
                    {
                        document.getElementById("err_item3").innerText = data.errors.item3;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item3").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item4 != "undefined")
                    {
                        document.getElementById("err_item4").innerText = data.errors.item4;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item4").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item5 != "undefined")
                    {
                        document.getElementById("err_item5").innerText = data.errors.item5;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item5").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item6 != "undefined")
                    {
                        document.getElementById("err_item6").innerText = data.errors.item6;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item6").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item7 != "undefined")
                    {
                        document.getElementById("err_item7").innerText = data.errors.item7;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item7").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item8 != "undefined")
                    {
                        document.getElementById("err_item8").innerText = data.errors.item8;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item8").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item9 != "undefined")
                    {
                        document.getElementById("err_item9").innerText = data.errors.item9;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item9").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item10 != "undefined")
                    {
                        document.getElementById("err_item10").innerText = data.errors.item10;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item10").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item11 != "undefined")
                    {
                        document.getElementById("err_item11").innerText = data.errors.item11;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item11").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item12 != "undefined")
                    {
                        document.getElementById("err_item12").innerText = data.errors.item12;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item12").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item13 != "undefined")
                    {
                        document.getElementById("err_item13").innerText = data.errors.item13;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item13").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item14 != "undefined")
                    {
                        document.getElementById("err_item14").innerText = data.errors.item14;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item14").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item15 != "undefined")
                    {
                        document.getElementById("err_item15").innerText = data.errors.item15;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item15").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item16 != "undefined")
                    {
                        document.getElementById("err_item16").innerText = data.errors.item16;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item16").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item17 != "undefined")
                    {
                        document.getElementById("err_item17").innerText = data.errors.item17;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item17").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item18 != "undefined")
                    {
                        document.getElementById("err_item18").innerText = data.errors.item18;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item18").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item19 != "undefined")
                    {
                        document.getElementById("err_item19").innerText = data.errors.item19;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item19").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item20 != "undefined")
                    {
                        document.getElementById("err_item20").innerText = data.errors.item20;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item20").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item21 != "undefined")
                    {
                        document.getElementById("err_item21").innerText = data.errors.item21;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item21").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item22 != "undefined")
                    {
                        document.getElementById("err_item22").innerText = data.errors.item22;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item22").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item23 != "undefined")
                    {
                        document.getElementById("err_item23").innerText = data.errors.item23;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item23").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item24 != "undefined")
                    {
                        document.getElementById("err_item24").innerText = data.errors.item24;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item24").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item25 != "undefined")
                    {
                        document.getElementById("err_item25").innerText = data.errors.item25;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item25").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item26 != "undefined")
                    {
                        document.getElementById("err_item26").innerText = data.errors.item26;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item26").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item27 != "undefined")
                    {
                        document.getElementById("err_item27").innerText = data.errors.item27;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item27").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item28 != "undefined")
                    {
                        document.getElementById("err_item28").innerText = data.errors.item28;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item28").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item29 != "undefined")
                    {
                        document.getElementById("err_item29").innerText = data.errors.item29;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item29").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item30 != "undefined")
                    {
                        document.getElementById("err_item30").innerText = data.errors.item30;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item30").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item31 != "undefined")
                    {
                        document.getElementById("err_item31").innerText = data.errors.item31;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item31").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item32 != "undefined")
                    {
                        document.getElementById("err_item32").innerText = data.errors.item32;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item32").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item33 != "undefined")
                    {
                        document.getElementById("err_item33").innerText = data.errors.item33;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item33").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item34 != "undefined")
                    {
                        document.getElementById("err_item34").innerText = data.errors.item34;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item34").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item35 != "undefined")
                    {
                        document.getElementById("err_item35").innerText = data.errors.item35;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item35").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item36 != "undefined")
                    {
                        document.getElementById("err_item36").innerText = data.errors.item36;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item36").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item37 != "undefined")
                    {
                        document.getElementById("err_item37").innerText = data.errors.item37;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item37").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item38 != "undefined")
                    {
                        document.getElementById("err_item38").innerText = data.errors.item38;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item38").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item39 != "undefined")
                    {
                        document.getElementById("err_item39").innerText = data.errors.item39;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item39").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item40 != "undefined")
                    {
                        document.getElementById("err_item40").innerText = data.errors.item40;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item40").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item41 != "undefined")
                    {
                        document.getElementById("err_item41").innerText = data.errors.item41;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item41").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item42 != "undefined")
                    {
                        document.getElementById("err_item42").innerText = data.errors.item42;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item42").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item43 != "undefined")
                    {
                        document.getElementById("err_item43").innerText = data.errors.item43;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item43").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item44 != "undefined")
                    {
                        document.getElementById("err_item44").innerText = data.errors.item44;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item44").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item45 != "undefined")
                    {
                        document.getElementById("err_item45").innerText = data.errors.item45;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item45").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item46 != "undefined")
                    {
                        document.getElementById("err_item46").innerText = data.errors.item46;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item46").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item47 != "undefined")
                    {
                        document.getElementById("err_item47").innerText = data.errors.item47;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item47").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item48 != "undefined")
                    {
                        document.getElementById("err_item48").innerText = data.errors.item48;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item48").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item49 != "undefined")
                    {
                        document.getElementById("err_item49").innerText = data.errors.item49;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item49").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item50 != "undefined")
                    {
                        document.getElementById("err_item50").innerText = data.errors.item50;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item50").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item51 != "undefined")
                    {
                        document.getElementById("err_item51").innerText = data.errors.item51;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item51").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item52 != "undefined")
                    {
                        document.getElementById("err_item52").innerText = data.errors.item52;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item52").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item53 != "undefined")
                    {
                        document.getElementById("err_item53").innerText = data.errors.item53;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item53").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item54 != "undefined")
                    {
                        document.getElementById("err_item54").innerText = data.errors.item54;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item54").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item55 != "undefined")
                    {
                        document.getElementById("err_item55").innerText = data.errors.item55;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item55").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item56 != "undefined")
                    {
                        document.getElementById("err_item56").innerText = data.errors.item56;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item56").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item57 != "undefined")
                    {
                        document.getElementById("err_item57").innerText = data.errors.item57;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item57").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item58 != "undefined")
                    {
                        document.getElementById("err_item58").innerText = data.errors.item58;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item58").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item59 != "undefined")
                    {
                        document.getElementById("err_item59").innerText = data.errors.item59;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item59").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item69 != "undefined")
                    {
                        document.getElementById("err_item69").innerText = data.errors.item69;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item69").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item70 != "undefined")
                    {
                        document.getElementById("err_item70").innerText = data.errors.item70;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item70").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item71 != "undefined")
                    {
                        document.getElementById("err_item71").innerText = data.errors.item71;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item71").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item72 != "undefined")
                    {
                        document.getElementById("err_item72").innerText = data.errors.item72;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item72").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item60 != "undefined")
                    {
                        document.getElementById("err_item60").innerText = data.errors.item60;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item60").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item61 != "undefined")
                    {
                        document.getElementById("err_item61").innerText = data.errors.item61;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item61").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item62 != "undefined")
                    {
                        document.getElementById("err_item62").innerText = data.errors.item62;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item62").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item63 != "undefined")
                    {
                        document.getElementById("err_item63").innerText = data.errors.item63;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item63").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item64 != "undefined")
                    {
                        document.getElementById("err_item64").innerText = data.errors.item64;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item64").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item65 != "undefined")
                    {
                        document.getElementById("err_item65").innerText = data.errors.item65;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item65").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item66 != "undefined")
                    {
                        document.getElementById("err_item66").innerText = data.errors.item66;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item66").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item67 != "undefined")
                    {
                        document.getElementById("err_item67").innerText = data.errors.item67;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item67").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item68 != "undefined")
                    {
                        document.getElementById("err_item68").innerText = data.errors.item68;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item68").scrollIntoView(true);
                        }
                    }

                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_fixfacility").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",9);
                }
                else
                {
                    if(document.getElementById("addmess")) document.getElementById("addmess").innerText = "";
                    document.getElementById("fixmess").innerText = '修正しました。';
                    Ctrl_pop('','collapse','');
                    window.scrollTo(0, 0);
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_fixfacility").style.visibility = 'collapse';
                Ctrl_pop("error","visible",9);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
    $('#form_facility_inputadd').submit(function(event)
    {
        event.preventDefault();
        document.getElementById("err_facility").innerText = "";
        document.getElementById("err_pass").innerText = "";
        document.getElementById("err_address").innerText = "";
        document.getElementById("err_tel").innerText = "";
        document.getElementById("err_mail").innerText = "";
        // document.getElementById("err_url").innerText = "";

        for(var i=1;i<73;i++)
        {
            var errid = "err_item"+i;
            document.getElementById(errid).innerText = "";
        }
        // フォームデータを取得
        // var formdata = new FormData(document.getElementById("form_facility_inputadd"));
        var formData = new FormData($(this)[0]);
        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            // data: $form.serialize(),
            processData : false,
            contentType : false,
            data:formData,
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                // alert(data);
                // return;
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.facility != "undefined")
                    {
                        document.getElementById("err_facility").innerText = data.errors.facility;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("facility").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.pass != "undefined")
                    {
                        document.getElementById("err_pass").innerText = data.errors.pass;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("pass").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.address != "undefined")
                    {
                        document.getElementById("err_address").innerText = data.errors.address;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("address").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.tel != "undefined")
                    {
                        document.getElementById("err_tel").innerText = data.errors.tel;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("tel").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.mail != "undefined")
                    {
                        document.getElementById("err_mail").innerText = data.errors.mail;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("mail").scrollIntoView(true);
                        }
                    }

                    //2021.05.18 追加
                    // if(typeof data.errors.url != "undefined")
                    // {
                    //     document.getElementById("err_url").innerText = data.errors.url;
                    //     if(focusflag == 0)
                    //     {
                    //         focusflag = 1;
                    //         document.getElementById("url").scrollIntoView(true);
                    //     }
                    // }


                    if(typeof data.errors.item1 != "undefined")
                    {
                        document.getElementById("err_item1").innerText = data.errors.item1;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item1").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item2 != "undefined")
                    {
                        document.getElementById("err_item2").innerText = data.errors.item2;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item2").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item3 != "undefined")
                    {
                        document.getElementById("err_item3").innerText = data.errors.item3;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item3").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item4 != "undefined")
                    {
                        document.getElementById("err_item4").innerText = data.errors.item4;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item4").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item5 != "undefined")
                    {
                        document.getElementById("err_item5").innerText = data.errors.item5;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item5").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item6 != "undefined")
                    {
                        document.getElementById("err_item6").innerText = data.errors.item6;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item6").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item7 != "undefined")
                    {
                        document.getElementById("err_item7").innerText = data.errors.item7;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item7").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item8 != "undefined")
                    {
                        document.getElementById("err_item8").innerText = data.errors.item8;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item8").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item9 != "undefined")
                    {
                        document.getElementById("err_item9").innerText = data.errors.item9;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item9").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item10 != "undefined")
                    {
                        document.getElementById("err_item10").innerText = data.errors.item10;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item10").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item11 != "undefined")
                    {
                        document.getElementById("err_item11").innerText = data.errors.item11;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item11").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item12 != "undefined")
                    {
                        document.getElementById("err_item12").innerText = data.errors.item12;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item12").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item13 != "undefined")
                    {
                        document.getElementById("err_item13").innerText = data.errors.item13;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item13").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item14 != "undefined")
                    {
                        document.getElementById("err_item14").innerText = data.errors.item14;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item14").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item15 != "undefined")
                    {
                        document.getElementById("err_item15").innerText = data.errors.item15;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item15").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item16 != "undefined")
                    {
                        document.getElementById("err_item16").innerText = data.errors.item16;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item16").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item17 != "undefined")
                    {
                        document.getElementById("err_item17").innerText = data.errors.item17;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item17").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item18 != "undefined")
                    {
                        document.getElementById("err_item18").innerText = data.errors.item18;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item18").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item19 != "undefined")
                    {
                        document.getElementById("err_item19").innerText = data.errors.item19;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item19").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item20 != "undefined")
                    {
                        document.getElementById("err_item20").innerText = data.errors.item20;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item20").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item21 != "undefined")
                    {
                        document.getElementById("err_item21").innerText = data.errors.item21;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item21").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item22 != "undefined")
                    {
                        document.getElementById("err_item22").innerText = data.errors.item22;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item22").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item23 != "undefined")
                    {
                        document.getElementById("err_item23").innerText = data.errors.item23;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item23").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item24 != "undefined")
                    {
                        document.getElementById("err_item24").innerText = data.errors.item24;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item24").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item25 != "undefined")
                    {
                        document.getElementById("err_item25").innerText = data.errors.item25;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item25").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item26 != "undefined")
                    {
                        document.getElementById("err_item26").innerText = data.errors.item26;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item26").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item27 != "undefined")
                    {
                        document.getElementById("err_item27").innerText = data.errors.item27;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item27").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item28 != "undefined")
                    {
                        document.getElementById("err_item28").innerText = data.errors.item28;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item28").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item29 != "undefined")
                    {
                        document.getElementById("err_item29").innerText = data.errors.item29;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item29").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item30 != "undefined")
                    {
                        document.getElementById("err_item30").innerText = data.errors.item30;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item30").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item31 != "undefined")
                    {
                        document.getElementById("err_item31").innerText = data.errors.item31;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item31").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item32 != "undefined")
                    {
                        document.getElementById("err_item32").innerText = data.errors.item32;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item32").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item33 != "undefined")
                    {
                        document.getElementById("err_item33").innerText = data.errors.item33;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item33").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item34 != "undefined")
                    {
                        document.getElementById("err_item34").innerText = data.errors.item34;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item34").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item35 != "undefined")
                    {
                        document.getElementById("err_item35").innerText = data.errors.item35;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item35").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item36 != "undefined")
                    {
                        document.getElementById("err_item36").innerText = data.errors.item36;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item36").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item37 != "undefined")
                    {
                        document.getElementById("err_item37").innerText = data.errors.item37;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item37").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item38 != "undefined")
                    {
                        document.getElementById("err_item38").innerText = data.errors.item38;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item38").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item39 != "undefined")
                    {
                        document.getElementById("err_item39").innerText = data.errors.item39;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item39").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item40 != "undefined")
                    {
                        document.getElementById("err_item40").innerText = data.errors.item40;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item40").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item41 != "undefined")
                    {
                        document.getElementById("err_item41").innerText = data.errors.item41;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item41").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item42 != "undefined")
                    {
                        document.getElementById("err_item42").innerText = data.errors.item42;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item42").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item43 != "undefined")
                    {
                        document.getElementById("err_item43").innerText = data.errors.item43;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item43").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item44 != "undefined")
                    {
                        document.getElementById("err_item44").innerText = data.errors.item44;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item44").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item45 != "undefined")
                    {
                        document.getElementById("err_item45").innerText = data.errors.item45;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item45").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item46 != "undefined")
                    {
                        document.getElementById("err_item46").innerText = data.errors.item46;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item46").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item47 != "undefined")
                    {
                        document.getElementById("err_item47").innerText = data.errors.item47;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item47").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item48 != "undefined")
                    {
                        document.getElementById("err_item48").innerText = data.errors.item48;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item48").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item49 != "undefined")
                    {
                        document.getElementById("err_item49").innerText = data.errors.item49;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item49").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item50 != "undefined")
                    {
                        document.getElementById("err_item50").innerText = data.errors.item50;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item50").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item51 != "undefined")
                    {
                        document.getElementById("err_item51").innerText = data.errors.item51;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item51").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item52 != "undefined")
                    {
                        document.getElementById("err_item52").innerText = data.errors.item52;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item52").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item53 != "undefined")
                    {
                        document.getElementById("err_item53").innerText = data.errors.item53;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item53").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item54 != "undefined")
                    {
                        document.getElementById("err_item54").innerText = data.errors.item54;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item54").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item55 != "undefined")
                    {
                        document.getElementById("err_item55").innerText = data.errors.item55;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item55").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item56 != "undefined")
                    {
                        document.getElementById("err_item56").innerText = data.errors.item56;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item56").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item57 != "undefined")
                    {
                        document.getElementById("err_item57").innerText = data.errors.item57;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item57").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item58 != "undefined")
                    {
                        document.getElementById("err_item58").innerText = data.errors.item58;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item58").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item59 != "undefined")
                    {
                        document.getElementById("err_item59").innerText = data.errors.item59;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item59").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item69 != "undefined")
                    {
                        document.getElementById("err_item69").innerText = data.errors.item69;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item69").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item70 != "undefined")
                    {
                        document.getElementById("err_item70").innerText = data.errors.item70;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item70").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item71 != "undefined")
                    {
                        document.getElementById("err_item71").innerText = data.errors.item71;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item71").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item72 != "undefined")
                    {
                        document.getElementById("err_item72").innerText = data.errors.item72;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item72").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item60 != "undefined")
                    {
                        document.getElementById("err_item60").innerText = data.errors.item60;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item60").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item61 != "undefined")
                    {
                        document.getElementById("err_item61").innerText = data.errors.item61;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item61").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item62 != "undefined")
                    {
                        document.getElementById("err_item62").innerText = data.errors.item62;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item62").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item63 != "undefined")
                    {
                        document.getElementById("err_item63").innerText = data.errors.item63;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item63").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item64 != "undefined")
                    {
                        document.getElementById("err_item64").innerText = data.errors.item64;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item64").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item65 != "undefined")
                    {
                        document.getElementById("err_item65").innerText = data.errors.item65;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item65").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item66 != "undefined")
                    {
                        document.getElementById("err_item66").innerText = data.errors.item66;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item66").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item67 != "undefined")
                    {
                        document.getElementById("err_item67").innerText = data.errors.item67;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item67").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item68 != "undefined")
                    {
                        document.getElementById("err_item68").innerText = data.errors.item68;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("item68").scrollIntoView(true);
                        }
                    }

                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_addfacility_input").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",8);
                }
                else
                {
                    while(document.getElementById("targetid").value == "") document.getElementById("targetid").value = data;

                    //submit()でフォームの内容を送信
                    document.fix_index.submit();
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_addfacility_input").style.visibility = 'collapse';
                Ctrl_pop("error","visible",8);
                return ;
            }
        });
    });

    //jquery + ajax フォーム内の値を送信
  $('#form_facility_inputfix').submit(function(event)
    {
        event.preventDefault();
        document.getElementById("err_facility").innerText = "";
        document.getElementById("err_pass").innerText = "";
        document.getElementById("err_address").innerText = "";
        document.getElementById("err_tel").innerText = "";
        document.getElementById("err_mail").innerText = "";
        // document.getElementById("err_url").innerText = "";

        for(var i=1;i<73;i++)
        {
            var errid = "err_item"+i;
            document.getElementById(errid).innerText = "";
        }
        // フォームデータを取得
        var formData = new FormData($(this)[0]);
        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            // data: $form.serialize(),
            processData : false,
            contentType : false,
            data:formData,
            dataType: 'json', //必須。json形式で返すように設定
            success: function(data)
            {
                // alert(data);
                // return;
                //戻り値はバリデーションエラーかインサートID
                if(typeof data.errors != "undefined")
                {
                    var focusflag = 0;
                    if(typeof data.errors.facility != "undefined")
                    {
                        document.getElementById("err_facility").innerText = data.errors.facility;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_facility").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.pass != "undefined")
                    {
                        document.getElementById("err_pass").innerText = data.errors.pass;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_pass").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.address != "undefined")
                    {
                        document.getElementById("err_address").innerText = data.errors.address;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_address").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.tel != "undefined")
                    {
                        document.getElementById("err_tel").innerText = data.errors.tel;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_tel").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.mail != "undefined")
                    {
                        document.getElementById("err_mail").innerText = data.errors.mail;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_mail").scrollIntoView(true);
                        }
                    }
                    // if(typeof data.errors.url != "undefined")
                    // {
                    //     document.getElementById("err_url").innerText = data.errors.url;
                    //     if(focusflag == 0)
                    //     {
                    //         focusflag = 1;
                    //         document.getElementById("url").scrollIntoView(true);
                    //     }
                    // }



                    if(typeof data.errors.item1 != "undefined")
                    {
                        document.getElementById("err_item1").innerText = data.errors.item1;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item1").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item2 != "undefined")
                    {
                        document.getElementById("err_item2").innerText = data.errors.item2;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item2").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item3 != "undefined")
                    {
                        document.getElementById("err_item3").innerText = data.errors.item3;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item3").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item4 != "undefined")
                    {
                        document.getElementById("err_item4").innerText = data.errors.item4;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item4").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item5 != "undefined")
                    {
                        document.getElementById("err_item5").innerText = data.errors.item5;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item5").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item6 != "undefined")
                    {
                        document.getElementById("err_item6").innerText = data.errors.item6;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item6").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item7 != "undefined")
                    {
                        document.getElementById("err_item7").innerText = data.errors.item7;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item7").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item8 != "undefined")
                    {
                        document.getElementById("err_item8").innerText = data.errors.item8;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item8").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item9 != "undefined")
                    {
                        document.getElementById("err_item9").innerText = data.errors.item9;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item9").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item10 != "undefined")
                    {
                        document.getElementById("err_item10").innerText = data.errors.item10;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item10").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item11 != "undefined")
                    {
                        document.getElementById("err_item11").innerText = data.errors.item11;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item11").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item12 != "undefined")
                    {
                        document.getElementById("err_item12").innerText = data.errors.item12;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item12").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item13 != "undefined")
                    {
                        document.getElementById("err_item13").innerText = data.errors.item13;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item13").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item14 != "undefined")
                    {
                        document.getElementById("err_item14").innerText = data.errors.item14;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item14").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item15 != "undefined")
                    {
                        document.getElementById("err_item15").innerText = data.errors.item15;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item15").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item16 != "undefined")
                    {
                        document.getElementById("err_item16").innerText = data.errors.item16;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item16").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item17 != "undefined")
                    {
                        document.getElementById("err_item17").innerText = data.errors.item17;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item17").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item18 != "undefined")
                    {
                        document.getElementById("err_item18").innerText = data.errors.item18;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item18").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item19 != "undefined")
                    {
                        document.getElementById("err_item19").innerText = data.errors.item19;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item19").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item20 != "undefined")
                    {
                        document.getElementById("err_item20").innerText = data.errors.item20;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item20").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item21 != "undefined")
                    {
                        document.getElementById("err_item21").innerText = data.errors.item21;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item21").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item22 != "undefined")
                    {
                        document.getElementById("err_item22").innerText = data.errors.item22;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item22").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item23 != "undefined")
                    {
                        document.getElementById("err_item23").innerText = data.errors.item23;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item23").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item24 != "undefined")
                    {
                        document.getElementById("err_item24").innerText = data.errors.item24;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item24").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item25 != "undefined")
                    {
                        document.getElementById("err_item25").innerText = data.errors.item25;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item25").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item26 != "undefined")
                    {
                        document.getElementById("err_item26").innerText = data.errors.item26;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item26").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item27 != "undefined")
                    {
                        document.getElementById("err_item27").innerText = data.errors.item27;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item27").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item28 != "undefined")
                    {
                        document.getElementById("err_item28").innerText = data.errors.item28;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item28").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item29 != "undefined")
                    {
                        document.getElementById("err_item29").innerText = data.errors.item29;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item29").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item30 != "undefined")
                    {
                        document.getElementById("err_item30").innerText = data.errors.item30;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item30").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item31 != "undefined")
                    {
                        document.getElementById("err_item31").innerText = data.errors.item31;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item31").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item32 != "undefined")
                    {
                        document.getElementById("err_item32").innerText = data.errors.item32;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item32").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item33 != "undefined")
                    {
                        document.getElementById("err_item33").innerText = data.errors.item33;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item33").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item34 != "undefined")
                    {
                        document.getElementById("err_item34").innerText = data.errors.item34;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item34").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item35 != "undefined")
                    {
                        document.getElementById("err_item35").innerText = data.errors.item35;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item35").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item36 != "undefined")
                    {
                        document.getElementById("err_item36").innerText = data.errors.item36;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item36").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item37 != "undefined")
                    {
                        document.getElementById("err_item37").innerText = data.errors.item37;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item37").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item38 != "undefined")
                    {
                        document.getElementById("err_item38").innerText = data.errors.item38;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item38").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item39 != "undefined")
                    {
                        document.getElementById("err_item39").innerText = data.errors.item39;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item39").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item40 != "undefined")
                    {
                        document.getElementById("err_item40").innerText = data.errors.item40;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item40").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item41 != "undefined")
                    {
                        document.getElementById("err_item41").innerText = data.errors.item41;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item41").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item42 != "undefined")
                    {
                        document.getElementById("err_item42").innerText = data.errors.item42;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item42").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item43 != "undefined")
                    {
                        document.getElementById("err_item43").innerText = data.errors.item43;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item43").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item44 != "undefined")
                    {
                        document.getElementById("err_item44").innerText = data.errors.item44;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item44").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item45 != "undefined")
                    {
                        document.getElementById("err_item45").innerText = data.errors.item45;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item45").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item46 != "undefined")
                    {
                        document.getElementById("err_item46").innerText = data.errors.item46;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item46").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item47 != "undefined")
                    {
                        document.getElementById("err_item47").innerText = data.errors.item47;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item47").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item48 != "undefined")
                    {
                        document.getElementById("err_item48").innerText = data.errors.item48;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item48").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item49 != "undefined")
                    {
                        document.getElementById("err_item49").innerText = data.errors.item49;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item49").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item50 != "undefined")
                    {
                        document.getElementById("err_item50").innerText = data.errors.item50;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item50").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item51 != "undefined")
                    {
                        document.getElementById("err_item51").innerText = data.errors.item51;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item51").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item52 != "undefined")
                    {
                        document.getElementById("err_item52").innerText = data.errors.item52;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item52").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item53 != "undefined")
                    {
                        document.getElementById("err_item53").innerText = data.errors.item53;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item53").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item54 != "undefined")
                    {
                        document.getElementById("err_item54").innerText = data.errors.item54;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item54").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item55 != "undefined")
                    {
                        document.getElementById("err_item55").innerText = data.errors.item55;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item55").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item56 != "undefined")
                    {
                        document.getElementById("err_item56").innerText = data.errors.item56;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item56").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item57 != "undefined")
                    {
                        document.getElementById("err_item57").innerText = data.errors.item57;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item57").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item58 != "undefined")
                    {
                        document.getElementById("err_item58").innerText = data.errors.item58;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item58").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item59 != "undefined")
                    {
                        document.getElementById("err_item59").innerText = data.errors.item59;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item59").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item60 != "undefined")
                    {
                        document.getElementById("err_item60").innerText = data.errors.item60;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item60").scrollIntoView(true);
                        }
                    }

                    if(typeof data.errors.item61 != "undefined")
                    {
                        document.getElementById("err_item61").innerText = data.errors.item61;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item61").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item62 != "undefined")
                    {
                        document.getElementById("err_item62").innerText = data.errors.item62;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item62").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item63 != "undefined")
                    {
                        document.getElementById("err_item63").innerText = data.errors.item63;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item63").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item64 != "undefined")
                    {
                        document.getElementById("err_item64").innerText = data.errors.item64;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item64").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item65 != "undefined")
                    {
                        document.getElementById("err_item65").innerText = data.errors.item65;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item65").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item66 != "undefined")
                    {
                        document.getElementById("err_item66").innerText = data.errors.item66;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item66").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item67 != "undefined")
                    {
                        document.getElementById("err_item67").innerText = data.errors.item67;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item67").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item68 != "undefined")
                    {
                        document.getElementById("err_item68").innerText = data.errors.item68;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item68").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item69 != "undefined")
                    {
                        document.getElementById("err_item69").innerText = data.errors.item69;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item69").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item70 != "undefined")
                    {
                        document.getElementById("err_item70").innerText = data.errors.item70;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item70").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item71 != "undefined")
                    {
                        document.getElementById("err_item71").innerText = data.errors.item71;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item71").scrollIntoView(true);
                        }
                    }
                    if(typeof data.errors.item72 != "undefined")
                    {
                        document.getElementById("err_item72").innerText = data.errors.item72;
                        if(focusflag == 0)
                        {
                            focusflag = 1;
                            document.getElementById("err_item72").scrollIntoView(true);
                        }
                    }

                    Ctrl_pop('','collapse','');
                }
                else if(data == "error")
                {
                    document.getElementById("btn_fixfacility_input").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",9);
                }
                else
                {
                    if(document.getElementById("addmess")) document.getElementById("addmess").innerText = "";
                    document.getElementById("fixmess").innerText = '修正しました。';
                    Ctrl_pop('','collapse','');
                    window.scrollTo(0, 0);
                }

            },
            error: function(error) {
                // Do something with the error
                document.getElementById("btn_fixfacility_input").style.visibility = 'collapse';
                Ctrl_pop("error","visible",9);
                return ;
            }
        });
    });



    // テーブルイベント
    // ここから ------------------------------>
    $(function ()
    {
        if(@json($title) == "施設一覧")
        {
            var tbname = "#table4 tr";
            tr_default("#table4");
        }
        else if(@json($title) == "作業者一覧")
        {
            var tbname = "#table3 tr";
            tr_default("#table3");
        }
        else if(@json($title) == "心拍センサー一覧")
        {
            var tbname = "#table2 tr";
            tr_default("#table2");
        }
        else if(@json($title) == "リスクデバイス一覧")
        {
            var tbname = "#table2 tr";
            tr_default("#table2");
        }
        else
        {
            var tbname = "#table1 tr";
            tr_default("#table1");
        }


        $(tbname).click(function()
        {
            if(@json($title) == "施設一覧") tr_default("#table4");
            else if(@json($title) == "作業者一覧") tr_default("#table3");
            else if(@json($title) == "心拍センサー一覧" || @json($title) == "リスクデバイス一覧") tr_default("#table2");
            else tr_default("#table1");
            tr_click($(this));
        });
   });


    function tr_default(tblID)
   {
        var vTR = tblID + " tr";
        $(vTR).css("background-color","#ffffff");
        $(vTR).mouseover(function(){
        $(this).css("background-color","#CCFFCC") .css("cursor","pointer")
        });
        $(vTR).mouseout(function(){
        $(this).css("background-color","#ffffff") .css("cursor","normal")
        });
   }

    function tr_click(trID)
   {
        getCELL();
        trID.css("background-color","#e49e61");
        trID.mouseover(function(){
        $(this).css("background-color","#CCFFCC") .css("cursor","pointer")
        });
        trID.mouseout(function(){
        $(this).css("background-color","#e49e61") .css("cursor","normal")

        });
   }
    // ここまで------------------------------>

    // クリック時のセルの情報取得
    function getCELL()
    {
        if(@json($title) == "施設一覧") var myTbl = document.getElementById('table4');
        else if(@json($title) == "作業者一覧") var myTbl = document.getElementById('table3');
        else if(@json($title) == "心拍センサー一覧" || @json($title) == "リスクデバイス一覧") var myTbl = document.getElementById('table2');
        else var myTbl = document.getElementById('table1');
        // trをループ。rowsコレクションで,行位置取得。
　      for (var i=0; i<myTbl.rows.length; i++)
        {
            // tr内のtdをループ。cellsコレクションで行内セル位置取得。
            for (var j=0; j<myTbl.rows[i].cells.length; j++)
            {
                var Cells=myTbl.rows[i].cells[j]; //i番行のj番列のセル "td"
                // var Cells = myTbl.rows[i].cells[0];
                // onclickで 'Mclk'を実行。thisはクリックしたセル"td"のオブジェクトを返す。
    　　        Cells.onclick = function()
                {
                    Mclk(this);
                }
　          }
        }
    }

    // クリック時のターゲットの書き込み
    function Mclk(Cell)
    {
        var rowINX = '行位置：'+Cell.parentNode.rowIndex;//Cellの親ノード'tr'の行位置
        var targetNo = Cell.parentNode.rowIndex;;
        var cellINX = 'セル位置：'+Cell.cellIndex;
        var cellVal = 'セルの内容：'+ Cell.innerHTML;
        // document.getElementById("login_no").style.backgroundColor = "#D2DAEF";
        if(@json($title) == "施設一覧") targetID = document.getElementById('table4').rows[targetNo].cells[0].innerHTML;
        else if(@json($title) == "作業者一覧") targetID = document.getElementById('table3').rows[targetNo].cells[0].innerHTML;
        else if(@json($title) == "心拍センサー一覧" || @json($title) == "リスクデバイス一覧") targetID = document.getElementById('table2').rows[targetNo].cells[0].innerHTML;
        else targetID = document.getElementById('table1').rows[targetNo].cells[0].innerHTML;

    }

    // try ～ catch 例外処理、エラー処理
    // イベントリスナーaddEventListener,attachEventメソッド
    try
    {
        // ロード時
        window.addEventListener("load",getCELL,false);
    }
    catch(e)
    {
        window.attachEvent("onload",getCELL);
    }


    //jquery + ajax フォーム内の値を送信
    $('#form_datadisp1').submit(function(event)
    {
        event.preventDefault();
        // document.getElementById("txt_wearable").style.visibility = 'hidden';
        var total = @json($data);
        // フォームデータを取得
        var formdata = new FormData(document.getElementById("form_datadisp1"));
        if(formdata.get('ymd') == "" || formdata.get('ymd') == null)
        {
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById("btn_yes").style.visibility = 'collapse';
            document.getElementById("btn_datadisp").style.visibility = 'collapse';
            Ctrl_pop("error","visible",23);
            return ;
        }
        if(formdata.get('hms') == "" || formdata.get('hms') == null)
        {
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById("btn_yes").style.visibility = 'collapse';
            document.getElementById("btn_datadisp").style.visibility = 'collapse';
            Ctrl_pop("error","visible",24);
            return ;
        }

        var $form = $(this);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            // data: dataString,
            success: function(data) {
                // Do something with the response
                var obj = JSON.stringify(data);
                var obj2 = JSON.parse(obj);
                console.log($form.attr('action'));
                if(obj2 == "" || obj2 == null)
                {
                    console.log("obj");
                    console.log(obj);
                    console.log("obj2");
                    console.log(obj2);
                    document.getElementById('pop_alert_back').style.visibility = 'collapse';
                    document.getElementById("btn_yes").style.visibility = 'collapse';
                    document.getElementById("btn_datadisp").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",31);
                    return ;
                }

                Object.keys(total).forEach(function (key)
                {
                    if(total[key]['ymd'] == formdata.get('ymd'))
                    {
                        if(total[key]['hms'].trim() == formdata.get('hms').trim())
                        {
                            document.getElementById("total_risk").innerHTML = total[key]['risk'];

                            document.getElementById("total_avetm_for").innerHTML = total[key]['fxa'].substr(0,2)+ ":" +
                                                                                total[key]['fxa'].substr(2,2);

                            document.getElementById("total_alltm_for").innerHTML = total[key]['fxt'].substr(0,2)+ ":" +
                                                                                total[key]['fxt'].substr(2,2)+":"+total[key]['fxt'].substr(4,2);

                            document.getElementById("total_avetm_tw").innerHTML = total[key]['txa'].substr(0,2)+ ":" +
                                                                                total[key]['txa'].substr(2,2);

                            document.getElementById("total_alltm_tw").innerHTML = total[key]['txt'].substr(0,2)+ ":" +
                                                                                total[key]['txt'].substr(2,2)+":"+total[key]['txt'].substr(4,2);

                            document.getElementById("total_cnt_for").innerHTML = total[key]['fxc'];
                            document.getElementById("total_cnt_tw").innerHTML = total[key]['txc'];


                            document.getElementById("total_starttm").innerHTML = total[key]['hms'].substr(0,2)+ ":" +
                                                                                total[key]['hms'].substr(2,2)+":"+total[key]['hms'].substr(4,2);

                            document.getElementById("total_endtm").innerHTML = total[key]['edhms'].substr(0,2) + ":" +
                                                                            total[key]['edhms'].substr(2,2)+":"+total[key]['edhms'].substr(4,2);

                            document.getElementById("total_alltm").innerHTML = total[key]['alhms'].substr(0,2)+ ":" +
                                                                            total[key]['alhms'].substr(2,2)+":"+total[key]['alhms'].substr(4,2);

                            // 平均値表示切り替えで、表示される項目に代入
                            document.getElementById("average_total_risk").innerHTML = total[key]['risk'];

                            document.getElementById("average_total_avetm_for").innerHTML = total[key]['fxa'].substr(0,2)+ ":" +
                                                                                total[key]['fxa'].substr(2,2);

                            document.getElementById("average_total_alltm_for").innerHTML = total[key]['fxt'].substr(0,2)+ ":" +
                                                                                total[key]['fxt'].substr(2,2)+":"+total[key]['fxt'].substr(4,2);

                            document.getElementById("average_total_avetm_tw").innerHTML = total[key]['txa'].substr(0,2)+ ":" +
                                                                                total[key]['txa'].substr(2,2);

                            document.getElementById("average_total_alltm_tw").innerHTML = total[key]['txt'].substr(0,2)+ ":" +
                                                                                total[key]['txt'].substr(2,2)+":"+total[key]['txt'].substr(4,2);

                            document.getElementById("average_total_cnt_for").innerHTML = total[key]['fxc'];
                            document.getElementById("average_total_cnt_tw").innerHTML = total[key]['txc'];


                            document.getElementById("average_total_starttm").innerHTML = total[key]['hms'].substr(0,2)+ ":" +
                                                                                total[key]['hms'].substr(2,2)+":"+total[key]['hms'].substr(4,2);

                            document.getElementById("average_total_endtm").innerHTML = total[key]['edhms'].substr(0,2) + ":" +
                                                                            total[key]['edhms'].substr(2,2)+":"+total[key]['edhms'].substr(4,2);

                            document.getElementById("average_total_alltm").innerHTML = total[key]['alhms'].substr(0,2)+ ":" +
                                                                            total[key]['alhms'].substr(2,2)+":"+total[key]['alhms'].substr(4,2);
                        }
                    }
                });

                // chart_disp(obj2,"","");
                //ウェアラブルセンサデータ
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: "/Wearabledata_disp",
                        data: $form.serialize(),
                        success: function(data) {
                            // Do something with the response
                            var obj3 = JSON.stringify(data);
                            var obj4 = JSON.parse(obj3);
                            if(obj4 == "" || obj4 == null)
                            {
                                document.getElementById('pop_alert_back').style.visibility = 'collapse';
                                document.getElementById("btn_yes").style.visibility = 'collapse';
                                document.getElementById("btn_datadisp").style.visibility = 'collapse';
                                Ctrl_pop("error","visible",32);
                                chart_disp(obj2,"","",2);
                                return ;
                            }
                            chart_disp(obj2,"",obj4,1);
                            Ctrl_pop('','collapse','');
                        },
                        error: function(error) {
                            // Do something with the error
                            document.getElementById('pop_alert_back').style.visibility = 'collapse';
                            document.getElementById("btn_yes").style.visibility = 'collapse';
                            document.getElementById("btn_datadisp").style.visibility = 'collapse';
                            Ctrl_pop("error","visible",32);
                            chart_disp(obj2,"","",2);
                            return ;
                        }
                    });

            },
            error: function(error) {
                // Do something with the error
                document.getElementById('pop_alert_back').style.visibility = 'collapse';
                document.getElementById("btn_yes").style.visibility = 'collapse';
                document.getElementById("btn_datadisp").style.visibility = 'collapse';
                Ctrl_pop("error","visible",30);
                return ;
            }
        });
    });


    //比較画面
    //jquery + ajax フォーム内の値を送信
    $('#form_datadisp2').submit(function(event)
    {
        var total = @json($data);
        // document.getElementById("txt_wearable1").style.visibility = 'hidden';
        // document.getElementById("txt_wearable2").style.visibility = 'hidden';
        event.preventDefault()
        // フォームデータを取得
        var formdata = new FormData(document.getElementById("form_datadisp2"));
        if(formdata.get('ymd') == "" || formdata.get('ymd') == null)
        {
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById("btn_yes").style.visibility = 'collapse';
            document.getElementById("btn_datadisp").style.visibility = 'collapse';
            Ctrl_pop("error","visible",23);
            return ;
        }
        if(formdata.get('hms') == "" || formdata.get('hms') == null)
        {
            document.getElementById('pop_alert_back').style.visibility = 'collapse';
            document.getElementById("btn_yes").style.visibility = 'collapse';
            document.getElementById("btn_datadisp").style.visibility = 'collapse';
            Ctrl_pop("error","visible",24);
            return ;
        }
        var $form = $(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            // data: dataString,
            success: function(data) {
                // Do something with the response
                //1個目のグラフ
                var obj = JSON.stringify(data);
                var obj2 = JSON.parse(obj);
                if(obj2 == "" || obj2 == null)
                {
                    console.log("obj");
                    console.log(obj);
                    console.log("obj2");
                    console.log(obj2);
                    document.getElementById('pop_alert_back').style.visibility = 'collapse';
                    document.getElementById("btn_yes").style.visibility = 'collapse';
                    document.getElementById("btn_datadisp").style.visibility = 'collapse';
                    Ctrl_pop("error","visible",31);
                    return ;
                }
                // フォームデータを取得
                var formdata = new FormData(document.getElementById("form_datadisp2"));
                Object.keys(total).forEach(function (key)
                {
                    if(total[key]['ymd'] == formdata.get('ymd'))
                    {
                        if(total[key]['hms'].trim() == formdata.get('hms').trim())
                        {
                            document.getElementById("total_risk").innerHTML = total[key]['risk'];

                            document.getElementById("total_avetm_for").innerHTML = total[key]['fxa'].substr(0,2)+ ":" +
                                                                                total[key]['fxa'].substr(2,2);

                            document.getElementById("total_alltm_for").innerHTML = total[key]['fxt'].substr(0,2)+ ":" +
                                                                                total[key]['fxt'].substr(2,2)+":"+total[key]['fxt'].substr(4,2);

                            document.getElementById("total_avetm_tw").innerHTML = total[key]['txa'].substr(0,2)+ ":" +
                                                                                total[key]['txa'].substr(2,2);

                            document.getElementById("total_alltm_tw").innerHTML = total[key]['txt'].substr(0,2)+ ":" +
                                                                                total[key]['txt'].substr(2,2)+":"+total[key]['txt'].substr(4,2);

                            document.getElementById("total_cnt_for").innerHTML = total[key]['fxc'];
                            document.getElementById("total_cnt_tw").innerHTML = total[key]['txc'];


                            document.getElementById("total_starttm").innerHTML = total[key]['hms'].substr(0,2)+ ":" +
                                                                                total[key]['hms'].substr(2,2)+":"+total[key]['hms'].substr(4,2);

                            document.getElementById("total_endtm").innerHTML = total[key]['edhms'].substr(0,2) + ":" +
                                                                            total[key]['edhms'].substr(2,2)+":"+total[key]['edhms'].substr(4,2);

                            document.getElementById("total_alltm").innerHTML = total[key]['alhms'].substr(0,2)+ ":" +
                                                                            total[key]['alhms'].substr(2,2)+":"+total[key]['alhms'].substr(4,2);
                        }
                    }

                });

                //ウェアラブルセンサデータ
                //1個目のグラフ
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: "/Wearabledata_disp",
                    data: $form.serialize(),
                    success: function(data) {
                        // Do something with the response
                        var obj3 = JSON.stringify(data);
                        var obj4 = JSON.parse(obj3);
                        if(obj4 == "" || obj4 == null)
                        {
                            document.getElementById('pop_alert_back').style.visibility = 'collapse';
                            document.getElementById("btn_yes").style.visibility = 'collapse';
                            document.getElementById("btn_datadisp").style.visibility = 'collapse';
                            Ctrl_pop("error","visible",321);
                            chart_disp(obj2,"","",5);
                            return ;
                        }
                        //グラフ描画
                        chart_disp(obj2,"",obj4,3);
                        Ctrl_pop('','collapse','');

                    },
                    error: function(error) {
                        // Do something with the error
                        document.getElementById('pop_alert_back').style.visibility = 'collapse';
                        document.getElementById("btn_yes").style.visibility = 'collapse';
                        document.getElementById("btn_datadisp").style.visibility = 'collapse';
                        Ctrl_pop("error","visible",321);
                        chart_disp(obj2,"","",5);
                        return ;
                    }
                });

                // 比較用のデータ取得
                //2個目のグラフ
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: $("#form_datadisp3").attr('action'),
                        data: $("#form_datadisp2, #form_datadisp3").serialize(),
                        success: function(data) {
                            // Do something with the response
                            var obj5 = JSON.stringify(data);
                            var obj6 = JSON.parse(obj5);
                            if(obj6 == "" || obj6 == null)
                            {
                                console.log("obj5");
                                console.log(obj5);
                                console.log("obj6");
                                console.log(obj6);
                                document.getElementById('pop_alert_back').style.visibility = 'collapse';
                                document.getElementById("btn_yes").style.visibility = 'collapse';
                                document.getElementById("btn_datadisp").style.visibility = 'collapse';
                                Ctrl_pop("error","visible",31);
                                return ;
                            }
                            // フォームデータを取得
                            var formdata2 = new FormData(document.getElementById("form_datadisp3"));
                            Object.keys(total).forEach(function (key)
                            {
                                if(total[key]['ymd'] == formdata2.get('ymd2'))
                                {
                                    if(total[key]['hms'].trim() == formdata2.get('hms2').trim())
                                    {

                                        document.getElementById("total_risk2").innerHTML = total[key]['risk'];

                                        document.getElementById("total_avetm_for2").innerHTML = total[key]['fxa'].substr(0,2)+ ":" +
                                                                                            total[key]['fxa'].substr(2,2);

                                        document.getElementById("total_alltm_for2").innerHTML = total[key]['fxt'].substr(0,2)+ ":" +
                                                                                            total[key]['fxt'].substr(2,2)+":"+total[key]['fxt'].substr(4,2);

                                        document.getElementById("total_avetm_tw2").innerHTML = total[key]['txa'].substr(0,2)+ ":" +
                                                                                            total[key]['txa'].substr(2,2);

                                        document.getElementById("total_alltm_tw2").innerHTML = total[key]['txt'].substr(0,2)+ ":" +
                                                                                            total[key]['txt'].substr(2,2)+":"+total[key]['txt'].substr(4,2);

                                        document.getElementById("total_cnt_for2").innerHTML = total[key]['fxc'];
                                        document.getElementById("total_cnt_tw2").innerHTML = total[key]['txc'];


                                        document.getElementById("total_starttm2").innerHTML = total[key]['hms'].substr(0,2)+ ":" +
                                                                                            total[key]['hms'].substr(2,2)+":"+total[key]['hms'].substr(4,2);

                                        document.getElementById("total_endtm2").innerHTML = total[key]['edhms'].substr(0,2) + ":" +
                                                                                        total[key]['edhms'].substr(2,2)+":"+total[key]['edhms'].substr(4,2);

                                        document.getElementById("total_alltm2").innerHTML = total[key]['alhms'].substr(0,2)+ ":" +
                                                                                        total[key]['alhms'].substr(2,2)+":"+total[key]['alhms'].substr(4,2);
                                    }
                                }

                            });

                             //ウェアラブルセンサデータ
                             //2個目のグラフ
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                type: 'POST',
                                url: "/Wearabledata_disp2",
                                data: $("#form_datadisp2, #form_datadisp3").serialize(),
                                success: function(data) {
                                    // Do something with the response
                                    var obj7 = JSON.stringify(data);
                                    var obj8 = JSON.parse(obj7);

                                    if(obj8 == "" || obj8 == null)
                                    {
                                        document.getElementById('pop_alert_back').style.visibility = 'collapse';
                                        document.getElementById("btn_yes").style.visibility = 'collapse';
                                        document.getElementById("btn_datadisp").style.visibility = 'collapse';
                                        Ctrl_pop("error","visible",322);
                                        chart_disp(obj6,"","",6);
                                        return ;
                                    }
                                    //グラフ描画
                                    chart_disp(obj6,"",obj8,4);
                                    Ctrl_pop('','collapse','');
                                },
                                error: function(error) {
                                    // Do something with the error
                                    document.getElementById('pop_alert_back').style.visibility = 'collapse';
                                    document.getElementById("btn_yes").style.visibility = 'collapse';
                                    document.getElementById("btn_datadisp").style.visibility = 'collapse';
                                    Ctrl_pop("error","visible",322);
                                    chart_disp(obj6,"","",6);
                                    return ;
                                }
                            });

                        },
                        error: function(error) {
                            // Do something with the error
                            document.getElementById('pop_alert_back').style.visibility = 'collapse';
                            document.getElementById("btn_yes").style.visibility = 'collapse';
                            document.getElementById("btn_datadisp").style.visibility = 'collapse';
                            Ctrl_pop("error","visible",322);
                            chart_disp(obj6,"","",6);
                            return ;
                        }
                    });


            },
            error: function(error) {
                // Do something with the error
                document.getElementById('pop_alert_back').style.visibility = 'collapse';
                document.getElementById("btn_yes").style.visibility = 'collapse';
                document.getElementById("btn_datadisp").style.visibility = 'collapse';
                Ctrl_pop("error","visible",30);
                return ;
            }
        });
    });


    //ajaxで送信
    function data_disp(url,name,senddata)
    {
        var xhr = new XMLHttpRequest();
        var requests = "name=" + name + "&data=" + senddata;
        xhr.open('POST', url, true);
        xhr.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
        var token = document.getElementsByName('csrf-token').item(0).content;
        xhr.setRequestHeader('X-CSRF-Token', token);
        xhr.send(requests);
        try
        {
            xhr.onreadystatechange = function(){

                    if(this.readyState === 4)
                    {
                        //datas -> ~split("\n")にしたら行で1要素
                        //   recievedata = this.responseText.split(",");        // 全レコードのデータ
                        // console.log(recievedata);
                        //成功
                        // if(name == "cxl_userfix")
                        // {
                        //     var obj = JSON.parse(this.responseText);
                        //     document.getElementById('username').value = (obj[0].username).trim();
                        // }
                        // else if(name == "cxl_wearablefix")
                        // {
                        //     var obj = JSON.parse(this.responseText || "null");
                        //     document.getElementById('devicename').value = obj[0].devicename;
                        //     document.getElementById('clientid').value = obj[0].clientid;
                        //     document.getElementById('clientsc').value = obj[0].clientsc;
                        // }
                        // else
                        // {
                            if(this.response == 1)
                            {
                                // document.getElementById('pop_error_back').style.visibility = 'collapse';
                                document.getElementById('lb_alert').innerHTML = @json($errdata[7]['error']);
                                document.getElementById('pop_alert_back').style.visibility = 'visible';
                                document.getElementById('pop_alert').style.visibility = 'visible';
                                var timeoutID = setTimeout(window.location.reload(),2000);
                            }
                            else
                            {
                                // document.getElementById('pop_error_back').style.visibility = 'collapse';
                                document.getElementById('lb_alert').innerHTML = @json($errdata[6]['error']);
                                document.getElementById('pop_alert_back').style.visibility = 'visible';
                                document.getElementById('pop_alert').style.visibility = 'visible';
                                var timeoutID = setTimeout(window.location.reload(),2000);
                            }
                        // }
                    }
                };
        }
        catch{alert();}
    }


    var time=5;
    var width_add = time + 15;
    var max1 = 100;
    var max1_2=100;
    var max2 = 100;
    var max2_2=100;
    function chart_disp(data,data2,data3,mode)
    {
        // span_m
        time = Number(document.getElementById('span_h').value)*60 + Number(document.getElementById('span_m').value);
        // removeData(mode);
        //グラフの初期化
        if(myChart&& (mode == 1 || mode == 2))
        {
            while(myChart.data.labels.length != 0)
            {
                myChart.data.labels.pop();
                myChart.data.datasets[0].data.pop();
                myChart.data.datasets[1].data.pop();
                myChart.data.datasets[2].data.pop();
                myChart.data.datasets[3].data.pop();
                // myChart.data.datasets[4].data.pop();
            }
        }
        if(myChart2 && (mode == 3 || mode == 5))
        {
            while(myChart2.data.labels.length != 0)
            {
                myChart2.data.labels.pop();
                myChart2.data.datasets[0].data.pop();
                myChart2.data.datasets[1].data.pop();
                myChart2.data.datasets[2].data.pop();
                myChart2.data.datasets[3].data.pop();
                // myChart2.data.datasets[4].data.pop();
            }
        }
        if(myChart3 && (mode == 4 || mode == 6))
        {
            while(myChart3.data.labels.length != 0)
            {
                myChart3.data.labels.pop();
                myChart3.data.datasets[0].data.pop();
                myChart3.data.datasets[1].data.pop();
                myChart3.data.datasets[2].data.pop();
                myChart3.data.datasets[3].data.pop();
                // myChart3.data.datasets[4].data.pop();
            }
        }

        var cnt=0;
        var loop=0;
        var total1=0;
        var total2=0;
        //腰痛＋ウェアラブル
        if(mode == 1)
        {
            var val1 =0;
            var val2=0;
            var label1=0;
            var flag = 0;

            //既存データを削除
            //ラベルの作成
            Object.keys(data).forEach(function (key)
            {
                loop++;
                //1時間毎
                for(var i=1;i<13;i++)
                {
                    //5分毎
                    //時間になった時点での合計
                    //time分スキップ、加算
                    cnt+=5;
                    var hou_ =0;
                    console.log("**"+( '00' + Number(data[key]['hou'])).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 ));

                    //終了時
                    //終了時とcnt == timeが一致する時は処理しない
                    if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null|| (data[key]['min'+ i] == data[key]['min'+ (i-1)])||(Object.keys(data).length == loop && i == 12))
                    {
                        console.log("^-->"+data[key]['min'+ i]);
                        if(loop != Object.keys(data).length)
                        {
                            console.log("^-->IN");
                            cnt = cnt-5;
                            break;
                        }


                        //ラベル用のデータ処理
                        if((Number(data[key]['min'+ (i-1)]) < Number(data[key]['min'+ (i-2)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            label1 = ( '00' + hou_ ).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+ (i-1)] )).slice( -2 );
                        }
                        else
                        {
                            label1 =  ( '00' + Number(data[key]['hou']) ).slice( -2 )+ ":" + ( '00' + Number(data[key]['min'+ (i-1)] )).slice( -2 );
                            hou_ = Number(data[key]['hou']);
                        }


                        if(myChart.data.labels[myChart.data.labels.length-1] == label1)
                        {
                            break;
                        }
                        //ラベルに追加
                        myChart.data.labels.push(label1);


                        var val3 = 0;
                        for(var j=0;j<Object.keys(data3).length;j++)
                        {
                            if(hou_ == Number(data3[j]['hou']))
                            {
                                if(Number(data[key]['min'+i]) == 0)
                                {
                                    val3 = data3[j]['dt0'];
                                }
                                else val3 = data3[j]['dt'+ Number(data[key]['min'+i])];
                                break;
                            }
                            if(j == Object.keys(data3).length-1) val3 =0;
                        }

                        //値の追加
                        myChart.data.datasets[0].data.push(val1);
                        myChart.data.datasets[1].data.push(val2);
                        // if(Number.isFinite(val3)) val3 =0;
                        // myChart.data.datasets[3].data.push(total1);
                        myChart.data.datasets[2].data.push(total1);
                        // myChart.data.datasets[4].data.push(total2);
                        myChart.data.datasets[3].data.push(total2);

                        //widthの調整
                        // var newwidth = $('.chartAreaWrapper2').width() + width_add;
                        // $('.chartAreaWrapper2').width(newwidth);
                        val1=0;
                        val2=0;
                        val3 =0;
                        cnt=0;
                        break;
                        // continue;
                    }
                    //cntがtimeに到達したら値を追加
                    else if(cnt == time)
                    {
                        if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null)
                        {

                            if(loop != Object.keys(data).length)
                            {
                                cnt = cnt-5;
                                break;
                            }
                        }
                        //ラベル用のデータ処理
                        // if((data[key]['min'+ i] < data[key]['min'+ (i-1)]) || flag == 1 || cnt != 5)
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            // label1 = hou_  + ":" + data[key]['min'+i];
                            label1 = ( '00' + hou_ ).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                        }
                        else
                        {
                            // label1 = data[key]['hou']+ ":" + data[key]['min'+i];
                            label1 = ( '00' + Number(data[key]['hou']) ).slice( -2 ) + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);
                        }

                        if(myChart.data.labels[myChart.data.labels.length-1] == label1)
                        {
                            break;
                        }

                        //ラベルに追加
                        myChart.data.labels.push(label1);

                        var val3 = 0;
                        for(var j=0;j<Object.keys(data3).length;j++)
                        {
                            if(hou_ == Number(data3[j]['hou']))
                            {
                                if(Number(data[key]['min'+i]) == 0)
                                {
                                    val3 = data3[j]['dt0'];
                                }
                                else
                                {
                                    val3 = data3[j]['dt'+ Number(data[key]['min'+i])];
                                }
                                break;
                            }
                            if(j == Object.keys(data3).length-1) val3 =0;
                        }

                        //値の追加
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);



                        //値の追加
                        myChart.data.datasets[0].data.push(val1);
                        myChart.data.datasets[1].data.push(val2);
                        // if(Number.isFinite(val3)) val3 =0;
                        // else if(Number.isFinite(val3) == undefined) val3 =0;
                        // myChart.data.datasets[3].data.push(total1);
                        myChart.data.datasets[2].data.push(total1);

                        // myChart.data.datasets[4].data.push(total2);
                        myChart.data.datasets[3].data.push(total2);


                        //widthの調整
                        // var newwidth = $('.chartAreaWrapper2').width() + width_add;
                        // $('.chartAreaWrapper2').width(newwidth);
                        val1=0;
                        val2=0;
                        val3 =0;
                        cnt=0;

                    }

                    else
                    {
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)]))|| flag == 1 )
                        {
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            flag = 1;
                        }
                        else hou_ = Number(data[key]['hou']);
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);
                    }

                    // cnt+=5;

                }
                flag = 0;
            });


            // var max0 = Math.max(Math.max.apply(null,myChart.data.datasets[0].data),Math.max.apply(null,myChart.data.datasets[1].data)
            //                 ,Math.max.apply(null,myChart.data.datasets[3].data),Math.max.apply(null,myChart.data.datasets[4].data));
            var max0 = Math.max(Math.max.apply(null,myChart.data.datasets[0].data),Math.max.apply(null,myChart.data.datasets[1].data)
                            ,Math.max.apply(null,myChart.data.datasets[2].data),Math.max.apply(null,myChart.data.datasets[3].data));
            // var max0_2 = Math.max.apply(null,myChart.data.datasets[2].data);


            ///20単位でy軸表示しているため、次の20の倍数になるところを最大値とする
            var y0_max=0;
            var y1_max=0;
            for(var i=max0;i<max0*20;i++)
            {
                if(i!=max0)
                {
                    if(i % 20 == 0)
                    {
                        y0_max = i;
                        break;
                    }
                }
            }

            // for(var i = max0_2;i<max0_2*20;i++)
            // {
            //     if(i!=max0_2)
            //     {
            //         if(i % 20 == 0)
            //         {
            //             y1_max = i;
            //             break;
            //         }
            //     }

            // }
            if(max0 > 100) y0_max = y0_max + 40;
            myChart.options.scales.yAxes[0].ticks.max = y0_max;
            // if(y1_max == 0) y1_max = 100;
            // myChart.options.scales.yAxes[1].ticks.max = y1_max;

            // グラフY軸の最大値を100に
            var y1_max=100;
            myChart.options.scales.yAxes[1].ticks.max = y1_max;


            //グラフ更新
            myChart.update();
        }
        //腰痛のみ
        else if(mode == 2)
        {
            var val1 =0;
            var val2=0;
            var label1=0;
            var flag = 0;
            //既存データを削除
            //ラベルの作成
            Object.keys(data).forEach(function (key)
            {
                loop++;
                //1時間毎
                for(var i=1;i<13;i++)
                {
                    //5分毎
                    //時間になった時点での合計
                    //time分スキップ、加算
                    cnt+=5;
                    var hou_ =0;
                    //cntがtimeに到達したら値を追加
                    console.log("**"+( '00' + Number(data[key]['hou'])).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 ));
                    //終了時
                    //終了時とcnt == timeが一致する時は処理しない
                    if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null || (data[key]['min'+ i] == data[key]['min'+ (i-1)])||(Object.keys(data).length == loop && i == 12))
                    {
                        if(loop != Object.keys(data).length)
                        {
                            cnt = cnt-5;
                            break;
                        }
                        //ラベル用のデータ処理
                        if((Number(data[key]['min'+ (i-1)]) < Number(data[key]['min'+ (i-2)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            label1 = ( '00' + hou_ ).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+ (i-1)]) ).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number(data[key]['hou'] )).slice( -2 )+ ":" + ( '00' + Number(data[key]['min'+ (i-1)]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart.data.labels[myChart.data.labels.length-1] == label1)
                        {
                            break;
                        }
                        //ラベルに追加
                        myChart.data.labels.push(label1);


                        //値の追加
                        myChart.data.datasets[0].data.push(val1);
                        myChart.data.datasets[1].data.push(val2);
                        // myChart.data.datasets[3].data.push(total1);
                        myChart.data.datasets[2].data.push(total1);
                        // myChart.data.datasets[4].data.push(total2);
                        myChart.data.datasets[3].data.push(total2);

                        //widthの調整
                        // var newwidth = $('.chartAreaWrapper2').width() + width_add;
                        // $('.chartAreaWrapper2').width(newwidth);
                        val1=0;
                        val2=0;
                        cnt=0;
                        break;
                        // continue;
                    }
                    else if(cnt == time)
                    {

                        if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null)
                        {

                            if(loop != Object.keys(data).length)
                            {
                                cnt = cnt-5;
                                break;
                            }
                        }
                        //ラベル用のデータ処理
                        // if((data[key]['min'+ i] < data[key]['min'+ (i-1)]) || flag == 1 || cnt != 5)
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            label1 =  ( '00' + hou_ ).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number( data[key]['hou'] )).slice( -2 ) + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart.data.labels[myChart.data.labels.length-1] == label1)
                        {
                            break;
                        }

                        //ラベルに追加
                        myChart.data.labels.push(label1);


                        //値の追加
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);

                        //値の追加
                        myChart.data.datasets[0].data.push(val1);
                        myChart.data.datasets[1].data.push(val2);
                        // myChart.data.datasets[3].data.push(total1);
                        myChart.data.datasets[2].data.push(total1);
                        // myChart.data.datasets[4].data.push(total2);
                        myChart.data.datasets[3].data.push(total2);

                        //widthの調整
                        // var newwidth = $('.chartAreaWrapper2').width() + width_add;
                        // $('.chartAreaWrapper2').width(newwidth);
                        val1=0;
                        val2=0;
                        cnt=0;

                    }

                    else
                    {
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)]))|| flag == 1 )
                        {
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            flag = 1;
                        }
                        else hou_ = Number(data[key]['hou']);
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);
                    }

                // cnt+=5;
                }
                flag = 0;
            });

            // var max0 = Math.max(Math.max.apply(null,myChart.data.datasets[0].data),Math.max.apply(null,myChart.data.datasets[1].data)
            //                 ,Math.max.apply(null,myChart.data.datasets[3].data),Math.max.apply(null,myChart.data.datasets[4].data));
            var max0 = Math.max(Math.max.apply(null,myChart.data.datasets[0].data),Math.max.apply(null,myChart.data.datasets[1].data)
                            ,Math.max.apply(null,myChart.data.datasets[2].data),Math.max.apply(null,myChart.data.datasets[3].data));

            ///20単位でy軸表示しているため、次の20の倍数になるところを最大値とする
            var y0_max=0;
            var y1_max=0;
            for(var i=max0;i<max0*20;i++)
            {
                if(i!=max0)
                {
                    if(i % 20 == 0)
                    {
                        y0_max = i;
                        break;
                    }
                }
            }
            if(max0 > 100) y0_max = y0_max + 40;
            myChart.options.scales.yAxes[0].ticks.max = y0_max;

            //グラフ更新
            myChart.update();
        }
        //比較　1個目のグラフ 腰痛+ウェアラブル
        else if(mode == 3)
        {
            var val1 =0;
            var val2=0;
            var label1=0;
            var flag = 0;
            //既存データを削除
            //ラベルの作成
            Object.keys(data).forEach(function (key)
            {
                loop++;
                //1時間毎
                for(var i=1;i<13;i++)
                {
                    //5分毎
                    //時間になった時点での合計
                    //time分スキップ、加算
                    // cnt+=5;
                    var hou_ =0;
                    //cntがtimeに到達したら値を追加
                    cnt+=5;
                    //終了時
                    //終了時とcnt == timeが一致する時は処理しない
                    if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null || (data[key]['min'+ i] == data[key]['min'+ (i-1)]) || (Object.keys(data).length == loop && i == 12))
                    {
                        if(loop != Object.keys(data).length)
                        {
                            cnt = cnt-5;
                            break;
                        }
                        //ラベル用のデータ処理
                        if((Number(data[key]['min'+ (i-1)]) < Number(data[key]['min'+ (i-2)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            label1 = ( '00' +hou_).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+ (i-1)]) ).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number(data[key]['hou'])).slice( -2 )+ ":" + ( '00' + Number(data[key]['min'+ (i-1)]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart2.data.labels[myChart2.data.labels.length-1] == label1)
                        {
                            break;
                        }

                        //ラベルに追加
                        myChart2.data.labels.push(label1);

                        // var val3 = 0;
                        // for(var j=0;j<Object.keys(data3).length;j++)
                        // {
                        //     if(hou_ == Number(data3[j]['hou']))
                        //     {
                        //         if(Number(data[key]['min'+i]) == 0)
                        //         {
                        //             val3 = data3[j]['dt0'];
                        //         }
                        //         else val3 = data3[j]['dt'+ Number(data[key]['min'+i])];
                        //         break;
                        //     }
                        //     if(j == Object.keys(data3).length-1) val3 =0;
                        // }

                        //値の追加
                        myChart2.data.datasets[0].data.push(val1);
                        myChart2.data.datasets[1].data.push(val2);
                        // if(Number.isFinite(val3)) val3 =0;
                        // myChart2.data.datasets[2].data.push(val3);
                        // myChart2.data.datasets[3].data.push(total1);
                        myChart2.data.datasets[2].data.push(total1);
                        // myChart2.data.datasets[4].data.push(total2);
                        myChart2.data.datasets[3].data.push(total2);

                        //widthの調整
                        // var newwidth2 = $('.chartAreaWrapper4').width() + width_add;
                        // $('.chartAreaWrapper4').width(newwidth2);
                        val1=0;
                        val2=0;
                        cnt=0;
                        break;
                        // continue;
                    }
                    else if(cnt == time)
                    {

                        if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null)
                        {

                            if(loop != Object.keys(data).length)
                            {
                                cnt = cnt-5;
                                break;
                            }
                        }
                        //ラベル用のデータ処理
                        // if((data[key]['min'+ i] < data[key]['min'+ (i-1)]) || flag == 1 || cnt != 5)
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            label1 = ( '00' +hou_ ).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+i] )).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number(data[key]['hou']) ).slice( -2 ) + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart2.data.labels[myChart2.data.labels.length-1] == label1)
                        {
                            break;
                        }

                        //ラベルに追加
                        myChart2.data.labels.push(label1);


                        // var val3 = 0;
                        // for(var j=0;j<Object.keys(data3).length;j++)
                        // {
                        //     if(hou_ == Number(data3[j]['hou']))
                        //     {

                        //         if(Number(data[key]['min'+i]) == 0)
                        //         {
                        //             val3 = data3[j]['dt0'];
                        //         }
                        //         else val3 = data3[j]['dt'+ Number(data[key]['min'+i])];
                        //         break;
                        //     }
                        //     if(j == Object.keys(data3).length-1) val3 =0;
                        // }

                        //値の追加
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        // if(Number.isFinite(val3)) val3 =0;
                        // myChart2.data.datasets[2].data.push(val3);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);

                        //値の追加
                        myChart2.data.datasets[0].data.push(val1);
                        myChart2.data.datasets[1].data.push(val2);
                        // myChart2.data.datasets[3].data.push(total1);
                        myChart2.data.datasets[2].data.push(total1);
                        // myChart2.data.datasets[4].data.push(total2);
                        myChart2.data.datasets[3].data.push(total2);


                        //widthの調整
                        // var newwidth2 = $('.chartAreaWrapper4').width() + width_add;
                        // $('.chartAreaWrapper4').width(newwidth2);
                        val1=0;
                        val2=0;
                        cnt=0;

                    }

                    else
                    {
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)]))|| flag == 1 )
                        {
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            flag = 1;
                        }
                        else hou_ = Number(data[key]['hou']);
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);
                    }

                    // cnt+=5;
                }
                flag = 0;

            });
            //グラフ更新
            myChart2.update();
            // max1 = Math.max(Math.max.apply(null,myChart2.data.datasets[0].data),Math.max.apply(null,myChart2.data.datasets[1].data)
            //                 ,Math.max.apply(null,myChart2.data.datasets[3].data),Math.max.apply(null,myChart2.data.datasets[4].data));
            max1 = Math.max(Math.max.apply(null,myChart2.data.datasets[0].data),Math.max.apply(null,myChart2.data.datasets[1].data)
                            ,Math.max.apply(null,myChart2.data.datasets[2].data),Math.max.apply(null,myChart2.data.datasets[3].data));
            // max1_2 = Math.max.apply(null,myChart2.data.datasets[2].data);
        }
        //比較　2個目のグラフ 腰痛+ウェアラブル
        else if(mode == 4)
        {
            var val1 =0;
            var val2=0;
            var label1=0;
            var flag = 0;
            var cum =0;
            var cum_tw =0;
            //既存データを削除
            //ラベルの作成
            Object.keys(data).forEach(function (key)
            {
                loop++;
                //1時間毎
                for(var i=1;i<13;i++)
                {
                    //5分毎
                    //時間になった時点での合計
                    //time分スキップ、加算
                    cnt+=5;
                    var hou_ =0;
                    //終了時
                    //終了時とcnt == timeが一致する時は処理しない
                    if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null || (data[key]['min'+ i] == data[key]['min'+ (i-1)]) || (Object.keys(data).length == loop && i == 12))
                    {
                        if(loop != Object.keys(data).length)
                        {
                            cnt = cnt-5;
                            break;
                        }
                        //ラベル用のデータ処理
                        if((Number(data[key]['min'+ (i-1)]) < Number(data[key]['min'+ (i-2)])) || flag == 1 )
                        {
                            flag = 1;
                            hou_ = Number(data[key]['hou']) + 1;
                            label1 = ( '00' + hou_).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+ (i-1)])).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number(data[key]['hou'])).slice( -2 ) + ":" + ( '00' + Number(data[key]['min'+ (i-1)])).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart3.data.labels[myChart3.data.labels.length-1] == label1)
                        {
                            break;
                        }
                        //ラベルに追加
                        myChart3.data.labels.push(label1);

                        // var val3 = 0;
                        // for(var j=0;j<Object.keys(data3).length;j++)
                        // {
                        //     if(hou_ == Number(data3[j]['hou']))
                        //     {
                        //         if(Number(data[key]['min'+i]) == 0)
                        //         {
                        //             val3 = data3[j]['dt0'];
                        //         }
                        //         else val3 = data3[j]['dt'+ Number(data[key]['min'+i])];
                        //         break;
                        //     }
                        //     if(j == Object.keys(data3).length-1) val3 =0;
                        // }

                        //値の追加
                        myChart3.data.datasets[0].data.push(val1);
                        myChart3.data.datasets[1].data.push(val2);
                        // myChart3.data.datasets[3].data.push(total1);
                        myChart3.data.datasets[2].data.push(total1);
                        // myChart3.data.datasets[4].data.push(total2);
                        myChart3.data.datasets[3].data.push(total2);
                        // if(Number.isFinite(val3)) val3 =0;
                        // myChart3.data.datasets[2].data.push(val3);

                        //widthの調整
                        // var newwidth2 = $('.chartAreaWrapper6').width() + width_add;
                        // $('.chartAreaWrapper6').width(newwidth2);
                        val1=0;
                        val2=0;
                        cnt=0;
                        break;
                        // continue;
                    }
                    //cntがtimeに到達したら値を追加
                    else if(cnt == time)
                    {

                        if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null)
                        {

                            if(loop != Object.keys(data).length)
                            {
                                cnt = cnt-5;
                                break;
                            }
                        }
                        //ラベル用のデータ処理
                        // if((data[key]['min'+ i] < data[key]['min'+ (i-1)]) || flag == 1 || cnt != 5)
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            label1 =  ( '00' +hou_ ).slice( -2 )  + ":" +  ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number(data[key]['hou'])).slice( -2 )+ ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart3.data.labels[myChart3.data.labels.length-1] == label1)
                        {
                            break;
                        }

                        //ラベルに追加
                        myChart3.data.labels.push(label1);


                        // var val3 = 0;
                        // for(var j=0;j<Object.keys(data3).length;j++)
                        // {
                        //     if(hou_ == Number(data3[j]['hou']))
                        //     {

                        //         if(Number(data[key]['min'+i]) == 0)
                        //         {
                        //             val3 = data3[j]['dt0'];
                        //         }
                        //         else val3 = data3[j]['dt'+ Number(data[key]['min'+i])];
                        //         break;
                        //     }
                        //     if(j == Object.keys(data3).length-1) val3 =0;
                        // }

                        //値の追加
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);

                        // if(Number.isFinite(val3)) val3 =0;
                        // myChart3.data.datasets[2].data.push(val3);


                        // cum +=Number(data[key]['ftilt'+i]);
                        // cum_tw += Number(data[key]['twist'+i]);
                        // myChart3.data.datasets[3].data.push(cum);
                        // myChart3.data.datasets[4].data.push(cum_tw);

                        //値の追加
                        myChart3.data.datasets[0].data.push(val1);
                        myChart3.data.datasets[1].data.push(val2);
                        // myChart3.data.datasets[3].data.push(total1);
                        myChart3.data.datasets[2].data.push(total1);
                        // myChart3.data.datasets[4].data.push(total2);
                        myChart3.data.datasets[3].data.push(total2);

                        //widthの調整
                        // var newwidth2 = $('.chartAreaWrapper6').width() + width_add;
                        // $('.chartAreaWrapper6').width(newwidth2);
                        val1=0;
                        val2=0;
                        cnt=0;

                    }

                    else
                    {
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)]))|| flag == 1 )
                        {
                            hou_ = Number(data[key]['hou']) + 1;
                            flag = 1;
                        }
                        else hou_ = Number(data[key]['hou']);
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);

                    }

                    // cnt+=5;
                }
                flag = 0;
            });

            // max2 = Math.max(Math.max.apply(null,myChart3.data.datasets[0].data),Math.max.apply(null,myChart3.data.datasets[1].data)
            //                 ,Math.max.apply(null,myChart3.data.datasets[3].data),Math.max.apply(null,myChart3.data.datasets[4].data));
            max2 = Math.max(Math.max.apply(null,myChart3.data.datasets[0].data),Math.max.apply(null,myChart3.data.datasets[1].data)
                            ,Math.max.apply(null,myChart3.data.datasets[2].data),Math.max.apply(null,myChart3.data.datasets[3].data));
            // max2_2 =Math.max.apply(null,myChart3.data.datasets[2].data);
            var y0_max_tmp = Math.max(max1,max2);
            // var y1_max_tmp = Math.max(max1_2,max2_2);

            ///20単位でy軸表示しているため、次の20の倍数になるところを最大値とする
            var y0_max=0;
            // var y1_max=0;
            for(var i=y0_max_tmp;i<y0_max_tmp*20;i++)
            {
                if(i % 20 == 0)
                {
                    y0_max = i;
                    break;
                }
            }
            // for(var i=y1_max_tmp;i<y1_max_tmp*20;i++)
            // {
            //     if(i % 20 == 0)
            //     {
            //         y1_max = i;
            //         break;
            //     }
            // }

            if(y0_max_tmp < 500 && y0_max_tmp > 100) y0_max = y0_max + 40;
            else if(y0_max_tmp > 500) y0_max = y0_max + 100;
            myChart2.options.scales.yAxes[0].ticks.max = y0_max;
            myChart3.options.scales.yAxes[0].ticks.max = y0_max;

            // if(y1_max == 0) y1_max = 100;
            // myChart2.options.scales.yAxes[1].ticks.max = y1_max;
            // myChart3.options.scales.yAxes[1].ticks.max = y1_max;
            // グラフY軸の最大値を100に
            var y1_max=100;
            myChart2.options.scales.yAxes[1].ticks.max = y1_max;
            myChart3.options.scales.yAxes[1].ticks.max = y1_max;

            //グラフ更新
            myChart2.update();
            myChart3.update();

        }
        //比較　1個目のグラフ 腰痛のみ
        else if(mode == 5)
        {
            var val1 =0;
            var val2=0;
            var label1=0;
            var flag = 0;
            //既存データを削除
            //ラベルの作成
            Object.keys(data).forEach(function (key)
            {
                loop++;
                //1時間毎
                for(var i=1;i<13;i++)
                {
                    //5分毎
                    //時間になった時点での合計
                    //time分スキップ、加算
                    cnt+=5;
                    var hou_ =0;
                     //終了時
                    //終了時とcnt == timeが一致する時は処理しない
                    if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null || (data[key]['min'+ i] == data[key]['min'+ (i-1)])||(Object.keys(data).length == loop && i == 12) )
                    {
                        if(loop != Object.keys(data).length)
                        {
                            cnt = cnt-5;
                            break;
                        }
                        //ラベル用のデータ処理
                        if((Number(data[key]['min'+ (i-1)]) < Number(data[key]['min'+ (i-2)])) || flag == 1 )
                        {
                            flag = 1;
                            hou_ = Number(data[key]['hou']) + 1;
                            label1 = ( '00' + hou_).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+ (i-1)]) ).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number(data[key]['hou']) ).slice( -2 )+ ":" + ( '00' + Number(data[key]['min'+ (i-1)]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart2.data.labels[myChart2.data.labels.length-1] == label1)
                        {
                            break;
                        }

                        //ラベルに追加
                        myChart2.data.labels.push(label1);


                        //値の追加
                        myChart2.data.datasets[0].data.push(val1);
                        myChart2.data.datasets[1].data.push(val2);
                        // myChart2.data.datasets[3].data.push(total1);
                        myChart2.data.datasets[2].data.push(total1);
                        // myChart2.data.datasets[4].data.push(total2);
                        myChart2.data.datasets[3].data.push(total2);

                        //widthの調整
                        // var newwidth2 = $('.chartAreaWrapper4').width() + width_add;
                        // $('.chartAreaWrapper4').width(newwidth2);
                        val1=0;
                        val2=0;
                        cnt=0;
                        break;
                        // continue;
                    }
                    //cntがtimeに到達したら値を追加
                    else if(cnt == time)
                    {

                        if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null)
                        {

                            if(loop != Object.keys(data).length)
                            {
                                cnt = cnt-5;
                                break;
                            }
                        }
                        //ラベル用のデータ処理
                        // if((data[key]['min'+ i] < data[key]['min'+ (i-1)]) || flag == 1 || cnt != 5)
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            label1 = ( '00' +hou_ ).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                        }
                        else
                        {
                            label1 =  ( '00' + Number(data[key]['hou']) ).slice( -2 ) + ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart2.data.labels[myChart2.data.labels.length-1] == label1)
                        {
                            break;
                        }

                        //ラベルに追加
                        myChart2.data.labels.push(label1);


                        //値の追加
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);


                        //値の追加
                        myChart2.data.datasets[0].data.push(val1);
                        myChart2.data.datasets[1].data.push(val2);
                        // myChart2.data.datasets[3].data.push(total1);
                        myChart2.data.datasets[2].data.push(total1);
                        // myChart2.data.datasets[4].data.push(total2);
                        myChart2.data.datasets[3].data.push(total2);


                        //widthの調整
                        // var newwidth2 = $('.chartAreaWrapper4').width() + width_add;
                        // $('.chartAreaWrapper4').width(newwidth2);
                        val1=0;
                        val2=0;
                        cnt=0;

                    }

                    else
                    {
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)]))|| flag == 1 )
                        {
                            hou_ = Number(data[key]['hou']) + 1;
                            flag = 1;
                        }
                        else hou_ = Number(data[key]['hou']);
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);
                    }

                    // cnt+=5;
                }
                flag = 0;
            });

            //グラフ更新
            myChart2.update();
            // max1 = Math.max(Math.max.apply(null,myChart2.data.datasets[0].data),Math.max.apply(null,myChart2.data.datasets[1].data)
            //         ,Math.max.apply(null,myChart2.data.datasets[3].data),Math.max.apply(null,myChart2.data.datasets[4].data));
            max1 = Math.max(Math.max.apply(null,myChart2.data.datasets[0].data),Math.max.apply(null,myChart2.data.datasets[1].data)
                    ,Math.max.apply(null,myChart2.data.datasets[2].data),Math.max.apply(null,myChart2.data.datasets[3].data));

        }
        //比較　2個目のグラフ 腰痛のみ
        else if(mode == 6)
        {
            var val1 =0;
            var val2=0;
            var label1=0;
            var flag = 0;
            var cum =0;
            var cum_tw =0;
            //既存データを削除
            //ラベルの作成
            Object.keys(data).forEach(function (key)
            {
                loop++;
                //1時間毎
                for(var i=1;i<13;i++)
                {
                    //5分毎
                    //時間になった時点での合計
                    //time分スキップ、加算
                    cnt+=5;
                    var hou_ =0;
                    //終了時
                    //終了時とcnt == timeが一致する時は処理しない
                    if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null || (data[key]['min'+ i] == data[key]['min'+ (i-1)])||(Object.keys(data).length == loop && i == 12) )
                    {
                        if(loop != Object.keys(data).length)
                        {
                            cnt = cnt-5;
                            break;
                        }
                        //ラベル用のデータ処理
                        if((Number(data[key]['min'+ (i-1)]) < Number(data[key]['min'+ (i-2)])) || flag == 1 )
                        {
                            flag = 1;
                            hou_ = Number(data[key]['hou']) + 1;
                            label1 = ( '00' + hou_).slice( -2 )  + ":" + ( '00' + Number(data[key]['min'+ (i-1)])).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number(data[key]['hou'])).slice( -2 ) + ":" + ( '00' + Number(data[key]['min'+ (i-1)])).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart3.data.labels[myChart3.data.labels.length-1] == label1)
                        {
                            break;
                        }
                        //ラベルに追加
                        myChart3.data.labels.push(label1);



                        //値の追加
                        myChart3.data.datasets[0].data.push(val1);
                        myChart3.data.datasets[1].data.push(val2);
                        // myChart3.data.datasets[3].data.push(total1);
                        myChart3.data.datasets[2].data.push(total1);
                        // myChart3.data.datasets[4].data.push(total2);
                        myChart3.data.datasets[3].data.push(total2);


                        //widthの調整
                        // var newwidth2 = $('.chartAreaWrapper6').width() + width_add;
                        // $('.chartAreaWrapper6').width(newwidth2);
                        val1=0;
                        val2=0;
                        cnt=0;
                        break;
                        // continue;
                    }
                    //cntがtimeに到達したら値を追加
                    else if(cnt == time)
                    {

                        if(data[key]['min'+ i] == "" || data[key]['min'+ i]  == null)
                        {

                            if(loop != Object.keys(data).length)
                            {
                                cnt = cnt-5;
                                break;
                            }
                        }
                        //ラベル用のデータ処理
                        // if((data[key]['min'+ i] < data[key]['min'+ (i-1)]) || flag == 1 || cnt != 5)
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)])) || flag == 1 )
                        {
                            flag = 1;
                            if(Number(data[key]['hou']) >= 23) hou_ = 0;
                            else hou_ = Number(data[key]['hou']) + 1;
                            label1 =  ( '00' +hou_ ).slice( -2 )  + ":" +  ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                        }
                        else
                        {
                            label1 = ( '00' + Number(data[key]['hou'])).slice( -2 )+ ":" + ( '00' + Number(data[key]['min'+i]) ).slice( -2 );
                            hou_ = Number(data[key]['hou']);

                        }

                        if(myChart3.data.labels[myChart3.data.labels.length-1] == label1)
                        {
                            break;
                        }

                        //ラベルに追加
                        myChart3.data.labels.push(label1);




                        //値の追加
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);



                        // cum +=Number(data[key]['ftilt'+i]);
                        // cum_tw += Number(data[key]['twist'+i]);
                        // myChart3.data.datasets[3].data.push(cum);
                        // myChart3.data.datasets[4].data.push(cum_tw);

                        //値の追加
                        myChart3.data.datasets[0].data.push(val1);
                        myChart3.data.datasets[1].data.push(val2);
                        // myChart3.data.datasets[3].data.push(total1);
                        myChart3.data.datasets[2].data.push(total1);
                        // myChart3.data.datasets[4].data.push(total2);
                        myChart3.data.datasets[3].data.push(total2);

                        //widthの調整
                        // var newwidth2 = $('.chartAreaWrapper6').width() + width_add;
                        // $('.chartAreaWrapper6').width(newwidth2);
                        val1=0;
                        val2=0;
                        cnt=0;

                    }

                    else
                    {
                        if((Number(data[key]['min'+ i]) < Number(data[key]['min'+ (i-1)]))|| flag == 1 )
                        {
                            hou_ = Number(data[key]['hou']) + 1;
                            flag = 1;
                        }
                        else hou_ = Number(data[key]['hou']);
                        val1 += Number(data[key]['ftilt'+i]);
                        val2 += Number(data[key]['twist'+i]);
                        total1+= Number(data[key]['ftilt'+i]);
                        total2+= Number(data[key]['twist'+i]);

                    }

                    // cnt+=5;
                }
                flag = 0;
            });

            // max2 = Math.max(Math.max.apply(null,myChart3.data.datasets[0].data),Math.max.apply(null,myChart3.data.datasets[1].data)
            //                 ,Math.max.apply(null,myChart3.data.datasets[3].data),Math.max.apply(null,myChart3.data.datasets[4].data));
            max2 = Math.max(Math.max.apply(null,myChart3.data.datasets[0].data),Math.max.apply(null,myChart3.data.datasets[1].data)
                            ,Math.max.apply(null,myChart3.data.datasets[2].data),Math.max.apply(null,myChart3.data.datasets[3].data));
            // max2_2 =Math.max.apply(null,myChart3.data.datasets[2].data);
            var y0_max_tmp = Math.max(max1,max2);
            // var y1_max_tmp = Math.max(max1_2,max2_2);

            ///20単位でy軸表示しているため、次の20の倍数になるところを最大値とする
            var y0_max=0;

            for(var i=y0_max_tmp;i<y0_max_tmp*20;i++)
            {
                if(i % 20 == 0)
                {
                    y0_max = i;
                    break;
                }
            }
            // for(var i=y1_max_tmp;i<y1_max_tmp*20;i++)
            // {
            //     if(i % 20 == 0)
            //     {
            //         y1_max = i;
            //         break;
            //     }
            // }
            if(y0_max_tmp < 500 && y0_max_tmp > 100) y0_max = y0_max + 40;
            else if(y0_max_tmp > 500) y0_max = y0_max + 100;
            myChart2.options.scales.yAxes[0].ticks.max = y0_max;
            myChart3.options.scales.yAxes[0].ticks.max = y0_max;

            // if(y1_max == 0) y1_max = 100;
            // myChart2.options.scales.yAxes[1].ticks.max = y1_max;
            // myChart3.options.scales.yAxes[1].ticks.max = y1_max;

            // グラフY軸の最大値を100に
            var y1_max=100;
            myChart2.options.scales.yAxes[1].ticks.max = y1_max;
            myChart3.options.scales.yAxes[1].ticks.max = y1_max;

            //グラフ更新
            myChart2.update();
            myChart3.update();
        }

    }


    function removeData(mode)
    {
        if(mode == 1 || mode == 2)
        {
            while(myChart.data.labels.length != 0)
            {
                myChart.data.labels.pop();
                myChart.data.datasets[0].data.pop();
                myChart.data.datasets[1].data.pop();
                myChart.data.datasets[2].data.pop();
                myChart.data.datasets[3].data.pop();
                // myChart.data.datasets[4].data.pop();
                if(10 <= myChart.data.labels.length)
                {
                    // var newwidth = $('.chartAreaWrapper2').width() - width_add -225;
                    // $('.chartAreaWrapper2').width(newwidth);
                }
            }
        }
        else if(mode == 3 || mode == 5)
        {
            while(myChart2.data.labels.length != 0)
            {
                myChart2.data.labels.pop();
                myChart2.data.datasets[0].data.pop();
                myChart2.data.datasets[1].data.pop();
                myChart2.data.datasets[2].data.pop();
                myChart2.data.datasets[3].data.pop();
                // myChart2.data.datasets[4].data.pop();
                if(10 <= myChart2.data.labels.length)
                {
                    // var newwidth = $('.chartAreaWrapper4').width() - width_add -255;
                    // $('.chartAreaWrapper4').width(newwidth);
                }
                myChart2.update();
            }
        }
        else if(mode == 4)
        {
            while(myChart3.data.labels.length != 0)
            {
                myChart3.data.labels.pop();
                myChart3.data.datasets[0].data.pop();
                myChart3.data.datasets[1].data.pop();
                myChart3.data.datasets[2].data.pop();
                myChart3.data.datasets[3].data.pop();
                // myChart3.data.datasets[4].data.pop();
                if(10 <= myChart3.data.labels.length)
                {
                    // var newwidth = $('.chartAreaWrapper6').width() - width_add -255;
                    // $('.chartAreaWrapper6').width(newwidth);
                }
                myChart3.update();
            }
        }
    }


    //グラフ
    Chart.defaults.global.defaultFontFamily = "Meiryo";

    if(@json($title)  == "作業者データ表示")
    {
        var ctx = document.getElementById("myChart").getContext("2d");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    type: 'bar',
                    label: '前傾回数',
                    data: [],
                    backgroundColor:'rgba(255, 99, 132, 0.2)',
                    borderColor:'rgba(255,99,132,1)',
                    yAxisID: "y-axis-0", // 追加
                    // バーの境界線の太さ(ピクセル単位)
                    borderWidth: 1,
                    // データセットが属するグループのID
                    stack: 1
                },
                {
                    type: 'bar',
                    label: 'ひねり回数',
                    data: [],
                    backgroundColor:'rgba(54, 162, 235, 0.2)',
                    borderColor:'rgba(54, 162, 235, 1)',
                    yAxisID: "y-axis-0", // 追加
                    // バーの境界線の太さ(ピクセル単位)
                    borderWidth: 1,
                    // データセットが属するグループのID
                    // stack 1にすると重ねグラフ
                    stack: 2
                },
                // {
                //     type: 'line',
                //     label: "心拍",
                //     backgroundColor: 'rgb(255, 136, 0)',
                //     borderColor: 'rgb(255, 136, 0)',
                //     yAxisID: "y-axis-2", // 追加
                //     // 線の幅(ピクセル単位)
                //     borderWidth: 2,
                //     // 線のベジェ曲線の張力。直線を描くには0に設定する。
                //     lineTension: 0,
                //     // 点の形状の半径。0に設定すると、点はレンダリングされない。
                //     pointRadius: 3,
                //     // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                //     pointHitRadius: 3,
                //     // 線の下を埋めるかどうか
                //     fill: false,
                //     data: [],
                // },
                {
                    type: 'line',
                    label: "前傾累積",
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    yAxisID: "y-axis-0", // 追加
                    // 線の幅(ピクセル単位)
                    borderWidth: 2,
                    // 線のベジェ曲線の張力。直線を描くには0に設定する。
                    lineTension: 0.2,
                    // 点の形状の半径。0に設定すると、点はレンダリングされない。
                    pointRadius: 3,
                    // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                    pointHitRadius: 3,
                    // 線の下を埋めるかどうか
                    fill: false,
                    data: [],
                },
                {
                    type: 'line',
                    label: "ひねり累積",
                    backgroundColor: 'rgb(54, 162, 235)',
                    borderColor: 'rgb(54, 162, 235)',
                    yAxisID: "y-axis-0", // 追加
                    // 線の幅(ピクセル単位)
                    borderWidth: 2,
                    // 線のベジェ曲線の張力。直線を描くには0に設定する。
                    lineTension: 0.2,
                    // 点の形状の半径。0に設定すると、点はレンダリングされない。
                    pointRadius: 3,
                    // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                    pointHitRadius: 3,
                    // 線の下を埋めるかどうか
                    fill: false,
                    data: [],
                }]
            },
            options: {
                responsive: true,//trueにすると画面の幅に合わせて作図してしまう
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: "top"
                },
                // animation: {
                //     onComplete: function(animation) {
                                // var sourceCanvas = myChart.chart.canvas;
                                // var copyWidth = myChart.scales['y-axis-0'].width - 10;
                                // var copyHeight = myChart.scales['y-axis-0'].height + myChart.scales['y-axis-0'].top + 10;
                                // var targetCtx = document.getElementById("myChartAxis").getContext("2d");
                                // targetCtx.canvas.width = copyWidth;

                                // console.log( "zoom = "+ zoom);
                                // if(zoom == 136|| zoom == 100) //100%
                                // {
                                //     targetCtx.scale(1, 1);
                                //     targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth, copyHeight, 0, 0, copyWidth, copyHeight);
                                //     console.log( "zoom = 100%");
                                // }
                                // else if(zoom == 122|| zoom == 90) //90%
                                // {
                                //     targetCtx.scale(1.1, 1.1);
                                //     targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth, copyHeight-5, 0, 0, copyWidth, copyHeight);
                                //     console.log( "zoom = 90%");
                                // }
                                // else if(zoom == 109|| zoom == 80) //80%
                                // {
                                //     targetCtx.scale(1.2, 1.2);
                                //     targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth, copyHeight-15, 0, 0, copyWidth, copyHeight);
                                //     console.log( "zoom = 80%");
                                // }
                                // else if(zoom == 149|| zoom == 110) //110%
                                // {
                                //     targetCtx.scale(1.1, 1);
                                //     targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth+6, copyHeight+33, 0, 0, copyWidth, copyHeight);
                                //     console.log( "zoom = 110%");
                                // }
                                // else if(zoom == 170|| zoom == 125) //125%
                                // {
                                //     targetCtx.scale(1.1, 1);
                                //     targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth+10, copyHeight+85, 0, 0, copyWidth, copyHeight);
                                //     console.log( "zoom = 125%");
                                // }
                                // else if(zoom == 204 || zoom == 150) //150%
                                // {
                                //     targetCtx.scale(1.1, 1);
                                //     targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth+20, copyHeight+170, 0, 0, copyWidth, copyHeight);
                                //     console.log( "zoom = 150%");
                                // }
                                // else
                                // {
                                //     targetCtx.scale(0.9, 0.9);
                                //     targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth, copyHeight, 0, 0, copyWidth, copyHeight);
                                //     console.log( "zoom = -%");
                                // }

                                //元イメージの座標(50, 50)から幅100高さ50の範囲を使用して、座標(10, 10)の位置に、サイズ200×50でイメージを表示
                                // context.drawImage(img01, 50, 50, 100, 50, 10, 10, 200, 50);

                                // // targetCtx.scale(1.1, 1);
                                // var max = Math.max(Math.max.apply(null,myChart.data.datasets[0].data),Math.max.apply(null,myChart.data.datasets[1].data),Math.max.apply(null,myChart.data.datasets[2].data));
                                // if(max = 999 < max) targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth+30, copyHeight+170, 0, 0, copyWidth, copyHeight);
                                // else targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth+20, copyHeight+170, 0, 0, copyWidth, copyHeight);
                                // // targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth, copyHeight, 0, 0, copyWidth, copyHeight);
                                // var targetCtx2 = document.getElementById("myChartAxis1_2").getContext("2d");
                                // targetCtx2.canvas.width = copyWidth+10;
                                // targetCtx2.drawImage(sourceCanvas, sourceCanvas.width-30, 0, copyWidth+10, copyHeight, 0, 0, copyWidth+10, copyHeight);
                        //     }
                        // },
                scales: {
                    yAxes: [{
                        id: "y-axis-0",   // Y軸のID
                        type: "linear",   // linear固定
                        position: "left", // どちら側に表示される軸か？
                        scaleLabel: {
                                //表示されるy軸の名称について
                                display: true, //表示するか否か
                                labelString: "回数",
                                fontSize: 15
                            },
                        ticks: {
                            beginAtZero: true,
                            suggestedmin: 0,                        // 最小値
                            suggestedMax: 100,                       // 最大値

                        },
                        // stacked: true   //積み上げ棒グラフにする設定
                     // }],
                    },{
                    // yAxes: [{
                        id: "y-axis-2",
                        type: "linear",   // linear固定
                        position: "right",
                        scaleLabel: {
                                //表示されるy軸の名称について
                                display: true, //表示するか否か
                                labelString: "bpm",
                                fontSize: 15
                            },
                        ticks: {
                            beginAtZero: true,
                            suggestedmin: 0,                        // 最小値
                            suggestedMax: 50,                       // 最大値
                        },
                        // stacked: true
                    }],
                    xAxes: [{
                        scaleLabel: {
                                //表示されるy軸の名称について
                                display: true, //表示するか否か
                                labelString: "時間",
                                fontSize: 15
                            },

                        ticks: {
                            // autoSkip: true,    // false にすると全ラベル表示
                            // maxTicksLimit: 10, //値の最大表示数
                            // min: 0,                        // 最小値
                            // max: 50,                       // 最大値



                            stepSize: 5,                   // 軸間隔
                            fontColor: "black",             // 目盛りの色
                            fontSize: 14                   // フォントサイズ
                        }
                    }]
                }

            }
        });
    }

    if(@json($title) == "作業者データ比較")
    {
        var ctx2 = document.getElementById("myChart2").getContext("2d");
        var myChart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    type: 'bar',
                    label: '前傾回数',
                    data: [],
                    backgroundColor:'rgba(255, 99, 132, 0.2)',
                    borderColor:'rgba(255,99,132,1)',
                    yAxisID: "y-axis-0", // 追加
                    // バーの境界線の太さ(ピクセル単位)
                    borderWidth: 1,
                    // データセットが属するグループのID
                    stack: 1
                },
                {
                    type: 'bar',
                    label: 'ひねり回数',
                    data: [],
                    backgroundColor:'rgba(54, 162, 235, 0.2)',
                    borderColor:'rgba(54, 162, 235, 1)',
                    yAxisID: "y-axis-0", // 追加
                    // バーの境界線の太さ(ピクセル単位)
                    borderWidth: 1,
                    // データセットが属するグループのID
                    // stack 1にすると重ねグラフ
                    stack: 2
                },
                // {
                //     type: 'line',
                //     label: "心拍",
                //     backgroundColor: 'rgb(255, 136, 0)',
                //     borderColor: 'rgb(255, 136, 0)',
                //     yAxisID: "y-axis-2", // 追加
                //     // 線の幅(ピクセル単位)
                //     borderWidth: 2,
                //     // 線のベジェ曲線の張力。直線を描くには0に設定する。
                //     lineTension: 0,
                //     // 点の形状の半径。0に設定すると、点はレンダリングされない。
                //     pointRadius: 3,
                //     // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                //     pointHitRadius: 3,
                //     // 線の下を埋めるかどうか
                //     fill: false,
                //     data: [],
                // },
                {
                    type: 'line',
                    label: "前傾累積",
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    yAxisID: "y-axis-0", // 追加
                    // 線の幅(ピクセル単位)
                    borderWidth: 2,
                    // 線のベジェ曲線の張力。直線を描くには0に設定する。
                    lineTension: 0.2,
                    // 点の形状の半径。0に設定すると、点はレンダリングされない。
                    pointRadius: 3,
                    // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                    pointHitRadius: 3,
                    // 線の下を埋めるかどうか
                    fill: false,
                    data: [],
                },
                {
                    type: 'line',
                    label: "ひねり累積",
                    backgroundColor: 'rgb(54, 162, 235)',
                    borderColor: 'rgb(54, 162, 235)',
                    yAxisID: "y-axis-0", // 追加
                    // 線の幅(ピクセル単位)
                    borderWidth: 2,
                    // 線のベジェ曲線の張力。直線を描くには0に設定する。
                    lineTension: 0.2,
                    // 点の形状の半径。0に設定すると、点はレンダリングされない。
                    pointRadius: 3,
                    // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                    pointHitRadius: 3,
                    // 線の下を埋めるかどうか
                    fill: false,
                    data: [],
                }]
            },
            options: {
                responsive: true,//trueにすると画面の幅に合わせて作図してしまう
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: "top"

                },
                scales: {
                    yAxes: [{
                        id: "y-axis-0",   // Y軸のID
                        type: "linear",   // linear固定
                        position: "left", // どちら側に表示される軸か？
                        scaleLabel: {
                                //表示されるy軸の名称について
                                display: true, //表示するか否か
                                labelString: "回数",
                                fontSize: 12
                            },
                        ticks: {
                            beginAtZero: true,
                            suggestedmin: 0,                        // 最小値
                            suggestedMax: 100,                       // 最大値
                        },
                    },{
                        id: "y-axis-2",
                        type: "linear",   // linear固定
                        position: "right",
                        scaleLabel: {
                            //表示されるy軸の名称について
                            display: true, //表示するか否か
                            labelString: "bpm",
                            fontSize: 15
                        },
                        ticks: {
                            beginAtZero: true,
                            suggestedmin: 0,                        // 最小値
                            suggestedMax: 50,                       // 最大値
                        },
                    }],
                    xAxes: [{
                        ticks: {
                            stepSize: 5,                   // 軸間隔
                            fontColor: "black",             // 目盛りの色
                            fontSize: 14                   // フォントサイズ
                        }
                    }]
                }

            }
        });

        var ctx3 = document.getElementById("myChart3").getContext("2d");
        var myChart3 = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    type: 'bar',
                    label: '前傾回数',
                    data: [],
                    backgroundColor:'rgba(255, 99, 132, 0.2)',
                    borderColor:'rgba(255,99,132,1)',
                    yAxisID: "y-axis-0", // 追加
                    // バーの境界線の太さ(ピクセル単位)
                    borderWidth: 1,
                    // データセットが属するグループのID
                    stack: 1
                },
                {
                    type: 'bar',
                    label: 'ひねり回数',
                    data: [],
                    backgroundColor:'rgba(54, 162, 235, 0.2)',
                    borderColor:'rgba(54, 162, 235, 1)',
                    yAxisID: "y-axis-0", // 追加
                    // バーの境界線の太さ(ピクセル単位)
                    borderWidth: 1,
                    // データセットが属するグループのID
                    // stack 1にすると重ねグラフ
                    stack: 2
                },
                // {
                //     type: 'line',
                //     label: "心拍",
                //     backgroundColor: 'rgb(255, 136, 0)',
                //     borderColor: 'rgb(255, 136, 0)',
                //     yAxisID: "y-axis-2", // 追加
                //     // 線の幅(ピクセル単位)
                //     borderWidth: 2,
                //     // 線のベジェ曲線の張力。直線を描くには0に設定する。
                //     lineTension: 0,
                //     // 点の形状の半径。0に設定すると、点はレンダリングされない。
                //     pointRadius: 3,
                //     // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                //     pointHitRadius: 3,
                //     // 線の下を埋めるかどうか
                //     fill: false,
                //     data: [],
                // },
                {
                    type: 'line',
                    label: "前傾累積",
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    yAxisID: "y-axis-0", // 追加
                    // 線の幅(ピクセル単位)
                    borderWidth: 2,
                    // 線のベジェ曲線の張力。直線を描くには0に設定する。
                    lineTension: 0.2,
                    // 点の形状の半径。0に設定すると、点はレンダリングされない。
                    pointRadius: 3,
                    // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                    pointHitRadius: 3,
                    // 線の下を埋めるかどうか
                    fill: false,
                    data: [],
                },
                {
                    type: 'line',
                    label: "ひねり累積",
                    backgroundColor: 'rgb(54, 162, 235)',
                    borderColor: 'rgb(54, 162, 235)',
                    yAxisID: "y-axis-0", // 追加
                    // 線の幅(ピクセル単位)
                    borderWidth: 2,
                    // 線のベジェ曲線の張力。直線を描くには0に設定する。
                    lineTension: 0.2,
                    // 点の形状の半径。0に設定すると、点はレンダリングされない。
                    pointRadius: 3,
                    // マウスオーバー検出のために点半径に追加される半径(ピクセル単位)
                    pointHitRadius: 3,
                    // 線の下を埋めるかどうか
                    fill: false,
                    data: [],
                }
                ]
            },
            options: {
                responsive: true,//trueにすると画面の幅に合わせて作図してしまう
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: "top"

                },
                scales: {
                    yAxes: [{
                        id: "y-axis-0",   // Y軸のID
                        type: "linear",   // linear固定
                        position: "left", // どちら側に表示される軸か？
                        scaleLabel: {
                                //表示されるy軸の名称について
                                display: true, //表示するか否か
                                labelString: "回数",
                                fontSize: 12
                            },
                        ticks: {
                            beginAtZero: true,
                            suggestedmin: 0,                        // 最小値
                            suggestedMax: 100,                       // 最大値
                        },
                    },{
                        id: "y-axis-2",
                        type: "linear",   // linear固定
                        position: "right",
                        scaleLabel: {
                            //表示されるy軸の名称について
                            display: true, //表示するか否か
                            labelString: "bpm",
                            fontSize: 15
                        },
                        ticks: {
                            beginAtZero: true,
                            suggestedmin: 0,                        // 最小値
                            suggestedMax: 50,                       // 最大値
                        },
                    }],
                    xAxes: [{

                        ticks: {
                            // autoSkip: true,    // false にすると全ラベル表示
                            // maxTicksLimit: 10, //値の最大表示数
                            // min: 0,                        // 最小値
                            // max: 50,                       // 最大値
                            stepSize: 5,                   // 軸間隔
                            fontColor: "black",             // 目盛りの色
                            fontSize: 14                   // フォントサイズ
                        }
                    }]
                }

            }
        });
    }
    </script>

    </body>


</html>
