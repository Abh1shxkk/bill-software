<nav id="appHeader" class="navbar navbar-expand-lg navbar-light app-header"
  style="background-color: white; border-bottom: 1px solid #dee2e6;">
  <div class="container-fluid">
    

    <div class="collapse navbar-collapse" id="topbarNav">
      <ul class="navbar-nav me-auto ms-3">
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
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.issued-index') }}">Issued - View All</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.received-transaction') }}">Received - Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.received-modification') }}">Received - Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.unused-dump-transaction') }}">Unused Dump - Transaction</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.unused-dump-modification') }}">Unused Dump - Modification</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.breakage-supplier.unused-dump-index') }}">Unused Dump - View All</a></li>
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
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sales Reports</a>
              <ul class="dropdown-menu">
                <!-- Sales Book Submenu - Level 3 -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sales Book</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sales-book') }}">Sale Book</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sales-book-gstr') }}">Sale Book GSTR</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sales-book-extra-charges') }}">Sale Book Extra Charges</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sales-book-tcs') }}">Sale Book With TCS</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.tcs-eligibility') }}">TCS Eligible Customers/Suppliers</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.tds-input') }}">TDS INPUT</a></li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sales-book-party-wise') }}">Sale Book Party Wise</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.day-sales-summary-item-wise') }}">Day Sales Summary - Item Wise</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sales-summary') }}">Sales Summary</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sales-bills-printing') }}">Sales Bills Printing</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sale-sheet') }}">Sale Sheet</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.dispatch-sheet') }}">Dispatch Sheet</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sale-return-book-item-wise') }}">Sale / Return Book Item Wise</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.local-central-sale-register') }}">Local / Central Sale Register</a></li>
                <!-- Sale Challan Reports Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sale Challan Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sale-challan-book') }}">Sale Challan Book</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.pending-challans') }}">Pending Challans</a></li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sales-stock-summary') }}">Sales Stock Summary</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-visit-status') }}">Customer Visit Status</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.shortage-report') }}">Shortage Report</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.sales.sale-return-list') }}">Sale Return List</a></li>
                <!-- Miscellaneous Sales Analysis -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Miscellaneous Sales Analysis</a>
                  <ul class="dropdown-menu">
                    <!-- Sales Man Wise Sales Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sales Man Wise Sales</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.all-salesman') }}">All Sales Man</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.bill-wise') }}">Bill Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.customer-wise') }}">Customer Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.company-wise') }}">Company Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.area-wise') }}">Area Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.route-wise') }}">Route Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.state-wise') }}">State Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.item-invoice-wise') }}">Item - Invoice Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.invoice-item-wise') }}">Invoice - Item Wise</a></li>
                        <!-- Month Wise Submenu -->
                        <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Month Wise</a>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.month-wise-summary') }}">Month Wise Summary</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.salesman-wise') }}">Salesman Wise</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.salesman-item-wise') }}">Salesman / Item Wise</a></li>
                          </ul>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.sale-book') }}">Sale Book</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-wise-sales.monthly-target') }}">Monthly Target</a></li>
                      </ul>
                    </li>
                    <!-- Area Wise Sale Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Area Wise Sale</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.all-area') }}">All Area</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.bill-wise') }}">Bill Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.customer-wise') }}">Customer Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.company-wise') }}">Company Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.salesman-wise') }}">Sales Man Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.route-wise') }}">Route Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.state-wise') }}">State Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.item-invoice-wise') }}">Item - Invoice Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.invoice-item-wise') }}">Invoice - Item Wise</a></li>
                        <!-- Month Wise Submenu -->
                        <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Month Wise</a>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.month-wise.area-wise') }}">Area Wise</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.month-wise.area-item-wise') }}">Area Item Wise</a></li>
                          </ul>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.area-wise-sales.sale-book') }}">Sale Book</a></li>
                      </ul>
                    </li>
                    <!-- Route Wise Sale Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Route Wise Sale</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.all-route') }}">All Route</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.bill-wise') }}">Bill Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.customer-wise') }}">Customer Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.company-wise') }}">Company Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.salesman-wise') }}">Sales Man Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.area-wise') }}">Area Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.state-wise') }}">State Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.item-invoice-wise') }}">Item - Invoice Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.invoice-item-wise') }}">Invoice - Item Wise</a></li>
                        <!-- Month Wise Submenu -->
                        <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Month Wise</a>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.month-wise.route-wise') }}">Route Wise</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.month-wise.route-item-wise') }}">Route / Item Wise</a></li>
                          </ul>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.route-wise-sale.sale-book') }}">Sale Book</a></li>
                      </ul>
                    </li>
                    <!-- State Wise Sale Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">State Wise Sale</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.all-state') }}">All State</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.bill-wise') }}">Bill Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.customer-wise') }}">Customer Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.company-wise') }}">Company Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.salesman-wise') }}">Sales Man Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.area-wise') }}">Area Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.route-wise') }}">Route Wise</a></li>
                        <li><a class="dropdown-item disabled" href="#">Item - Invoice Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.invoice-item-wise') }}">Invoice - Item Wise</a></li>
                        <!-- Month Wise Submenu -->
                        <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Month Wise</a>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.month-wise.state-wise') }}">State Wise</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.state-wise-sale.month-wise.state-item-wise') }}">State / Item Wise</a></li>
                          </ul>
                        </li>
                      </ul>
                    </li>
                    <!-- Customer Wise Sale Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Customer Wise Sale</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.all-customer') }}">All Customer</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.bill-wise') }}">Bill Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.company-wise') }}">Company Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.item-invoice-wise') }}">Item - Invoice Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.invoice-item-wise') }}">Invoice - Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.quantity-wise-summary') }}">Quantity wise Summary</a></li>
                        <!-- Month Wise Submenu -->
                        <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Month Wise</a>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.month-wise.customer-wise') }}">Customer Wise</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.month-wise.customer-item-wise') }}">Customer / Item Wise</a></li>
                          </ul>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.party-billwise-volume-discount') }}">Party BillWise Volume Discount</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.customer-wise-sale.sale-with-area') }}">Sale With Area</a></li>
                      </ul>
                    </li>
                    <!-- Company Wise Sales Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Company Wise Sales</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.all-company') }}">All Company</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.bill-wise') }}">Bill Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.salesman-wise') }}">Sales Man Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.area-wise') }}">Area Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.route-wise') }}">Route Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.customer-wise') }}">Customer Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.customer-item-invoice-wise') }}">Customer - Item Invoice Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.customer-item-wise') }}">Customer - Item Wise</a></li>
                        <!-- Month Wise Submenu -->
                        <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Month Wise</a>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.month-wise.company-item-wise') }}">Company / Item Wise</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.sales.company-wise-sales.month-wise.company-customer-wise') }}">Company / Customer Wise</a></li>
                          </ul>
                        </li>
                      </ul>
                    </li>
                    <!-- Item Wise Sales Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Item Wise Sales</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.all-item-sale') }}">All Item Sale</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.all-item-summary') }}">All Item Summary</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.bill-wise') }}">Item Wise Sale</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.salesman-wise') }}">Sales Man Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.area-wise') }}">Area Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.area-wise-matrix') }}">Area Wise (Matrix)</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.route-wise') }}">Route Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.state-wise') }}">State Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.customer-wise') }}">Customer Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.item-wise-sales.below-cost-item-sale') }}">Sale Below Cost</a></li>
                      </ul>
                    </li>
                    <!-- Discount Wise Sales Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Discount Wise Sales</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.discount-wise-sales.all-discount') }}">All Discount</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.discount-wise-sales.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.discount-wise-sales.item-wise-invoice-wise') }}">Item Wise - Invoice Wise</a></li>
                      </ul>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <!-- Sales Man and other Level Sale -->
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.salesman-level-sale') }}">Sales Man and other Level Sale</a></li>
                    <!-- Scheme Issued Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Scheme Issued</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.scheme-issued') }}?report_type=free_scheme">Free Scheme Issued</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.scheme-issued') }}?report_type=half_scheme">Half Scheme Issued</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.scheme-issued') }}?report_type=item_wise_less">Item Wise Less</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.scheme-issued') }}?report_type=free_issues_without_qty">Free Issues WithOut Qty.</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales.scheme-issued') }}?report_type=invalid_free_scheme">Invalid Free Scheme Issued</a></li>
                      </ul>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.mrp-wise-sales') }}">MRP Wise Sales</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.display-amount-report') }}">Display Amount Report</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.cancelled-invoices') }}">List of Cancelled Invoices</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.missing-invoices') }}">List of Missing Invoices</a></li>
                  </ul>
                </li>
                <!-- Other Reports -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Other Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.cash-coll-trnf-sale') }}">Cash Collection Transfer</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sale-bill-wise-discount') }}">Discount On Sale - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sales-book-with-return') }}">Sale Book With Sale Return</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.rate-difference') }}">Rate Change Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sales-matrix') }}">Sales Matrix</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.minus-qty-sale') }}">Minus Qty in Sale Invoice</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sales-details') }}">Sales Details</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.invoice-documents') }}">Invoice Documents</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sale-remarks') }}">Sale Remarks Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.item-wise-discount') }}">Item Wise Discount</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.item-wise-scheme') }}">Item Wise Scheme</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.tax-percentage-wise-sale') }}">Tax Percentage Wise Sale</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.transaction-book-address') }}">Transaction Book with Address</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sale-stock-detail') }}">Sale/Stock Detail</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.customer-stock-details') }}">Customer Stock Details</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.gst-sale-book') }}">GST Sale Book</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.customer-consistency') }}">Customer Consistency Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sale-return-adjustment') }}">Sale Return Adjustment</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.pending-orders') }}">Pending Orders</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.st38-outword') }}">ST-38 OutWord</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.frige-item') }}">Frige Item Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.volume-discount') }}">Volume Discount</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.party-volume-discount') }}">Party Volume Discount</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.schedule-h1-drugs') }}">Schedule H1 Drugs</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sale-book-sc') }}">Sale Book SC</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales.other.sale-book-summarised') }}">Sale Book Summarised</a></li>
                  </ul>
                </li>
              </ul>
            </li>

            <!-- Purchase Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Reports</a>
              <ul class="dropdown-menu">
                <!-- Purchase Book Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Book</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.purchase-book') }}">Purchase Book</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.purchase-book-gstr') }}">Purchase Book GSTR</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.purchase-book-tcs') }}">Purchase Book With TCS</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.tds-output') }}">TDS OUTPUT</a></li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.purchase-book-sale-value') }}">Purchase Book With Sale Value</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.party-wise-purchase') }}">Party Wise Purchase</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.monthly-purchase-summary') }}">Monthly Purchase Sales Summary</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.debit-credit-note') }}">Debit / Credit Note Report</a></li>
                <!-- GST SET OFF Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">GST-SET OFF</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.gst-set-off') }}">GST-SET OFF</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.gst-set-off-gstr') }}">GST SET OFF GSTR</a></li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.day-purchase-summary') }}">Day Purchase Summary - Item Wise</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.purchase-return-item-wise') }}">Purchase / Return Book Item Wise</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.local-central-register') }}">Local / Central Purchase Register</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.purchase-voucher-detail') }}">Purchase Voucher Detail</a></li>
                <!-- Purchase Challan Reports Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Purchase Challan Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.challan.purchase-challan-book') }}">Purchase Challan Book</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.challan.pending-challans') }}">Pending Challans</a></li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.short-expiry-received') }}">Short Expiry Received</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.purchase-return-list') }}">Purchase Return List</a></li>
                
                <li><hr class="dropdown-divider"></li>
                
                
                
                <!-- Miscellaneous Purchase Analysis Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Miscellaneous Purchase Analysis</a>
                  <ul class="dropdown-menu">
                    <!-- Supplier Wise Purchase Folder -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Supplier Wise Purchase</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.supplier.all-supplier') }}">All Supplier</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.supplier.bill-wise') }}">Bill Wise</a></li>
                         <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.supplier.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.supplier.item-invoice-wise') }}">Item - Invoice Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.supplier.invoice-item-wise') }}">Invoice - Item Wise</a></li>
                      </ul>
                    </li>
                    
                    <!-- Company Wise Purchase Folder -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Company Wise Purchase</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.company.all-company') }}">All Company</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.company.item-wise') }}">Item Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.company.party-wise') }}">Party Wise</a></li>
                      </ul>
                    </li>

                    <!-- Item Wise Purchase Folder -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Item Wise Purchase</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.item.bill-wise') }}">Bill Wise</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.item.all-item-purchase') }}">All Item Purchase</a></li>
                      </ul>
                    </li>

                    <!-- Purchase with Item Details (Single File) -->
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.purchase-with-item-details') }}">Purchase with Item Details</a></li>

                    <!-- Schemed Received Folder -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Schemed Received</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.schemed.free-schemed') }}">Free Schemed Received</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.schemed.half-schemed') }}">Half Schemed Received</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.misc.schemed.free-without-qty') }}">Free Received Without Qty.</a></li>
                      </ul>
                    </li>
                  </ul>
                </li>
                
                <!-- Other Reports Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Other Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.other.supplier-visit-report') }}">Supplier Visit Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.other.supplier-wise-companies') }}">Supplier Wise Companies</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.other.purchase-book-item-details') }}">Purchase Book - Item Details</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.other.central-purchase-local-value') }}">Central Purchase with Local Value</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.other.party-wise-all-purchase-details') }}">Party Wise All Purchase Details</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.purchase.other.register-schedule-h1-drugs') }}">Register of Schedule H1 Drugs</a></li>
                  </ul>
                </li>
              </ul>
            </li>

            <!-- Inventory Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Inventory Reports</a>
              <ul class="dropdown-menu">
                <!-- Item Reports Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Item Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.min-max-level') }}">Minimum / Maximum Level Items</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.display-item-list') }}">Display Item List</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.tax-mrp-rate-range') }}">Item List - Tax / MRP / Rate Range</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.margin-wise') }}">Margin-Wise Items</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.margin-wise-running') }}">Margin-Wise Items (Running)</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.multi-rate') }}">Multi Rate Items</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.new-items-customers-suppliers') }}">New Items / Customers / Suppliers</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.rate-list') }}">Rate List</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.vat-wise') }}">Vat-Wise Items</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.item-list-with-salts') }}">Item List with Salts</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.list-of-schemes') }}">List of Schemes</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.item-search-by-batch') }}">Item Search By Batch</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.item.item-ledger-printing') }}">Item Ledger Printing</a></li>
                  </ul>
                </li>
                <!-- Stock Reports Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.current-stock-status') }}">Current Stock Status</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.category-wise-stock-status') }}">Category Wise Stock Status</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.stock-and-sales-analysis') }}">Stock and Sales Analysis</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.valuation-of-closing-stock') }}">Valuation of Closing Stock</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.category-wise-valuation-closing-stock') }}">Category Wise Valuation of Closing Stock</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.company-wise-stock-value') }}">Company Wise Stock Value</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.stock-register-it-return') }}">Stock Register for IT Return</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.list-of-old-stock') }}">List of Old Stock</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.sales-and-stock-variation') }}">Sales and Stock Variation</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.current-stock-status-supplier-wise') }}">Current Stock Status Supplier Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.annual-stock-ledger-summary') }}">Annual Stock Ledger Summary</a></li>
                    <!-- Others Submenu -->
                    <li class="dropdown-submenu">
                      <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Others</a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.other.stock-register') }}">Stock Register</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.other.stock-and-sales-with-value') }}">Stock and Sales with Value</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.other.batch-wise-stock') }}">Batch Wise Stock</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.other.location-wise-stock') }}">Location Wise Stock</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.stock.other.category-wise-stock-status') }}">Category Wise Stock Status</a></li>
                      </ul>
                    </li>
                  </ul>
                </li>
                <!-- FIFO Alteration Report -->
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.fifo-alteration') }}">FiFo Alteration Report</a></li>
                
                <li><hr class="dropdown-divider"></li>
                
                <!-- Direct Reports -->
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.reorder-sale-basis') }}">Reorder on Sale Basis</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.reorder-min-stock-basis') }}">Reorder on Minimum Stock Basis</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.reorder-min-stock-sale-basis') }}">Reorder on Minimum Stock & Sale Basis</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.order-form-3-column') }}">Order Form 3 Column</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.order-form-6-column') }}">Order Form 6 Column</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.list-hold-batches') }}">List of Hold Batches</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.remove-batch-hold') }}">Remove Batch Hold Status</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.list-hold-batches-sr-pb') }}">List of Hold Batches (SR,PB)</a></li>
                <!-- Others Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Others</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.others.fifo-ledger') }}">FiFo Ledger</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.inventory.others.stock-os-report-bank') }}">Stock & O/S Report for Bank</a></li>
                  </ul>
                </li>
              </ul>
            </li>

            <!-- Management Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Management Reports</a>
              <ul class="dropdown-menu">
                <!-- Due Reports Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Due Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.due-list') }}">Due List</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.bill-tagging') }}">Bill Tagging</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.due-list-with-pdc') }}">Due List With PDC</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.due-list-company-wise') }}">Due List Company Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.due-list-account-ledger') }}">Due List Account Ledger</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.ageing-analysis') }}">Ageing Analysis</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.ageing-analysis-account-ledger') }}">Ageing Analysis Account Ledger</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.list-of-pending-tags') }}">List of Pending Tags</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.bill-history') }}">Bill History</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.due-list-summary') }}">Due List Summary</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.due-list-reminder-letter') }}">Due List Reminder Letter</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.balance-confirmation-letter') }}">Balance Confirmation Letter</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.balance-confirmation-letter-account-ledger') }}">Balance Confirmation Letter Account Ledger</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.due-list-monthly') }}">Due List Monthly</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.due-reports.due-list-adjustment-analysis') }}">Due List Adjustment Analysis</a></li>
                  </ul>
                </li>
                <!-- Gross Profit Reports Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Gross Profit Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.bill-wise') }}">Gross Profit - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.item-bill-wise') }}">Gross Profit - Item - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.selective-all-items') }}">Gross Profit - Selective/All Items</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.company-bill-wise') }}">Gross Profit - Company - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.selective-all-companies') }}">Gross Profit - Selective/All Companies</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.customer-bill-wise') }}">Gross Profit - Customer - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.selective-all-customers') }}">Gross Profit - Selective/All Customers</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.selective-all-suppliers') }}">Gross Profit - Selective/All Suppliers</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.salt-wise') }}">Gross Profit - Salt wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.claim-items-sold-on-loss') }}">Claim Items - Sold on Loss</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.gross-profit.selective-all-salesman') }}">Gross Profit - Selective/All Salesman</a></li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.management.list-of-expired-items') }}">List of Expired Items</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.management.sale-purchase-schemes') }}">Sale/Purchase Schemes</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.management.suppliers-pending-order') }}">Supplier's Pending Order</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.management.customers-pending-order') }}">Customer's Pending Order</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.management.non-moving-items') }}">Non Moving Items</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.management.slow-moving-items') }}">Slow Moving Items</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.management.performance-report') }}">Performance Report</a></li>
                <!-- Others Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Others</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.day-check-list') }}">Day Check List</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.prescription-reminder-list') }}">Prescription Reminder List</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.ledger-due-list-mismatch-report') }}">Ledger Due List Mismatch Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.salepurchase1-due-list-mismatch-report') }}">Salepurchase1 Due List Mismatch Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.attendence-sheet') }}">Attendence Sheet</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.list-of-modifications') }}">List of Modifications</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.list-of-master-modifications') }}">List of Master Modifications</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.cl-sl-date-wise-ledger-summary') }}">CL/SL - Date Wise Ledger Summary</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.user-work-summary') }}">User Work Summary</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.management.others.hsn-wise-sale-purchase-report') }}">HSN Wise Sale Purchase Report</a></li>
                  </ul>
                </li>
              </ul>
            </li>

            <!-- Misc. Transaction Reports -->
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Misc. Transaction Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.misc-transaction-book') }}">Misc Transaction Book</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-adjustment') }}">Stock Adjustment</a></li>
                <!-- Stock Transfer Outgoing Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Outgoing</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-transfer-outgoing.bill-wise') }}">Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-transfer-outgoing.party-bill-wise') }}">Party - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-transfer-outgoing.item-bill-wise') }}">Item - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-transfer-outgoing.item-party-bill-wise') }}">Item - Party - Bill Wise</a></li>
                  </ul>
                </li>
                <!-- Stock Transfer Incoming Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Stock Transfer Incoming</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-transfer-incoming.bill-wise') }}">Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-transfer-incoming.party-bill-wise') }}">Party - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-transfer-incoming.item-bill-wise') }}">Item - Bill Wise</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.stock-transfer-incoming.item-party-bill-wise') }}">Item - Party - Bill Wise</a></li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.sale-return-replacement') }}">Sale Return Replacement</a></li>
                <!-- Sample Reports Submenu -->
                <li class="dropdown-submenu">
                  <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Sample Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.sample-reports.list-of-sample-issued') }}">List of Sample Issued</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.sample-reports.list-of-sample-received') }}">List of Sample Received</a></li>
                  </ul>
                </li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.misc-transaction.bill-printing') }}">Misc. Tran. Bill Printing</a></li>
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
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.receipt-from-customer') }}">Receipt from Customer</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.payment-to-supplier') }}">Payment to Supplier</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.post-dated-cheques') }}">List of Post Dated Cheques</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.returned-cheques') }}">List of Returned Cheques</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.cash-cheque-collection') }}">Cash/Cheque Collection</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.cash-collection-summary') }}">Cash Collection Summary</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.pay-in-slip') }}">Pay - In - Slip Report</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.currency-detail') }}">Currency Detail</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.receipt-customer-month-wise') }}">Receipt from Customer - Month Wise</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.reports.receipt-payment.payment-history') }}">Payment History</a></li>
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
          <ul class="dropdown-menu admin-menu">
            <li>
              <a class="dropdown-item" href="{{ route('admin.administration.hotkeys.index') }}">
                <i class="bi bi-keyboard me-2"></i>Hotkey Management
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('admin.page-settings.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i>Page Content Settings
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
        <li class="nav-item dropdown d-none d-sm-inline">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown"
          aria-expanded="false">
          <img
            src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : 'https://i.pravatar.cc/32' }}"
            class="rounded-circle me-2" width="32" height="32" alt="avatar">
          <span class="d-none d-sm-inline">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end profile-dropdown">
          <li class="px-3 py-2 text-muted" style="font-size: 0.9rem;">
            {{ auth()->user()->email }}
          </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="{{ route('profile.settings') }}"><i
                  class="bi bi-gear me-2"></i>Settings</a></li>
            <li>
              <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                @csrf
                <button class="btn btn-sm btn-outline-danger w-100 py-1" style="font-size: 0.85rem;"><i
                    class="bi bi-box-arrow-right me-1"></i>Logout</button>
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

  /* Profile Dropdown Fix */
  .profile-dropdown {
    position: absolute !important;
    right: 0 !important;
    left: auto !important;
    top: 100% !important;
    min-width: 220px;
    max-width: 280px;
    transform: none !important;
    margin-top: 0.5rem !important;
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
    padding-left: 28px; /* Space for icon */
    margin: 0; /* Full width items for sharp look */
    width: 100%;
    border-radius: 0px; /* Sharp corners */
    transition: all 0.1s ease;
    white-space: nowrap;
    font-size: 12px;
    position: relative;
  }
  
  /* File icon for regular menu items (without submenus) - excluding admin-menu and items with icons */
  .dropdown-menu:not(.admin-menu) > li:not(.dropdown-submenu) > .dropdown-item:not(.dropdown-toggle)::before {
    content: "";
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    /* File/document icon SVG */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23666666'%3E%3Cpath d='M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.414A2 2 0 0 0 13.414 3L11 .586A2 2 0 0 0 9.586 0H4zm5 1.5v2A1.5 1.5 0 0 0 10.5 5h2l-4-4v.5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
  }
  
  /* Change file icon color on hover - excluding admin-menu */
  .dropdown-menu:not(.admin-menu) > li:not(.dropdown-submenu) > .dropdown-item:not(.dropdown-toggle):hover::before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230d6efd'%3E%3Cpath d='M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.414A2 2 0 0 0 13.414 3L11 .586A2 2 0 0 0 9.586 0H4zm5 1.5v2A1.5 1.5 0 0 0 10.5 5h2l-4-4v.5z'/%3E%3C/svg%3E");
  }
  
  /* Folder icon for submenu toggles */
  .dropdown-submenu > .dropdown-toggle::before {
    content: "";
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    /* Folder icon SVG */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffc107'%3E%3Cpath d='M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5v-9z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
  }
  
  /* Keep folder icon on hover (same color) */
  .dropdown-submenu > .dropdown-toggle:hover::before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffc107'%3E%3Cpath d='M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5v-9z'/%3E%3C/svg%3E");
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
  
  // Calculator keyboard support - use capture phase to intercept before other handlers
  document.addEventListener('keydown', function(e) {
    // Check if calculator is visible
    const calcModal = document.getElementById('header-calculator-modal');
    
    // Calculator is visible if it exists AND display is NOT 'none' (including empty string which means visible)
    const isCalcVisible = calcModal && calcModal.style.display !== 'none';
    
    if (isCalcVisible) {
      const key = e.key;
      
      // Numbers 0-9
      if (/^[0-9]$/.test(key)) {
        e.preventDefault();
        e.stopPropagation();
        headerCalcInput(key);
        return;
      }
      
      // Operators
      if (key === '+' || key === '-' || key === '*' || key === '/' || key === '%') {
        e.preventDefault();
        e.stopPropagation();
        headerCalcInput(key);
        return;
      }
      
      // Decimal point
      if (key === '.' || key === ',') {
        e.preventDefault();
        e.stopPropagation();
        headerCalcInput('.');
        return;
      }
      
      // Enter or = for equals
      if (key === 'Enter' || key === '=') {
        e.preventDefault();
        e.stopPropagation();
        headerCalcEquals();
        return;
      }
      
      // Backspace for delete
      if (key === 'Backspace') {
        e.preventDefault();
        e.stopPropagation();
        headerCalcBackspace();
        return;
      }
      
      // C or Delete for clear
      if (key === 'c' || key === 'C' || key === 'Delete') {
        e.preventDefault();
        e.stopPropagation();
        headerCalcClear();
        return;
      }
      
      // Escape to close calculator
      if (key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        closeHeaderCalculator();
        return;
      }
    }
    
    // If calculator not visible, handle Escape for other modals
    if (e.key === 'Escape') {
      closeHeaderCalculator();
      // Also close the shortcuts panel from keyboard-shortcuts.js
      const shortcutPanel = document.getElementById('shortcut-help-panel');
      const shortcutBackdrop = document.getElementById('shortcut-help-backdrop');
      if (shortcutPanel) shortcutPanel.remove();
      if (shortcutBackdrop) shortcutBackdrop.remove();
    }
  }, true); // Use capture phase to intercept before other handlers
</script>