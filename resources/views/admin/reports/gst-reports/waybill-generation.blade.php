{{-- WayBill Generation Report --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <h5>WayBill Generation</h5>
        </div>
        <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
            <form id="filterForm" method="GET">
                {{-- Filter fields will go here --}}
                <p>WayBill Generation filter form placeholder</p>
                
                <button type="submit" name="view" value="1" class="btn btn-primary">View</button>
                <button type="button" onclick="printReport()" class="btn btn-secondary">Print</button>
            </form>
        </div>
    </div>
</div>

<script>
function printReport() {
    window.open('{{ route("admin.reports.gst.waybill-generation") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endsection
