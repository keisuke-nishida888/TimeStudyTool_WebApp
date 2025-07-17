@extends('layouts.parent')

@section('content')
<script src="/js/loginuser.js"></script>

<div class="allcont">
    <input type="image" id="btn_fixuser_pre"  src="image/img_fix.png" alt="修正" onclick="VisibleChange(this.id)" border="0">

    <!-- キャンセルボタン -->
    <input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_userfix')" border="0">
    <!-- キャンセルボタン -->
    <form  action = '/loginuser_fix' name = "fix_index"  method = "post">
    @csrf
    @if(isset($adddata[0]['id']))
        <input type="hidden" name="userid" value="{{$adddata[0]['id']}}">
    @else
        <input type="hidden" name="userid" value="{{old('id')}}">
    @endif
    <input type="image" id="btn_cxl_do" style="visibility:hidden;"  src="image/img_yes.png" alt="はい" border="0">
    </form>

            
<form method="POST" id="form_userfix" action="loginuser_fixctrl" name ="form_userfix" autocomplete="off">
@csrf
    <div class="container">
        <table class ="tb"> 
        <tr></tr>
            <tr>
                <!-- No -->
                <td><label for="id">{{ __('No') }}</label></td>
                <td>
                    @if(isset($adddata[0]['id']))
                        <input type="text" value="{{$adddata[0]['id']}}" disabled>
                        <input id="id" type="hidden" name="id" value="{{$adddata[0]['id']}}">
                    @else
                        <input type="text" value="{{ old('id') }}" disabled>
                        <input id="id" type="hidden" name="id" value="{{ old('id') }}">
                    @endif
                </td>
            </tr>      
            <tr>
                <!-- ユーザ名 -->
                <td><label for="username">{{ __('ユーザ名') }}</label></td>
                <td>
                        @if(isset($adddata[0]['username']))
                            <input id="username" type="text" name="username" value="{{\Illuminate\Support\Str::of($adddata[0]['username'])->rtrim()}}" maxlength="20" autofocus>                     
                        @else
                            <input id="username" type="text" name="username" value="{{ old('username') }}" maxlength="20" autofocus>
                        @endif
                        
                        <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_username"></nobr>
                        </span>
                </td>
            </tr>      
            <tr>
                <!-- パスワード入力 -->
                <td><label for="password">{{ __('パスワード') }}</label></td>
                <td>
                        <input id="pass" type="password" name="pass" maxlength="20" autocomplete="new-password">

                        <span class="invalid-feedback validate" role="alert">
                     <nobr id="err_pass"></nobr>
                    </span>
                </td>
            </tr>      
            <tr>  
                <!-- パスワード再入力 -->
                <td><label for="password-confirm">{{ __('再入力') }}</label></td>
                <td>
                    <input id="password-confirm" type="password" name="pass_confirmation" maxlength="20" autocomplete="new-password">
                </td>
            </tr>      
            <tr> 



                <!-- 権限 -->
                <td><label for="authority">{{ __('権限') }}</label></td>
                <td>
                    <select  id="authority"  name="authority" value="{{ old('authority') }}" onchange="auth_change()">
                        @foreach($code as $val)
                            @if($val['codeno']==1)
                                @if(isset($adddata))
                                    @foreach($adddata as $addval)
                                        @if($val['dispno']==1 && $val['value']==1);
                                        @elseif(intval($addval['authority']) ==$val['value'])
                                            <option value="{{$val['value']}}" selected>{{$val['selectname']}}</option>
                                        @elseif(old('authority') == $val['value'])
                                            <option value="{{$val['value']}}" selected>{{$val['selectname']}}</option>
                                        @else
                                            <option value="{{$val['value']}}">{{$val['selectname']}}</option>
                                        @endif
                                    @endforeach
                                @else
                                    @if($val['dispno']==1 && $val['value']==1);
                                    @elseif(old('authority') == $val['value'])
                                        <option value="{{$val['value']}}" selected>{{$val['selectname']}}</option>
                                    @else
                                        <option value="{{$val['value']}}">{{$val['selectname']}}</option>
                                    @endif
                                @endif
                            @endif                                                                              
                        @endforeach
                    </select>
                    <span class="invalid-feedback validate" role="alert">
                    <nobr id="err_authority"></nobr>
                </span>
                </td>
            </tr>


                <!-- 権限が施設ユーザのときのみ、表示する -->
                <!-- 施設 -->
                @if(isset($adddata[0]['authority']))
                    @if($adddata[0]['authority'] == 3)
                        <tr id="facilityno_div" style="visibility:visible;">
                    @else
                        <tr id="facilityno_div" style="visibility:collapse;">
                    @endif
                @else
                    @if(old('authority') == 3)
                        <tr id="facilityno_div" style="visibility:visible;">
                    @else
                        <tr id="facilityno_div" style="visibility:collapse;">
                    @endif
                @endif
                <td><label for="facilityno">{{ __('施設') }}</label></td>
                <td>
                    <select  id="facilityno" name="facilityno" value="{{ old('facilityno') }}">
                    {{--<option value="0">-</option>--}}
                            @foreach($facility as $val)
                                @if(isset($adddata))
                                    @foreach($adddata as $addval)
                                        @if(intval($addval['facilityno']) == $val['id'])
                                            <option value={{$val['id']}} selected>{{$val['facility']}}</option>
                                        @else
                                            <option value={{$val['id']}}>{{$val['facility']}}</option>
                                        @endif
                                    @endforeach
                                @else
                                    @if(old('facilityno') == $val['id'])
                                        <option value={{$val['id']}} selected>{{$val['facility']}}</option>
                                    @else
                                        <option value={{$val['id']}}>{{$val['facility']}}</option>
                                    @endif
                                @endif
                            @endforeach
                            --}}
                            </select>

                            <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_facilityno"></nobr>
                    </span>
                </td>
            </tr>
            <tr>
                <!-- ポリシー権限 -->
                <td><label for="policyflag">{{ __('プライバシーポリシーの承諾有無') }}</label></td>
                <td>
                    @if(isset($adddata[0]['policyflag']))
                        @if($adddata[0]['policyflag'] == 0)
                            <input type="text" value="未承諾" disabled>
                        @elseif($adddata[0]['policyflag'] == 1)
                            <input type="text" value="承諾済み" disabled>
                        @else
                            <input type="text" value="エラー" disabled>
                        @endif
                        <input id="policyflag" type="hidden" name="policyflag" value="{{$adddata[0]['policyflag']}}">
                    @else
                        <input type="text" value="{{ old('policyflag') }}" disabled>
                        <input id="policyflag" type="hidden" name="policyflag" value="{{ old('policyflag') }}">
                    @endif
                </td>
            </tr>
        </table>
    

        </div>

    <!-- 修正ボタン -->
    <input id="btn_fixuser"  type="image" src="image/img_yes.png" alt="修正" border="0">
    <!-- <button type="submit" class="btn btn-primary">修正</button> -->

</form>
</div>
@endsection
