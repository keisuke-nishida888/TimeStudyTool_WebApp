@extends('layouts.parent')

@section('content')
<div class="allcont">

    {{-- 作業者一覧へ（選択グループをもって遷移） --}}
    <form id="a_helper" action="/helper?facilityno={{ $facilityno }}" method="post" onsubmit="return submitToHelper()">
        @csrf
        <input type="hidden" id="groupno" name="groupno" value="">
        <input type="image" class="img_style2" src="{{ asset('image/img_helper.png') }}" alt="作業者一覧" border="0">
    </form>
    {{-- グループ追加 --}}
    <a id="a_group_add" a href="{{ route('groups.add', ['facilityno' => $facilityno]) }}">
        <img src="{{ asset('image/img_add.png') }}" class="img_style" alt="グループ追加" border="0">
    </a>

    {{-- 削除：POSTで /group_del へ facilityno と選択 groupno を送る --}}
    <form id="a_group_del" action="{{ url('/group_del') }}" method="post" onsubmit="return setGroupDelTarget()">
        @csrf
        <input type="hidden" name="facilityno" value="{{ $facilityno }}">
        <input type="hidden" id="del_groupno" name="groupno" value="">
        <input type="image" class="img_style" src="{{ asset('image/img_del.png') }}" alt="グループ削除" border="0">
    </form>
    {{-- GET で /group_fix へ、facilityno と選択 groupno を送る --}}
    <form id="a_group_fix" action="/group_fix" method="get" onsubmit="return setGroupFixTarget()">
    <input type="hidden" name="facilityno" value="{{ $facilityno }}">
    <input type="hidden" id="fix_groupno" name="groupno" value="">
    <input type="image" class="img_style" src="{{ asset('image/img_fix.png') }}" alt="グループ修正" border="0">
</form>

    {{-- テーブルヘッダ（task と同じ ID を使う） --}}
    <img id="img_task_tb" src="{{ asset('image/img_group_tb.png') }}" alt="">

    <table id="table_task">
        <tbody class="scrollBody">
        @if(isset($groups) && count($groups) > 0)
            @foreach($groups as $g)
                <tr class="row-group" data-group-id="{{ $g->group_id }}">
                    {{-- 左列は連番表示（task と合せて $loop->iteration ） --}}
                    <td class="task_id">{{ $loop->iteration }}</td>
                    <td class="task_name" title="{{ $g->group_name }}">{{ $g->group_name }}</td>
                    {{-- task の列数に合わせてダミー列を出すなら以下を追加（不要なら消してOK） --}}
                    {{-- <td class="task_type_no"></td>
                    <td class="task_category_no"></td> --}}
                </tr>
                @if($loop->last && $loop->count < 12)
                    @for($i=$loop->count;$i<12;$i++)
                        <tr>
                            <td class="task_id"></td>
                            <td class="task_name"></td>
                            {{-- <td class="task_type_no"></td>
                            <td class="task_category_no"></td> --}}
                        </tr>
                    @endfor
                @endif
            @endforeach
        @else
            @for($i=0;$i<12;$i++)
                <tr>
                    <td class="task_id"></td>
                    <td class="task_name"></td>
                    {{-- <td class="task_type_no"></td>
                    <td class="task_category_no"></td> --}}
                </tr>
            @endfor
        @endif
        </tbody>
    </table>
</div>

{{-- ここで直接スクリプトを定義（layouts.parent 側の @stack 依存を避ける） --}}
<script>
(function() {
    let selectedRow = null;

    function selectRow(tr) {
        if (selectedRow) selectedRow.classList.remove('selected');
        selectedRow = tr;
        tr.classList.add('selected');
        document.getElementById('groupno').value = tr.dataset.groupId;
    }

    function submitToHelper() {
        const groupno = document.getElementById('groupno').value;
        if (!groupno) {
            alert('グループを選択してください。');
            return false;
        }
        return true;
    }

    // クリックイベントは JS で付与（inline onclick 不要）
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#table_task .row-group').forEach(function(tr) {
            tr.addEventListener('click', function() { selectRow(tr); });
        });
    });
    
    // ★ 修正ボタン送信用
    window.setGroupFixTarget = function() {
        const sel = document.getElementById('groupno').value;
        if (!sel) { alert('グループを選択してください。'); return false; }
        document.getElementById('fix_groupno').value = sel;
        return true;
    };
    // ★ 削除ボタン送信用
    window.setGroupDelTarget = function() {
    const sel = document.getElementById('groupno').value;
    if (!sel) { alert('グループを選択してください。'); return false; }
    if (!confirm('選択したグループを削除します。よろしいですか？')) return false;
    document.getElementById('del_groupno').value = sel;
    return true;
};

    // フォーム用に関数をグローバルへ（onsubmit から呼ぶため）
    window.submitToHelper = submitToHelper;
})();
</script>

{{-- グループ名がはみ出す場合のフォールバック（必要に応じて） --}}
<style>
/* task の見た目に合わせてテキストをトリミング */
#table_task td.task_name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
/* 選択行のハイライト */
#table_task tr.selected { background: #e8f0fe; }
</style>
@endsection
