{{-- resources/views/group_fix.blade.php --}}
@extends('layouts.parent')

@section('content')
<script src="{{ asset('js/group_fix.js') }}"></script>

<div class="allcont">
  <form method="POST" action="{{ url('/group_fixctrl') }}" id="form_groupfix" name="form_groupfix" autocomplete="off">
    @csrf

    {{-- 必須パラメータ（パンくず／戻るでも利用） --}}
    <input type="hidden" id="facilityno" name="facilityno"
           value="{{ $facilityno ?? old('facilityno') ?? request('facilityno') }}">
    <input type="hidden" id="groupno" name="groupno"
           value="{{ $groupno ?? ($groupRow->group_id ?? old('groupno') ?? request('groupno')) }}">

    <div class="container">
      <table class="tb">
        {{-- 施設名（閲覧のみ） --}}
        <tr>
          <td class="lb">施設名</td>
          <td>
            <input type="text"
                   value="{{ $facility->facility ?? ($facilityname ?? '') }}"
                   disabled>
          </td>
        </tr>

        {{-- グループ名（既存値を表示して編集可） --}}
        <tr>
          <td class="lb">グループ名</td>
          <td>
            <input type="text" name="group_name" maxlength="100"
                   value="{{ old('group_name', $groupRow->group_name ?? '') }}" required>
            @error('group_name')
              <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
            @enderror
          </td>
        </tr>
      </table>

      {{-- 更新（送信） --}}
      <input type="image"
             id="btn_update_group"
             src="{{ asset('image/img_yes.png') }}"
             alt="更新"
             style="margin-top:10px;"
             border="0">

      {{-- キャンセル（一覧へ戻る） --}}
      <input type="image"
             id="btn_cxl_groupfix"
             src="{{ asset('image/img_cancel.png') }}"
             alt="キャンセル"
             style="margin-left:8px;"
             onclick="return goBackToGroupList();"
             border="0">
    </div>
  </form>
</div>

{{-- キャンセル時の戻り先制御 --}}
<script>
function goBackToGroupList() {
  var fac = document.getElementById('facilityno')?.value || '';
  if (!fac) {
    window.location.href = "{{ url('/facility') }}";
    return false;
  }
  window.location.href = "{{ url('/groups') }}" + "?facilityno=" + encodeURIComponent(fac);
  return false; // 画像ボタンのデフォルト送信を止める
}
</script>
@endsection
