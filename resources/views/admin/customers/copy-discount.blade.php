@extends('layouts.admin')
@section('title', 'Copy Discount - ' . $customer->name)
@section('content')
    <style>
        .copy-discount-container {
            background: #fff;
            border: 2px solid #999;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            margin: 50px auto;
        }

        .copy-discount-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #8b4513;
            font-style: italic;
            background: #ffcccc;
            padding: 15px;
            margin: -30px -30px 30px -30px;
            border-radius: 6px 6px 0 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            font-size: 13px;
            border-radius: 4px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 5px rgba(0, 102, 204, 0.3);
        }

        .new-party-display {
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-weight: bold;
            color: #0066cc;
        }

        .copy-from-section {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .copy-from-section input {
            flex: 1;
        }

        .copy-from-section button {
            padding: 8px 16px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .copy-from-section button:hover {
            background: #0052a3;
        }

        .old-party-section {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .old-party-section label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .old-party-display {
            background: #fff;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-height: 40px;
            display: flex;
            align-items: center;
            font-size: 13px;
        }

        .discount-display {
            background: #fff;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            color: #0066cc;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }

        .button-group button {
            padding: 10px 30px;
            font-size: 14px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-ok {
            background: #4CAF50;
            color: white;
        }

        .btn-ok:hover {
            background: #45a049;
        }

        .btn-close {
            background: #f44336;
            color: white;
        }

        .btn-close:hover {
            background: #da190b;
        }

        .info-message {
            background: #e3f2fd;
            border-left: 4px solid #0066cc;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 12px;
            color: #0066cc;
        }

        .error-message {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 12px;
            color: #c62828;
        }

        .success-message {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 12px;
            color: #2e7d32;
        }
    </style>

    <div class="copy-discount-container">
        <div class="copy-discount-title">Copy Discount</div>

        @if($errors->any())
            <div class="error-message">
                <strong>Error:</strong>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="info-message">
            <strong>ℹ️ How to use:</strong> Select a customer from the dropdown to copy their discount settings to this customer.
        </div>

        <form method="POST" action="{{ route('admin.customers.copy-discount.store', $customer) }}" id="copyDiscountForm">
            @csrf

            <!-- New Party Section -->
            <div class="form-group">
                <label>New Party:</label>
                <div class="new-party-display">{{ $customer->name }}</div>
            </div>

            <!-- Copy From Section -->
            <div class="form-group">
                <label>Copy from Other Party:</label>
                <div class="copy-from-section">
                    <select name="source_customer_id" id="sourceCustomer" required onchange="loadSourceDiscounts()">
                        <option value="">-- Select Customer --</option>
                        @foreach($allCustomers as $cust)
                            @if($cust->id !== $customer->id)
                                <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button type="button" onclick="loadSourceDiscounts()" title="Load discounts">
                        🔄
                    </button>
                </div>
            </div>

            <!-- Old Party Display Section -->
            <div class="old-party-section">
                <label>Old Party:</label>
                <div class="old-party-display" id="oldPartyDisplay">
                    -- Select a customer above --
                </div>
            </div>

            <!-- Discount Display Section -->
            <div class="form-group">
                <label>Normal Discount:</label>
                <div class="discount-display" id="discountDisplay">
                    0.00
                </div>
                <input type="hidden" name="discount_percent" id="discountPercent" value="0.00">
            </div>

            <!-- Action Buttons -->
            <div class="button-group">
                <button type="submit" class="button-group button btn-ok">
                    Ok
                </button>
                <a href="{{ route('admin.customers.show', $customer) }}" class="button-group button btn-close" style="text-decoration: none;">
                    Close
                </a>
            </div>
        </form>
    </div>

    <script>
        function loadSourceDiscounts() {
            const sourceCustomerId = document.getElementById('sourceCustomer').value;
            
            if (!sourceCustomerId) {
                document.getElementById('oldPartyDisplay').textContent = '-- Select a customer above --';
                document.getElementById('discountDisplay').textContent = '0.00';
                document.getElementById('discountPercent').value = '0.00';
                return;
            }

            // Fetch customer details and discounts via AJAX
            const url = `{{ url('/admin/api/customer-discounts') }}/${sourceCustomerId}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const customer = data.customer;
                        const discounts = data.discounts;

                        // Update old party display
                        document.getElementById('oldPartyDisplay').textContent = customer.name;

                        // Calculate average discount or show first one
                        let avgDiscount = 0;
                        if (discounts.length > 0) {
                            avgDiscount = discounts.reduce((sum, d) => sum + parseFloat(d.discount_percent), 0) / discounts.length;
                        }

                        // Update discount display
                        document.getElementById('discountDisplay').textContent = avgDiscount.toFixed(2);
                        document.getElementById('discountPercent').value = avgDiscount.toFixed(2);
                    } else {
                        alert('Error loading customer discounts');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading discounts');
                });
        }
    </script>
@endsection
