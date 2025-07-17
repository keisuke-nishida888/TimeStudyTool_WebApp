@extends('layouts.parent')


@section('content')
<script src="/js/cost_ctrl.js"></script>

<!-- <a href="C:\Users\jibiki\Desktop\backpain\public" download="cat.jpg" class="demo_btn">現状コストダウンロード</a>
<a href="https://www.kipure.com/shared/api/my/dl_img/?img=/img/demo/cat/001.jpg" download="cat.jpg" class="demo_btn">導入コストダウンロード</a> -->


<div class="allcont">
<!-- アップロード -->
<form id = "form_currentfile"  method = "post"  enctype="multipart/form-data">
@csrf
  <input type="file" id = "currentfile_file" name="currentfile_file" accept=".xlsx" required>
  <input type="hidden"  name="fname" value="currentfile">
  @if(isset($data[0]['id']))
    <input id="id" type="hidden"  name="id" value="{{$data[0]['id']}}">
  @endif
</form>
<input type="image" id="btn_currentfile"  src="image/img_CurrentFile_upload.png" alt="現状コストアップロード" onclick="file_upload(this.id)" border="0">

<form id = "form_introfile"  method = "post" enctype="multipart/form-data">
@csrf
  <input type="file" id = "introfile_file"  name="introfile_file" accept=".xlsx" required>
  <input type="hidden" name="fname" value="introfile">
  @if(isset($data[0]['id']))
    <input type="hidden" name="id" value="{{$data[0]['id']}}">
  @endif
</form>
<input type="image" id="btn_introfile"  src="image/img_IntroFile_upload.png" alt="導入コストアップロード" onclick="file_upload(this.id)" border="0">



<!-- ダウンロード -->
<form id = "form_2currentfile"  method = "post"  enctype="multipart/form-data">
@csrf
  <input type="hidden" name="fname" value="currentfile">
  @if(isset($data[0]['id']))
    <input type="hidden" name="id" value="{{$data[0]['id']}}">
  @endif
</form>
  <input type="image" id="btn_2currentfile" src="image/img_CurrentFile_download.png" alt="導入コストダウンロード" border="0" onclick="file_download(this.id)">

<form id = "form_2introfile"  method = "post"  enctype="multipart/form-data">
@csrf
  <input type="hidden" name="fname" value="introfile">
  @if(isset($data[0]['id']))
    <input  type="hidden"  name="id" value="{{$data[0]['id']}}">
  @endif
</form>
  <input type="image" id="btn_2introfile" src="image/img_IntroFile_download.png" alt="導入コストダウンロード" border="0" onclick="file_download(this.id)">


<div>


@endsection