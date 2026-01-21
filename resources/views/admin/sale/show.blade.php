@extends('layouts.admin')
@section('title', 'Sale Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0 d-flex align-items-center">
      <i class="bi bi-receipt-cutoff me-2"></i> Sale Details
    </h4>
    <div class="text-muted small">Complete details of sale transaction</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.sale.invoices') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Invoices
    </a>
    <button type="button" class="btn btn-success" id="sendEmailBtn">
      <i class="bi bi-envelope"></i> Send Email
    </button>
    <button type="button" class="btn btn-success" id="shareWhatsAppBtn" style="background-color: #25D366; border-color: #25D366;">
      <i class="bi bi-whatsapp"></i> Share on WhatsApp
    </button>
    <a href="{{ route('admin.sale.modification') }}?invoice_no={{ $transaction->invoice_no }}" class="btn btn-primary">
      <i class="bi bi-pencil"></i> Edit Sale
    </a>
  </div>
</div>

<!-- Sale Header Information -->
<div class="row mb-4">
  <div class="col-md-8">
    <div class="card shadow-sm border-0 rounded">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Sale Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Invoice No:</label>
            <div class="form-control-plaintext">{{ $transaction->invoice_no }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Series:</label>
            <div class="form-control-plaintext">{{ $transaction->series ?? 'SB' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Sale Date:</label>
            <div class="form-control-plaintext">{{ $transaction->sale_date ? $transaction->sale_date->format('d/m/Y') : 'N/A' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Due Date:</label>
            <div class="form-control-plaintext">{{ $transaction->due_date ? $transaction->due_date->format('d/m/Y') : 'N/A' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Status:</label>
            <div class="form-control-plaintext">
              <span class="badge bg-success">{{ ucfirst($transaction->status ?? 'Completed') }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Payment Status:</label>
            <div class="form-control-plaintext">
              @if($transaction->payment_status == 'paid')
                <span class="badge bg-success">Paid</span>
              @elseif($transaction->payment_status == 'partial')
                <span class="badge bg-warning">Partial</span>
              @else
                <span class="badge bg-danger">Pending</span>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="card shadow-sm border-0 rounded">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Customer & Salesman</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label fw-bold">Customer Name:</label>
          <div class="form-control-plaintext">{{ $transaction->customer->name ?? 'N/A' }}</div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Customer ID:</label>
          <div class="form-control-plaintext">{{ $transaction->customer_id ?? 'N/A' }}</div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Salesman:</label>
          <div class="form-control-plaintext">{{ $transaction->salesman->name ?? 'N/A' }}</div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Payment Mode:</label>
          <div class="form-control-plaintext">
            @if($transaction->cash_flag == 'Y')
              <span class="badge bg-info">Cash</span>
            @endif
            @if($transaction->transfer_flag == 'Y')
              <span class="badge bg-primary">Transfer</span>
            @endif
            @if($transaction->cash_flag != 'Y' && $transaction->transfer_flag != 'Y')
              <span class="badge bg-secondary">Credit</span>
            @endif
          </div>
        </div>
        @if($transaction->remarks)
        <div class="mb-3">
          <label class="form-label fw-bold">Remarks:</label>
          <div class="form-control-plaintext">{{ $transaction->remarks }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Sale Items -->
<div class="card shadow-sm border-0 rounded mb-4">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Sale Items</h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Batch No</th>
            <th>Expiry Date</th>
            <th class="text-end">Qty</th>
            <th class="text-end">Free Qty</th>
            <th class="text-end">Sale Rate</th>
            <th class="text-end">MRP</th>
            <th class="text-end">Discount %</th>
            <th class="text-end">Amount</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transaction->items as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->item_code }}</td>
            <td>{{ $item->item_name }}</td>
            <td>{{ $item->batch_no ?? '-' }}</td>
            <td>{{ $item->expiry_date ?? '-' }}</td>
            <td class="text-end">{{ number_format($item->qty, 0) }}</td>
            <td class="text-end">{{ number_format($item->free_qty ?? 0, 0) }}</td>
            <td class="text-end">₹{{ number_format($item->sale_rate, 2) }}</td>
            <td class="text-end">₹{{ number_format($item->mrp ?? 0, 2) }}</td>
            <td class="text-end">{{ number_format($item->discount_percent ?? 0, 2) }}%</td>
            <td class="text-end fw-bold">₹{{ number_format($item->amount, 2) }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="11" class="text-center text-muted">No items found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Financial Summary -->
<div class="row">
  <div class="col-md-6">
    <div class="card shadow-sm border-0 rounded">
      <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Amount Breakdown</h5>
      </div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label fw-bold">N.T. Amount:</label>
            <div class="form-control-plaintext">₹{{ number_format($transaction->nt_amount ?? 0, 2) }}</div>
          </div>
          <div class="col-6">
            <label class="form-label fw-bold">SC Amount:</label>
            <div class="form-control-plaintext">₹{{ number_format($transaction->sc_amount ?? 0, 2) }}</div>
          </div>
          <div class="col-6">
            <label class="form-label fw-bold">F.T. Amount:</label>
            <div class="form-control-plaintext">₹{{ number_format($transaction->ft_amount ?? 0, 2) }}</div>
          </div>
          <div class="col-6">
            <label class="form-label fw-bold">Discount Amount:</label>
            <div class="form-control-plaintext">₹{{ number_format($transaction->dis_amount ?? 0, 2) }}</div>
          </div>
          <div class="col-6">
            <label class="form-label fw-bold">SCM Amount:</label>
            <div class="form-control-plaintext">₹{{ number_format($transaction->scm_amount ?? 0, 2) }}</div>
          </div>
          <div class="col-6">
            <label class="form-label fw-bold">Tax Amount:</label>
            <div class="form-control-plaintext">₹{{ number_format($transaction->tax_amount ?? 0, 2) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card shadow-sm border-0 rounded">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>Final Totals</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-bold">SCM Percent:</label>
            <div class="form-control-plaintext">{{ number_format($transaction->scm_percent ?? 0, 3) }}%</div>
          </div>
          <div class="col-6">
            <label class="form-label fw-bold">TCS Amount:</label>
            <div class="form-control-plaintext">₹{{ number_format($transaction->tcs_amount ?? 0, 2) }}</div>
          </div>
          <div class="col-6">
            <label class="form-label fw-bold">Excise Amount:</label>
            <div class="form-control-plaintext">₹{{ number_format($transaction->excise_amount ?? 0, 2) }}</div>
          </div>
          <div class="col-12">
            <hr>
            <label class="form-label fw-bold fs-5">Final Net Amount:</label>
            <div class="form-control-plaintext fs-4 fw-bold text-success">
              ₹{{ number_format($transaction->net_amount ?? 0, 2) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
.form-control-plaintext {
  padding: 0.375rem 0;
  margin-bottom: 0;
  font-size: 0.875rem;
  line-height: 1.5;
  color: #212529;
  background-color: transparent;
  border: solid transparent;
  border-width: 1px 0;
}

.card-header h5 {
  margin: 0;
  font-weight: 600;
}

.table th {
  font-weight: 600;
  font-size: 0.875rem;
}

.table td {
  font-size: 0.875rem;
}
</style>
@endpush

@push('scripts')
<!-- OTP Verification Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="otpModalLabel">
          <i class="bi bi-shield-lock me-2"></i>Email Verification
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-4">
          <div class="mb-3">
            <i class="bi bi-envelope-check" style="font-size: 48px; color: #007bff;"></i>
          </div>
          <p class="mb-2">We've sent a 6-digit OTP to:</p>
          <p class="fw-bold text-primary" id="otpEmailDisplay"></p>
          <small class="text-muted">Valid for 10 minutes</small>
        </div>
        
        <div class="mb-3">
          <label for="otpInput" class="form-label">Enter OTP</label>
          <input type="text" 
                 class="form-control form-control-lg text-center" 
                 id="otpInput" 
                 maxlength="6" 
                 placeholder="000000"
                 style="letter-spacing: 10px; font-size: 24px; font-family: monospace;">
          <div class="invalid-feedback" id="otpError"></div>
        </div>
        
        <div class="alert alert-info" role="alert">
          <i class="bi bi-info-circle me-2"></i>
          <small>Didn't receive the OTP? Check your spam folder or click "Resend OTP"</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="resendOtpBtn">
          <i class="bi bi-arrow-clockwise me-2"></i>Resend OTP
        </button>
        <button type="button" class="btn btn-primary" id="verifyOtpBtn">
          <i class="bi bi-check-circle me-2"></i>Verify & Send Email
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
    const otpInput = document.getElementById('otpInput');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const transactionId = '{{ $transaction->id }}';
    let userEmail = '';
    
    if (sendEmailBtn) {
        sendEmailBtn.addEventListener('click', function() {
            sendOtp();
        });
    }
    
    // Send OTP
    function sendOtp() {
        const btn = sendEmailBtn;
        const originalHtml = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending OTP...';
        
        fetch('{{ route("admin.sale.send-otp", $transaction->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userEmail = data.email;
                document.getElementById('otpEmailDisplay').textContent = data.email;
                otpInput.value = '';
                otpInput.classList.remove('is-invalid');
                otpModal.show();
                
                // Focus on OTP input
                setTimeout(() => otpInput.focus(), 500);
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('❌ Failed to send OTP. Please try again.');
            console.error('Error:', error);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }
    
    // Verify OTP and Send Email
    if (verifyOtpBtn) {
        verifyOtpBtn.addEventListener('click', function() {
            const otp = otpInput.value.trim();
            
            if (otp.length !== 6) {
                otpInput.classList.add('is-invalid');
                document.getElementById('otpError').textContent = 'Please enter a 6-digit OTP';
                return;
            }
            
            const originalHtml = verifyOtpBtn.innerHTML;
            verifyOtpBtn.disabled = true;
            verifyOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
            
            fetch('{{ route("admin.sale.verify-otp-send-email", $transaction->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ otp: otp })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    otpModal.hide();
                    alert('✅ ' + data.message + '\nSent to: ' + data.email);
                } else {
                    otpInput.classList.add('is-invalid');
                    document.getElementById('otpError').textContent = data.message;
                }
            })
            .catch(error => {
                alert('❌ Failed to verify OTP. Please try again.');
                console.error('Error:', error);
            })
            .finally(() => {
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.innerHTML = originalHtml;
            });
        });
    }
    
    // Resend OTP
    if (resendOtpBtn) {
        resendOtpBtn.addEventListener('click', function() {
            otpModal.hide();
            setTimeout(() => sendOtp(), 300);
        });
    }
    
    // Allow only numbers in OTP input
    if (otpInput) {
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 0) {
                this.classList.remove('is-invalid');
            }
        });
        
        // Auto-submit on 6 digits
        otpInput.addEventListener('input', function(e) {
            if (this.value.length === 6) {
                verifyOtpBtn.click();
            }
        });
    }
});
</script>

<!-- WhatsApp Share Handler -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shareWhatsAppBtn = document.getElementById('shareWhatsAppBtn');
    
    if (shareWhatsAppBtn) {
        shareWhatsAppBtn.addEventListener('click', function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            
            // Disable button and show loading state
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Preparing...';
            
            fetch('{{ route("admin.sale.share-whatsapp", $transaction->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Open WhatsApp in new tab
                    window.open(data.whatsapp_url, '_blank');
                    
                    // Show success message
                    alert('✅ WhatsApp opened!\n\nMessage prepared for: ' + data.phone + '\n\nReview and click Send in WhatsApp.');
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                alert('❌ Failed to prepare WhatsApp share. Please try again.');
                console.error('Error:', error);
            })
            .finally(() => {
                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    }
});
</script>
@endpush
