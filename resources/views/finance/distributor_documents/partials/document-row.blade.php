<div class="d-flex align-items-center justify-content-between gap-2 mb-2">
    @include('finance.distributor_documents.partials.document-file', ['document' => $document])
    @include('finance.distributor_documents.partials.document-actions', ['document' => $document, 'year' => $year, 'tab' => 'monthly'])
</div>