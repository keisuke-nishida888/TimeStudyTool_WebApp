@extends('layouts.parent')


@section('content')
<script src="/js/facility.js"></script>

<div class="allcont">

<input type="image" id="btn_fixfacility_pre"  src="image/img_fix.png" alt="修正" onclick="VisibleChange(this.id)" border="0" autocomplete="off">

<!-- キャンセルボタン -->
<input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_facilityfix')" border="0" autocomplete="off">
<form  action = '/facility_fix' name = "fix_index"  method = "post">
@csrf
@if(isset($adddata[0]['id']))
    <input type="hidden" name="id" value="{{$adddata[0]['id']}}">
@else
    <input type="hidden" name="id" value="{{old('id')}}">
@endif
<input type="image" id="btn_cxl_do" style="visibility:hidden;"  src="image/img_yes.png" alt="はい" border="0">
</form>



            
<form method="POST" id="form_facilityfix" action="facility_fixctrl" name = "form_facilityfix" enctype="multipart/form-data">
@csrf
    <div class="container_facil">   
        <table  class="facil_table1">
                    <!-- 施設概要 -->
                    <tr><td  colspan="3"><label class = "lb">{{ __('施設概要') }}</label></td></tr>

                    <!-- No -->
                    <tr>
                        <td><label for="id">{{ __('No') }}</label></td>

                        <td>
                            @if(isset($adddata[0]['id']))
                                <input  type="text" value={{$adddata[0]['id']}} disabled>
                                <input id="id" type="hidden"  name="id" value={{$adddata[0]['id']}}>
                            @else
                                <input type="text" value="{{old('id')}}" disabled>
                                <input id="id" type="hidden"  name="id" value="{{old('id')}}">
                            @endif                                                    
                        </td>
                    </tr>

                  
                        <!-- 施設名 -->
                        <!-- ログインしているユーザから取得？ -->
                    <tr>
                        <td> <label for="facility">{{ __('施設名') }}</label></td>
                        <td>
                            @if(isset($adddata[0]['facility']))
                                <input id="facility" type="text" maxlength="20" name="facility" value="{{\Illuminate\Support\Str::of($adddata[0]['facility'])->rtrim()}}" autofocus>
                            
                            @else
                                <input id="facility" type="text" maxlength="20" name="facility" value="{{old('facility')}}"  autofocus>
                            
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

                                @if(isset($adddata[0]['pass']))
                                    <select  id="pass"  name="pass" value="{{$adddata[0]['pass']}}">
                                @else
                                    <select  id="pass"  name="pass" value="{{old('pass')}}">
                                @endif
                                @if(isset($adddata[0]['pass']))
                                    @foreach($code as $valcode)
                                        @if($valcode['codeno']==2)
                                            @if($valcode['value'] == $adddata[0]['pass'])
                                                <option value={{$valcode['value']}} selected>{{$valcode['selectname']}}</option>
                                            @else 
                                                <option value={{$valcode['value']}}>{{$valcode['selectname']}}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    @foreach($code as $valcode)
                                        @if($valcode['codeno']==2)
                                            <option value={{$valcode['value']}}>{{$valcode['selectname']}}</option>
                                        @endif
                                    @endforeach
                                @endif
                                </select>
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_pass"></nobr>
                                </span>
                            </td>
                        </tr>


                        <!-- 住所 -->
                        <tr>
                            <td><label for="address">{{ __('住所') }}</label></td>
                            <td>
                                @if(isset($adddata[0]['address']))
                                        <input id="address" type="text" maxlength="100" name="address" value="{{\Illuminate\Support\Str::of($adddata[0]['address'])->rtrim()}}">
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
                                @if(isset($adddata[0]['address']))
                                        <input id="tel" type="text" maxlength="20" name="tel" value="{{\Illuminate\Support\Str::of($adddata[0]['tel'])->rtrim()}}">
                                @else
                                    <input id="tel" type="text" maxlength="20" name="tel" value="{{old('tel')}}">                        
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

                                @if(isset($adddata[0]['mail']))
                                    <input id="mail" type="text" maxlength="40" name="mail" value="{{\Illuminate\Support\Str::of($adddata[0]['mail'])->rtrim()}}">
                                @else
                                    <input id="mail" type="text" maxlength="40" name="mail" value="{{old('mail')}}">                        
                                @endif
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_mail"></nobr>
                                </span>
                            </td>
                        </tr>

                       <!-- 2021.05.18　追加 -->                
                        <!-- アンケートサイト -->
                        <!-- 一般ユーザでログインしたときのみ -->
                        <tr>
                            <td><label for="url">{{ __('アンケートサイトURL') }}</label></td>
                            <td>
                                @if(isset($adddata[0]['questurl']))
                                    <input id="url" type="text" maxlength="256" name="url" value="{{\Illuminate\Support\Str::of($adddata[0]['questurl'])->rtrim()}}">
                                @else
                                    <input id="url" type="text" maxlength="256" name="url" value="{{old('url')}}">                        
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
                                @if(isset($adddata[0]['item1']))
                                    <input id="item1" type="text" maxlength="10" name="item1" value="{{\Illuminate\Support\Str::of($adddata[0]['item1'])->rtrim()}}">人
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
                                    @if(isset($adddata[0]['item2']))
                                        <input id="item2" type="text" maxlength="10" name="item2" value="{{\Illuminate\Support\Str::of($adddata[0]['item2'])->rtrim()}}">名
                                    @else
                                        <input id="item2" type="text" maxlength="10" name="item2" value="{{old('item2')}}">名     
                                    @endif

                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item2"></nobr>
                                    </span>
                                </td>

                            <!-- 従来型 -->
                                <td><label for="item3">{{ __('従来型(一フロア)') }}</label>
                                    @if(isset($adddata[0]['item3']))
                                        <input id="item3" type="text" maxlength="10"  name="item3" value="{{\Illuminate\Support\Str::of($adddata[0]['item3'])->rtrim()}}">名
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
                                @if(isset($adddata[0]['item4']))
                                    <input id="item4" type="text" maxlength="10" name="item4" value="{{\Illuminate\Support\Str::of($adddata[0]['item4'])->rtrim()}}">名
                                @else
                                    <input id="item4" type="text" maxlength="10" name="item4" value="{{old('item4')}}">名
                                @endif
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
                                    @if(isset($adddata[0]['item5']))
                                        <input id="item5" type="text" maxlength="10" name="item5" value="{{\Illuminate\Support\Str::of($adddata[0]['item5'])->rtrim()}}">名
                                    @else
                                        <input id="item5" type="text" maxlength="10"  name="item5" value="{{old('item5')}}">名
                                    @endif

                                    <span class="invalid-feedback validate" role="alert">
                                        <nobr id="err_item5"></nobr>
                                    </span>
                                </td>

                            <!-- 従来型 -->
                                <td><label for="item6">{{ __('従来型(一フロア)') }}</label>

                                    @if(isset($adddata[0]['item6']))
                                        <input id="item6" type="text" maxlength="10" name="item6" value="{{\Illuminate\Support\Str::of($adddata[0]['item6'])->rtrim()}}">名
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
                            @if(isset($adddata[0]['item7']))
                                <input id="item7" type="text" maxlength="10" name="item7" value="{{\Illuminate\Support\Str::of($adddata[0]['item7'])->rtrim()}}">人/日
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
                            @if(isset($adddata[0]['item8']))
                                <input id="item8" type="text" maxlength="10" name="item8" value="{{\Illuminate\Support\Str::of($adddata[0]['item8'])->rtrim()}}">円
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
                            <td><label for="item9">定員一名あたり<br>サービス活動収益費(日額)</label></td>
                            <td>
                            @if(isset($adddata[0]['item9']))
                                <input id="item9" type="text" maxlength="10" name="item9" value="{{\Illuminate\Support\Str::of($adddata[0]['item9'])->rtrim()}}">円
                            @else
                                <input id="item9" type="text" maxlength="10" name="item9" value="{{old('item9')}}">円
                            @endif  

                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_item9"></nobr>
                            </span>
                            </td>
                        </tr>
                        <!-- ▼ グループ（既存＋追加） -->
                        <tr>
                        <td><label>グループ</label></td>
                        <td colspan="2">
                            <div id="group-fields">
                            @forelse($groups ?? [] as $g)
                                <div class="group-row" style="margin-bottom:6px;">
                                {{-- 既存グループはIDを hidden で持ち、名前は上書き可能 --}}
                                <input type="hidden" name="group_ids[]" value="{{ $g->group_id }}">
                                <input type="text"
                                        name="group_names[]"
                                        maxlength="100"
                                        value="{{ old('group_names.'.$loop->index, $g->group_name) }}"
                                        placeholder="グループ名">
                                </div>
                            @empty
                                {{-- 既存が無いときは空で1行出しておいてもOK（任意） --}}
                            @endforelse
                            </div>

                            {{-- ＋ボタン（新規行を追加） --}}
                            <button type="button" id="btn_add_group" style="margin-top:6px;" onclick="addGroupField()">＋</button>

                            <div>
                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_groups"></nobr>
                            </span>
                            </div>
                        </td>
                        </tr>

        <!-- 修正ボタン -->
        <input id="btn_fixfacility" type="image" src="image/img_yes.png" alt="修正" border="0">
 
</form>
</div>
@endsection

<script>
function addGroupField() {
  const wrap = document.getElementById('group-fields');
  const div  = document.createElement('div');
  div.className = 'group-row';
  div.style.marginBottom = '6px';

  // 新規は ID を持たない。名前だけ new_group_names[] で送る
  div.innerHTML =
    '<input type="text" name="new_group_names[]" maxlength="100" ' +
    'placeholder="新規グループ名">';

  wrap.appendChild(div);
}
</script>
