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

                    
                    <!-- 施設概要 -->
                    <tr><td colspan="2"><label>{{ __('入所者要介護度内訳') }}</label></td></tr>

                    <!-- Ⅰ　Ⅱ -->
                    <tr>
                            <td><label for="item10">{{ __('Ⅰ Ⅱ') }}</label></td>

                            <td><input id="item10" type="text" maxlength="10" name="item10" value="{{ old('item10') }}">人
                                <!-- @if($errors->has('item10'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item10')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item10"></nobr>
                                </span>
                            </td>
                    </tr>

                    <!-- Ⅲ -->
                    <tr>
                        <td><label for="item11">{{ __('Ⅲ') }}</label></td>

                        <td><input id="item11" type="text" maxlength="10" name="item11" value="{{ old('item11') }}">人
                                <!-- @if($errors->has('item11'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item11')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item11"></nobr>
                                </span>
                            </td>
                        </tr>

                    <!-- Ⅳ -->
                    <tr>
                        <td><label for="item12">{{ __('Ⅳ') }}</label></td>

                        <td><input id="item12" type="text" maxlength="10" name="item12" value="{{ old('item12') }}">人
                            <!-- @if($errors->has('item12'))
                                <span class="invalid-feedback validate" role="alert">
                                {{$errors->first('item12')}}
                                </span>
                            @endif -->
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item12"></nobr>
                            </span>
                        </td>
                    </tr>

                    <!-- Ⅴ -->
                    <tr>
                        <td><label for="item13">{{ __('Ⅴ') }}</label></td>

                        <td><input id="item13" type="text" maxlength="10" name="item13" value="{{ old('item13') }}">人
                            <!-- @if($errors->has('item13'))
                                <span class="invalid-feedback validate" role="alert">
                                {{$errors->first('item13')}}
                                </span>
                            @endif -->
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item13"></nobr>
                            </span>
                        </td>
                    </tr>

                </table>

                    <!-- 移乗介助に関する労働力 -->

                    <table class="facil_table">
                        <tr><td  colspan="5"><label class = "lb">{{ __('移乗介助コスト') }}</label></td></tr>
                        <tr>
                            <td>移乗介助</td>
                            <td>移乗介助対象者</td>
                            <td>うち二人介助を要する<br>対象者</td>
                            <td>介助対象者一人当たりの<br>所要時間（分）</td>
                            <td>介助対象者一人当たりの<br>移乗回数/日</td>
                        </tr>
                        
                        <tr>
                            <td>寝室</td>
                            <td>
                            <!-- 寝室 - 移乗介助対象者 -->
                                <input id="item14" type="text" maxlength="10" name="item14" value="{{ old('item14') }}">人
                                <!-- @if($errors->has('item14'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item14')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item14"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 寝室 - うち2人介助対象者 -->                      

                                    <input id="item15" type="text" maxlength="10" name="item15" value="{{ old('item15') }}">人
                                    <!-- @if($errors->has('item15'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item15')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item15"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 寝室 - ※平均介助時間/分 -->
                                    <input id="item16" type="text" maxlength="10" name="item16" value="{{ old('item16') }}">分
                                    <!-- @if($errors->has('item16'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item16')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item16"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 寝室 - 一人あたり一日の平均移乗回数 -->
                                    <input id="item17" type="text" maxlength="10" name="item17" value="{{ old('item17') }}">回

                                    <!-- @if($errors->has('item17'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item17')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item17"></nobr>
                                    </span>
                            </td>
                        </tr>

                        <tr>
                            <td>脱衣室</td>
                            <td>
                            <!-- 脱衣室 - 移乗介助対象者 -->
                                    <input id="item18" type="text" maxlength="10"  name="item18" value="{{ old('item18') }}">人

                                    <!-- @if($errors->has('item18'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item18')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item18"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 脱衣室 - うち2人介助対象者 -->
                                <input id="item19" type="text" maxlength="10" name="item19" value="{{ old('item19') }}">人

                                <!-- @if($errors->has('item19'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item19')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item19"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 脱衣室 - ※平均介助時間/分 -->
                                <input id="item20" type="text" maxlength="10"  name="item20" value="{{ old('item20') }}">分

                                <!-- @if($errors->has('item20'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item20')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item20"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 脱衣室 - 一人あたり一日の平均移乗回数 -->
                                <input id="item21" type="text" maxlength="10" name="item21" value="{{ old('item21') }}">回
                                    <!-- @if($errors->has('item21'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item21')}}
                                        </span>
                                    @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item21"></nobr>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>浴室</td>
                            <td>
                            <!-- 浴室 - 移乗介助対象者 -->                   

                                    <input id="item22" type="text" maxlength="10" name="item22" value="{{ old('item22') }}">人

                                    <!-- @if($errors->has('item22'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item22')}}
                                    </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item22"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 浴室 - うち2人介助対象者 -->
                                    
                                    <input id="item23" type="text" maxlength="10" name="item23" value="{{ old('item23') }}">人
                                    <!-- @if($errors->has('item23'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item23')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item23"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 浴室 - ※平均介助時間/分 -->
                                    <input id="item24" type="text" maxlength="10" name="item24" value="{{ old('item24') }}">分

                                    <!-- @if($errors->has('item24'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item24')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item24"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 浴室 - 一人あたり一日の平均移乗回数 -->
                                    <input id="item25" type="text" maxlength="10" name="item25" value="{{ old('item25') }}">回

                                    <!-- @if($errors->has('item25'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item25')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item25"></nobr>
                                    </span>
                            </td>
                        </tr>

                        <tr>
                            <td>トイレ</td>
                            <td>
                            <!-- トイレ - 移乗介助対象者 -->
                                <input id="item26" type="text" maxlength="10" name="item26" value="{{ old('item26') }}">人

                                <!-- @if($errors->has('item26'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item26')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item26"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- トイレ - うち2人介助対象者 -->
                                <input id="item27" type="text" maxlength="10" name="item27" value="{{ old('item27') }}">人

                                <!-- @if($errors->has('item27'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item27')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item27"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- トイレ - ※平均介助時間/分 -->
                                <input id="item28" type="text" maxlength="10" name="item28" value="{{ old('item28') }}">分

                                <!-- @if($errors->has('item28'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item28')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item28"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- トイレ - 一人あたり一日の平均移乗回数 -->
                                <input id="item29" type="text" maxlength="10" name="item29" value="{{ old('item29') }}">回

                                <!-- @if($errors->has('item29'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item29')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item29"></nobr>
                                </span>
                            </td>
                        </tr>

                    </table>

                    
                    <!-- 採用に関する労働力 -->

                    <table class="facil_table">
                    <tr><td  colspan="5"><label class = "lb">{{ __('採用・教育コスト') }}</label></tr></td>
                        <tr>
                            <td>人材</td>
                            <td>一ヵ月あたり<br>求人サイト掲載費用</td>
                            <td>平均掲載期間</td>
                            <td>一ヵ月あたり<br>面接人数</td>
                            <td>一人あたり<br>平均面接時間</td>
                        </tr>
                        
                        <tr>
                            <td>求人</td>
                            <td>
                            <!-- 求人 - １ヵ月あたり求人サイト掲載費用 -->            
                                <input id="item30" type="text" maxlength="10" name="item30" value="{{ old('item30') }}">円

                                <!-- @if($errors->has('item30'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item30')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item30"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 求人 - 平均掲載期間 -->
                                <input id="item31" type="text" maxlength="10" name="item31" value="{{ old('item31') }}">ヵ月
                                <!-- @if($errors->has('item31'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item31')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item31"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 求人 - １ヵ月あたり面接人数 -->
                                <input id="item32" type="text" maxlength="10" name="item32" value="{{ old('item32') }}">人
                                <!-- @if($errors->has('item32'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item32')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item32"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 求人 - 1人あたり平均面接時間 -->
                                <input id="item33" type="text" maxlength="10" name="item33" value="{{ old('item33') }}">分
                                <!-- @if($errors->has('item33'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item33')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item33"></nobr>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>人材Ⅱ</td>
                            <td>年間入社人数</td>
                            <td>一人あたり研修担当職員数</td>
                            <td>一日あたり研修時間</td>
                            <td>研修期間日数</td>                           
                        </tr>

                        <tr>
                            <td>新人研修</td>
                            <td>
                            <!-- 新人研修 - 年間入社人数 -->
                                <input id="item34" type="text" maxlength="10" name="item34" value="{{ old('item34') }}">人
                                <!-- @if($errors->has('item34'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item34')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item34"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 新人研修 - ※1人あたり研修担当職員数 -->
                                <input id="item35" type="text" maxlength="10" name="item35" value="{{ old('item35') }}">人
                                <!-- @if($errors->has('item35'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item35')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item35"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 新人研修 - １日あたり研修時間 -->
                                <input id="item36" type="text" maxlength="10" name="item36" value="{{ old('item36') }}">分
                                <!-- @if($errors->has('item36'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item36')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item36"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 新人研修 - 研修期間日数 -->
                                <input id="item37" type="text" maxlength="10" name="item37" value="{{ old('item37') }}">日

                                <!-- @if($errors->has('item37'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item37')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item37"></nobr>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>人材Ⅲ</td>
                            <td>研修回数/年</td>
                            <td>平均参加費/回</td>
                            <td></td>
                            <td></td>                           
                        </tr>
                        

                        <tr>
                            <td>外部研修</td>
                            <td>
                            <!-- 外部研修 - 研修回数/年 -->
                                <input id="item38" type="text" maxlength="10" name="item38" value="{{ old('item38') }}">回
                                <!-- @if($errors->has('item38'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item38')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item38"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 外部研修 - 平均参加費/回 -->
                                <input id="item39" type="text" maxlength="10" name="item39" value="{{ old('item39') }}">円
                                <!-- @if($errors->has('item39'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item39')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item39"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 外部研修 - blank -->
                            </td>
                            <td>
                            <!-- 新人研修 - blank -->

                            </td>
                        </tr>
                    </table>

                    <!-- アクシデントに関する労働力 -->
                    <table  class="facil_table">
                    <tr><td  colspan="5"><label class = "lb">{{ __('アクシデントコスト') }}</label></td></tr>
                        <tr>
                            <td rowspan="2">皮膚剥離</td>
                            <td>年間発生件数</td>
                            <td>一件あたり処置時間</td>
                            <td>一件あたり報告書作成時間</td>
                        </tr>
                        <tr>
                            <td>
                                <!-- 皮膚剥離 - 年間発生件数 -->
                                <input id="item40" type="text" maxlength="12" name="item40" value="{{ old('item40') }}" >件

                                <!-- @if($errors->has('item40'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item40')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item40"></nobr>
                                </span>
                            </td>
                            <td>
                                <!-- 皮膚剥離 - 1件あたり処置時間 -->
                                <input id="item41" type="text" maxlength="10" name="item41" value="{{ old('item41') }}">分

                                <!-- @if($errors->has('item41'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item41')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item41"></nobr>
                                </span>
                            </td>
                            <td>
                                <!-- 皮膚剥離 - 1件あたり報告書作成時間 -->
                                <input id="item42" type="text" maxlength="10" name="item42" value="{{ old('item42') }}">分

                                <!-- @if($errors->has('item42'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item42')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item42"></nobr>
                                </span>
                            </td>
                        </tr>
                    
                        <tr>
                            <td rowspan="2">褥瘡</td>
                            <td>年間発生件数</td>
                            <td>一件あたり処置時間</td>
                            <td>一件あたり報告書作成時間</td>
                        </tr>
                        <tr>
                            <td>
                                <!-- 褥瘡 - 年間発生件数 -->
                                <input id="item43" type="text" maxlength="10" name="item43" value="{{ old('item43') }}">件

                                <!-- @if($errors->has('item43'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item43')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item43"></nobr>
                                </span>
                            </td>
                            <td>
                                <!-- 褥瘡 - 1件あたり処置時間 -->
                                <input id="item44" type="text" maxlength="10" name="item44" value="{{ old('item44') }}">分

                                <!-- @if($errors->has('item44'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item44')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item44"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 褥瘡 - 1件あたり報告書作成時間 -->
                                <input id="item45" type="text" maxlength="10" name="item45" value="{{ old('item45') }}">分

                                <!-- @if($errors->has('item45'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item45')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item45"></nobr>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            
                            <tr>
                                <td rowspan="2">骨折</td>
                                <td>年間発生件数</td>
                                <td></td>
                                <td>一件あたり報告書作成時間</td>
                            </tr>
                            <td>
                            <!-- 骨折 - 年間発生件数 -->
                                <input id="item46" type="text" maxlength="10" name="item46" value="{{ old('item46') }}">件
                                <!-- @if($errors->has('item46'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item46')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item46"></nobr>
                                </span>
                            </td>
                            <td></td>
                            <td>
                            <!-- 骨折 - 1件あたり報告書作成時間 -->
                                <input id="item47" type="text" maxlength="10" name="item47" value="{{ old('item47') }}">分
                                <!-- @if($errors->has('item47'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item47')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item47"></nobr>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="2">通院</td>
                            <td>年間発生件数</td>
                            <td>一件あたり通院介助時間</td>
                            <td>一件あたり作業者数</td>
                        </tr>
                        <tr>
                            <td>
                                <!-- ※通院 - 年間発生件数 -->
                                    <input id="item48" type="text" maxlength="10" name="item48" value="{{ old('item48') }}">件
                                    <!-- @if($errors->has('item48'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item48')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item48"></nobr>
                                    </span>
                            </td>
                            <td>
                                <!-- ※通院 - 1件あたり通院介助時間 -->
                                <input id="item49" type="text" maxlength="10"  name="item49" value="{{ old('item49') }}">分

                                <!-- @if($errors->has('item49'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item49')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item49"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- ※通院 - １件あたり作業者数 -->
                                <input id="item50" type="text" maxlength="10" name="item50" value="{{ old('item50') }}">人

                                <!-- @if($errors->has('item50'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item50')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item50"></nobr>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="2">入院</td>
                            <td>年間発生件数</td>
                            <td>一件あたり平均入院期間</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <!-- ※入院 - 年間発生件数 -->
                                <input id="item51" type="text" maxlength="10" name="item51" value="{{ old('item51') }}">件
                                <!-- @if($errors->has('item51'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item51')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item51"></nobr>
                                </span>
                            </td>
                            <td>
                                <!-- ※入院 - １件あたり平均入院期間 -->
                                <input id="item52" type="text" maxlength="10" name="item52" value="{{ old('item52') }}">日

                                <!-- @if($errors->has('item52'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item52')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item52"></nobr>
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </table>


                    <!-- その他ケアに関するコスト -->
                    <table class="facil_table">
                    <tr><td colspan="5"><label class = "lb">{{ __('その他ケアに関するコスト') }}</label></td></tr>
                        <!-- オムツコスト（パッド含む） -->
                        <tr>
                            <td rowspan="2">オムツコスト<br>(パッド含む)</td>
                            <td>オムツ使用者</td>
                            <td>使用枚数/日</td>
                            <td>オムツ単価/枚</td>
                            <td></td>
                        </tr>
                        <tr>
                        <td>
                            <!-- オムツコスト（パッド含む） - オムツ使用者 -->
                                <input id="item53" type="text" maxlength="10" name="item53" value="{{ old('item53') }}">人

                                <!-- @if($errors->has('item53'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item53')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item53"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- オムツコスト（パッド含む） - 1日あたり総使用枚数 -->
                                <input id="item54" type="text" maxlength="10" name="item54" value="{{ old('item54') }}">枚
                                <!-- @if($errors->has('item54'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item54')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item54"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- オムツコスト（パッド含む） - 1枚あたりオムツ単価 -->
                                <input id="item55" type="text" maxlength="10" name="item55" value="{{ old('item55') }}">円

                                <!-- @if($errors->has('item55'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item55')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item55"></nobr>
                                </span>
                            </td>
                            <td></td>
                        </tr>


                        <!-- 排泄介助コスト -->
                        <tr>
                        <!-- class="cs_hidden"を介助したらrowspan="4"へすること -->
                            <td rowspan="2">オムツ交換コスト</td>
                            <td>介助が必要な対象者(人)</td>
                            <td>うち二人介助を要する<br>対象者（人）</td>
                            <td>一人当たりの介助所要時間/分</td>
                            <td>介助対象者一人当たりの<br>交換回数/日</td>
                        </tr>
                        <tr>
                        <td>
                            <!-- 排泄介助コスト - 要一部介助 -->
                                <input id="item56" type="text" maxlength="10" name="item56" value="{{ old('item56') }}">人

                                <!-- @if($errors->has('item56'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item56')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item56"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - うち二人介助を要する対象者数 -->
                                <input id="item57" type="text" maxlength="10" name="item57" value="{{ old('item57') }}">人
                                <!-- @if($errors->has('item57'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item57')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item57"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - 一人あたり介助所要時間 -->
                                <input id="item58" type="text" maxlength="10" name="item58" value="{{ old('item58') }}">分

                                <!-- @if($errors->has('item58'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item58')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item58"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - 作業者一人あたりの介助回数 -->
                                <input id="item59" type="text" maxlength="10" name="item59" value="{{ old('item59') }}">回

                                <!-- @if($errors->has('item59'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item59')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item59"></nobr>
                                </span>
                            </td>
                        </tr>

                        <tr class="cs_hidden">
                            <td>要全作業者</td>
                            <td>うち二人介助を要する<br>対象者数</td>
                            <td>一人あたり介助所要時間</td>
                            <td>作業者一人あたりの<br>介助回数</td>
                        </tr>
                        <tr class="cs_hidden">
                            <td>
                            <!-- 排泄介助コスト - 要全作業者 -->
                                <input id="item69" type="text" maxlength="10" name="item69" value="{{ old('item69') }}">人

                                <!-- @if($errors->has('item56'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item56')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item69"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - うち二人介助を要する対象者数 -->
                                <input id="item70" type="text" maxlength="10" name="item70" value="{{ old('item70') }}">人
                                <!-- @if($errors->has('item57'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item57')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item70"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - 一人あたり介助所要時間 -->
                                <input id="item71" type="text" maxlength="10" name="item71" value="{{ old('item71') }}">分

                                <!-- @if($errors->has('item58'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item58')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item71"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - 作業者一人あたりの介助回数 -->
                                <input id="item72" type="text" maxlength="10" name="item72" value="{{ old('item72') }}">回

                                <!-- @if($errors->has('item59'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item59')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item72"></nobr>
                                </span>
                            </td>
                        </tr>


                        <!-- 食事介助コスト -->
                        <tr>
                            <td rowspan="2">食事介助コスト</td>
                            <td>介助が必要な対象者（人）</td>
                            <td>食事介助に付く<br>スタッフ（人）/回</td>
                            <td>介助対象者一人当たりの<br>所要時間/分</td>
                            <td>介助対象者一人当たりの<br>介助回数/日</td>
                        </tr>
                        <tr>
                        <td>
                            <!-- 食事介助コスト - 要食事作業者 -->
                                <input id="item60" type="text" maxlength="10" name="item60" value="{{ old('item60') }}">人

                                <!-- @if($errors->has('item60'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item60')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item60"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 食事介助コスト - 1日あたりのべ作業者数 -->
                                <input id="item61" type="text" maxlength="10" name="item61" value="{{ old('item61') }}">人


                                <!-- @if($errors->has('item61'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item61')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item61"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 食事介助コスト - 1人あたり介助所要時間 -->
                                <input id="item62" type="text" maxlength="10" name="item62" value="{{ old('item62') }}">分


                                <!-- @if($errors->has('item62'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item62')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item62"></nobr>
                                </span>
                            </td>
                            <td>
                            <!-- 食事介助コスト - 一日辺りの食事介助回数 -->
                                <input id="item63" type="text" maxlength="10" name="item63" value="{{ old('item63') }}">回

                                <!-- @if($errors->has('item63'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('item63')}}
                                    </span>
                                @endif -->
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item63"></nobr>
                                </span>
                            </td>
                        </tr>
                    
                    
                        <!-- 座位修正介助コスト -->
                        <tr class="cs_hidden">
                                <td rowspan="2">座位修正介助コスト</td>
                                <td>対象者</td>
                                <td>一日あたりのべ作業者数</td>
                                <td>一人あたり介助所要時間</td>
                                <td>一日あたり総介助回数</td>
                        </tr>
                        <tr class="cs_hidden">
                            <td>
                                <!-- 座位修正介助コスト - 対象者 -->
                                    <input id="item64" type="text" maxlength="10" name="item64" value="{{ old('item64') }}">人
                                    <!-- @if($errors->has('item64'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item64')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item64"></nobr>
                                    </span>
                                </td>
                                <td>
                                <!-- 座位修正介助コスト - 一日あたりのべ作業者数 -->
                                    <input id="item65" type="text" maxlength="10" name="item65" value="{{ old('item65') }}">人
                                    <!-- @if($errors->has('item65'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item65')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item65"></nobr>
                                    </span>
                                </td>
                                <td>
                                <!-- 座位修正介助コスト - 1人あたり介助所要時間 -->
                                    <input id="item66" type="text" maxlength="12" name="item66" value="{{ old('item66') }}">分

                                    <!-- @if($errors->has('item66'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item66')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item66"></nobr>
                                    </span>
                                </td>
                                <td>
                                <!-- 座位修正介助コスト - 1日あたり総介助回数 -->
                                    <input id="item67" type="text" maxlength="10" name="item67" value="{{ old('item67') }}">回

                                    <!-- @if($errors->has('item67'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('item67')}}
                                        </span>
                                    @endif -->
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item67"></nobr>
                                    </span>
                                </td>
                        </tr>
                    
                    
                    </table>


                    <!-- 腰痛コスト -->
                    <table class="facil_table">
                    <tr><td colspan="5"><label class = "lb">{{ __('腰痛保持率') }}</label></td></tr>
                    <tr>
                        <td>腰痛保持率</td>
                        <td>
                                <!-- 腰痛保持率 -->
                                <input id="item68" type="text" maxlength="10" name="item68" value="{{ old('item68') }}">%

                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item68"></nobr>
                                </span>
                            </td>
                    </tr>
                    </table>
            </div>
            
            <!-- 追加ボタン -->
            <input id="btn_addfacility" type="image" src="image/img_yes.png" alt="追加" border="0">
                
</form>
</div>
@endsection
