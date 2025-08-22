@extends('layouts.parent')

@section('content')
<div class="allcont">

    {{-- 作業者一覧へ（選択グループをもって遷移） --}}
    <form id="a_helper" action="/helper?facilityno={{ $facilityno }}" method="post" onsubmit="return submitToHelper()">
        @csrf
        <input type="hidden" id="groupno" name="groupno" value="">
        <input type="image" class="img_style2" src="{{ asset('image/img_helper.png') }}" alt="作業者一覧" border="0">
    </form>

    {{-- 見出し --}}
    <img id="img_group_tb" src="{{ asset('image/img_group_tb.png') }}" alt="">

    <table id="table_groups">
        <tbody class="scrollBody">
        @forelse($groups as $g)
            <tr class="row-group"
                data-groupid="{{ $g->group_id }}"
                onclick="selectRow(this)">
                <td class="group_id">{{ $g->group_id }}</td>
                <td class="group_name">{{ $g->group_name }}</td>
            </tr>
        @empty
            @for($i=0;$i<12;$i++)
                <tr><td class="group_id"></td><td class="group_name"></td></tr>
            @endfor
        @endforelse
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
    // hidden に group_id を反映
    document.getElementById('groupno').value = tr.dataset.groupid;
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
/* 簡易ハイライト */
#table_groups tr.selected { background: #e8f0fe; }
#table_groups td { padding: 6px 10px; }
</style>
@endpush
