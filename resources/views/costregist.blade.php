@extends('layouts.parent')


@section('content')

<script src="/js/costregist.js"></script>

<div class="allcont">
<form id = "form_currentfile"  method = "post"  enctype="multipart/form-data">
@csrf
  <input type="file" id = "currentfile_file" name="currentfile_file" accept=".xlsx">
  <input id="currentfile" type="hidden"  name="fname" value="currentfile">

</form>
  <input type="image" id="btn_currentfile"  src="image/img_CurrentFile_upload.png" alt="現状コストアップロード" onclick="file_upload(this.id)" border="0">

<form id = "form_introfile"  method = "post" enctype="multipart/form-data">
@csrf
  <input type="file" id = "introfile_file"  name="introfile_file" accept=".xlsx">
  <input id="introfile" type="hidden" name="fname" value="introfile">

</form>

<input type="image" id="btn_introfile"  src="image/img_IntroFile_upload.png" alt="導入コストアップロード" onclick="file_upload(this.id)" border="0">
</div>
@endsection
