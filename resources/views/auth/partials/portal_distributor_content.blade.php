@include('finance.distributor_documents.partials.detail-content', [
    'distributor' => $distributor,
    'year' => $year,
    'tab' => $tab ?? 'monthly',
    'monthlyDocs' => $monthlyDocs,
    'transferDocs' => $transferDocs,
    'transferYear' => $transferYear ?? 'all',
    'availableTransferYears' => $availableTransferYears,
    'isPortal' => true,
])
