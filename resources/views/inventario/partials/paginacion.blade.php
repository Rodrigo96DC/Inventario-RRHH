@if($items->hasPages())
<div class="flex-center gap-8" style="justify-content:center;flex-wrap:wrap;">
    @if($items->onFirstPage())
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-left"></i></button>
    @else
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $items->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></button>
    @endif

    @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
        <button class="btn btn-sm pagina-link {{ $items->currentPage() == $page ? 'btn-primary' : 'btn-secondary' }}" data-url="{{ $url }}">{{ $page }}</button>
    @endforeach

    @if($items->hasMorePages())
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $items->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></button>
    @else
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-right"></i></button>
    @endif
</div>
@endif