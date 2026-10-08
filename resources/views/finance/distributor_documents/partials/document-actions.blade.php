@php
    // SECURE BY DEFAULT:
    // Requires authenticated internal user with permission AND explicit internal mode.
    $isPortal = isset($isPortal) ? (bool) $isPortal : (!auth()->check());
    $canManage = auth()->check() && auth()->user()->can('manage-distributor-docs') && !$isPortal;
    $routePrefix = $isPortal ? 'portal.distributor.' : 'distributor.documents.';
    $previewCallback = $isPortal ? 'openPublicPdfPreview' : 'openPdfPreview';
@endphp

<div class="d-inline-flex gap-1 align-items-center">
    <button type="button"
            class="btn btn-sm btn-outline-secondary px-2"
            title="Preview PDF"
            aria-label="Preview PDF"
            onclick="{{ $previewCallback }}('{{ route($routePrefix . 'preview', $document->id) }}', '{{ addslashes($document->title ?: $document->file_name) }}', '{{ route($routePrefix . 'download', $document->id) }}')">
        <i class="iconoir-eye"></i>
    </button>
    <a href="{{ route($routePrefix . 'download', $document->id) }}"
       class="btn btn-sm btn-outline-primary px-2"
       title="Download"
       aria-label="Download">
        <i class="iconoir-download"></i>
    </a>
    @if ($canManage)
        <button type="button"
                class="btn btn-sm btn-outline-danger px-2"
                title="Hapus"
                aria-label="Hapus"
                onclick="deleteDocument('{{ route('distributor.documents.destroy', $document->id) }}', '{{ addslashes($document->title ?: $document->file_name) }}')">
            <i class="iconoir-trash"></i>
        </button>
    @endif
</div>