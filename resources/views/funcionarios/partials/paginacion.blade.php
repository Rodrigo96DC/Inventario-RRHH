@if($funcionarios->hasPages())
<div class="flex-center gap-8" style="justify-content:center;flex-wrap:wrap;">

    {{-- Anterior --}}
    @if($funcionarios->onFirstPage())
        <button class="btn btn-secondary btn-sm" disabled>
            <i class="ti ti-chevron-left"></i>
        </button>
    @else
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $funcionarios->previousPageUrl() }}">
            <i class="ti ti-chevron-left"></i>
        </button>
    @endif

    {{-- Páginas --}}
    @foreach($funcionarios->getUrlRange(1, $funcionarios->lastPage()) as $page => $url)
        <button
            class="btn btn-sm pagina-link {{ $funcionarios->currentPage() == $page ? 'btn-primary' : 'btn-secondary' }}"
            data-url="{{ $url }}"
        >
            {{ $page }}
        </button>
    @endforeach

    {{-- Siguiente --}}
    @if($funcionarios->hasMorePages())
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $funcionarios->nextPageUrl() }}">
            <i class="ti ti-chevron-right"></i>
        </button>
    @else
        <button class="btn btn-secondary btn-sm" disabled>
            <i class="ti ti-chevron-right"></i>
        </button>
    @endif

</div>
@endif