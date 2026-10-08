<div class="d-inline-flex gap-1 align-items-center">
    <button type="button"
            class="btn btn-sm btn-outline-secondary px-2"
            title="Preview PDF"
            aria-label="Preview PDF"
            onclick="openPublicPdfPreview('{{ route('portal.distributor.preview', $document->id) }}', '{{ addslashes($document->title ?: $document->file_name) }}', '{{ route('portal.distributor.download', $document->id) }}')">
        <i class="iconoir-eye"></i>
    </button>
    <a href="{{ route('portal.distributor.download', $document->id) }}"
       class="btn btn-sm btn-outline-primary px-2"
       title="Download"
       aria-label="Download">
        <i class="iconoir-download"></i>
    </a>
</div>
