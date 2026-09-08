@php
    header("Location: " . route('sales-submissions.index', ['action' => 'create']));
    exit;
@endphp
<script>
    window.location.href = "{{ route('sales-submissions.index', ['action' => 'create']) }}";
</script>
