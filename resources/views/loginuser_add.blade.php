@extends('layouts.parent')

@section('content')
<script src="/js/loginuser.js"></script>

<div class="allcont">
<input type="image" id="btn_adduser_pre" src="image/img_add.png" alt="追加" onclick="VisibleChange(this.id)" border="0">

<!-- キャンセルボタン -->
<input type="image" id="btn_cxl" src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_useradd')" border="0">

<form  action = '/loginuser_fix' name = "fix_index"  method = "post">
@csrf
<input id="targetid" type="hidden" name="userid" value="">
<input type="hidden" name="addmess" value="追加しました。">
</form>



<form method="POST" id="form_useradd" action="loginuser_addctrl" name = "form_useradd"  autocomplete="off">
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
            <!-- ユーザ名 -->
            <td><label for="username">{{ __('ユーザ名') }}</label></td>
            <td>
                <input id="username" type="text" name="username" value="{{ old('username') }}" maxlength="20" autofocus>

                <span class="invalid-feedback validate" role="alert">
                    <nobr id="err_username"></nobr>
                </span>
            </td>
        </tr>      
        <tr>
            <!-- パスワード入力 -->
            <td><label for="password">{{ __('パスワード') }}</label></td>
            <td>
                <input id="pass" type="password" name="pass" maxlength="20">

                    <span class="invalid-feedback validate" role="alert">
                     <nobr id="err_pass"></nobr>
                    </span>
            </td>
        </tr>      
        <tr>
            
            <!-- パスワード再入力 -->
            
            <td><label for="password-confirm">{{ __('再入力') }}</label></td>
            <td>
                <input id="password-confirm" type="password" name="pass_confirmation" maxlength="20">      
            </td>
        </tr>      
        <tr>

            <!-- 権限 -->
            <td><label for="authority">{{ __('権限') }}</label></td>
            <td>
                <select  id="authority" name="authority" value="{{old('authority')}}" onchange="auth_change()">
                    @foreach($code as $val)
                        @if($val['codeno']==1)
                            <!-- [一般ユーザ]は非表示 -->
                            @if($val['dispno']==1 && $val['value']==1);
                            @elseif(old('authority') == $val['value'])
                                <option value="{{$val['value']}}" selected>{{$val['selectname']}}</option>
                            @else
                                <option value="{{$val['value']}}">{{$val['selectname']}}</option>
                            @endif
                        @endif
                    @endforeach
                </select>
                <span class="invalid-feedback validate" role="alert">
                    <nobr id="err_authority"></nobr>
                </span>
            </td>
        </tr>
        <tr id="facilityno_div" style="visibility:collapse;">


            <!-- 権限が施設ユーザのときのみ、表示する -->
            <!-- 施設 -->
           
                <td><label for="facilityno">{{ __('施設') }}</label></td>
                <td>
                    <select  id="facilityno" name="facilityno" value="{{old('facilityno')}}">                        
                    {{--<option value="0">-</option>--}}
                        @foreach($facility as $val)
                            <option value={{$val['id']}}>{{$val['facility']}}</option>
                        @endforeach
                    </select>

                    <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_facilityno"></nobr>
                    </span>
                    </td>
        </tr>

        </table>
        </div>

        <!-- 追加ボタン -->
        <input id="btn_adduser" type="image" src="image/img_yes.png" alt="追加" border="0">
           
        </form>
        </div>
@endsection
