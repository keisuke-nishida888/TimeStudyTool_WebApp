@extends('layouts.parent')


@section('content')


<div class="allcont">
<a id="a_wearable_add" href="{{ url('/wearable_add') }}"> <img class="img_style" src="image/img_add.png" alt="心拍センサー追加"  border="0"> </a>

<input type="image" id="btn_delwearable" src="image/img_del.png" alt="心拍センサー削除" onclick="del_check(targetID,this.id)" border="0">

<form id="a_wearable_fix" action = '/wearable_fix'  method = "post" onsubmit = "return Idcheck(targetID)">
    @csrf
    <input id="targetid" type="hidden" name="id" value="">
    <input type="image" class="img_style" src="image/img_fix.png" alt="心拍センサー修正" border="0">
</form>

<!-- テーブルヘッダ -->
<img id = "img_wearable_tb" src="image/img_wearable_tb.png" alt="" >

<table id="table2">
    <tbody class="scrollBody">
    @if(isset($data))
        @if(count($data)<=0)
        <!--  配列の総アイテム数が10未満 -->
        @for($i=0;$i<12;$i++)
            <tr>
                <td class="wearable_no"></td><td class="deviceName"></td>
                <td class="facility"></td><td class="helperName"></td>
            </tr>  
        @endfor
        @else
            @foreach($data as $val)            
                <tr><td class="wearable_no">{{$val['wearable_id']}}</td>
                    <td class="deviceName">{{$val['devicename']}}</td>
                    {{-- <td class="facility">{{$val['facility']}}</td>
                    <td class="helpername">{{$val['helpername']}}</td> --}}

                
                    </tr>
                <!--  最後のループ -->
                @if (($loop->last))
                    @if ($loop->count < 12)
                        <!--  配列の総アイテム数が10未満 -->
                        @for($i=$loop->count;$i<12;$i++)
                        <tr>
                            <td class="wearable_no"></td><td class="deviceName"></td>
                            {{-- <td class="facility"></td><td class="helperName"></td> --}}
                        </tr>  
                        @endfor
                    @endif
                @endif
            @endforeach
        @endif
    @else
    <!--  配列の総アイテム数が10未満 -->
    @for($i=0;$i<12;$i++)
        <tr>
            <td class="wearable_no"></td><td class="deviceName"></td>
            {{-- <td class="facility"></td><td class="helperName"></td> --}}
        </tr>  
    @endfor
    @endif
    </tbody>
</table>
</div>

@endsection