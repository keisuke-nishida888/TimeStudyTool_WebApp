@extends('layouts.parent')

@section('content')

<div class="allcont">
<input type="image" id="btn_fixwearable_pre"  src="image/img_fix.png" alt="修正" onclick="VisibleChange(this.id)" border="0">

<!-- キャンセルボタン -->
<input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_wearablefix')" border="0">


<!-- キャンセルボタン -->
<form  action = '/wearable_fix' name = "fix_index"  method = "post">
    @csrf
    @if(isset($adddata[0]['id']))
        <input type="hidden" name="id" value="{{$adddata[0]['id']}}">
    @else
        <input type="hidden" name="id" value="{{old('id')}}">
    @endif
    <input type="image" id="btn_cxl_do" style="visibility:hidden;"  src="image/img_yes.png" alt="はい" border="0">
</form>


<form method="POST" id="form_wearablefix" action="wearable_fixctrl" name ="form_wearablefix" autocomplete="off">
@csrf
    <div class="container">
        <table class ="tb">
        <tr></tr>
            <tr>
                <!-- No -->
                <td><label for="id">{{ __('No') }}</label></td>
                <td>
                    @if(isset($adddata[0]['id']))
                        <input type="text" value={{$adddata[0]['id']}} disabled>
                        <input id="id" type="hidden" name="id" value={{$adddata[0]['id']}}>
                    @else
                        <input type="text" value="{{old('id')}}" disabled>
                        <input id="id" type="hidden" name="id" value="{{old('id')}}">
                    @endif
                   
                </td>
            </tr>


            <tr>
                <!-- デバイス名 -->
                <td><label for="devicename">{{ __('センサー名') }}</label></td>

                <td>   
                    @if(isset($adddata[0]['devicename']))
                        <input id="devicename" type="text" maxlength="20" name="devicename" value="{{\Illuminate\Support\Str::of($adddata[0]['devicename'])->rtrim()}}" autofocus>
                
                    @else
                        <input id="devicename" type="text" maxlength="20" name="devicename" value="{{old('devicename')}}" autofocus>
                    @endif
                    
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_devicename"></nobr>
                        </span>
                </td>
            </tr>     
             
            {{-- <tr>
                <!-- 施設名 -->
                <td><label for="password">{{ __('施設名') }}</label></td>

                <td>
                    @if(isset($adddata[0]['facility']))
                        <input type="text" value="{{$adddata[0]['facility']}}" disabled>
                        <input id="facility" type="hidden"  name="facility" value="{{$adddata[0]['facility']}}">
                    @else
                        <input type="text" value="{{old('facility')}}" disabled>
                        <input id="facility" type="hidden"  name="facility" value="{{old('facility')}}">
                    @endif

                </td>
            </tr>      
            <tr>
                <!-- 介助者名 -->
                <td><label for="helpername">{{ __('介助者名') }}</label></td>
                <td>
                    @if(isset($adddata[0]['helpername']))
                        <input type="text" value="{{$adddata[0]['helpername']}}" disabled>
                        <input id="helpername" type="hidden" name="helpername" value="{{$adddata[0]['helpername']}}">                            
                    
                    @else
                        <input type="text" value="{{old('helpername')}}" disabled>
                        <input id="helpername" type="hidden" name="helpername" value="{{old('helpername')}}">
                    @endif

                </td>
            </tr>      
            <tr> --}}

                <!-- UserID -->
                <td><label for="userid">{{ __('UserID') }}</label></td>
                <td>
                    @if(isset($adddata[0]['userid']))
                        <input id="userid" type="text" maxlength="40" name="userid" value="{{\Illuminate\Support\Str::of($adddata[0]['userid'])->rtrim()}}">
                    @else
                        <input id="userid" type="text" maxlength="40" name="userid" value="{{ old('userid') }}">
                    @endif 
                        
                    <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_userid"></nobr>
                    </span>
                </td>
            </tr>      
            <tr>

                <!-- パスワード -->
                <td><label for="passwd">{{ __('passwd') }}</label></td>
                <td>
                    @if(isset($adddata[0]['passwd']))
                        <input id="passwd" type="text" maxlength="40" name="passwd" value="{{\Illuminate\Support\Str::of($adddata[0]['passwd'])->rtrim()}}">
                    @else
                        <input id="passwd" type="text" maxlength="40" name="passwd" value="{{ old('passwd') }}">
                    @endif 

                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_passwd"></nobr>
                        </span>
                </td>
            </tr>      

            <tr>
                <!-- Client ID -->
                <td><label for="clientid">{{ __('Client ID') }}</label></td>

                <td>
                    @if(isset($adddata[0]['clientid']))
                        <input id="clientid" type="text" maxlength="20" name="clientid" value="{{\Illuminate\Support\Str::of($adddata[0]['clientid'])->rtrim()}}">
                    @else
                        <input id="clientid" type="text" maxlength="20" name="clientid" value="{{ old('clientid') }}">
                    @endif

                    <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_clientid"></nobr>
                            </span>
                </td>
            </tr>      
            <tr>
                <!-- Client Secret -->
                <td><label for="clientsc">{{ __('Client Secret') }}</label></td>
                <td>
                        @if(isset($adddata[0]['clientsc']))
                            <input id="clientsc" type="text" maxlength="40" name="clientsc" value="{{\Illuminate\Support\Str::of($adddata[0]['clientsc'])->rtrim()}}">
                        @else
                            <input id="clientsc" type="text" maxlength="40" name="clientsc" value="{{ old('clientsc') }}">
                        @endif

                        <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_clientsc"></nobr>
                            </span>
                </td>
            </tr>      
            <tr>
                <!-- Authorization -->
                <td><label for="auth">{{ __('Authorization') }}</label></td>
                <td>
                        @if(isset($adddata[0]['auth']))                                
                            <input id="auth" type="text" maxlength="60" name="auth" value="{{\Illuminate\Support\Str::of($adddata[0]['auth'])->rtrim()}}">
                        @else
                            <input id="auth" type="text" maxlength="60" name="auth" value="{{ old('auth') }}">
                        @endif
                        <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_auth"></nobr>
                            </span>
                </td> 
            </tr>

            </table>
        </div>
    <!-- 修正ボタン -->
    <input id="btn_fixwearable"  type="image" src="image/img_yes.png" alt="修正" border="0">
    

</form>
</div>
@endsection
