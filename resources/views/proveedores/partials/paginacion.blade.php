@if($proveedores->hasPages())
<div class="flex-center gap-8" style="justify-content:center;flex-wrap:wrap;">
    @if($proveedores->onFirstPage())
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-left"></i></button>
    @else
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $proveedores->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></button>
    @endif

    @foreach($proveedores->getUrlRange(1, $proveedores->lastPage()) as $page => $url)
        <button class="btn btn-sm pagina-link {{ $proveedores->currentPage() == $page ? 'btn-primary' : 'btn-secondary' }}" data-url="{{ $url }}">{{ $page }}</button>
    @endforeach

    @if($proveedores->hasMorePages())
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $proveedores->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></button>
    @else
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-right"></i></button>
    @endif
</div>
@endif