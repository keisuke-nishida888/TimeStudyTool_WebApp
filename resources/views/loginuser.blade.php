@extends('layouts.parent')

@section('content')


<div class="allcont">
<a id="a_loginuser_add" href="{{ url('/loginuser_add') }}"> <img class="img_style" src="image/img_add.png" alt="ログインユーザ追加"  border="0"> </a>

<!-- <button id="btn_deluser" onclick="VisibleChange(this.id)">削除</button> -->
<input type="image" id="btn_deluser" src="image/img_del.png" alt="ログインユーザ削除" onclick="del_check(targetID,this.id)" border="0" >

<form  id="a_loginuser_fix" action = '/loginuser_fix'  method = "post" onsubmit = "return Idcheck(targetID)">
    @csrf
    <input id="targetid" type="hidden" name="userid" value="">
    <input type="image" class="img_style" src="image/img_fix.png" alt="ログインユーザ修正" border="0">
</form>


<!-- テーブルヘッダ -->
<img id = "img_loginuser_tb" src="image/img_loginuser_tb.png" alt="" >

<table id="table1">
    <tbody class="scrollBody">
    @if(isset($value))
        @if(count($value)<=0)
            <!--  配列の総アイテム数が10未満 -->
            @for($i=0;$i<12;$i++)
                <tr><td class="login_no"></td><td class="login_name"></td><td class="login_auth"></td></tr>  
            @endfor
        @else    
            @foreach($value as $val)
            <tr>
                <td class="login_no">{{$val->id}}</td>
                <td class="login_name">{{$val->username}}</td>
                @foreach($code as $valcode)
                    @if($valcode['codeno']==1)
                        @if($valcode['value'] == $val->authority)
                            <td class="login_auth">{{$valcode['selectname']}}</td>
                            @break
                        @endif
                    @endif
                    @if(($loop->last)) <td class="login_auth"></td>
                    @endif
                @endforeach
            </tr>
                <!--  最後のループ -->
                @if(($loop->last))
                    @if ($loop->count < 12)
                        <!--  配列の総アイテム数が10未満 -->
                        @for($i=$loop->count;$i<12;$i++)
                        <tr><td class="login_no"></td><td class="login_name"></td><td class="login_auth"></td></tr>  
                        @endfor
                    @endif
                @endif
            @endforeach
        @endif
    @else
        <!--  配列の総アイテム数が10未満 -->
        @for($i=0;$i<12;$i++)
            <tr><td class="login_no"></td><td class="login_name"></td><td class="login_auth"></td></tr>  
        @endfor
    @endif
    </tbody>
</table>
</div>

@endsection