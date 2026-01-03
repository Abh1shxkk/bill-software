@extends('layouts.admin')

@section('title', 'Stock Register for IT Return')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-start gap-3">
        <div style="min-width: 100px;">
            <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-text me-2"></i> Stock Register for IT Return</h4>
            <div class="text-muted small">Stock register report for income tax return</div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-success" onclick="exportToExcel()"><i class="bi bi-file-excel me-1"></i> Excel</button>
        <button type="button" class="btn btn-outline-primary" onclick="printReport()"><i class="bi bi-printer me-1"></i> Print (F7)</button>
        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()"><i class="bi bi-x-circle me-1"></i> Close</button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.inventory.stock.stock-register-it-return') }}" class="row g-3" id="filter-form">
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date', date('Y-m-01')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order By</label>
                    <select name="order_by" class="form-select">
                        <option value="company" {{ request('order_by') == 'company' ? 'selected' : '' }}>Company</option>
                        <option value="item" {{ request('order_by') == 'item' ? 'selected' : '' }}>Item</option>
                        <option value="opening" {{ request('order_by') == 'opening' ? 'selected' : '' }}>Opening</option>
                        <option value="closing" {{ request('order_by') == 'closing' ? 'selected' : '' }}>Closing</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-input" name="search" value="{{ request('search') }}" placeholder="Search item/company...">
                        <button class="btn btn-outline-secondary" type="button" id="clear-search"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="view" value="1" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> View</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Company</th>
                    <th class="text-end">Opening</th>
                    <th class="text-end">Purchase</th>
                    <th class="text-end">Sale</th>
                    <th class="text-end">Shortage</th>
                    <th class="text-end">Closing</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @if(isset($reportData) && $reportData->count() > 0)
                    @foreach($reportData as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row['item_name'] }}</td>
                        <td>{{ $row['company_name'] }}</td>
                        <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['purchase'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['sale'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['shortage'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['closing'], 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="8" class="text-center text-muted">No data found. Click "View" to load report.</td></tr>
                @endif
            </tbody>
            @if(isset($reportData) && $reportData->count() > 0)
            <tfoot class="table-secondary fw-bold">
                <tr style="color: #cc00cc;">
                    <td colspan="3">TOTAL :</td>
                    <td class="text-end">{{ number_format($totals['total_opening'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($totals['total_purchase'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($totals['total_sale'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($totals['total_shortage'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($totals['total_closing'] ?? 0, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if(isset($reportData) && $reportData->count() > 0)
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>Total Records: {{ $reportData->count() }}</div>
            <div class="text-muted">Report Date: {{ date('d-m-Y') }}</div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const clearBtn = document.getElementById('clear-search');
    const tableBody = document.getElementById('table-body');
    
    // Client-side search filter
    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const itemName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const companyName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                if(itemName.includes(searchTerm) || companyName.includes(searchTerm) || searchTerm === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Clear search
    if(clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('keyup'));
            searchInput.focus();
        });
    }
    
    // F7 shortcut for print
    document.addEventListener('keydown', function(e) {
        if(e.key === 'F7') {
            e.preventDefault();
            printReport();
        }
    });
});

function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.getElementById('filter-form')));
    params.set('export', 'excel');
    window.location.href = '{{ route("admin.reports.inventory.stock.stock-register-it-return") }}?' + params.toString();
}

function printReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('filter-form')));
    params.set('print', '1');
    window.open('{{ route("admin.reports.inventory.stock.stock-register-it-return") }}?' + params.toString(), 'PrintReport', 'width=900,height=700');
}
</script>
@endpush
