

@extends('layouts.parent')


@section('content')
<script src="/js/facility.js"></script>

<div class="allcont">

@if($target == 0)
    <!-- 追加ボタン -->
    <input type="image" id="btn_addfacility_input_pre" src="image/img_add.png" alt="追加" onclick="VisibleChange(this.id)" border="0">
    <!-- キャンセルボタン -->
    <input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_facility_inputadd')" border="0">
@else
    <!-- 修正ボタン -->
    <input type="image" id="btn_fixfacility_input_pre"  src="image/img_fix.png" alt="修正" onclick="VisibleChange(this.id)" border="0">
    <!-- キャンセルボタン -->
    <input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_facility_inputfix')" border="0" autocomplete="off">
    <form  action = '/facilityinput' name = "fix_index"  method = "post">
        @csrf
        @if(isset($data[0]['id']))
            <input id="id" type="hidden" name="id" value="{{$data[0]['id']}}">
        @else
            <input id="id" type="hidden" name="id" value="{{old('id')}}">
        @endif
        <input type="image" id="btn_cxl_do" style="visibility:hidden;"  src="image/img_yes.png" alt="はい" border="0">
    </form>
@endif


<form  action = '/facilityinput' name = "fix_index"  method = "post">
@csrf
<input id="targetid" type="hidden" name="id" value="">
<input type="hidden" name="addmess" value="追加しました。">
</form>

@if($target == 0)
    <form method="POST" id="form_facility_inputadd" action="facility_input_addctrl" name = "form_facility_inputadd" enctype="multipart/form-data"  autocomplete="off">
@else
    <form method="POST" id= "form_facility_inputfix" action="facility_input_fixctrl" name = "form_facility_inputfix" enctype="multipart/form-data" autocomplete="off">
@endif
@csrf
                
    <div class="container_facil">
        <table  class="facil_table1">
                    <!-- 施設概要 -->
                    <tr><td  colspan="3"><label class = "lb">{{ __('施設概要') }}</label></td></tr>

                        <!-- No -->
                    <tr>
                        <td><label for="id">{{ __('No') }}</label>
                        <td>
                            @if($target == 0)
                                <input  type="text" value="" disabled>
                                <input id="id" type="hidden" name="id" value="">
                            @else
                                @if(isset($data[0]['id']))
                                    <input  type="text" value={{$data[0]['id']}} disabled>
                                    <input id="id" type="hidden" name="id" value={{$data[0]['id']}}>
                                @else
                                    <input type="text" value="{{old('id')}}" disabled>
                                    <input id="id" type="hidden" name="id" value="{{old('id')}}">
                                @endif
                            @endif
                        </td>                               
                    </tr>

                    <!-- 施設名 -->
                    <!-- ログインしているユーザから取得？ -->
                    <tr>
                        <td><label for="facility">{{ __('施設名') }}</label></td>

                        <td>
                            @if(isset($data[0]['facility']))                                
                                @if($errors->has('facility'))
                                    <input id="facility" type="text" maxlength="20" name="facility" value="{{old('facility')}}" autofocus>
                                @else
                                <input id="facility" type="text" maxlength="20" name="facility" value="{{\Illuminate\Support\Str::of($data[0]['facility'])->rtrim()}}" autofocus>
                                @endif
                            @else
                                <input id="facility" type="text" maxlength="20" name="facility" value="{{old('facility')}}" autofocus>
                                
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_facility"></nobr>
                                </span>
                        </td>
                    </tr>
                        
                        <!-- 施設区分 -->
                        <tr>
                        <td><label for="pass">{{ __('施設区分') }}</label></td>
                        <td>
                                @if(isset($data[0]['pass']))
                                <select  id="pass" name="pass" value="{{$data[0]['pass']}}">
                                    @foreach($code as $valcode)
                                        @if($valcode['codeno']==2)
                                                @if($valcode['value'] == $data[0]['pass'])
                                                    <option value={{$valcode['value']}} selected>{{$valcode['selectname']}}</option>
                                                @else 
                                                    <option value={{$valcode['value']}}>{{$valcode['selectname']}}</option>
                                                @endif
                                        @endif
                                    @endforeach
                                    </select>
                                @else
                                    <select  id="pass"  name="pass" value="{{old('selectname')}}">
                                    @foreach($code as $valcode)
                                        @if($valcode['codeno']==2)
                                            <option value={{$valcode['value']}}>{{$valcode['selectname']}}</option>
                                        @endif
                                    @endforeach
                                    
                                </select>
                                
                                @endif
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_pass"></nobr>
                                    </span>
                            </td>
                        </tr>

                        <!-- 住所 -->
                        <tr>
                            <td><label for="address">{{ __('住所') }}</label></td>
                            <td>
                                @if(isset($data[0]['address']))
                                   
                                    @if($errors->has('address'))
                                    <input id="address" type="text" maxlength="100" name="address" value="{{old('address')}}">
                                    @else
                                    <input id="address" type="text" maxlength="100" name="address" value="{{\Illuminate\Support\Str::of($data[0]['address'])->rtrim()}}">
                                    @endif
                                @else
                                    <input id="address" type="text" maxlength="100" name="address" value="{{old('address')}}">
                                    
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_address"></nobr>
                                    </span>
                                
                            </td>
                        </tr>

                        <!-- 電話番号 -->
                        <tr>
                            <td><label for="tel">{{ __('電話番号(ハイフンなし)') }}</label></td>
                            <td>
                            @if(isset($data[0]['tel']))
                                @if($errors->has('tel'))
                                <input id="tel" type="text" name="tel" maxlength="20" value="{{old('tel')}}">
                                @else
                                <input id="tel" type="text" name="tel" maxlength="20" value="{{\Illuminate\Support\Str::of($data[0]['tel'])->rtrim()}}">
                                @endif
                            @else
                                <input id="tel" type="text" name="tel" maxlength="20" value="{{old('tel')}}"> 
                                                   
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_tel"></nobr>
                                </span>    
                         
                            </td>
                        </tr>

                        <!-- メールアドレス -->
                        <tr>
                            <td><label for="mail">{{ __('メールアドレス') }}</label></td>
                            <td>
                                
                                @if(isset($data[0]['mail']))
                                    @if($errors->has('mail'))
                                    <input id="mail" type="text" name="mail" maxlength="40" value="{{old('mail')}}">
                                    @else
                                    <input id="mail" type="text" name="mail" maxlength="40" value="{{\Illuminate\Support\Str::of($data[0]['mail'])->rtrim()}}">
                                    @endif
                                @else
                                    <input id="mail" type="text" name="mail" maxlength="40" value="{{old('mail')}}">     
                                                         
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_mail"></nobr>
                                </span>  
                               
                            </td>
                        </tr>

                        <!-- 2021.05.18　追加 -->                       
                        <!-- アンケートサイト -->
                        <tr style="visibility:collapse">
                            <td><label for="url">{{ __('アンケートサイトURL') }}</label></td>
                            <td>
                                @if(isset($data[0]['questurl']))
                                    @if($errors->has('url'))
                                    <input id="url" type="hidden" name="url" maxlength="256" value="{{old('url')}}">
                                    @else
                                    <input id="url" type="hidden" name="url" maxlength="256" value="{{\Illuminate\Support\Str::of($data[0]['questurl'])->rtrim()}}">
                                    @endif

                                @else
                                    <input id="url" type="hidden" maxlength="256" name="url" value="{{old('url')}}">                        
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_url"></nobr>
                                </span>
                            </td>
                        </tr>


                        <!-- 定員 -->
                        <tr>
                        <td><label for="item1">{{ __('定員') }}</label></td>
                            <td>
                                @if(isset($data[0]['item1']))
                                    @if($errors->has('item1'))
                                    <input id="item1" type="text" maxlength="10" name="item1" value="{{old('item1')}}">人
                                    @else
                                    <input id="item1" type="text" maxlength="10" name="item1" value="{{\Illuminate\Support\Str::of($data[0]['item1'])->rtrim()}}">人
                                    @endif
                                    @else
                                    <input id="item1" type="text" maxlength="10" name="item1" value="{{old('item1')}}">人
                                        
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item1"></nobr>
                                    </span>
                          
                            </td>
                        </tr>

                        <!-- 施設区分Ⅱ -->
                        <tr>
                            <td><label for="item2">{{ __('施設区分Ⅱ') }}</label></td>
                            <!-- ユニットケア -->

                            <td><label for="item2">{{ __('ユニットケア(一ユニット)') }}</label>                               

                                @if(isset($data[0]['item2']))
                                    @if($errors->has('item2'))
                                    <input id="item2" type="text" maxlength="10" name="item2" value="{{old('item2')}}">名
                                    @else
                                    <input id="item2" type="text" maxlength="10" name="item2" value="{{\Illuminate\Support\Str::of($data[0]['item2'])->rtrim()}}">名
                                    @endif
                                @else
                                    <input id="item2" type="text" maxlength="10" name="item2" value="{{old('item2')}}">名
                                @endif
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item2"></nobr>
                                    </span>

                                
                            </td>


                            <!-- 従来型 -->
                            <td><label for="item3">{{ __('従来型(一フロア)') }}</label>

                                    @if(isset($data[0]['item3']))
                                        @if($errors->has('item3'))
                                        <input id="item3" type="text" maxlength="10" name="item3" value="{{old('item3')}}">名
                                        @else
                                        <input id="item3" type="text" maxlength="10" name="item3" value="{{\Illuminate\Support\Str::of($data[0]['item3'])->rtrim()}}">名
                                        @endif
                                        @else
                                        <input id="item3" type="text" maxlength="10" name="item3" value="{{old('item3')}}">名
                                         
                                    @endif
                                    <span class="invalid-feedback validate" role="alert">
                                            <nobr id="err_item3"></nobr>
                                        </span>
                                   
                            </td>
                        </tr>

                        <!-- 職員数 -->
                        <tr>
                            <td><label for="item4">{{ __('職員数') }}</label></td>
                            <td>
                                @if(isset($data[0]['item4']))
                                    @if($errors->has('item4'))
                                    <input id="item4" type="text" maxlength="10" name="item4" value="{{old('item4')}}">名
                                    @else
                                    <input id="item4" type="text" maxlength="10" name="item4" value="{{\Illuminate\Support\Str::of($data[0]['item4'])->rtrim()}}">名
                                    @endif
                                @else
                                    <input id="item4" type="text" maxlength="10" name="item4" value="{{old('item4')}}">名
                                @endif

                                <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item4"></nobr>
                                    </span>
                                
                            </td>
                        </tr>

                        <!-- 職員数Ⅱ -->
                        <tr>
                            <td><label for="item5">{{ __('職員数Ⅱ') }}</label></td>
                            <!-- ユニットケア -->

                            <td><label for="item5">{{ __('ユニットケア(一ユニット)') }}</label>

                                @if(isset($data[0]['item5']))
                                    @if($errors->has('item5'))
                                    <input id="item5" type="text" maxlength="10" name="item5" value="{{old('item5')}}">名
                                    @else
                                    <input id="item5" type="text" maxlength="10" name="item5" value="{{\Illuminate\Support\Str::of($data[0]['item5'])->rtrim()}}">名
                                    @endif
                                @else
                                    <input id="item5" type="text" maxlength="10" name="item5" value="{{old('item5')}}">名
                                @endif                            
                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item5"></nobr>
                                    </span>    
                            </td>



                            <!-- 従来型 -->
                            <td><label for="item6">{{ __('従来型(一フロア)') }}</label>

                                @if(isset($data[0]['item6']))
                                    @if($errors->has('item6'))
                                        <input id="item6" type="text" maxlength="10" name="item6" value="{{old('item6')}}">名
                                    @else
                                    <input id="item6" type="text" maxlength="10" name="item6" value="{{\Illuminate\Support\Str::of($data[0]['item6'])->rtrim()}}">名
                                    @endif
                                @else
                                    <input id="item6" type="text" maxlength="10" name="item6" value="{{old('item6')}}">名                                   
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item6"></nobr>
                                </span>
                                
                            </td>
                        </tr>


                        <!-- 夜勤人数/月 -->
                        <tr>
                            <td><label for="item7">{{ __('夜勤人数/日') }}</label></td>
                            <td>
                                @if(isset($data[0]['item7']))
                                @if($errors->has('item7'))
                                    <input id="item7" type="text" maxlength="10" name="item7" value="{{old('item7')}}">人/日
                                @else
                                <input id="item7" type="text" maxlength="10" name="item7" value="{{\Illuminate\Support\Str::of($data[0]['item7'])->rtrim()}}">人/日
                                @endif
                                @else
                                    <input id="item7" type="text" maxlength="10" name="item7" value="{{old('item7')}}">人/日
                                  
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item7"></nobr>
                                    </span>
                                
                            </td>
                        </tr>

                        

                        <!-- 平均賃金/月 -->
                        <tr>
                            <td><label for="item8">{{ __('平均賃金/月') }}</label></td>
                            <td>

                            @if(isset($data[0]['item8']))
                                @if($errors->has('item8'))
                                    <input id="item8" type="text" maxlength="10" name="item8" value="{{old('item8')}}">円
                                @else
                                <input id="item8" type="text" maxlength="10" name="item8" value="{{\Illuminate\Support\Str::of($data[0]['item8'])->rtrim()}}">円
                                @endif
                            @else
                                <input id="item8" type="text" maxlength="10" name="item8" value="{{old('item8')}}">円
                             
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item8"></nobr>
                                </span>
                           
                            </td>
                        </tr>


                        <!-- 定員1名あたりサービス活動収益費（日額） -->
                        <tr>
                            <td><label for="item9">{{ __('定員一名あたりサービス活動収益費（日額）') }}</label></td>
                        <td>
                            @if(isset($data[0]['item9']))
                                @if($errors->has('item9'))
                                <input id="item9" type="text" maxlength="10" name="item9" value="{{old('item9')}}">円
                                @else
                                <input id="item9" type="text" maxlength="10" name="item9" value="{{\Illuminate\Support\Str::of($data[0]['item9'])->rtrim()}}">円
                                @endif
                            @else
                                <input id="item9" type="text" maxlength="10" name="item9" value="{{old('item9')}}">円
                            
                            @endif
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
                    <td>

                            @if(isset($data[0]['item10']))
                                @if($errors->has('item10'))
                                    <input id="item10" type="text" maxlength="10" name="item10" value="{{old('item10')}}">人
                                @else
                                <input id="item10" type="text" maxlength="10" name="item10" value="{{\Illuminate\Support\Str::of($data[0]['item10'])->rtrim()}}">人
                                @endif
                            @else
                                <input id="item10" type="text" maxlength="10" name="item10" value="{{old('item10')}}">人
                               
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item10"></nobr>
                            </span>
                    </td>
                </tr>


                    <!-- Ⅲ -->
                    <tr>
                        <td><label for="item11">{{ __('Ⅲ') }}</label></td>

                        <td>
                            @if(isset($data[0]['item11']))
                                @if($errors->has('item11'))
                                    <input id="item11" type="text" maxlength="10" name="item11" value="{{old('item11')}}">人
                                @else
                                <input id="item11" type="text" maxlength="10" name="item11" value="{{\Illuminate\Support\Str::of($data[0]['item11'])->rtrim()}}">人
                                @endif
                            @else
                                <input id="item11" type="text" maxlength="10" name="item11" value="{{old('item11')}}">人
                             
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item11"></nobr>
                                </span>
                            
                        </td>
                    </tr>

                    <!-- Ⅳ -->
                    <tr>
                        <td><label for="item12">{{ __('Ⅳ') }}</label></td>
                        <td>
                            @if(isset($data[0]['item12']))
                                @if($errors->has('item12'))
                                    <input id="item12" type="text" maxlength="10" name="item12" value="{{old('item12')}}">人
                                @else
                                <input id="item12" type="text" maxlength="10" name="item12" value="{{\Illuminate\Support\Str::of($data[0]['item12'])->rtrim()}}">人
                                @endif
                            @else
                                <input id="item12" type="text" maxlength="10" name="item12" value="{{old('item12')}}">人
                                    
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item12"></nobr>
                                </span>
                            
                        </td>
                    </tr>

                    <!-- Ⅴ -->
                    <tr>
                        <td><label for="item13">{{ __('Ⅴ') }}</label></td>
                        <td>
                            @if(isset($data[0]['item13']))
                                @if($errors->has('item13'))
                                    <input id="item13" type="text" maxlength="10" name="item13" value="{{old('item13')}}">人
                                @else
                                <input id="item13" type="text" maxlength="10" name="item13" value="{{\Illuminate\Support\Str::of($data[0]['item13'])->rtrim()}}">人
                                @endif
                            @else
                                <input id="item13" type="text" maxlength="10" name="item13" value="{{old('item13')}}">人
                                    
                            @endif
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
                    @if(isset($data[0]['item14']))
                        @if($errors->has('item14'))
                            <input id="item14" type="text" maxlength="10" name="item14" value="{{old('item14')}}">人
                        @else
                        <input id="item14" type="text" maxlength="10" name="item14" value="{{\Illuminate\Support\Str::of($data[0]['item14'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item14" type="text" maxlength="10" name="item14" value="{{old('item14')}}">人
                      
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item14"></nobr>
                        </span>
                    
                </td>
                <td>
                <!-- 寝室 - うち2人介助対象者 -->
                    @if(isset($data[0]['item15']))
                        @if($errors->has('item15'))
                            <input id="item15" type="text" maxlength="10" name="item15" value="{{old('item15')}}">人
                        @else
                        <input id="item15" type="text" maxlength="10" name="item15" value="{{\Illuminate\Support\Str::of($data[0]['item15'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item15" type="text" maxlength="10" name="item15" value="{{old('item15')}}">人
                      
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item15"></nobr>
                        </span>
                  
                </td>
                <td>
                <!-- 寝室 - ※平均介助時間/分 -->
                    @if(isset($data[0]['item16']))
                        @if($errors->has('item16'))
                            <input id="item16" type="text" maxlength="10" name="item16" value="{{old('item16')}}">分
                        @else
                        <input id="item16" type="text" maxlength="10" name="item16" value="{{\Illuminate\Support\Str::of($data[0]['item16'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item16" type="text" maxlength="10" name="item16" value="{{old('item16')}}">分
                        
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item16"></nobr>
                        </span>
                    
                </td>
                <td>
                <!-- 寝室 - 一人あたり一日の平均移乗回数 -->
                    @if(isset($data[0]['item17']))
                        @if($errors->has('item17'))
                        <input id="item17" type="text" maxlength="10" name="item17" value="{{old('item17')}}">回
                        @else
                        <input id="item17" type="text" maxlength="10" name="item17" value="{{\Illuminate\Support\Str::of($data[0]['item17'])->rtrim()}}">回
                        @endif
                    @else
                        <input id="item17" type="text" maxlength="10" name="item17" value="{{old('item17')}}">回
                        
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item17"></nobr>
                        </span>
                    
                </td>
            </tr>

            <tr>
                <td>脱衣室</td>
                <td>
                <!-- 脱衣室 - 移乗介助対象者 -->
                    @if(isset($data[0]['item18']))
                        @if($errors->has('item18'))
                        <input id="item18" type="text" maxlength="10" name="item18" value="{{old('item18')}}">人
                   
                        @else
                        <input id="item18" type="text" maxlength="10" name="item18" value="{{\Illuminate\Support\Str::of($data[0]['item18'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item18" type="text" maxlength="10" name="item18" value="{{old('item18')}}">人
                      
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item18"></nobr>
                        </span>
                </td>
                <td>
                <!-- 脱衣室 - うち2人介助対象者 -->
                    @if(isset($data[0]['item19']))
                        @if($errors->has('item19'))
                        <input id="item19" type="text" maxlength="10" name="item19" value="{{old('item19')}}">人
                          
                        @else
                        <input id="item19" type="text" maxlength="10" name="item19" value="{{\Illuminate\Support\Str::of($data[0]['item19'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item19" type="text" maxlength="10" name="item19" value="{{old('item19')}}">人
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item19"></nobr>
                        </span>
                </td>
                <td>
                <!-- 脱衣室 - ※平均介助時間/分 -->

                    @if(isset($data[0]['item20']))
                        @if($errors->has('item20'))
                        <input id="item20" type="text" maxlength="10" name="item20" value="{{old('item20')}}">分
                         
                        @else
                        <input id="item20" type="text" maxlength="10" name="item20" value="{{\Illuminate\Support\Str::of($data[0]['item20'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item20" type="text" maxlength="10" name="item20" value="{{old('item20')}}">分
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item20"></nobr>
                        </span>
                </td>
                <td>
                <!-- 脱衣室 - 一人あたり一日の平均移乗回数 -->
                        @if(isset($data[0]['item21']))
                            @if($errors->has('item21'))
                            <input id="item21" type="text" maxlength="10" name="item21" value="{{old('item21')}}">回
                               
                            @else
                            <input id="item21" type="text" maxlength="10" name="item21" value="{{\Illuminate\Support\Str::of($data[0]['item21'])->rtrim()}}">回
                            @endif
                        @else
                            <input id="item21" type="text" maxlength="10" name="item21" value="{{old('item21')}}">回
                            
                        @endif
                        <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item21"></nobr>
                            </span>
                        
                </td>
            </tr>

            <tr>
                <td>浴室</td>
                <td>
                <!-- 浴室 - 移乗介助対象者 -->

                    @if(isset($data[0]['item22']))
                        @if($errors->has('item22'))
                        <input id="item22" type="text" maxlength="10" name="item22" value="{{old('item22')}}">人
                     
                        @else
                        <input id="item22" type="text" maxlength="10" name="item22" value="{{\Illuminate\Support\Str::of($data[0]['item22'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item22" type="text" maxlength="10" name="item22" value="{{old('item22')}}">人
                  
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item22"></nobr>
                        </span>
                </td>
                <td>
                <!-- 浴室 - うち2人介助対象者 -->

                    @if(isset($data[0]['item22']))
                        @if($errors->has('item23'))
                        <input id="item23" type="text" maxlength="10" name="item23" value="{{old('item23')}}">人
                            
                        @else
                        <input id="item23" type="text" maxlength="10" name="item23" value="{{\Illuminate\Support\Str::of($data[0]['item23'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item23" type="text" maxlength="10" name="item23" value="{{old('item23')}}">人
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item23"></nobr>
                        </span>
                </td>
                <td>
                <!-- 浴室 - ※平均介助時間/分 -->
                    @if(isset($data[0]['item24']))
                        @if($errors->has('item24'))
                        <input id="item24" type="text" maxlength="10" name="item24" value="{{old('item24')}}">分
                           
                        @else
                        <input id="item24" type="text" maxlength="10" name="item24" value="{{\Illuminate\Support\Str::of($data[0]['item24'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item24" type="text" maxlength="10" name="item24" value="{{old('item24')}}">分
                      
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item24"></nobr>
                        </span>
                </td>
                <td>
                <!-- 浴室 - 一人あたり一日の平均移乗回数 -->

                    @if(isset($data[0]['item25']))
                        @if($errors->has('item25'))
                        <input id="item25" type="text" maxlength="10" name="item25" value="{{old('item25')}}">回
                     
                        @else
                        <input id="item25" type="text" maxlength="10" name="item25" value="{{\Illuminate\Support\Str::of($data[0]['item25'])->rtrim()}}">回
                        @endif
                    @else
                        <input id="item25" type="text" maxlength="10" name="item25" value="{{old('item25')}}">回
                        
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item25"></nobr>
                        </span>
                </td>
            </tr>

            <tr>
                <td>トイレ</td>
                <td>
                <!-- トイレ - 移乗介助対象者 -->
                    @if(isset($data[0]['item26']))
                        @if($errors->has('item26'))
                        <input id="item26" type="text" maxlength="10" name="item26" value="{{old('item26')}}">人
                     
                        @else
                        <input id="item26" type="text" maxlength="10" name="item26" value="{{\Illuminate\Support\Str::of($data[0]['item26'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item26" type="text" maxlength="10" name="item26" value="{{old('item26')}}">人
                      
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item26"></nobr>
                        </span>
                   
                </td>
                <td>
                <!-- トイレ - うち2人介助対象者 -->
                    @if(isset($data[0]['item27']))
                        @if($errors->has('item27'))
                        <input id="item27" type="text" maxlength="10" name="item27" value="{{old('item27')}}">人
                          
                        @else
                        <input id="item27" type="text" maxlength="10" name="item27" value="{{\Illuminate\Support\Str::of($data[0]['item27'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item27" type="text" maxlength="10" name="item27" value="{{old('item27')}}">人
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item27"></nobr>
                        </span>
                </td>
                <td>
                <!-- トイレ - ※平均介助時間/分 -->
                    @if(isset($data[0]['item28']))
                        @if($errors->has('item28'))
                        <input id="item28" type="text" maxlength="10" name="item28" value="{{old('item28')}}">分
                    
                        @else
                        <input id="item28" type="text" maxlength="10" name="item28" value="{{\Illuminate\Support\Str::of($data[0]['item28'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item28" type="text" maxlength="10" name="item28" value="{{old('item28')}}">分
             
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item28"></nobr>
                        </span>
                   
                </td>
                <td>
                <!-- トイレ - 一人あたり一日の平均移乗回数 -->

                    @if(isset($data[0]['item29']))
                        @if($errors->has('item29'))
                        <input id="item29" type="text" maxlength="10" name="item29" value="{{old('item29')}}">回
                       
                        @else
                        <input id="item29" type="text" maxlength="10" name="item29" value="{{\Illuminate\Support\Str::of($data[0]['item29'])->rtrim()}}">回
                        @endif
                    @else
                        <input id="item29" type="text" maxlength="10" name="item29" value="{{old('item29')}}">回
                      
                    @endif
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

                    @if(isset($data[0]['item30']))
                        
                        @if($errors->has('item30'))
                        <input id="item30" type="text" maxlength="10" name="item30" value="{{old('item30')}}">円
                      
                        @else
                        <input id="item30" type="text" maxlength="10" name="item30" value="{{\Illuminate\Support\Str::of($data[0]['item30'])->rtrim()}}">円
                        @endif
                    @else
                        <input id="item30" type="text" maxlength="10" name="item30" value="{{old('item30')}}">円
                        
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item30"></nobr>
                        </span>

                </td>
                <td>
                <!-- 求人 - 平均掲載期間 -->

                            @if(isset($data[0]['item31']))
                                @if($errors->has('item31'))
                                <input id="item31" type="text" maxlength="10" name="item31" value="{{old('item31')}}">ヵ月
                            
                                @else
                                <input id="item31" type="text" maxlength="10" name="item31" value="{{\Illuminate\Support\Str::of($data[0]['item31'])->rtrim()}}">ヵ月
                                @endif
                            @else
                                <input id="item31" type="text" maxlength="10" name="item31" value="{{old('item31')}}">ヵ月
                              
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item31"></nobr>
                                </span>
                </td>
                <td>
                <!-- 求人 - １ヵ月あたり面接人数 -->

                            @if(isset($data[0]['item32']))
                                @if($errors->has('item32'))
                                <input id="item32" type="text" maxlength="10" name="item32" value="{{old('item32')}}">人
                           
                                @else
                                <input id="item32" type="text" maxlength="10" name="item32" value="{{\Illuminate\Support\Str::of($data[0]['item32'])->rtrim()}}">人
                                @endif
                            @else
                                <input id="item32" type="text" maxlength="10" name="item32" value="{{old('item32')}}">人
                         
                            @endif
                            <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_item32"></nobr>
                                </span>
                           
                </td>
                <td>
                <!-- 求人 - 1人あたり平均面接時間 -->

                    @if(isset($data[0]['item33']))
                        @if($errors->has('item33'))
                        <input id="item33" type="text" maxlength="10" name="item33" value="{{old('item33')}}">分
                            
                        @else
                        <input id="item33" type="text" maxlength="10" name="item33" value="{{\Illuminate\Support\Str::of($data[0]['item33'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item33" type="text" maxlength="10" name="item33" value="{{old('item33')}}">分
                     
                    @endif
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
                <label for="item34">{{ __('新人研修 - 年間入社人数 ') }}</label>
                    @if(isset($data[0]['item34']))
                        @if($errors->has('item34'))
                        <input id="item34" type="text" maxlength="10" name="item34" value="{{old('item34')}}">人
                   
                        @else
                        <input id="item34" type="text" maxlength="10" name="item34" value="{{\Illuminate\Support\Str::of($data[0]['item34'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item34" type="text" maxlength="10" name="item34" value="{{old('item34')}}">人
                        
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item34"></nobr>
                        </span>
                </td>
                <td>
                <!-- 新人研修 - ※1人あたり研修担当職員数 -->
                    @if(isset($data[0]['item35']))
                        @if($errors->has('item35'))
                        <input id="item35" type="text" maxlength="10" name="item35" value="{{old('item35')}}">人
                       
                        @else
                        <input id="item35" type="text" maxlength="10" name="item35" value="{{\Illuminate\Support\Str::of($data[0]['item35'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item35" type="text" maxlength="10" name="item35" value="{{old('item35')}}">人
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item35"></nobr>
                        </span>
                </td>
                <td>
                <!-- 新人研修 - １日あたり研修時間 -->
                    @if(isset($data[0]['item36']))
                        @if($errors->has('item36'))
                        <input id="item36" type="text" maxlength="10" name="item36" value="{{old('item36')}}">分
                        
                        @else
                        <input id="item36" type="text" maxlength="10" name="item36" value="{{\Illuminate\Support\Str::of($data[0]['item36'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item36" type="text" maxlength="10" name="item36" value="{{old('item36')}}">分
                      
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item36"></nobr>
                        </span>
                </td>
                <td>
                <!-- 新人研修 - 研修期間日数 -->
                    @if(isset($data[0]['item37']))
                        @if($errors->has('item37'))
                        <input id="item37" type="text" maxlength="10" name="item37" value="{{old('item37')}}">日
                        
                        @else
                        <input id="item37" type="text" maxlength="10" name="item37" value="{{\Illuminate\Support\Str::of($data[0]['item37'])->rtrim()}}">日
                        @endif
                    @else
                        <input id="item37" type="text" maxlength="10" name="item37" value="{{old('item37')}}">日
                       
                    @endif
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
                    @if(isset($data[0]['item38']))
                        @if($errors->has('item38'))
                        <input id="item38" type="text" maxlength="10" name="item38" value="{{old('item38')}}">回
                            
                        @else
                        <input id="item38" type="text" maxlength="10" name="item38" value="{{\Illuminate\Support\Str::of($data[0]['item38'])->rtrim()}}">回
                        @endif
                    @else
                        <input id="item38" type="text" maxlength="10" name="item38" value="{{old('item38')}}">回
                        
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item38"></nobr>
                        </span>
                </td>
                <td>
                <!-- 外部研修 - 平均参加費/回 -->
                    @if(isset($data[0]['item39']))
                        @if($errors->has('item39'))
                        <input id="item39" type="text" maxlength="10" name="item39" value="{{old('item39')}}">円
                          
                        @else
                        <input id="item39" type="text" maxlength="10" name="item39" value="{{\Illuminate\Support\Str::of($data[0]['item39'])->rtrim()}}">円
                        @endif
                    @else
                        <input id="item39" type="text" maxlength="10" name="item39" value="{{old('item39')}}">円
                        
                    @endif
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
                    @if(isset($data[0]['item40']))
                        @if($errors->has('item40'))
                        <input id="item40" type="text" maxlength="12" name="item40" value="{{old('item40')}}">件
                           
                        @else
                        <input id="item40" type="text" maxlength="12" name="item40" value="{{\Illuminate\Support\Str::of($data[0]['item40'])->rtrim()}}">件
                        @endif
                    @else
                        <input id="item40" type="text" maxlength="12" name="item40" value="{{old('item40')}}">件
                    
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item40"></nobr>
                        </span>
                </td>
                <td>
                    <!-- 皮膚剥離 - 1件あたり処置時間 -->
                    @if(isset($data[0]['item41']))
                        @if($errors->has('item41'))
                        <input id="item41" type="text" maxlength="10" name="item41" value="{{old('item41')}}">分
                        @else
                        <input id="item41" type="text" maxlength="10" name="item41" value="{{\Illuminate\Support\Str::of($data[0]['item41'])->rtrim()}}">分
                 
                        @endif
                    @else
                        <input id="item41" type="text" maxlength="10" name="item41" value="{{old('item41')}}">分
                        
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item41"></nobr>
                        </span>
                </td>
                <td>
                    <!-- 皮膚剥離 - 1件あたり報告書作成時間 -->
                    @if(isset($data[0]['item42']))
                        @if($errors->has('item42'))
                        <input id="item42" type="text" maxlength="10" name="item42" value="{{old('item42')}}">分
                       
                        @else
                        <input id="item42" type="text" maxlength="10" name="item42" value="{{\Illuminate\Support\Str::of($data[0]['item42'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item42" type="text" maxlength="10" name="item42" value="{{old('item42')}}">分
               
                    @endif
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
                    @if(isset($data[0]['item43']))
                        @if($errors->has('item43'))
                        <input id="item43" type="text" maxlength="12" name="item43" value="{{old('item43')}}">件
                           
                        @else
                        <input id="item43" type="text" maxlength="12" name="item43" value="{{\Illuminate\Support\Str::of($data[0]['item43'])->rtrim()}}">件
                        @endif
                    @else
                        <input id="item43" type="text" maxlength="12" name="item43" value="{{old('item43')}}">件
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item43"></nobr>
                        </span>
                </td>
                <td>
                    <!-- 褥瘡 - 1件あたり処置時間 -->
                        @if(isset($data[0]['item44']))
                            @if($errors->has('item44'))
                            <input id="item44" type="text" maxlength="10" name="item44" value="{{old('item44')}}">分
                            
                            @else
                            <input id="item44" type="text" maxlength="10" name="item44" value="{{\Illuminate\Support\Str::of($data[0]['item44'])->rtrim()}}">分
                            @endif
                        @else
                            <input id="item44" type="text" maxlength="10" name="item44" value="{{old('item44')}}">分
                        
                        @endif
                        <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item44"></nobr>
                            </span>
                </td>
                <td>
                <!-- 褥瘡 - 1件あたり報告書作成時間 -->

                    @if(isset($data[0]['item45']))
                        @if($errors->has('item45'))
                        <input id="item45" type="text" maxlength="10" name="item45" value="{{old('item45')}}">分
                    
                        @else
                        <input id="item45" type="text" maxlength="10" name="item45" value="{{\Illuminate\Support\Str::of($data[0]['item45'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item45" type="text" maxlength="10" name="item45" value="{{old('item45')}}">分
                 
                    @endif
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
                <tr>
                <td>
                <!-- 骨折 - 年間発生件数 -->
                    @if(isset($data[0]['item46']))
                        @if($errors->has('item46'))
                        <input id="item46" type="text" maxlength="12" name="item46" value="{{old('item46')}}">件
                   
                        @else
                        <input id="item46" type="text" maxlength="12" name="item46" value="{{\Illuminate\Support\Str::of($data[0]['item46'])->rtrim()}}">件
                        @endif
                    @else
                        <input id="item46" type="text" maxlength="12" name="item46" value="{{old('item46')}}">件
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item46"></nobr>
                        </span>
                </td>
                <td></td>
                <td>
                <!-- 骨折 - 1件あたり報告書作成時間 -->
                    @if(isset($data[0]['item47']))
                        @if($errors->has('item47'))
                        <input id="item47" type="text" maxlength="10" name="item47" value="{{old('item47')}}">分
                          
                        @else
                        <input id="item47" type="text" maxlength="10" name="item47" value="{{\Illuminate\Support\Str::of($data[0]['item47'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item47" type="text" maxlength="10" name="item47" value="{{old('item47')}}">分
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item47"></nobr>
                        </span>
                </td>
            </tr>

            <tr>
                <td rowspan="2">通院</td>
                <td>年間発生件数</td>
                <td>一件あたり通院介助時間</td>
                <td>一件あたり介助者数</td>
            </tr>

            <tr>
                <td>
                    <!-- ※通院 - 年間発生件数 -->
                        @if(isset($data[0]['item48']))
                            @if($errors->has('item48'))
                            <input id="item48" type="text" maxlength="12" name="item48" value="{{old('item48')}}">件
                          
                            @else
                            <input id="item48" type="text" maxlength="12" name="item48" value="{{\Illuminate\Support\Str::of($data[0]['item48'])->rtrim()}}">件
                            @endif
                        @else
                            <input id="item48" type="text" maxlength="12" name="item48" value="{{old('item48')}}">件
                       
                        @endif
                        <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item48"></nobr>
                            </span>
                </td>
                <td>
                <!-- ※通院 - 1件あたり通院介助時間 -->

                    @if(isset($data[0]['item49']))
                        @if($errors->has('item49'))
                        <input id="item49" type="text" maxlength="10" name="item49" value="{{old('item49')}}">分
                         
                        @else
                        <input id="item49" type="text" maxlength="10" name="item49" value="{{\Illuminate\Support\Str::of($data[0]['item49'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item49" type="text" maxlength="10" name="item49" value="{{old('item49')}}">分
                        
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item49"></nobr>
                        </span>
                </td>
                <td>
                    <!-- ※通院 - １件あたり介助者数 -->

                    @if(isset($data[0]['item50']))
                        @if($errors->has('item50'))
                        <input id="item50" type="text" maxlength="10" name="item50" value="{{old('item50')}}">人
                          
                        @else
                        <input id="item50" type="text" maxlength="10" name="item50" value="{{\Illuminate\Support\Str::of($data[0]['item50'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item50" type="text" maxlength="10" name="item50" value="{{old('item50')}}">人
                     
                    @endif
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
                    @if(isset($data[0]['item51']))
                        @if($errors->has('item51'))
                        <input id="item51" type="text" maxlength="10" name="item51" value="{{old('item51')}}">件
             
                        @else
                        <input id="item51" type="text" maxlength="10" name="item51" value="{{\Illuminate\Support\Str::of($data[0]['item51'])->rtrim()}}">件
                        @endif
                    @else
                        <input id="item51" type="text" maxlength="10" name="item51" value="{{old('item51')}}">件
                   
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item51"></nobr>
                        </span>
                </td>
                <td>
                    <!-- ※入院 - １件あたり平均入院期間 -->
                    @if(isset($data[0]['item52']))
                        @if($errors->has('item52'))
                        <input id="item52" type="text" maxlength="10" name="item52" value="{{old('item52')}}">日
                  
                        @else
                        <input id="item52" type="text" maxlength="10" name="item52" value="{{\Illuminate\Support\Str::of($data[0]['item52'])->rtrim()}}">日
                        @endif
                    @else
                        <input id="item52" type="text" maxlength="10" name="item52" value="{{old('item52')}}">日
                
                    @endif
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
            
                @if(isset($data[0]['item53']))
                    @if($errors->has('item53'))
                    <input id="item53" type="text" maxlength="10" name="item53" value="{{old('item53')}}">人
            
                    @else
                    <input id="item53" type="text" maxlength="10" name="item53" value="{{\Illuminate\Support\Str::of($data[0]['item53'])->rtrim()}}">人
                    @endif
                @else
                    <input id="item53" type="text" maxlength="10" name="item53" value="{{old('item53')}}">人
                  
                @endif
                <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_item53"></nobr>
                    </span>
            </td>
            <td>
                <!-- オムツコスト（パッド含む） - 1日あたり総使用枚数 -->

                    @if(isset($data[0]['item54']))
                        @if($errors->has('item54'))
                        <input id="item54" type="text" maxlength="10" name="item54" value="{{old('item54')}}">枚
                      
                        @else
                        <input id="item54" type="text" maxlength="10" name="item54" value="{{\Illuminate\Support\Str::of($data[0]['item54'])->rtrim()}}">枚
                        @endif
                    @else
                        <input id="item54" type="text" maxlength="10" name="item54" value="{{old('item54')}}">枚
                   
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item54"></nobr>
                        </span>

                </td>
                <td>
                <!-- オムツコスト（パッド含む） - 1枚あたりオムツ単価 -->
                @if(isset($data[0]['item55']))
                    @if($errors->has('item55'))
                    <input id="item55" type="text" maxlength="10" name="item55" value="{{old('item55')}}">円
                     
                    @else
                    <input id="item55" type="text" maxlength="10" name="item55" value="{{\Illuminate\Support\Str::of($data[0]['item55'])->rtrim()}}">円
                    @endif
                @else
                    <input id="item55" type="text" maxlength="10" name="item55" value="{{old('item55')}}">円
           
                @endif
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
                <!-- 排泄介助コスト - オムツ使用者 -->

                @if(isset($data[0]['item56']))
                    @if($errors->has('item56'))
                    <input id="item56" type="text" maxlength="10" name="item56" value="{{old('item56')}}">人
              
                    @else
                    <input id="item56" type="text" maxlength="10" name="item56" value="{{\Illuminate\Support\Str::of($data[0]['item56'])->rtrim()}}">人
                    @endif
                @else
                    <input id="item56" type="text" maxlength="10" name="item56" value="{{old('item56')}}">人
                  
                @endif
                <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_item56"></nobr>
                    </span>
                </td>
                <td>
                <!-- 排泄介助コスト - うち二人介助を要する対象者数 -->

                    @if(isset($data[0]['item57']))
                        @if($errors->has('item57'))
                            <input id="item57" type="text" maxlength="10" name="item57" value="{{old('item57')}}">人
                        
                        @else
                        <input id="item57" type="text" maxlength="10" name="item57" value="{{\Illuminate\Support\Str::of($data[0]['item57'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item57" type="text" maxlength="10" name="item57" value="{{old('item57')}}">人
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item57"></nobr>
                        </span>
                </td>
                <td>
                <!-- 排泄介助コスト - 1人あたり所要時間 -->

                    @if(isset($data[0]['item58']))
                        
                        @if($errors->has('item58'))
                            <input id="item58" type="text" maxlength="10" name="item58" value="{{old('item58')}}">分
                       
                        @else
                        <input id="item58" type="text" maxlength="10" name="item58" value="{{\Illuminate\Support\Str::of($data[0]['item58'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item58" type="text" maxlength="10" name="item58" value="{{old('item58')}}">分
                       
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item58"></nobr>
                        </span>
                </td>
                <td>
                <!-- 排泄介助コスト - 1日あたり総排泄介助回数 -->
                    @if(isset($data[0]['item59']))
                        @if($errors->has('item59'))
                        <input id="item59" type="text" maxlength="10" name="item59" value="{{old('item59')}}">回
                          
                        @else
                        <input id="item59" type="text" maxlength="10" name="item59" value="{{\Illuminate\Support\Str::of($data[0]['item59'])->rtrim()}}">回
                        @endif
                    @else
                        <input id="item59" type="text" maxlength="10" name="item59" value="{{old('item59')}}">回
                      
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item59"></nobr>
                        </span>
                </td>
            </tr>

            <tr class="cs_hidden">
                <td>要全介助者</td>
                <td>うち二人介助を要する対象者数</td>
                <td>一人あたり介助所要時間</td>
                <td>利用者一人あたりの介助回数</td>
            </tr>
            <tr class="cs_hidden">
                            <td>
                            <!-- 排泄介助コスト - 要全介助者 -->
                                @if(isset($data[0]['item69']))                                   
                                    @if($errors->has('item69'))
                                        <input id="item69" type="text" maxlength="10" name="item69" value="{{old('item69')}}">人
                                      
                                    @else
                                    <input id="item69" type="text" maxlength="10" name="item69" value="{{\Illuminate\Support\Str::of($data[0]['item69'])->rtrim()}}">回
                                    @endif
                                @else
                                    <input id="item69" type="text" maxlength="10" name="item69" value="{{old('item69')}}">人
                                  
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item69"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - うち二人介助を要する対象者数 -->
                                @if(isset($data[0]['item70']))
                                    @if($errors->has('item70'))
                                        <input id="item70" type="text" maxlength="10" name="item70" value="{{old('item70')}}">人
                                        
                                    @else
                                    <input id="item70" type="text" maxlength="10" name="item70" value="{{\Illuminate\Support\Str::of($data[0]['item70'])->rtrim()}}">回
                                    @endif
                                @else
                                    <input id="item70" type="text" maxlength="10" name="item70" value="{{old('item70')}}">人
                                  
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item70"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - 一人あたり介助所要時間 -->
                                @if(isset($data[0]['item71']))
                                   
                                    @if($errors->has('item71'))
                                        <input id="item71" type="text" maxlength="10" name="item71" value="{{old('item71')}}">人
                                  
                                    @else
                                    <input id="item71" type="text" maxlength="10" name="item71" value="{{\Illuminate\Support\Str::of($data[0]['item71'])->rtrim()}}">回
                                    @endif
                                @else
                                    <input id="item71" type="text" maxlength="10" name="item71" value="{{old('item71')}}">人
                                    
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item71"></nobr>
                                    </span>
                            </td>
                            <td>
                            <!-- 排泄介助コスト - 利用者一人あたりの介助回数 -->
                                @if(isset($data[0]['item72']))
                                    
                                    @if($errors->has('item72'))
                                        <input id="item72" type="text" maxlength="10" name="item72" value="{{old('item72')}}">人
                                 
                                    @else
                                    <input id="item72" type="text" maxlength="10" name="item72" value="{{\Illuminate\Support\Str::of($data[0]['item72'])->rtrim()}}">回
                                    @endif
                                @else
                                    <input id="item72" type="text" maxlength="10" name="item72" value="{{old('item72')}}">人
                                   
                                @endif
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
                <!-- 食事介助コスト - 要食事介助者 -->

                    @if(isset($data[0]['item60']))
                                              
                        @if($errors->has('item60'))
                            <input id="item60" type="text" maxlength="10" name="item60" value="{{old('item60')}}">人
                        
                        @else
                        <input id="item60" type="text" maxlength="10" name="item60" value="{{\Illuminate\Support\Str::of($data[0]['item60'])->rtrim()}}">人  
                        @endif
                    @else
                        <input id="item60" type="text" maxlength="10" name="item60" value="{{old('item60')}}">人
                      
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item60"></nobr>
                        </span>
                </td>
                <td>
                <!-- 食事介助コスト - 1日あたりのべ介助者数 -->
                    @if(isset($data[0]['item61']))
                        
                        @if($errors->has('item61'))
                            <input id="item61" type="text" maxlength="10" name="item61" value="{{old('item61')}}">人
                        
                        @else
                        <input id="item61" type="text" maxlength="10" name="item61" value="{{\Illuminate\Support\Str::of($data[0]['item61'])->rtrim()}}">人
                        @endif
                    @else
                        <input id="item61" type="text" maxlength="10" name="item61" value="{{old('item61')}}">人
                     
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item61"></nobr>
                        </span>
                </td>
                <td>
                <!-- 食事介助コスト - 1人あたり介助所要時間 -->
                    @if(isset($data[0]['item62']))
                        
                        @if($errors->has('item62'))
                            <input id="item62" type="text" maxlength="10" name="item62" value="{{old('item62')}}">分
                           
                        @else
                        <input id="item62" type="text" maxlength="10" name="item62" value="{{\Illuminate\Support\Str::of($data[0]['item62'])->rtrim()}}">分
                        @endif
                    @else
                        <input id="item62" type="text" maxlength="10" name="item62" value="{{old('item62')}}">分
                   
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item62"></nobr>
                        </span>
                </td>
                <td>
                <!-- 食事介助コスト - 一日辺りの食事介助回数 -->

                    @if(isset($data[0]['item63']))
                        
                        @if($errors->has('item63'))
                        <input id="item63" type="text" maxlength="10" name="item63" value="{{old('item63')}}">回
                           
                        @else
                        <input id="item63" type="text" maxlength="10" name="item63" value="{{\Illuminate\Support\Str::of($data[0]['item63'])->rtrim()}}">回
                        @endif
                    @else
                        <input id="item63" type="text" maxlength="10" name="item63" value="{{old('item63')}}">回
                     
                    @endif
                    <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_item63"></nobr>
                        </span>
                </td>
            </tr>
        
        
            <!-- 座位修正介助コスト -->
            <tr class="cs_hidden">
                <td rowspan="2">座位修正介助コスト</td>
                <td>対象者</td>
                <td>一日あたりのべ介助者数</td>
                <td>一人あたり介助所要時間</td>
                <td>一日あたり総介助回数</td>
            </tr>
            <tr class="cs_hidden">
            <td>
                    <!-- 座位修正介助コスト - 対象者 -->
                        @if(isset($data[0]['item64']))
                            
                            @if($errors->has('item64'))
                                <input id="item64" type="text" maxlength="10" name="item64" value="{{old('item64')}}">人
                                
                            @else
                            <input id="item64" type="text" maxlength="10" name="item64" value="{{\Illuminate\Support\Str::of($data[0]['item64'])->rtrim()}}">人
                            @endif
                        @else
                            <input id="item64" type="text" maxlength="10" name="item64" value="{{old('item64')}}">人
                          
                        @endif

                        <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item64"></nobr>
                            </span>
                    </td>
                    <td>
                    <!-- 座位修正介助コスト - 一日あたりのべ介助者数 -->
                        @if(isset($data[0]['item65']))
                           
                            @if($errors->has('item65'))
                                <input id="item65" type="text" maxlength="10" name="item65" value="{{old('item65')}}">人
                                
                            @else
                            <input id="item65" type="text" maxlength="10" name="item65" value="{{\Illuminate\Support\Str::of($data[0]['item65'])->rtrim()}}">人
                            @endif
                        @else
                            <input id="item65" type="text" maxlength="10" name="item65" value="{{old('item65')}}">人
                            
                        @endif
                        <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item65"></nobr>
                            </span>
                    </td>
                    <td>
                    <!-- 座位修正介助コスト - 1人あたり介助所要時間 -->
                        @if(isset($data[0]['item66']))
                            
                            @if($errors->has('item66'))
                                <input id="item66" type="text" maxlength="12" name="item66" value="{{old('item66')}}">分
                                
                            @else
                            <input id="item66" type="text" maxlength="12" name="item66" value="{{\Illuminate\Support\Str::of($data[0]['item66'])->rtrim()}}">分
                            @endif
                        @else
                            <input id="item66" type="text" maxlength="12" name="item66" value="{{old('item66')}}">分
                            
                        @endif
                        <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item66"></nobr>
                            </span>
                    </td>
                    <td>
                    <!-- 座位修正介助コスト - 1日あたり総介助回数 -->
                        @if(isset($data[0]['item67']))
                           
                            @if($errors->has('item67'))
                                <input id="item67" type="text" maxlength="10" name="item67" value="{{old('item67')}}">回                                
                            @else
                            <input id="item67" type="text" maxlength="10" name="item67" value="{{\Illuminate\Support\Str::of($data[0]['item67'])->rtrim()}}">回
                            @endif
                        @else
                            <input id="item67" type="text" maxlength="10" name="item67" value="{{old('item67')}}">回
                          
                        @endif
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
                @if(isset($data[0]['item68']))
                    @if($errors->has('item66'))
                    <!-- 腰痛保持率 -->
                        <input id="item68" type="text" maxlength="10" name="item68" value="{{old('item68')}}">%    
                    @else
                        <input id="item68" type="text" maxlength="10" name="item68" value="{{\Illuminate\Support\Str::of($data[0]['item68'])->rtrim()}}">%
                    @endif                 
                @else
                    <input id="item68" type="text" maxlength="10" name="item68" value="{{old('item68')}}">%
                @endif
                    <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_item68"></nobr>
                    </span>
            </td>
        </tr>
        </table>
    </div>

    @if($target == 0)
    <!-- 追加ボタン -->
        <input id="btn_addfacility_input" type="image" src="image/img_yes.png" alt="修正" border="0">

    @else
    <!-- 修正ボタン -->
        <input id="btn_fixfacility_input" type="image" src="image/img_yes.png" alt="修正" border="0">
        
    @endif    
    </form>
    </div>
@endsection
