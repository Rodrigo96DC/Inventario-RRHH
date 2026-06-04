@if($insumos->hasPages())
<div class="flex-center gap-8" style="justify-content:center;flex-wrap:wrap;">
    @if($insumos->onFirstPage())
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-left"></i></button>
    @else
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $insumos->previousPageUrl() }}">
            <i class="ti ti-chevron-left"></i>
        </button>
    @endif

    @foreach($insumos->getUrlRange(1, $insumos->lastPage()) as $page => $url)
        <button class="btn btn-sm pagina-link {{ $insumos->currentPage() == $page ? 'btn-primary' : 'btn-secondary' }}"
            data-url="{{ $url }}">{{ $page }}</button>
    @endforeach

    @if($insumos->hasMorePages())
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $insumos->nextPageUrl() }}">
            <i class="ti ti-chevron-right"></i>
        </button>
    @else
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-right"></i></button>
    @endif
</div>
@endif