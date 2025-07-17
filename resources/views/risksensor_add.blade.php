@extends('layouts.parent')

@section('content')

<div class="allcont">
<input type="image" id="btn_addrisksensor_pre"  src="image/img_add.png" alt="追加" onclick="VisibleChange(this.id)" border="0">

<!-- キャンセルボタン -->
<input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_risksensoradd')" border="0">
           

<form  action = '/risksensor_fix' name = "fix_index"  method = "post">
@csrf
<input id="targetid" type="hidden" name="id" value="">
<input type="hidden" name="addmess" value="追加しました。">
</form>           

<form method="POST" action="risksensor_addctrl" id="form_risksensoradd" name = "form_risksensoradd" autocomplete="off" >
@csrf              

        <div class="container"> 
            <table class ="tb">
            <tr></tr>
                <tr>
                        <!-- No -->
                        <td><label for="id">{{ __('No') }}</label></td>
                        <td>
                            <input  type="text" value="" disabled>
                            <input id="id" type="hidden"  name="id" value="">
                        </td>                                
                </tr>
                <tr>
                        <!-- デバイス名 -->
                        <td><label for="devicename">{{ __('デバイス名') }}</label></td>
                        <td>
                            <input id="devicename" type="text" maxlength="3" name="devicename" value="{{ old('devicename') }}" autofocus>
                            <!-- @if($errors->has('devicename'))
                                <span class="invalid-feedback validate" role="alert">
                                {{$errors->first('devicename')}}
                                </span>
                            @endif -->
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_devicename"></nobr>
                            </span>
                        </td>
                </tr>
            </table>
        </div>
    <!-- 追加ボタン -->
    <input id="btn_addrisksensor"  type="image" src="image/img_yes.png" alt="追加" border="0">


</form>
</div>
@endsection
