{{-- 
    Transaction Date Input Component with Validation
    
    Usage:
    @include('components.transaction-date-input', [
        'name' => 'date',
        'label' => 'Date',
        'transactionType' => 'sale',
        'value' => $transaction->sale_date ?? now()->format('Y-m-d'),
        'excludeId' => $transaction->id ?? null
    ])
--}}

@php
    $name = $name ?? 'date';
    $label = $label ?? 'Date';
    $transactionType = $transactionType ?? 'sale';
    $value = $value ?? now()->format('Y-m-d');
    $excludeId = $excludeId ?? null;
    $required = $required ?? true;
    $class = $class ?? '';
@endphp

<div class="input-group input-group-sm">
    @if($label)
    <span class="input-group-text">{{ $label }}</span>
    @endif
    <input type="date" 
           name="{{ $name }}" 
           id="{{ $name }}"
           class="form-control {{ $class }}" 
           value="{{ $value }}"
           data-txn-date-type="{{ $transactionType }}"
           @if($excludeId) data-txn-exclude-id="{{ $excludeId }}" @endif
           @if($required) required @endif>
</div>

@once
@push('scripts')
<script src="{{ asset('js/transaction-date-validator.js') }}"></script>
@endpush
@endonce
