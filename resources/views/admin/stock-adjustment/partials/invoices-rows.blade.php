@forelse($adjustments as $index => $adjustment)
<tr>
    <td>{{ $adjustments->firstItem() + $index }}</td>
    <td><strong>{{ $adjustment->trn_no }}</strong></td>
    <td>{{ $adjustment->adjustment_date->format('d-m-Y') }}</td>
    <td>{{ $adjustment->day_name ?? '-' }}</td>
    <td class="text-center">{{ $adjustment->total_items }}</td>
    <td class="text-center text-danger">{{ $adjustment->shortage_items }}</td>
    <td class="text-center text-success">{{ $adjustment->excess_items }}</td>
    <td class="text-end {{ $adjustment->total_amount < 0 ? 'text-danger' : 'text-success' }}">
        {{ number_format($adjustment->total_amount, 2) }}
    </td>
    <td>{{ Str::limit($adjustment->remarks, 30) ?? '-' }}</td>
    <td>
        <a href="{{ route('admin.stock-adjustment.modification', ['trn_no' => $adjustment->trn_no]) }}" class="btn btn-sm btn-outline-warning" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAdjustment({{ $adjustment->id }}, '{{ $adjustment->trn_no }}')" title="Delete">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center text-muted py-4">No stock adjustments found</td>
</tr>
@endforelse
