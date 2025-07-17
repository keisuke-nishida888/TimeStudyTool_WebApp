@extends('layouts.parent')

@section('content')
<div class="allcont">
<input type="image" id="btn_addwearable_pre"  src="image/img_add.png" alt="追加" onclick="VisibleChange(this.id)" border="0">

<!-- キャンセルボタン -->
<input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_wearableadd')" border="0">


<form  action = '/wearable_fix' name = "fix_index"  method = "post">
@csrf
<input id="targetid" type="hidden" name="id" value="">
<input type="hidden" name="addmess" value="追加しました。">
@csrf
</form>



<form method="POST" id="form_wearableadd" action="wearable_addctrl" name = "form_wearableadd"  autocomplete="off">
@csrf
    <div class="container">
        <table class ="tb">
        <tr></tr>
            <tr>
                <!-- No -->
                <td><label for="id">{{ __('No') }}</label></td>
                <td>
                    <input  type="text" value="" disabled>
                    <input id="id" type="hidden" name="id" value="">
                </td>
            </tr>      
            <tr>
                <!-- デバイス名 -->
                <td><label for="devicename">{{ __('センサー名') }}</label></td>
                <td>
                    <input id="devicename" type="text" name="devicename" value="{{ old('devicename') }}" maxlength="20" autofocus>
      
                        <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_devicename"></nobr>
                        </span>
                </td>                                
            </tr>
            <tr>
                <!-- UserID -->
                <td><label for="userid">{{ __('UserID') }}</label></td>
                <td>
                    <input id="userid" type="text"  name="userid" value="{{ old('userid') }}" maxlength="40">
                    <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_userid"></nobr>
                    </span>
                </td>                                
            </tr>
            <tr>
                    <!-- パスワード -->
                    <td> <label for="passwd">{{ __('passwd') }}</label></td>
                    <td>
                        <input id="passwd" type="text" maxlength="40" name="passwd">
                        <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_passwd"></nobr>
                        </span>
                    </td>                                
            </tr>


                <tr>
                        <!-- Client ID -->
                        <td><label for="clientid">{{ __('Client ID') }}</label></td>

                        <td>
                            <input id="clientid" type="text" maxlength="20" name="clientid" value="{{ old('clientid') }}">
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_clientid"></nobr>
                            </span>
                        </td>                                
                </tr>
                <tr>
                        <!-- Client Secret -->
                        <td><label for="clientsc">{{ __('Client Secret') }}</label></td>

                        <td>
                            <input id="clientsc" type="text" maxlength="40" name="clientsc" value="{{ old('clientsc') }}">
          
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_clientsc"></nobr>
                            </span>
                        </td>                                
                </tr>
                <tr>
                        <!-- Authorization -->
                        <td> <label for="auth">{{ __('Authorization') }}</label></td>
                        <td>
                            <input id="auth" type="text" maxlength="60" name="auth" value="{{ old('auth') }}">
                 
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_auth"></nobr>
                            </span>
                        </td>                                
                </tr>

    </table>

    </div>
    <!-- 追加ボタン -->
    <input id="btn_addwearable" type="image" src="image/img_yes.png" alt="追加" border="0">


    </form>
    </div>
@endsection
