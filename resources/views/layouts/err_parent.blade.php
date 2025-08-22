<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name=”viewport” content=”width=device-width,initial-scale=1.0″>
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <title>Time Study Tool</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <!-- Styles -->
    <!-- //assetを使うとpublicフォルダ内のリソースを読み込めるようになる -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="shortcut icon" href="{{ asset('/image/img_logo1.ico') }}">
    <script src="{{ asset('/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('/js/Chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <!-- TimelineプラグインCDN -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-timeline@4.2.0/dist/chartjs-chart-timeline.umd.min.js"></script>
</head>

<body>
<div id = "bar_div">
        <!-- タイトルバー -->
        <img id = "img_bar" src="image/img_bar.png" alt="" >
        <!-- タイトル -->
        <nobr id="title">エラー</nobr>
        <!-- ロゴ -->
        <a  href="{{ url('/mainmenu') }}"> <img id="img_logo" src="image/img_logo3.png" alt="JCLS" border="0"> </a>
        <!-- ログアウト -->
        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <img id="btn_logout" src="image/img_logout.png" alt="ログアウト" >
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

@yield('content')
</div>
</body>
</html>
