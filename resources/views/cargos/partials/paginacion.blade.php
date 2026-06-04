@if($cargos->hasPages())
<div class="flex-center gap-8" style="justify-content:center;flex-wrap:wrap;">
    @if($cargos->onFirstPage())
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-left"></i></button>
    @else
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $cargos->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></button>
    @endif

    @foreach($cargos->getUrlRange(1, $cargos->lastPage()) as $page => $url)
        <button class="btn btn-sm pagina-link {{ $cargos->currentPage() == $page ? 'btn-primary' : 'btn-secondary' }}" data-url="{{ $url }}">{{ $page }}</button>
    @endforeach

    @if($cargos->hasMorePages())
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $cargos->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></button>
    @else
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-right"></i></button>
    @endif
</div>
@endif