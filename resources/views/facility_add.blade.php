@extends('layouts.parent')

@section('content')
<script src="/js/facility.js"></script>

<div class="allcont">
<input type="image" id="btn_addfacility_pre" src="image/img_add.png" alt="追加" onclick="VisibleChange(this.id)" border="0" >

<!-- キャンセルボタン -->
<input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_facilityadd')" border="0" >


<form  action = '/facility_fix' name = "fix_index"  method = "post">
@csrf
<input id="targetid" type="hidden" name="id" value="">
<input type="hidden" name="addmess" value="追加しました。">
</form>  

            
<form method="POST" action="facility_addctrl" id="form_facilityadd" name = "form_facilityadd" enctype="multipart/form-data"  autocomplete="off">
@csrf
                     
    <div class="container_facil">
                    <table  class="facil_table1">
                    <!-- 施設概要 -->
                    <tr><td  colspan="3"><label class = "lb">{{ __('施設概要') }}</label></td></tr>

                    <!-- No -->
                    <tr>
                        <td><label for="id">{{ __('No') }}</label></td>
                        <td>
                            <input  type="text" value="" disabled>
                            <input id="id" type="hidden" name="id" value="">
                        </td>                               
                    </tr>

                        <!-- 施設名 -->
                        <!-- ログインしているユーザから取得？ -->
                    <tr>
                        <td> <label for="facility">{{ __('施設名') }}</label></td>
                        <td>
                            <input id="facility" type="text" maxlength="20" name="facility" value="{{ old('facility') }}"  autofocus>
                        
                            <!-- @if($errors->has('facility'))
                                <span class="invalid-feedback validate" role="alert">
                                {{$errors->first('facility')}}
                                </span>
                            @endif -->

                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_facility"></nobr>
                            </span>

                        </td>
                    </tr>

                        
                        <!-- 施設区分 -->
                        <tr>
                            <td><label for="pass">{{ __('施設区分') }}</label></td>
                            <td>
                                <select  id="pass"  name="pass" value="{{ old('pass') }}">
                                    @foreach($code as $val)
                                        @if($val['codeno']==2) <option value={{$val['value']}}>{{$val['selectname']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <!-- @if($errors->has('pass'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('pass')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_pass"></nobr>
                                </span>
                            </td>
                        </tr>

                        <!-- 住所 -->
                        <tr>
                            <td><label for="address">{{ __('住所') }}</label></td>
                            <td>
                                <input id="address" type="text" maxlength="100" name="address" value="{{ old('address') }}">

                                <!-- @if($errors->has('address'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('address')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_address"></nobr>
                                </span>
                            </td>
                        </tr>

                        

                        <!-- 電話番号 -->
                        <tr>
                            <td><label for="tel">{{ __('電話番号(ハイフンなし)') }}</label></td>
                            <td><input id="tel" type="text" maxlength="20" name="tel" value="{{ old('tel') }}">
                                <!-- @if($errors->has('tel'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('tel')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_tel"></nobr>
                                </span>
                            </td>
                        </tr>


                        <!-- メールアドレス -->
                        <tr>
                            <td><label for="mail">{{ __('メールアドレス') }}</label></td>

                            <td>
                                <!-- <input id="mail" type="email" maxlength="40" name="mail" value="{{ old('mail') }}"> -->
                                <input id="mail" type="text" maxlength="40" name="mail" value="{{ old('mail') }}">
                                <!-- @if($errors->has('mail'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('mail')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_mail"></nobr>
                                </span>
                            </td>
                        </tr>

                        <!-- 2021.05.18　追加 -->


                        <!-- アンケートサイト -->
                        <tr>
                            <td><label for="url">{{ __('アンケートサイトURL') }}</label></td>

                            <td>
                                <input id="url" type="text" maxlength   "256" name="url" value="{{ old('url') }}">
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_url"></nobr>
                                </span>
                            </td>
                        </tr>
                        <!-- ここまで -->


                       
                        <!-- 定員 -->
                        <tr>
                            <td><label for="item1">{{ __('定員') }}</label></td>

                            <td><input id="item1" type="text" maxlength="10" name="item1" value="{{ old('item1') }}">人
                                <!-- @if($errors->has('item1'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item1')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item1"></nobr>
                                </span>
                            </td>
                        </tr>
                        

                        <!-- 施設区分Ⅱ -->

                            <!-- ユニットケア -->
                            <tr>
                                <td><label for="item2">{{ __('施設区分Ⅱ') }}</label></td>
                                
                                <td><label for="item2">{{ __('ユニットケア(一ユニット)') }}</label>
                                <input id="item2" type="text" name="item2" maxlength="10"  value="{{ old('item2')}}">名
                                    <!-- @if($errors->has('item2'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item2')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item2"></nobr>
                                    </span>
                                </td>

                            <!-- 従来型 -->
                                <td><label for="item3">{{ __('従来型(一フロア)') }}</label>
                                <input id="item3" type="text"  name="item3" maxlength="10"  value="{{ old('item3')}}">名
                                    <!-- @if($errors->has('item3'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item3')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item3"></nobr>
                                    </span>
                                </td>
                            </tr>

                        <!-- 職員数 -->
                                <tr>
                                    <td><label for="item4">{{ __('職員数') }}</label></td>

                                    <td><input id="item4" type="text" maxlength="10" name="item4" value="{{ old('item4') }}">名
                                        <!-- @if($errors->has('item4'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item4')}}
                                        </span>
                                        @endif -->
                                        <span class="invalid-feedback validate" role="alert">
                                            <nobr id="err_item4"></nobr>
                                        </span>
                                    </td>
                                </tr>


                        <!-- 職員数Ⅱ -->
                            <!-- ユニットケア -->
                                <tr>
                                    <td><label for="item5">{{ __('職員数Ⅱ') }}</label></td>

                                    <td><label for="item5">ユニットケア(一ユニット)</label>
                                    <input id="item5" type="text" maxlength="10"  name="item5" value="{{ old('item5') }}">名
                                        <!-- @if($errors->has('item5'))
                                            <span class="invalid-feedback validate" role="alert">
                                            {{$errors->first('item5')}}
                                            </span>
                                        @endif -->
                                        <span class="invalid-feedback validate" role="alert">
                                            <nobr id="err_item5"></nobr>
                                        </span>
                                    </td>
                            <!-- 従来型 -->
                                <td><label for="item6">{{ __('従来型(一フロア)') }}</label>
                                <input id="item6" type="text" maxlength="10"  name="item6" value="{{ old('item6') }}">名
                                        <!-- @if($errors->has('item6'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item6')}}
                                        </span>
                                        @endif -->
                                        <span class="invalid-feedback validate" role="alert">
                                            <nobr id="err_item6"></nobr>
                                        </span>
                                    </td>
                                </tr>


                        <!-- 夜勤人数/月 -->
                            <tr>
                                <td><label for="item7">{{ __('夜勤人数/日') }}</label></td>
                                <td><input id="item7" type="text" maxlength="10"  name="item7" value="{{ old('item7') }}">人/日
                                    <!-- @if($errors->has('item7'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item7')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item7"></nobr>
                                    </span>
                                </td>
                            </tr>

                        

                        <!-- 平均賃金/月 -->
                        <tr>
                            <td><label for="item8">{{ __('平均賃金/月') }}</label></td>

                            <td><input id="item8" type="text" maxlength="10"  name="item8" value="{{ old('item8') }}">円
                                <!-- @if($errors->has('item8'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item8')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item8"></nobr>
                                </span>
                            </td>
                        </tr>


                        <!-- 定員1名あたりサービス活動収益費（日額） -->
                        <tr>
                            <td><label for="item9">定員一名あたり<br>サービス活動収益費(日額)</label></td>
                               <td><input id="item9" type="text" maxlength="10"  name="item9" value="{{ old('item9') }}">円
                                <!-- @if($errors->has('item9'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item9')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item9"></nobr>
                                </span>
                            </td>
                        </tr>
                        <!-- グループ入力欄 -->
                        <tr>
                        <td><label>{{ __('グループ') }}</label></td>
                        <td>
                            <div id="groupFields">
                            <div class="group-row">
                                <input type="text" name="group_names[]" maxlength="100" placeholder="グループ名">
                            </div>
                            </div>
                            <button type="button" class="btn-add-group" onclick="addGroupField()">＋</button>
                            <div class="hint">※ 追加したい分だけ［＋］で行を増やしてください（未入力は無視して保存されます）</div>
                        </td>
                        </tr>
            <!-- 追加ボタン -->
            <input id="btn_addfacility" type="image" src="image/img_yes.png" alt="追加" border="0">
                
</form>
</div>
@endsection

<script>
function addGroupField() {
  const wrap = document.getElementById('groupFields');
  const row  = document.createElement('div');
  row.className = 'group-row';
  row.innerHTML = '<input type="text" name="group_names[]" maxlength="100" placeholder="グループ名">';
  wrap.appendChild(row);
}
</script>
<style>
.group-row { margin-bottom: 6px; }
.btn-add-group { margin-top: 4px; padding: 2px 8px; }
.hint { color:#666; font-size:12px; margin-top:4px; }
</style>
