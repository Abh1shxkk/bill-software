<nav id="appHeader" class="navbar navbar-expand-lg navbar-light app-header"
  style="background-color: white; border-bottom: 1px solid #dee2e6;">
  <div class="container-fluid">
    <button class="btn btn-outline-dark me-2" id="headerSidebarToggle" aria-label="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    

    <div class="collapse navbar-collapse" id="topbarNav">
      <ul class="navbar-nav mx-auto">
        <!-- Transaction Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Transaction
          </a>
          <ul class="dropdown-menu">
            <!-- Sale Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sale</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.sale.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale.invoices') }}">Sale Invoices</a></li>
              </ul>
            </li>
            
           
            
            <!-- Purchase Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.purchase.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase.invoices') }}">Purchase Invoices</a></li>
              </ul>
            </li>
            
           
            
            <!-- Sale Return Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sales Return</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.sale-return.transaction') }}">Transaction </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale-return.modification') }}">Modification </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale-return.index') }}">Sale Return Invoice</a></li>
              </ul>
            </li>
            
            <!-- Breakage/Expiry from customer Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Breakage/Expiry from customer</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.breakage-expiry.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-expiry.modification') }}">Modification </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-expiry.expiry-date') }}">Expiry Date Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-expiry.index') }}">Breakage/Expiry Invoice</a></li>
              </ul>
            </li>
            
            <!-- Purchase Return Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Return</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.purchase-return.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase-return.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase-return.index') }}">Purchase Return Invoice</a></li>
              </ul>
            </li>
            
            <!-- Breakage/Expiry to Supplier Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Breakage/Expiry to Supplier</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.issued-transaction') }}">Issued - Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.issued-modification') }}">Issued - Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.received-transaction') }}">Received - Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.received-modification') }}">Received - Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.unused-dump-transaction') }}">Unused Dump - Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.unused-dump-modification') }}">Unused Dump - Modification</a></li>
              </ul>
            </li>

             <!-- Sale Challan Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sale Challan</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.sale-challan.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale-challan.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.sale-challan.invoices') }}">Sale Challan Invoice</a></li>
              </ul>
            </li>

             <!-- Purchase Challan Submenu -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Challan</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.purchase-challan.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase-challan.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.purchase-challan.invoices') }}">Purchase Challan Invoice</a></li>
              </ul>
            </li>

            <li><hr class="dropdown-divider"></li>

            <!-- Credit Note -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Credit Note</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.credit-note.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.credit-note.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.credit-note.invoices') }}">Credit Note Invoices</a></li>
              </ul>
            </li>

            <!-- Debit Note -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Debit Note</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.debit-note.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.debit-note.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.debit-note.invoices') }}">Debit Note Invoices</a></li>
              </ul>
            </li>

            <li><hr class="dropdown-divider"></li>

            <!-- Misc Transaction -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Misc Transaction</a>
              <ul class="dropdown-menu">
                <!-- Quotation -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Quotation</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.quotation.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.quotation.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.quotation.index') }}">Quotation List</a></li>
                  </ul>
                </li>
                
                <!-- Replacement Note -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Replacement Note</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-note.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-note.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-note.index') }}">Replacement Note Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Replacement Received -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Replacement Received</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-received.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-received.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.replacement-received.index') }}">Replacement Received Invoice</a></li>
                  </ul>
                </li>

                <!-- Sale Return Replacement (RG) -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sale Return Replacement</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.sale-return-replacement.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sale-return-replacement.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sale-return-replacement.index') }}">Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Stock Adjustment -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Adjustment</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-adjustment.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-adjustment.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-adjustment.invoices') }}">Invoices</a></li>
                  </ul>
                </li>
                
                <!-- Stock Transfer Outgoing -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Outgoing</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing.index') }}"> Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Stock Transfer Outgoing Return -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Outgoing Return</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing-return.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing-return.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-outgoing-return.index') }}"> Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Stock Transfer Incoming -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Incoming</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming.index') }}">Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Stock Transfer Incoming Return -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Incoming Return</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming-return.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming-return.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stock-transfer-incoming-return.index') }}">Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Sample Issued -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sample Issued</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.sample-issued.create') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sample-issued.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sample-issued.index') }}">Invoice</a></li>
                  </ul>
                </li>
                
                <!-- Sample Received -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sample Received</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.sample-received.create') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sample-received.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sample-received.index') }}">Invoice</a></li>
                  </ul>
                </li>

                <!-- Godown Breakage/Expiry -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Godown Breakage/Expiry</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.godown-breakage-expiry.create') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.godown-breakage-expiry.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.godown-breakage-expiry.index') }}">Invoice</a></li>
                  </ul>
                </li>

                <!-- New Item Generation in Pending Order -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">New Item in Pending Order</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.pending-order-item.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.pending-order-item.index') }}">List</a></li>
                  </ul>
                </li>

                <!-- Claim to Supplier -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Claim to Supplier</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.claim-to-supplier.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.claim-to-supplier.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.claim-to-supplier.index') }}">Claim Invoices</a></li>
                  </ul>
                </li>

                <!-- Sale Voucher (HSN) -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sale Voucher (HSN)</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.sale-voucher.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sale-voucher.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sale-voucher.index') }}">Invoice</a></li>
                  </ul>
                </li>

                <!-- Purchase Voucher (HSN) -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Voucher (HSN)</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.purchase-voucher.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.purchase-voucher.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.purchase-voucher.index') }}">Invoice</a></li>
                  </ul>
                </li>

                <!-- Sale Return Voucher (HSN) -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sale Return Voucher (HSN)</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.sale-return-voucher.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sale-return-voucher.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.sale-return-voucher.index') }}">Invoice</a></li>
                  </ul>
                </li>

                <!-- Purchase Return Voucher (HSN) -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Return Voucher (HSN)</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.purchase-return-voucher.transaction') }}">Transaction</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.purchase-return-voucher.modification') }}">Modification</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.purchase-return-voucher.index') }}">Invoice</a></li>
                  </ul>
                </li>
              </ul>
            </li>

            <!-- Receipt from Customer -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Receipt from Customer</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.customer-receipt.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.customer-receipt.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.customer-receipt.index') }}">Receipt List</a></li>
              </ul>
            </li>

            <!-- Payment to Supplier -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Payment to Supplier</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.supplier-payment.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.supplier-payment.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.supplier-payment.index') }}">Payment List</a></li>
              </ul>
            </li>

            <!-- Cheque Returned Unpaid -->
            <li>
              <a class="dropdown-item" href="{{ route('admin.cheque-return.index') }}">
                Cheque Returned Unpaid
              </a>
            </li>

            <!-- Deposit Slip -->
            <li>
              <a class="dropdown-item" href="{{ route('admin.deposit-slip.index') }}">
                Deposit Slip
              </a>
            </li>

            
            <li><hr class="dropdown-divider"></li>
            
            <!-- Voucher Entry -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Voucher Entry</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.voucher-entry.transaction') }}">Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.voucher-entry.modification') }}">Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.voucher-entry.index') }}">Invoice</a></li>
              </ul>
            </li>
            <!-- Voucher Purchase (Input GST) -->
            <li>
              <a class="dropdown-item" href="{{ route('admin.voucher-purchase.transaction') }}">
                Voucher Purchase (Input GST)
              </a>
            </li>

            <!-- Voucher Income (Output GST) -->
            <li>
              <a class="dropdown-item" href="{{ route('admin.voucher-income.transaction') }}">
                Voucher Income (Output GST)
              </a>
            </li>

            <!-- Multi Voucher Entry -->
            <li>
              <a class="dropdown-item" href="{{ route('admin.multi-voucher.transaction') }}">
                Multi Voucher Entry
              </a>
            </li>

            <!-- Cash Deposited / Withdrawn from Bank -->
            <li>
              <a class="dropdown-item" href="{{ route('admin.bank-transaction.transaction') }}">
                Cash Deposited / Withdrawn
              </a>
            </li>
          </ul>
        </li>

        <!-- Reports Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Reports
          </a>
          <ul class="dropdown-menu">
            <!-- Sales Reports -->
            <li>
              <a class="dropdown-item" href="{{ route('admin.reports.sales') }}">Sales Reports</a>
            </li>

            <!-- Purchase Reports -->
            <li>
              <a class="dropdown-item" href="{{ route('admin.reports.purchase') }}">Purchase Reports</a>
            </li>

            <!-- Inventory Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Inventory Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Stock Report</a></li>
                <li><a class="dropdown-item" href="#">Stock Ledger</a></li>
                <li><a class="dropdown-item" href="#">Stock Valuation</a></li>
                <li><a class="dropdown-item" href="#">Reorder Level Report</a></li>
              </ul>
            </li>

            <!-- Management Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Management Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Profit & Loss</a></li>
                <li><a class="dropdown-item" href="#">Balance Sheet</a></li>
                <li><a class="dropdown-item" href="#">Trial Balance</a></li>
              </ul>
            </li>

            <!-- Misc. Transaction Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Misc. Transaction Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Stock Transfer Report</a></li>
                <li><a class="dropdown-item" href="#">Sample Report</a></li>
                <li><a class="dropdown-item" href="#">Adjustment Report</a></li>
              </ul>
            </li>

            <li><hr class="dropdown-divider"></li>

            <!-- Breakage/Expiry Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Breakage/Expiry Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Breakage Report</a></li>
                <li><a class="dropdown-item" href="#">Expiry Report</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.batches.expiry-report') }}">Batch Expiry Report</a></li>
              </ul>
            </li>

            <!-- Receipt / Payment Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Receipt / Payment Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Receipt Report</a></li>
                <li><a class="dropdown-item" href="#">Payment Report</a></li>
                <li><a class="dropdown-item" href="#">Outstanding Report</a></li>
              </ul>
            </li>

            <!-- Financial Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Financial Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Day Book</a></li>
                <li><a class="dropdown-item" href="#">Cash Book</a></li>
                <li><a class="dropdown-item" href="#">Bank Book</a></li>
                <li><a class="dropdown-item" href="#">Ledger Report</a></li>
              </ul>
            </li>

            <li><hr class="dropdown-divider"></li>

            <!-- Other Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Other Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Customer List</a></li>
                <li><a class="dropdown-item" href="#">Supplier List</a></li>
                <li><a class="dropdown-item" href="#">Item List</a></li>
              </ul>
            </li>

            <!-- Label Generation -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Label Generation</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Item Labels</a></li>
                <li><a class="dropdown-item" href="#">Barcode Labels</a></li>
              </ul>
            </li>

            <li><hr class="dropdown-divider"></li>

            <!-- GST Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">GST Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">GSTR-1</a></li>
                <li><a class="dropdown-item" href="#">GSTR-2</a></li>
                <li><a class="dropdown-item" href="#">GSTR-3B</a></li>
                <li><a class="dropdown-item" href="#">HSN Summary</a></li>
              </ul>
            </li>
          </ul>
        </li>

        <!-- Administration Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Administration
          </a>
          <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item" href="{{ route('admin.administration.hotkeys.index') }}">
                <i class="bi bi-keyboard me-2"></i>Hotkey Management
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="{{ route('profile.settings') }}">
                <i class="bi bi-gear me-2"></i>Settings
              </a>
            </li>
          </ul>
        </li>
      </ul>
      
      <!-- Right Side Icons -->
      <ul class="navbar-nav align-items-center">
        <!-- Keyboard Shortcuts Button -->
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0)" onclick="if(typeof createHelpPanel === 'function') createHelpPanel();" title="Keyboard Shortcuts (F1)" style="padding: 0.5rem 0.75rem;">
            <i class="bi bi-keyboard" style="font-size: 1.2rem;"></i>
          </a>
        </li>
        
        <!-- Calculator Button -->
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0)" onclick="openHeaderCalculator();" title="Calculator (Ctrl+Shift+K)" style="padding: 0.5rem 0.75rem;">
            <i class="bi bi-calculator" style="font-size: 1.2rem;"></i>
          </a>
        </li>
        
        <!-- Profile Dropdown -->
        <li class="nav-item d-none d-sm-inline">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown"
          aria-expanded="false">
          <img
            src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : 'https://i.pravatar.cc/32' }}"
            class="rounded-circle me-2" width="32" height="32" alt="avatar">
          <span class="d-none d-sm-inline">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li class="px-3 py-2 small text-muted">
            {{ auth()->user()->email }}
          </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="{{ route('profile.settings') }}"><i
                  class="bi bi-gear me-2"></i>Settings</a></li>
            <li>
              <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                @csrf
                <button class="btn btn-sm btn-outline-danger w-100"><i
                    class="bi bi-box-arrow-right me-2"></i>Logout</button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  /* Header styling */
  .app-header {
    grid-area: header;
    z-index: 100;
    position: relative;
  }

  /* Sidebar toggle button */
  #headerSidebarToggle {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
  }

  #headerSidebarToggle:hover {
    background-color: rgba(0, 0, 0, 0.1);
    border: none !important;
  }

  #headerSidebarToggle:focus,
  #headerSidebarToggle:active {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background-color: rgba(0, 0, 0, 0.15);
  }

  /* Navbar toggler */
  .navbar-toggler {
    border: none;
  }

  /* Dropdown menu styling - Windows 11 Style (Sharp) */
  .dropdown-menu {
    border-radius: 0px; /* Sharp corners */
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(0, 0, 0, 0.08);
    padding: 4px;
    display: none;
    font-size: 12px;
    background-color: #ffffff;
    animation: dropdownFadeIn 0.15s ease-out;
  }
  
  @keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .dropdown-menu.show {
    display: block;
  }

  .dropdown-item {
    padding: 6px 12px;
    margin: 0; /* Full width items for sharp look */
    width: 100%;
    border-radius: 0px; /* Sharp corners */
    transition: all 0.1s ease;
    white-space: nowrap;
    font-size: 12px;
    position: relative;
  }

  .dropdown-item:hover, .dropdown-item:focus {
    background-color: rgba(13, 110, 253, 0.1); /* Keep original color */
    color: #0d6efd;
  }

  /* Nested dropdown (submenu) */
  .dropdown-submenu {
    position: relative;
  }

  .dropdown-submenu > .dropdown-menu {
    top: -4px;
    left: 100%;
    margin-top: 0;
    margin-left: 6px; /* Slight gap */
    min-width: 160px;
    z-index: 1060;
  }
  
  /* Remove the old invisible bridge as we use JS delay now, 
     but keeping a small one doesn't hurt for fast movements */
  .dropdown-submenu > .dropdown-menu::before {
    content: '';
    position: absolute;
    top: 0;
    left: -10px;
    width: 10px;
    height: 100%;
    background: transparent;
  }
  
  /* Submenu opens upward when near bottom */
  .dropdown-submenu.dropup > .dropdown-menu {
    top: auto;
    bottom: 0;
  }

  /* Active/hover state for submenu toggle */
  .dropdown-submenu:hover > .dropdown-toggle {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
  }

  /* Align submenu arrow to right and use sharp SVG */
  .dropdown-submenu > .dropdown-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .dropdown-submenu > .dropdown-toggle::after {
    border: none;
    content: "";
    width: 10px;
    height: 10px;
    /* Sharp chevron right SVG */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none' stroke='%23666' stroke-width='1.5' stroke-linecap='square' stroke-linejoin='miter'%3E%3Cpath d='M6 12l4-4-4-4'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    transform: none;
    margin-left: 10px;
  }

  /* Change arrow color on hover */
  .dropdown-submenu > .dropdown-toggle:hover::after {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none' stroke='%230d6efd' stroke-width='1.5' stroke-linecap='square' stroke-linejoin='miter'%3E%3Cpath d='M6 12l4-4-4-4'/%3E%3C/svg%3E");
  }
</style>
<script>
  // Sidebar toggle functionality
  document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('headerSidebarToggle');
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function () {
        document.body.classList.toggle('sidebar-collapsed');
      });
    }

    // Close all submenus
    function closeAllSubmenus() {
      document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(menu) {
        menu.classList.remove('show');
      });
      document.querySelectorAll('.dropdown-submenu').forEach(function(submenu) {
        submenu.classList.remove('dropup');
      });
    }

    // Function to position submenu using fixed positioning to break out of scrollable parents
    function adjustSubmenuPosition(submenu, submenuDropdown) {
      if (!submenuDropdown) return;
      
      const rect = submenu.getBoundingClientRect();
      const viewportWidth = window.innerWidth;
      const viewportHeight = window.innerHeight;
      
      // Temporarily show to get dimensions if needed
      submenuDropdown.style.display = 'block';
      submenuDropdown.style.visibility = 'hidden';
      const menuWidth = submenuDropdown.offsetWidth || 220;
      const menuHeight = submenuDropdown.scrollHeight; // Use scrollHeight to get full height
      submenuDropdown.style.visibility = '';
      submenuDropdown.style.display = '';

      // Set fixed positioning to break out of any scrollable container
      submenuDropdown.style.position = 'fixed';
      submenuDropdown.style.zIndex = '9999';
      
      // Calculate Left Position (Next Container)
      // Default: Place to the right of the parent item
      let left = rect.right;
      
      // If no space on right, place to the left
      if (left + menuWidth > viewportWidth) {
          left = rect.left - menuWidth;
      }
      submenuDropdown.style.left = left + 'px';

      // Calculate Top Position
      const headerHeight = 60; // Approximate header height
      
      // Strategy:
      // 1. Try aligning top with parent (Standard)
      // 2. If it overflows bottom, try aligning bottom to viewport bottom (Shift Up)
      // 3. If shifting up makes it go above header (Too Tall), then Center it & Scroll.
      
      let top = rect.top;
      
      // Check if it fits below
      if (top + menuHeight > viewportHeight) {
          // Calculate shift up position (align bottom to viewport bottom)
          const bottomEdge = viewportHeight - 10;
          let shiftedTop = bottomEdge - menuHeight;
          
          if (shiftedTop < headerHeight + 5) {
             // Case 3: Too tall to fit normally. CENTER IT.
             let centeredTop = (viewportHeight - menuHeight) / 2;
             
             if (centeredTop < headerHeight + 5) {
                 // Even centered it's too tall/high, clamp to header
                 centeredTop = headerHeight + 5;
                 submenuDropdown.style.maxHeight = (viewportHeight - centeredTop - 10) + 'px';
                 submenuDropdown.style.overflowY = 'auto';
             } else {
                 submenuDropdown.style.maxHeight = 'none';
                 submenuDropdown.style.overflowY = 'visible';
             }
             top = centeredTop;
          } else {
             // Case 2: Fits if shifted up
             top = shiftedTop;
             submenuDropdown.style.maxHeight = 'none';
             submenuDropdown.style.overflowY = 'visible';
          }
      } else {
          // Case 1: Fits normally
          submenuDropdown.style.maxHeight = 'none';
          submenuDropdown.style.overflowY = 'visible';
      }
      
      submenuDropdown.style.top = top + 'px';
      submenuDropdown.style.bottom = 'auto';
    }

    // Submenu functionality for nested dropdowns with Delay
    document.querySelectorAll('.dropdown-submenu').forEach(function (submenu) {
      const submenuDropdown = submenu.querySelector(':scope > .dropdown-menu');
      const toggle = submenu.querySelector(':scope > .dropdown-toggle');
      let enterTimeout;
      let leaveTimeout;
      
      // Mouse enter - open submenu with slight delay
      submenu.addEventListener('mouseenter', function () {
        clearTimeout(leaveTimeout); // Cancel any pending close
        
        enterTimeout = setTimeout(function() {
            if (submenuDropdown) {
              // Close sibling submenus first
              const parentMenu = submenu.closest('.dropdown-menu');
              if (parentMenu) {
                parentMenu.querySelectorAll(':scope > .dropdown-submenu > .dropdown-menu').forEach(function(menu) {
                  if (menu !== submenuDropdown) {
                    menu.classList.remove('show');
                  }
                });
              }
              submenuDropdown.classList.add('show');
              adjustSubmenuPosition(submenu, submenuDropdown);
            }
        }, 300); // 300ms delay to prevent accidental opening
      });

      // Mouse leave - close submenu with delay
      submenu.addEventListener('mouseleave', function () {
        clearTimeout(enterTimeout); // Cancel any pending open
        
        leaveTimeout = setTimeout(function() {
            if (submenuDropdown) {
              submenuDropdown.classList.remove('show');
              submenu.classList.remove('dropup');
            }
        }, 600); // 600ms delay to allow moving to submenu
      });

      // Keep submenu open when hovering the menu itself
      if (submenuDropdown) {
          submenuDropdown.addEventListener('mouseenter', function() {
              clearTimeout(leaveTimeout);
          });
          submenuDropdown.addEventListener('mouseleave', function() {
              leaveTimeout = setTimeout(function() {
                  submenuDropdown.classList.remove('show');
                  submenu.classList.remove('dropup');
              }, 600);
          });
      }

      // Click support for mobile/touch devices
      if (toggle) {
        toggle.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          
          if (submenuDropdown) {
            const isShowing = submenuDropdown.classList.contains('show');
            
            // Close sibling submenus
            const parentMenu = submenu.closest('.dropdown-menu');
            if (parentMenu) {
              parentMenu.querySelectorAll(':scope > .dropdown-submenu > .dropdown-menu').forEach(function(menu) {
                menu.classList.remove('show');
              });
            }
            
            if (!isShowing) {
              submenuDropdown.classList.add('show');
              adjustSubmenuPosition(submenu, submenuDropdown);
            } else {
              submenuDropdown.classList.remove('show');
              submenu.classList.remove('dropup');
            }
          }
        });
      }
    });

    // Close submenus when main dropdown closes
    document.querySelectorAll('.nav-item.dropdown').forEach(function(dropdown) {
      dropdown.addEventListener('hidden.bs.dropdown', function () {
        closeAllSubmenus();
      });
    });

    // Close submenus when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.dropdown-menu') && !e.target.closest('.dropdown-toggle')) {
        closeAllSubmenus();
      }
    });
  });

  // ========== GLOBAL CALCULATOR ==========
  let headerCalcExpression = '';
  
  function openHeaderCalculator() {
    // Check if calculator already exists
    if (document.getElementById('header-calculator-modal')) {
      document.getElementById('header-calculator-modal').style.display = 'block';
      return;
    }
    
    // Create calculator modal
    const calcModal = document.createElement('div');
    calcModal.id = 'header-calculator-modal';
    calcModal.innerHTML = `
      <style>
        #header-calculator-modal {
          position: fixed;
          top: 60px;
          right: 20px;
          z-index: 10001;
          animation: calcSlideDown 0.2s ease-out;
        }
        @keyframes calcSlideDown {
          from { opacity: 0; transform: translateY(-10px); }
          to { opacity: 1; transform: translateY(0); }
        }
        .header-calc-container {
          background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
          border-radius: 12px;
          box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
          overflow: hidden;
          width: 260px;
        }
        .header-calc-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 10px 14px;
          background: rgba(255,255,255,0.1);
          color: white;
        }
        .header-calc-header h6 { margin: 0; font-size: 0.85rem; font-weight: 600; }
        .header-calc-body { padding: 12px; }
        .header-calc-display {
          width: 100%;
          background: #0f172a;
          border: none;
          color: #22d3ee;
          font-size: 1.6rem;
          font-family: 'Consolas', monospace;
          text-align: right;
          padding: 12px;
          border-radius: 8px;
          margin-bottom: 10px;
          font-weight: 600;
        }
        .header-calc-buttons {
          display: grid;
          grid-template-columns: repeat(4, 1fr);
          gap: 6px;
        }
        .header-calc-btn {
          padding: 12px;
          font-size: 1.1rem;
          border: none;
          border-radius: 8px;
          cursor: pointer;
          transition: all 0.15s ease;
          background: #334155;
          color: white;
          font-weight: 600;
        }
        .header-calc-btn:hover { background: #475569; transform: scale(1.05); }
        .header-calc-btn:active { transform: scale(0.95); }
        .header-calc-btn.op { background: #6366f1; }
        .header-calc-btn.op:hover { background: #4f46e5; }
        .header-calc-btn.clear { background: #ef4444; }
        .header-calc-btn.clear:hover { background: #dc2626; }
        .header-calc-btn.equals { background: #10b981; }
        .header-calc-btn.equals:hover { background: #059669; }
        .header-calc-btn.zero { grid-column: span 2; }
      </style>
      <div class="header-calc-container">
        <div class="header-calc-header">
          <h6><i class="bi bi-calculator me-2"></i>Calculator</h6>
          <button type="button" class="btn-close btn-close-white btn-sm" onclick="closeHeaderCalculator()"></button>
        </div>
        <div class="header-calc-body">
          <input type="text" id="header-calc-display" class="header-calc-display" readonly value="0">
          <div class="header-calc-buttons">
            <button class="header-calc-btn clear" onclick="headerCalcClear()">C</button>
            <button class="header-calc-btn op" onclick="headerCalcBackspace()">⌫</button>
            <button class="header-calc-btn op" onclick="headerCalcInput('%')">%</button>
            <button class="header-calc-btn op" onclick="headerCalcInput('/')">÷</button>
            <button class="header-calc-btn" onclick="headerCalcInput('7')">7</button>
            <button class="header-calc-btn" onclick="headerCalcInput('8')">8</button>
            <button class="header-calc-btn" onclick="headerCalcInput('9')">9</button>
            <button class="header-calc-btn op" onclick="headerCalcInput('*')">×</button>
            <button class="header-calc-btn" onclick="headerCalcInput('4')">4</button>
            <button class="header-calc-btn" onclick="headerCalcInput('5')">5</button>
            <button class="header-calc-btn" onclick="headerCalcInput('6')">6</button>
            <button class="header-calc-btn op" onclick="headerCalcInput('-')">−</button>
            <button class="header-calc-btn" onclick="headerCalcInput('1')">1</button>
            <button class="header-calc-btn" onclick="headerCalcInput('2')">2</button>
            <button class="header-calc-btn" onclick="headerCalcInput('3')">3</button>
            <button class="header-calc-btn op" onclick="headerCalcInput('+')">+</button>
            <button class="header-calc-btn zero" onclick="headerCalcInput('0')">0</button>
            <button class="header-calc-btn" onclick="headerCalcInput('.')">.</button>
            <button class="header-calc-btn equals" onclick="headerCalcEquals()">=</button>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(calcModal);
  }
  
  function closeHeaderCalculator() {
    const modal = document.getElementById('header-calculator-modal');
    if (modal) modal.style.display = 'none';
  }
  
  function headerCalcInput(val) {
    if (headerCalcExpression === '0' && val !== '.') headerCalcExpression = val;
    else headerCalcExpression += val;
    document.getElementById('header-calc-display').value = headerCalcExpression || '0';
  }
  
  function headerCalcClear() {
    headerCalcExpression = '';
    document.getElementById('header-calc-display').value = '0';
  }
  
  function headerCalcBackspace() {
    headerCalcExpression = headerCalcExpression.slice(0, -1);
    document.getElementById('header-calc-display').value = headerCalcExpression || '0';
  }
  
  function headerCalcEquals() {
    try {
      let result = eval(headerCalcExpression);
      result = Math.round(result * 100) / 100;
      document.getElementById('header-calc-display').value = result;
      headerCalcExpression = result.toString();
    } catch (e) {
      document.getElementById('header-calc-display').value = 'Error';
      headerCalcExpression = '';
    }
  }
  
  // Close modals on Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeHeaderCalculator();
      // Also close the shortcuts panel from keyboard-shortcuts.js
      const shortcutPanel = document.getElementById('shortcut-help-panel');
      const shortcutBackdrop = document.getElementById('shortcut-help-backdrop');
      if (shortcutPanel) shortcutPanel.remove();
      if (shortcutBackdrop) shortcutBackdrop.remove();
    }
  });
</script>