
@if (count($breadcrumbs))
    <dev class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && !$loop->last)
                <nobr class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></nobr>
                
                <!--  最後のループではないとき -->
                @if (!($loop->last))
                    <nobr> > </nobr>
                @endif
                
            @else
                <nobr class="breadcrumb-item active">{{ $breadcrumb->title }}</nobr>
            @endif
        @endforeach
    </dev>
@endif