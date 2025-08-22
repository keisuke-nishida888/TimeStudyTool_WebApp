@extends('layouts.parent')

@section('content')
<div class="allcont">

    {{-- 作業者一覧へ（選択グループをもって遷移） --}}
    <form id="a_helper" action="/helper?facilityno={{ $facilityno }}" method="post" onsubmit="return submitToHelper()">
        @csrf
        <input type="hidden" id="groupno" name="groupno" value="">
        <input type="image" class="img_style2" src="{{ asset('image/img_helper.png') }}" alt="グループ一覧" border="0">
    </form>

    {{-- テーブルヘッダ画像（task.blade.php と同じ位置関係） --}}
    <img id = "img_task_tb" src="image/img_task_tb.png" alt="" >

    <table id="table_task">
        <tbody class="scrollBody">
        @if(isset($groups))
            @if(count($groups) <= 0)
                {{-- データ0件なら空行で12行埋め --}}
                @for($i=0;$i<12;$i++)
                    <tr><td class="group_id"></td><td class="group_name"></td></tr>
                @endfor
            @else
                @foreach($groups as $g)
                    <tr class="row-group"
                        data-group-id="{{ $g->group_id }}"
                        onclick="selectRow(this)">
                        {{-- 左列は行番号でもOK。group_idを出したい場合は {{$g->group_id}} に変更 --}}
                        <td class="group_id">{{ $loop->iteration }}</td>
                        <td class="group_name">{{ $g->group_name }}</td>
                    </tr>
                    {{-- 最後のループ後に12行まで空行を追加 --}}
                    @if($loop->last && $loop->count < 12)
                        @for($i=$loop->count;$i<12;$i++)
                            <tr><td class="group_id"></td><td class="group_name"></td></tr>
                        @endfor
                    @endif
                @endforeach
            @endif
        @else
            {{-- $groups 未定義時も12行埋め --}}
            @for($i=0;$i<12;$i++)
                <tr><td class="group_id"></td><td class="group_name"></td></tr>
            @endfor
        @endif
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
let selectedRow = null;

function selectRow(tr) {
    if (selectedRow) selectedRow.classList.remove('selected');
    selectedRow = tr;
    tr.classList.add('selected');
    // hidden に group_id をセット
    document.getElementById('groupno').value = tr.dataset.groupId || tr.getAttribute('data-group-id');
}

function submitToHelper() {
    const groupno = document.getElementById('groupno').value;
    if (!groupno) {
        alert('グループを選択してください。');
        return false;
    }
    return true;
}
</script>
<style>
/* 行選択の見た目 */
#table_group tr.selected { background: #e8f0fe; }
#table_group td { padding: 6px 10px; }
</style>
@endpush
