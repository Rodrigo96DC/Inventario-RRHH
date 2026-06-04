@if($areas->hasPages())
<div class="flex-center gap-8" style="justify-content:center;flex-wrap:wrap;">
    @if($areas->onFirstPage())
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-left"></i></button>
    @else
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $areas->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></button>
    @endif

    @foreach($areas->getUrlRange(1, $areas->lastPage()) as $page => $url)
        <button class="btn btn-sm pagina-link {{ $areas->currentPage() == $page ? 'btn-primary' : 'btn-secondary' }}" data-url="{{ $url }}">{{ $page }}</button>
    @endforeach

    @if($areas->hasMorePages())
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $areas->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></button>
    @else
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-right"></i></button>
    @endif
</div>
@endif