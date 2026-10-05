<div class="d-flex align-items-center gap-2">
    <span class="document-file-icon flex-shrink-0">
        @if ($document->file_ext === 'pdf')
            <i class="iconoir-page text-secondary"></i>
        @endif
    </span>
    <div class="overflow-hidden">
        <div class="fw-semibold text-truncate text-dark" style="max-width: 17rem;" title="{{ $document->title ?: $document->file_name }}">
            {{ $document->title ?: $document->file_name }}
        </div>
        <div class="small text-muted" style="font-size: 0.75rem;">
            {{ $document->human_file_size }}
            @if ($document->title && $document->title !== $document->file_name)
                <span class="text-secondary opacity-75">({{ $document->file_name }})</span>
            @endif
        </div>
    </div>
</div>