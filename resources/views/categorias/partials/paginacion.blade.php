@if($categorias->hasPages())
<div class="flex-center gap-8" style="justify-content:center;flex-wrap:wrap;">
    @if($categorias->onFirstPage())
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-left"></i></button>
    @else
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $categorias->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></button>
    @endif

    @foreach($categorias->getUrlRange(1, $categorias->lastPage()) as $page => $url)
        <button class="btn btn-sm pagina-link {{ $categorias->currentPage() == $page ? 'btn-primary' : 'btn-secondary' }}" data-url="{{ $url }}">{{ $page }}</button>
    @endforeach

    @if($categorias->hasMorePages())
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $categorias->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></button>
    @else
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-right"></i></button>
    @endif
</div>
@endif