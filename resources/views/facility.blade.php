@extends('layouts.parent')


@section('content')
<script src="/js/facility.js"></script>

<div class="allcont">
<form id="a_task" action = '/task'  method = "get">
    <input type="image" class="img_style2" src="image/img_task.png" alt="作業名内容一覧" border="0">
</form>

<form id="a_helper" action = '/helper'  method = "post" onsubmit = "return Idcheck(targetID)">
    @csrf
    <input id="targetid" type="hidden" name="id" value="">
    <input type="image" class="img_style2" src="image/img_helper.png" alt="介助者一覧" border="0">
</form>


<a id="a_facility_add" href="{{ url('/facility_add') }}"> <img src="image/img_add.png" class="img_style" alt="施設情報追加"  border="0"> </a>

<input type="image" id="btn_delfacility"  src="image/img_del.png" alt="施設情報削除" onclick="del_check(targetID,this.id)" border="0">



<form id="a_facility_fix" action = '/facility_fix'  method = "post" onsubmit = "return Idcheck(targetID)">
    @csrf
    <input id="targetid2" type="hidden" name="id" value="">
    <input type="image" class="img_style" src="image/img_fix.png" alt="施設情報修正" border="0">
</form>



<!-- テーブルヘッダ -->
<img id = "img_facility_tb" src="image/img_facility_tb.png" alt="" >

<table id="table4">
    <tbody class="scrollBody">
    @if(isset($value))
        @if(count($value)<=0)
            <!--  配列の総アイテム数が10未満 -->
            @for($i=0;$i<12;$i++)
                <tr><td class="facility_no"></td><td class="facility_name"></td><td class="facility_Pass"></td>
                    <td class="facility_address"></td>
                    <td class="facility_tel"></td>
                    <td class="facility_mail"></td></tr>  
            @endfor
        @else    
            @foreach($value as $val)
                <tr><td class="facility_no">{{$val['id']}}</td>
                    <td class="facility_name">{{$val['facility']}}</td>
                    @foreach($code as $valcode)
                        @if($valcode['codeno']==2)
                            @if($valcode['value'] == $val['pass'])
                                <td class="facility_Pass">{{$valcode['selectname']}}</td>
                                @break
                            @endif
                        @endif
                        @if(($loop->last)) <td class="facility_Pass"></td>
                        @endif
                    @endforeach
                    <td class="facility_address">{{$val['address']}}</td>
                    <td class="facility_tel">{{$val['tel']}}</td>
                    <td class="facility_mail">{{$val['mail']}}</td></tr>  
                <!--  最後のループ -->
                @if(($loop->last))
                    @if ($loop->count < 12)
                        <!--  配列の総アイテム数が10未満 -->
                        @for($i=$loop->count;$i<12;$i++)
                        <tr><td class="facility_no"></td><td class="facility_name"></td><td class="facility_Pass"></td> 
                            <td class="facility_address"></td>
                            <td class="facility_tel"></td>
                            <td class="facility_mail"></td></tr>  
                        @endfor
                    @endif
                @endif
            @endforeach
        @endif
    @else
        <!--  配列の総アイテム数が10未満 -->
        @for($i=0;$i<12;$i++)
        <tr><td class="facility_no"></td><td class="facility_name"></td><td class="facility_Pass"></td> 
            <td class="facility_address"></td>
            <td class="facility_tel"></td>
            <td class="facility_mail"></td></tr>    
        @endfor
    @endif
    </tbody>
</table>
</div>
@endsection