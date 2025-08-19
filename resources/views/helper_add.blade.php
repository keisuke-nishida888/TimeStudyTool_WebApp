@extends('layouts.parent')

@section('content')
<script src="/js/helper.js"></script>

<div class="allcont">
<input type="image" id="btn_addhelper_pre" src="image/img_add.png" alt="追加" onclick="VisibleChange(this.id)" border="0">


<!-- キャンセルボタン -->
<input type="image" id="btn_cxl" src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_helperadd')" border="0">



@if(isset($data2[0]['id']))
    <form action = '/helper_fix?facilityno={{$data2[0]['id']}}' method = "post" name = "fix_index">
@else
    <form action = '/helper_fix'  method = "post"  name = "fix_index">
@endif
@csrf
<input id="targetid" type="hidden" name="id" value="">
<input type="hidden" name="addmess" value="追加しました。">
</form>



@if(isset($data2[0]['id']))
    <form action = '/helper_addctrl?facilityno={{$data2[0]['id']}}'
        method = "post" id="form_helperadd" name = "form_helperadd" enctype="multipart/form-data" autocomplete="off">
@else
    <form action = '/helper_addctrl'  method = "post" id="form_helperadd"  name = "form_helperadd" enctype="multipart/form-data" autocomplete="off">
@endif
        <div class="container">   
        @csrf
            <table class ="tb">
                <tr></tr>
                <!-- 施設名 -->
                <tr>
                    <td><label for="facility">{{ __('施設名') }}</label></td>
                    <td>
                        @if(isset($data2[0]['facility'])&& isset($data2[0]['id']))
                            <input  type="text"  value={{$data2[0]['facility']}} disabled>
                            <input id="facilityno" type="hidden" name="facilityno" value="{{\Illuminate\Support\Str::of($data2[0]['id'])->rtrim()}}">
                            
                        @else
                            <input  type="text"  value="" disabled>
                            <input id="facilityno" type="hidden" name="facilityno" value="">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                    <!-- No -->
                        <label for="id">{{ __('No') }}</label></td>
                    <td>
                            <input  type="text" value="" disabled>
                            <input id="id" type="hidden" name="id" value="">                                
                    </td>
                </tr>
                    
                <!-- 作業者名 -->
                <tr>
                    <td><label for="helpername">{{ __('作業者名') }}</label></td>
                    <td>
                        <input id="helpername" type="text" maxlength="20" name="helpername" value="{{old('helpername')}}">

                        <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_helpername"></nobr>
                        </span>
                        </td>
                </tr>

                <!-- ウェアラブルデバイス名 -->
                {{-- 
                <tr>
                    <td><label for="wearableno">{{ __('心拍センサー名') }}</label></td>
                    <td>
                        <select  id="wearableno"  name="wearableno" value="{{old('wearableno')}}">
                            <option value="">-</option>
                            @foreach($wearable as $val)
                                <option value={{$val['id']}}>{{$val['devicename']}}</option>
                            @endforeach
                            </select>

                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_wearableno"></nobr>
                            </span>
                    </td>
                </tr>
                --}}
                <!-- 職種 -->
                <tr>
                    <td><label for="position">{{ __('職種') }}</label></td>
                    <td>
                        <select  id="position" name="position" value="{{ old('position') }}">
                        @foreach($code as $val)
                            @if($val['codeno']==3) <option value={{$val['value']}}>{{$val['selectname']}}</option>
                            @endif
                        @endforeach
                        </select>

                        <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_position"></nobr>
                        </span>
                    </td>
                </tr>

                <!-- 腰痛リスクデバイス名 -->
                {{-- 
                <tr>
                    <td><label for="backpainno">{{ __('腰痛リスクデバイス名') }}</label></td>
                    <td>
                        <select  id="backpainno" name="backpainno" value="{{old('backpainno')}}">
                        <option value="">-</option>
                        @foreach($backPain as $val)
                            <option value={{$val['id']}}>{{$val['devicename']}}</option>
                        @endforeach
                        </select>

                        <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_backpainno"></nobr>
                        </span>
                    </td>
                </tr>
                --}}
                <!-- 年齢 -->
                <tr>
                    <td><label for="age">{{ __('年齢') }}</label></td>
                    <td>
                        <input id="age" type="text" maxlength="2" name="age" value="{{old('age')}}">


                        <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_age"></nobr>
                        </span>
                        </td>
                </tr>

                <!-- 性別 -->
                <tr>
                    <td><label for="sex">{{ __('性別') }}</label></td>
                    <td>
                        <select  id="sex" name="sex" value="{{ old('sex') }}">
                        @foreach($code as $val)
                            @if($val['codeno']==4) <option value={{$val['value']}}>{{$val['selectname']}}</option>
                            @endif
                        @endforeach
                        </select>
                        <span class="invalid-feedback validate" role="alert">
                            <nobr id="err_sex"></nobr>
                        </span>
                    </td>
                </tr>

                {{-- 
                <!-- 勤務時間 -->
                <tr>
                    <td><label for="jobfrom">{{ __('勤務時間') }}</label></td>
                    <td>
                            <input class="jobfrom" maxlength="2" size="2" type="text" name="jobfrom_h"
                            id="jobfrom_h" autocomplete="off" style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" value = "00" 
                            onchange="select_ctrl(this.id)"
                            oncut = "return false" onpaste="return false" oncontextmenu = "return false">
                            
                            :
                            <input class="jobfrom" maxlength="2" size="2" type="text" name="jobfrom_m"
                            id="jobfrom_m" autocomplete="off" style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" value = "00" 
                            onchange="select_ctrl(this.id)"
                            oncut = "return false" onpaste="return false" oncontextmenu = "return false">
                            
                    ~
                    
                        <input class="jobto" maxlength="2" size="2" type="text" name="jobto_h"
                            id="jobto_h" autocomplete="off" style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" value = "00" 
                            onchange="select_ctrl(this.id)"
                            oncut = "return false" onpaste="return false" oncontextmenu = "return false">                       

                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_jobto"></nobr>
                            </span>
                            :
                            <input class="jobto" maxlength="2" size="2" type="text" name="jobto_m"
                            id="jobto_m" autocomplete="off" style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" value = "00" 
                            onchange="select_ctrl(this.id)"
                            oncut = "return false" onpaste="return false" oncontextmenu = "return false">                       

                            <span class="invalid-feedback validate" role="alert">
                                <nobr id="err_job"></nobr>
                            </span>
                            
                    </td>
                </tr> 
                <tr>
                    <td></td>
                <td>
                    <select id="jobfrom_h_sel" style="visibility:Hidden;width:calc(70px / var(--coef));" onchange="select_change(this.id)">                                    
                        @for($i=0;$i<24;$i++)
                                <?php               
                                    $hh = $i;
                                    $hh = sprintf('%02d',$hh);                                                 
                                    $tmp = <<<EOF
                                        <option value='$hh'>$hh</option>
                                    EOF;
                                    echo $tmp;
                                ?>
                        @endfor
                    </select>
                    &nbsp;
                    <select id="jobfrom_m_sel" style="visibility:Hidden;width:calc(70px / var(--coef));" onchange="select_change(this.id)">
                        @for($j=0;$j<12;$j++)
                            <?php               
                                $mm = $j*5;
                                $mm = sprintf('%02d',$mm);                                                    
                                $tmp = <<<EOF
                                    <option value='$mm'>$mm</option>
                                EOF;
                                echo $tmp; 
                            ?>
                        @endfor
                    </select>
                    &nbsp;&nbsp;&nbsp;
                    <select id="jobto_h_sel" style="visibility:Hidden;width:calc(70px / var(--coef));" onchange="select_change(this.id)">
                        @for($i=0;$i<24;$i++)
                            <?php                 
                                $hh = $i;
                                $hh = sprintf('%02d',$hh);                                           
                                $tmp = <<<EOF
                                    <option value='$hh'>$hh</option>
                                EOF;
                                echo $tmp;
                            ?>
                        @endfor
                        </select>
                    &nbsp;
                    <select id="jobto_m_sel" style="visibility:Hidden;width:calc(70px / var(--coef));" onchange="select_change(this.id)">
                        @for($j=0;$j<12;$j++)
                            <?php             
                                $mm = $j*5;
                                $mm = sprintf('%02d',$mm);                                                    
                                $tmp = <<<EOF
                                    <option value='$mm'>$mm</option>
                                EOF;
                                echo $tmp;
                            ?>
                        @endfor
                        </select>
                    </td>
                </tr>


                <!-- 日付入力 -->
                <tr>
                    <td><label for="measufrom">{{ __('計測期間') }}</label></td>
                    <td>
                        <input type="date" id="measufrom" name="measufrom"></input>
                    ~                    
                        <input type="date" id="measuto" name="measuto"></input>
                    </td>
                </tr>
                <tr>
                <td>
                    <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_measufrom"></nobr>
                    </span>
                </td>
                <td>
                    <span class="invalid-feedback validate" role="alert">
                        <nobr id="err_measuto"></nobr>
                    </span>
                </td>

                </tr> --}}
        </table>
    
    </div>
        <!-- 追加ボタン -->
        <input id="btn_addhelper" type="image" src="image/img_yes.png" alt="追加" border="0">

    </form>
</div>
@endsection


