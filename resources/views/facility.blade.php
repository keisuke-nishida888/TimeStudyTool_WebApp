@extends('layouts.parent')


@section('content')
<script src="/js/facility.js"></script>

<div class="allcont">
    <!-- ★作業一覧（専用フォーム） -->
<form id="a_task" action = '/task'  method = "get" onsubmit="return submitTaskList();">
    <!-- 実際に送るパラメータ -->
    <input id="facilityno_for_task" type="hidden" name="facilityno" value="">
    <!-- 互換: facility.js が入れる想定の選択施設ID -->
    <input id="targetid" type="hidden" value="">
    <input type="image" class="img_style2" src="image/img_task.png" 
            alt="作業名一覧" border="0" onclick="return submitTaskList();" >
</form>

    <!-- ★グループ一覧（専用フォーム） -->
    <form id="a_group" action="/groups" method="get" onsubmit="return submitGroupList();">
        <!-- 実際に送るパラメータ -->
        <input id="facilityno_for_group" type="hidden" name="facilityno" value="">
        <!-- 互換: facility.js が入れる想定の選択施設ID -->
        <input id="targetid" type="hidden" value="">
  <input type="image" class="img_style2" src="image/img_group.png"
         alt="グループ一覧" onclick="return submitGroupList();" border="0">
</form>
<script>
function submitGroupList() {
  // facility.js が #targetid に入れる or window.targetID をフォールバック
  var selected = (document.getElementById('targetid') && document.getElementById('targetid').value
                  ? document.getElementById('targetid').value
                  : (window.targetID || '')).toString().trim();
  if (!selected) {
    alert('施設を選択してください。');
    return false;
  }
  document.getElementById('facilityno_for_group').value = selected;
  return true; // 送信
}

function submitTaskList() {
  // 1) a_task フォーム内の hidden を最優先
  var fieldInForm = document.querySelector('#a_task #targetid');
  var selected = '';

  if (fieldInForm && fieldInForm.value) {
    selected = fieldInForm.value.trim();
  } else if (document.getElementById('targetid') && document.getElementById('targetid').value) {
    // 2) ページ内の（最初の）#targetid
    selected = document.getElementById('targetid').value.trim();
  } else if (typeof window.targetID !== 'undefined' && window.targetID !== null) {
    // 3) facility.js が入れてくれるフォールバック
    selected = String(window.targetID).trim();
  }

  if (!selected) {
    alert('施設を選択してください。');
    return false; // 送信中止
  }

  // 送信用 hidden にセット
  var hidden = document.getElementById('facilityno_for_task');
  if (hidden) hidden.value = selected;

  return true; // 送信実行
}
</script>


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

<script>

function submitGroupList() {
    // facility.js が入れてくれる値を最優先で参照。なければ window.targetID を使用
    var selected = (document.getElementById('targetid')?.value || window.targetID || '').toString().trim();
    if (!selected) {
        alert('施設を選択してください。');
        return false; // 送信中止
    }
    document.getElementById('facilityno_for_group').value = selected;
    // input type="image" は onclick の戻り値 true で通常送信される
    return true;
}
</script>
