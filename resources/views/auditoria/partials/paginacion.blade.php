@if($query->hasPages())
<div class="flex-center gap-8" style="justify-content:center;flex-wrap:wrap;">
    @if($query->onFirstPage())
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-left"></i></button>
    @else
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $query->previousPageUrl() }}"><i class="ti ti-chevron-left"></i></button>
    @endif

    @foreach($query->getUrlRange(1, $query->lastPage()) as $page => $url)
        <button class="btn btn-sm pagina-link {{ $query->currentPage() == $page ? 'btn-primary' : 'btn-secondary' }}" data-url="{{ $url }}">{{ $page }}</button>
    @endforeach

    @if($query->hasMorePages())
        <button class="btn btn-secondary btn-sm pagina-link" data-url="{{ $query->nextPageUrl() }}"><i class="ti ti-chevron-right"></i></button>
    @else
        <button class="btn btn-secondary btn-sm" disabled><i class="ti ti-chevron-right"></i></button>
    @endif
</div>
@endif