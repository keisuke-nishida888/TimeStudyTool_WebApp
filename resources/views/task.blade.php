@extends('layouts.parent')

@section('content')
<script src="/js/task.js"></script>

<div class="allcont">
<a id="a_task_add" href="{{ url('/task_add') }}"> <img src="image/img_add.png" class="img_style" alt="作業内容追加"  border="0"> </a>

<input type="image" id="btn_deltask"  src="image/img_del.png" alt="作業内容削除" onclick="del_check_task(targetID,this.id)" border="0">

<form id="a_task_fix" action = '/task_fix'  method = "post" onsubmit = "return Idcheck_task(targetID)">
    @csrf
    <input id="targetid_task" type="hidden" name="id" value="">
    <input type="image" class="img_style" src="image/img_fix.png" alt="作業内容修正" border="0">
</form>

<!-- テーブルヘッダ -->
<img id = "img_task_tb" src="image/img_task_tb.png" alt="" >

<table id="table_task">
    <tbody class="scrollBody">
    @if(isset($tasks))
        @if(count($tasks)<=0)
            <!--  配列の総アイテム数が12未満 -->
            @for($i=0;$i<12;$i++)
                <tr><td class="task_id"></td><td class="task_name"></td><td class="task_type_no"></td>
                    <td class="task_category_no"></td></tr>  
            @endfor
        @else    
            @foreach($tasks as $task)
                <tr data-task-id="{{$task['task_id']}}"><td class="task_id">{{$loop->iteration}}</td>
                    <td class="task_name">{{$task['task_name']}}</td>
                    <td class="task_type_no">
                        @if($task['task_type_no'] == 0)
                            直接
                        @elseif($task['task_type_no'] == 1)
                            間接
                        @elseif($task['task_type_no'] == 2)
                            その他
                        @else
                            {{$task['task_type_no']}}
                        @endif
                    </td>
                    <td class="task_category_no">
                        @if($task['task_category_no'] == 0)
                            肉体的負担
                        @elseif($task['task_category_no'] == 1)
                            精神的負担
                        @elseif($task['task_category_no'] == 2)
                            その他
                        @else
                            {{$task['task_category_no']}}
                        @endif
                    </td></tr>  
                <!--  最後のループ -->
                @if(($loop->last))
                    @if ($loop->count < 12)
                        <!--  配列の総アイテム数が12未満 -->
                        @for($i=$loop->count;$i<12;$i++)
                        <tr><td class="task_id"></td><td class="task_name"></td><td class="task_type_no"></td> 
                            <td class="task_category_no"></td></tr>  
                        @endfor
                    @endif
                @endif
            @endforeach
        @endif
    @else
        <!--  配列の総アイテム数が12未満 -->
        @for($i=0;$i<12;$i++)
        <tr><td class="task_id"></td><td class="task_name"></td><td class="task_type_no"></td> 
            <td class="task_category_no"></td></tr>    
        @endfor
    @endif
    </tbody>
</table>
</div>
@endsection 