@extends('layouts.parent')

@section('content')
<script src="/js/helper.js"></script>

<div class="allcont">
<input type="image" id="btn_fixhelper_pre"  src="image/img_fix.png" alt="修正" onclick="VisibleChange(this.id)" border="0">

<!-- キャンセルボタン -->
<input type="image" id="btn_cxl"  src="image/img_cancel.png" alt="キャンセル" onclick="VisibleChange('form_helperfix')" border="0">
<!-- キャンセルボタン -->
    @if(isset($data2[0]['id']))
        <form action = '/helper_fix?facilityno={{$data2[0]['id']}}' method = "post">
    @else
        <form action = '/helper_fix?facilityno={{old('facilityno')}}'  method = "post">
    @endif
    @csrf
    @if(isset($data[0]['id']))
        <input id="id" type="hidden" name="id" value="{{$data[0]['id']}}">
    @else
        <input id="id" type="hidden" name="id" value="{{old('id')}}">
    @endif
<input type="image" id="btn_cxl_do" style="visibility:hidden;"  src="image/img_yes.png" alt="はい" border="0">
</form>

@if(isset($data2[0]['id']))
    <form action = '/helper_fix?facilityno={{$data2[0]['id']}}' method = "post" name = "fix_index">
@else
    <form action = '/helper_fix'  method = "post"  name = "fix_index">
@endif
@csrf
<input id="targetid" type="hidden" name="id" value="">
<input type="hidden" name="addmess" value="追加しました。">
</form>


{{-- 
@if(isset($data2[0]['id'])) <form method="POST" id="form_helperfix"  action="helper_fixctrl?facilityno={{$data2[0]['id']}}" name = "form_helperfix" enctype="multipart/form-data" onsubmit = "return datechx()" autocomplete="off">
@else <form method="POST" id="form_helperfix" action="helper_fixctrl?facilityno={{old('facilityno')}}" name = "form_helperfix" enctype="multipart/form-data" onsubmit = "return datechx()" autocomplete="off">
--}}
@if(isset($data2[0]['id'])) <form method="POST" id="form_helperfix"  action="helper_fixctrl?facilityno={{$data2[0]['id']}}" name = "form_helperfix" enctype="multipart/form-data" onsubmit = "return datechx()" autocomplete="off">
@else <form method="POST" id="form_helperfix" action="helper_fixctrl?facilityno={{old('facilityno')}}" name = "form_helperfix" enctype="multipart/form-data" onsubmit = "return datechx()" autocomplete="off">

@endif
@csrf


<div class="container">

<table  class="tb">
            @if(isset($data2[0]['facility'])) <input type="hidden"  name="facilityno" value="{{$data2[0]['id']}}">
            @else <input type="hidden" name="facilityno" value="{{old('facilityno')}}">
            @endif
                <tr></tr>
                        <!-- 施設名 -->
                        <tr>
                            <td><label for="facility">{{ __('施設名') }}</label></td>
                            <td>
                                @if(isset($data2[0]['facility']))
                                    <input type="text" value={{$data2[0]['facility']}} disabled>
                                    <input id="facility" type="hidden" name="facility" value="{{$data2[0]['id']}}">
                                    <input type="hidden" name="faciname" value="{{$data2[0]['facility']}}">
                                @else
                                    <input  type="text" value="{{old('faciname')}}" disabled>
                                    <input id="facility" type="hidden"  name="facility" value="{{old('facilityno')}}">
                                    <input type="hidden" name="faciname" value="{{old('faciname')}}">
                                @endif
                                @if($errors->has('facility'))
                                    <span class="invalid-feedback validate" role="alert">
                                    {{$errors->first('facility')}}
                                    </span>
                                @endif
                                </td>
                        </tr>

                        <!-- No -->
                        <tr>
                            <td><label for="id">{{ __('No') }}</label></td>
                            <td>

                                @if(isset($data[0]['id']))
                                    <input type="text" value="{{$data[0]['id']}}" disabled>
                                    <input id="id" type="hidden" name="id" value="{{$data[0]['id']}}">
                                @else
                                    <input type="text" value="{{old('id')}}" disabled>
                                    <input id="id" type="hidden" name="id" value="{{old('id')}}">
                                @endif
                            </td>
                        </tr>
                        
                        <!-- 介助者名 -->
                        <tr>
                            <td><label for="helpername">{{ __('介助者名') }}</label></td>
                            <td>

                                @if(isset($data[0]['helpername']))
                                    <input id="helpername" type="text" maxlength="20" name="helpername" value="{{\Illuminate\Support\Str::of($data[0]['helpername'])->rtrim()}}" autofocus>
                                @else
                                    <input id="helpername" type="text" maxlength="20" name="helpername" value="{{old('helpername')}}" autofocus>
                                @endif
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
                                @if(isset($data[0]['wearableno'])) <select  id="wearableno" name="wearableno" value="{{$data[0]['wearableno']}}">
                                @else <select  id="wearableno" name="wearableno" value="{{old('wearableno')}}">
                                @endif
                                
                                <option value="0">-</option>
                                @foreach($wearable as $val)
                                    @if(isset($data[0]['wearableno'])) 
                                        @if($val['id'] == $data[0]['wearableno']) <option value={{$val['id']}} selected>{{$val['devicename']}}</option>
                                        @else <option value={{$val['id']}}>{{$val['devicename']}}</option>
                                        @endif     
                                    @else                                    
                                        @if(old('wearableno') == $val['id'])
                                            <option value={{$val['id']}} selected>{{$val['devicename']}}</option>
                                        @else
                                            <option value={{$val['id']}}>{{$val['devicename']}}</option>
                                        @endif
                                    @endif                               
                                @endforeach
                                </select>


                                @if(isset($data[0]['wearableno']))
                                    <text type="hidden" name="device_pre" vale="{{$data[0]['wearableno']}}">
                                @else
                                    <text type="hidden" name="device_pre" vale="{{old('wearableno')}}">
                                @endif
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
                            @if(isset($data[0]['id'])) <select  id="position"  name="position" value="{{$data[0]['position']}}">
                            @else <select  id="position"  name="position" value="{{old('position')}}">
                            @endif
                            @foreach($code as $val)
                                @if($val['codeno']==3)
                                    @if(isset($data[0]['position']))
                                        @if($val['value'] ==  $data[0]['position']) <option value={{$val['value']}} selected>{{$val['selectname']}}</option>
                                        @else <option value={{$val['value']}}>{{$val['selectname']}}</option>
                                        @endif
                                    @else
                                        @if(old('position') == $val['value'])
                                            <option value={{$val['value']}} selected>{{$val['selectname']}}</option>
                                        @else
                                            <option value={{$val['value']}}>{{$val['selectname']}}</option>
                                        @endif
                                    @endif
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

                                @if(isset($data[0]['backpainno'])) <select  id="backpainno"  name="backpainno" value="{{$data[0]['backpainno']}}">
                                @else <select  id="backpainno"  name="backpainno" value="{{old('backpainno')}}">
                                @endif
                            
                                <option value="{{old('pass')}}">-</option>
                                @foreach($backPain as $val)
                                    @if(isset($data[0]['backpainno']))
                                        @if($val['id'] == $data[0]['backpainno']) <option value={{$val['id']}} selected>{{$val['devicename']}}</option>
                                        @else <option value={{$val['id']}}>{{$val['devicename']}}</option>
                                        @endif    
                                    @else
                                        @if(old('wearableno') == $val['id'])
                                            <option value={{$val['id']}} selected>{{$val['devicename']}}</option>
                                        @else
                                            <option value={{$val['id']}}>{{$val['devicename']}}</option>
                                        @endif

                                    @endif                                
                                @endforeach
                                </select>

                                @if(isset($data[0]['backpainno'])) <text type="hidden" name="backpainno_pre" vale="{{$data[0]['backpainno']}}">
                                @else <text type="hidden" name="backpainno_pre" vale="{{old('backpainno')}}">
                                @endif
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

                                @if(isset($data[0]['age']))
                                    <input id="age" type="text" maxlength="2" name="age" value="{{\Illuminate\Support\Str::of($data[0]['age'])->rtrim()}}">
                                @else
                                    <input id="age" type="text" maxlength="2" name="age" value="{{old('age')}}">                              
                                @endif

                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_age"></nobr>
                                </span>
                            </td>
                        </tr>
                        <!-- 性別 -->
                        

                        <tr>
                            <td><label for="sex">{{ __('性別') }}</label></td>
                            <td>
             
                                @if(isset($data[0]['sex']))
                                    <select  id="sex"  name="sex" value="{{$data[0]['sex']}}">
                                @else
                                    <select  id="sex"  name="sex" value="{{old('sex')}}">                         
                                @endif
                            @foreach($code as $val)
                                @if($val['codeno']==4)
                                    @if(isset($data[0]['sex']))
                                        @if($val['value'] ==  $data[0]['sex']) <option value={{$val['value']}} selected>{{$val['selectname']}}</option>
                                        @else <option value={{$val['value']}}>{{$val['selectname']}}</option>
                                        @endif
                                    @else <option value={{$val['value']}}>{{$val['selectname']}}</option>
                                    @endif
                                @endif
                            @endforeach
                            </select>
                                <span class="invalid-feedback validate" role="alert">
                                    <nobr id="err_sex"></nobr>
                                </span>
                            </td>
                        </tr>

                     <!-- 勤務時間 -->
                     {{-- 
                     <tr>
                        <td><label for="jobfrom">{{ __('勤務時間') }}</label></td>
                        <td>
                            @if(isset($data[0]['jobfrom']))
                                <?php                  
                                    $H = substr($data[0]['jobfrom'],0,2);
                                    $m = substr($data[0]['jobfrom'],2,4);
                                    $tmp = <<<EOF
                                    <input class="jobfrom" maxlength="2" size="2" type="text" name="jobfrom_h"
                                    id="jobfrom_h" autocomplete="off" value = '$H'  style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" 
                                    onchange="select_ctrl(this.id)"
                                    oncut = "return false" onpaste="return false" oncontextmenu = "return false">
                                    :
                                    <input class="jobfrom" maxlength="2" size="2" type="text" name="jobfrom_m"
                                    id="jobfrom_m" autocomplete="off" value = '$m'  style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" 
                                    onchange="select_ctrl(this.id)"
                                    oncut = "return false" onpaste="return false" oncontextmenu = "return false">
                                    EOF;
                                    echo $tmp;
                                ?>
                            @else
                                    <input class="jobfrom" maxlength="2" size="2" type="text" name="jobfrom_h"
                                    id="jobfrom_h" autocomplete="off" style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" value = "00" 
                                    onchange="select_ctrl(this.id)"
                                    oncut = "return false" onpaste="return false" oncontextmenu = "return false">
                                    
                                    @if($errors->has('jobfrom_h'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('jobfrom_h')}}
                                        </span>
                                    @enderror
                                    :
                                    <input class="jobfrom" maxlength="2" size="2" type="text" name="jobfrom_m"
                                    id="jobfrom_m" autocomplete="off" style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" value = "00" 
                                    onchange="select_ctrl(this.id)"
                                    oncut = "return false" onpaste="return false" oncontextmenu = "return false">
                            @endif
                            
                            ~
                            
                            @if(isset($data[0]['jobto']))
                                <?php                  
                                    $H = substr($data[0]['jobto'],0,2);
                                    $m = substr($data[0]['jobto'],2,4);                              
                                    $tmp = <<<EOF
                                    <input class="jobto" maxlength="2" size="2" type="text" name="jobto_h"
                                    id="jobto_h" autocomplete="off" value = '$H' style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" 
                                    onchange="select_ctrl(this.id)"
                                    oncut = "return false" onpaste="return false" oncontextmenu = "return false">                    
                                    :
                                    <input class="jobto" maxlength="2" size="2" type="text" name="jobto_m"
                                    id="jobto_m" autocomplete="off" value = '$m' style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" 
                                    onchange="select_ctrl(this.id)"
                                    oncut = "return false" onpaste="return false" oncontextmenu = "return false">
                                    EOF;
                                    echo $tmp;
                                ?>                 
                            @else
                                    <input class="jobto" maxlength="2" size="2" type="text" name="jobto_h"
                                    id="jobto_h" autocomplete="off" style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" value = "00" 
                                    onchange="select_ctrl(this.id)"
                                    oncut = "return false" onpaste="return false" oncontextmenu = "return false">                       

                                    @if($errors->has('jobto_h'))
                                        <span class="invalid-feedback validate" role="alert">
                                        {{$errors->first('jobto')}}
                                        </span>
                                    @enderror
                                    :
                                    <input class="jobto" maxlength="2" size="2" type="text" name="jobto_m"
                                    id="jobto_m" autocomplete="off" style="background-color: rgb(255, 255, 255);width:calc(60px / var(--coef));text-align:center;" value = "00" 
                                    onchange="select_ctrl(this.id)"
                                    oncut = "return false" onpaste="return false" oncontextmenu = "return false">      
                            @endif
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
                                            if(isset($data[0]['jobfrom']))
                                            {
                                                $H = substr($data[0]['jobfrom'],0,2);
                                            }
                                            else
                                            {
                                                $H = "00";
                                            }
                                            if($hh == $H)
                                            {
                                                $tmp = <<<EOF
                                                <option value='$hh' selected>$hh</option>
                                                EOF;
                                            }
                                            else
                                            {
                                                $tmp = <<<EOF
                                                <option value='$hh'>$hh</option>
                                                EOF;
                                            }                                    
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
                                            if(isset($data[0]['jobfrom']))
                                            {
                                                $m = substr($data[0]['jobfrom'],2,4);
                                            }
                                            else
                                            {
                                                $m = "00";
                                            }
                                            if($mm == $m)
                                            {
                                                $tmp = <<<EOF
                                                <option value='$mm' selected>$mm</option>
                                                EOF;
                                            }
                                            else
                                            {
                                                $tmp = <<<EOF
                                                <option value='$mm'>$mm</option>
                                                EOF;
                                            }                                    
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
                                            if(isset($data[0]['jobto']))
                                            {
                                                $H = substr($data[0]['jobto'],0,2);
                                            }
                                            else
                                            {
                                                $H = "00";
                                            } 
                                            if($hh == $H)
                                            {
                                                $tmp = <<<EOF
                                                <option value='$hh' selected>$hh</option>
                                                EOF;
                                            }      
                                            else
                                            {
                                                $tmp = <<<EOF
                                                <option value='$hh'>$hh</option>
                                                EOF;
                                            }                     
                                            
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
                                                if(isset($data[0]['jobto']))
                                                {
                                                    $m = substr($data[0]['jobto'],2,4);
                                                }
                                                else
                                                {
                                                    $m = "00";
                                                }
                                                if($mm == $m)
                                                {
                                                    $tmp = <<<EOF
                                                    <option value='$mm' selected>$mm</option>
                                                    EOF;
                                                }      
                                                else
                                                {
                                                    $tmp = <<<EOF
                                                    <option value='$mm'>$mm</option>
                                                    EOF;
                                                }                     
                                                
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
                        @if(isset($data[0]['measufrom']))
                            <?php                    
                                $Y = substr($data[0]['measufrom'],0,4);
                                $M = substr($data[0]['measufrom'],4,2);   
                                $D = substr($data[0]['measufrom'],6,2);         
                                $tmp = <<<EOF
                                <input type="date" id="measufrom" name="measufrom" value = "$Y-$M-$D"></input>
                                <input type="hidden" name="measufrom_pre" value = "$Y-$M-$D"></input>
                                EOF;
                                echo $tmp;
                            ?>
                            
                        @else
                            <input type="date" id="measufrom" name="measufrom" value = "{{old('measufrom')}}"></input>
                            <input type="hidden" name="measufrom_pre" value = {{old('measufrom_pre')}}></input>
                        @endif
                        ~
                    <!-- 日付入力 -->
                    @if(isset($data[0]['measuto']))
                        <?php                    
                                $Y = substr($data[0]['measuto'],0,4);
                                $M = substr($data[0]['measuto'],4,2);   
                                $D = substr($data[0]['measuto'],6,2);         
                                $tmp = <<<EOF
                                <input type="date" id="measuto" name="measuto" value = "$Y-$M-$D"></input>
                                <input type="hidden" name="measuto_pre" value = "$Y-$M-$D"></input>
                                EOF;
                                echo $tmp;
                        ?>
                    @else
                        <input type="date" id="measuto" name="measuto" value = "{{old('measuto')}}"></input>
                        <input type="hidden" name="measuto_pre" value = {{old('measuto_pre')}}></input>
                    @endif
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

            </tr>
            --}}
        </table>
    </div>

    <!-- 修正ボタン -->
    <input id="btn_fixhelper" type="image" src="image/img_yes.png" alt="修正" border="0">

</form>
</div>
@endsection


