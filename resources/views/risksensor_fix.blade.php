@extends('layouts.parent')

@section('content')

<div class="allcont">
<input type="image" id="btn_fixrisksensor_pre"  src="image/img_fix.png" alt="修正" onclick="VisibleChange(this.id)" border="0">

<!-- キャンセルボタン -->
<form  action = '/risksensor_fix' name = "fix_index"  method = "post">
@csrf
@if(isset($data[0]['id']))
    <input id="id" type="hidden" name="id" value="{{$data[0]['id']}}">
@else
    <input id="id" type="hidden" name="id" value="{{old('id')}}">
@endif
<input type="image" id="btn_cxl_do" style="visibility:hidden;"  src="image/img_yes.png" alt="はい" border="0">
</form>

<input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_risksensorfix')" border="0">


<form method="POST" id="form_risksensorfix" action="risksensor_fixctrl" name = "form_risksensorfix"  autocomplete="off">
@csrf

    <div class="container">
        <table class ="tb">
        <tr></tr>
                <tr>             
                    <!-- No -->
                    <td><label for="id">{{ __('No') }}</label></td>
                    <td>
                        @if(isset($data[0]['id']))
                            <input  type="text" value="{{$data[0]['id']}}" disabled>
                            <input id="id" type="hidden" name="id" value="{{$data[0]['id']}}">
                        @else
                            <input  type="text" value="{{old('id')}}" disabled>
                            <input id="id" type="hidden" name="id" value="{{old('id')}}">
                        @endif
                    </td>
                </tr>

                <tr>
                        <!-- デバイス名 -->
                        <td><label for="devicename">{{ __('デバイス名') }}</label></td>
                        <td>
                            @if(isset($data[0]['devicename']))
                                <input id="devicename" type="text" maxlength="3" name="devicename" value="{{\Illuminate\Support\Str::of($data[0]['devicename'])->rtrim()}}"  autofocus>
                            @else
                                <input id="devicename" type="text" maxlength="3" name="devicename" value="{{old('devicename')}}" autofocus>
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_devicename"></nobr>
                            </span>
                        </td>
                </tr>
                <tr> 
                        
                        <!-- 施設名 -->
                        <!-- ログインしているユーザから取得？ -->
                        <td><label for="facility">{{ __('施設名') }}</label></td>
                        <td>
                            @if(isset($data[0]['facility']))
                                <input  type="text" value={{$data[0]['facility']}} disabled>
                                <input id="facility" type="hidden"  name="facility" value={{$data[0]['facility']}}>
                            @else
                                <input  type="text" value="{{old('facility')}}" disabled>
                                <input id="facility" type="hidden" name="facility" value="{{old('facility')}}">
                            @endif

                        </td>
                </tr>
                <tr>       
                        <!-- 介助者名 -->
                        <td><label for="helpername">{{ __('介助者名') }}</label></td>
                        <td>
                            @if(isset($data[0]['helpername']))
                                <input  type="text" value={{$data[0]['helpername']}} disabled>
                                <input id="helpername" type="hidden" name="helpername" value={{$data[0]['helpername']}}>
                            @else
                                <input  type="text" value="{{old('helpername')}}" disabled>
                                <input id="helpername" type="hidden" name="helpername" value="{{old('helpername')}}">
                   
                            @endif
                        </td>
                </tr>
        </table>
    </div>
    <!-- 追加ボタン -->
    <input id="btn_fixrisksensor" type="image" src="image/img_yes.png" alt="修正" border="0">


</form>
</div>
@endsection
