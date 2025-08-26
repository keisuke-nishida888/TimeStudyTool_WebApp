{{-- resources/views/group_add.blade.php --}}
@extends('layouts.parent')

@section('content')
<script src="{{ asset('js/group_add.js') }}"></script>

<div class="allcont">
  <form method="POST" action="{{ url('/group_addctrl') }}" id="form_groupadd" name="form_groupadd" autocomplete="off">
    @csrf

    {{-- facilityno は必ず送る（親のパンくず／戻るボタンでも利用される） --}}
    @php
      $facilityno_in_view = $facilityno
        ?? old('facilityno')
        ?? request('facilityno');
    @endphp
    <input type="hidden" id="facilityno" name="facilityno" value="{{ $facilityno_in_view }}">

    <div class="container">
      <table class="tb">
        {{-- 施設名 --}}
        <tr>
          <td class="lb">施設名</td>
          <td>
            <input type="text"
                   value="{{ $facility->facility ?? ($facilityname ?? '') }}"
                   disabled>
          </td>
        </tr>

        {{-- グループ名 --}}
        <tr>
          <td class="lb">グループ名</td>
          <td>
            <input type="text" name="group_name" maxlength="100"
                   value="{{ old('group_name') }}" required>
            @error('group_name')
              <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
            @enderror
          </td>
        </tr>
      </table>

      {{-- 追加（送信） --}}
      <input type="image"
             id="btn_addgroup"
             src="{{ asset('image/img_yes.png') }}"
             alt="追加"
             style="margin-top:10px;"
             border="0">

      {{-- キャンセル（一覧に戻る） --}}
      <input type="image"
             id="btn_cxl_groupadd"
             src="{{ asset('image/img_cancel.png') }}"
             alt="キャンセル"
             style="margin-left:8px;"
             onclick="return goBackToGroupList();"
             border="0">
    </div>
  </form>
</div>

{{-- 最低限のJS（キャンセルで元の施設のグループ一覧へ戻す） --}}
<script>
function goBackToGroupList() {
  var fac = document.getElementById('facilityno')?.value || '';
  if (!fac) {
    // facilityno が取れないときは施設一覧へ
    window.location.href = "{{ url('/facility') }}";
    return false;
  }
  window.location.href = "{{ url('/groups') }}" + "?facilityno=" + encodeURIComponent(fac);
  return false; // 画像ボタンのデフォルト送信を止める
}
</script>
@endsection
