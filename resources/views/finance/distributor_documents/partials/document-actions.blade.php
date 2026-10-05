<div class="d-inline-flex gap-1 align-items-center">
    <button type="button"
            class="btn btn-sm btn-outline-secondary px-2"
            title="Preview PDF"
            aria-label="Preview PDF"
            onclick="openPdfPreview('{{ route('distributor.documents.preview', $document->id) }}', '{{ addslashes($document->title ?: $document->file_name) }}', '{{ route('distributor.documents.download', $document->id) }}')">
        <i class="iconoir-eye"></i>
    </button>
    <a href="{{ route('distributor.documents.download', $document->id) }}"
       class="btn btn-sm btn-outline-primary px-2"
       title="Download"
       aria-label="Download">
        <i class="iconoir-download"></i>
    </a>
    <button type="button"
            class="btn btn-sm btn-outline-danger px-2"
            title="Hapus"
            aria-label="Hapus"
            onclick="deleteDocument('{{ route('distributor.documents.destroy', $document->id) }}', '{{ addslashes($document->title ?: $document->file_name) }}')">
        <i class="iconoir-trash"></i>
    </button>
</div>