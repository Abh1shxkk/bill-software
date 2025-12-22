<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - {{ $settings['company_name'] ?? 'InvoiceLab' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-body: #f8fafc;
        }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
        }
        .page-header {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }
        .page-header p {
            color: var(--text-muted);
            margin-bottom: 0;
        }
        .page-header .breadcrumb {
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        .page-header .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .page-header .breadcrumb-item.active {
            color: var(--text-muted);
        }
        .sidebar {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            position: sticky;
            top: 20px;
        }
        .sidebar .search-box input {
            background: #f1f5f9;
            border: 1px solid transparent;
        }
        .sidebar .search-box input:focus {
            background: white;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .sidebar h5 {
            color: var(--text-dark);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        .sidebar .nav-link {
            color: var(--text-muted);
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            margin-bottom: 2px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }
        .sidebar .nav-link:hover {
            background: var(--primary-light);
            color: var(--primary-color);
        }
        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: white;
        }
        .doc-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .doc-card h2 {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        .doc-card h2 i {
            color: var(--primary-color);
        }
        .doc-card h3 {
            color: var(--text-dark);
            font-size: 1.15rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .doc-card p, .doc-card li {
            color: #475569;
            line-height: 1.7;
            font-size: 0.95rem;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .feature-item {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.25rem;
            text-align: center;
            transition: border-color 0.2s;
        }
        .feature-item:hover {
            border-color: var(--primary-color);
        }
        .feature-item i {
            font-size: 1.75rem;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
            display: block;
        }
        .feature-item h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }
        .feature-item p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0;
        }
        .shortcut-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }
        .shortcut-table th {
            background: #f1f5f9;
            color: var(--text-dark);
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .shortcut-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
        }
        .shortcut-table tr:last-child td {
            border-bottom: none;
        }
        .shortcut-table td kbd {
            background: #f1f5f9;
            color: var(--text-dark);
            border: 1px solid #cbd5e1;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }
        .step-list {
            counter-reset: step;
            list-style: none;
            padding-left: 0;
        }
        .step-list li {
            position: relative;
            padding-left: 2.5rem;
            margin-bottom: 1rem;
        }
        .step-list li::before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            left: 0;
            top: 0;
            width: 1.75rem;
            height: 1.75rem;
            background: var(--primary-light);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .tip-box {
            background: #ecfdf5;
            border: 1px solid #10b981;
            padding: 1rem 1.25rem;
            border-radius: 6px;
            margin: 1.5rem 0;
            color: #065f46;
        }
        .tip-box strong {
            color: #047857;
        }
        .warning-box {
            background: #fffbeb;
            border: 1px solid #f59e0b;
            padding: 1rem 1.25rem;
            border-radius: 6px;
            margin: 1.5rem 0;
            color: #92400e;
        }
        .warning-box strong {
            color: #b45309;
        }
        .btn-primary-solid {
            background: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-primary-solid:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
        }
        .btn-outline-secondary {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 0.6rem 1.25rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-outline-secondary:hover {
            background: #f1f5f9;
            color: var(--text-dark);
            border-color: #cbd5e1;
        }
        .footer {
            background: white;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            padding: 2rem 0;
            margin-top: auto;
        }
        .footer a {
            color: var(--text-muted);
            text-decoration: none;
        }
        .footer a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .search-box input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .search-box i {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Documentation</li>
                </ol>
            </nav>
            <h1><i class="bi bi-book me-2"></i>Documentation</h1>
            <p>Complete guide to using {{ $settings['company_name'] ?? 'InvoiceLab' }} billing software</p>
        </div>
    </div>

    <!-- Content -->
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="sidebar">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search docs..." id="docSearch">
                    </div>
                    <h5><i class="bi bi-list me-1"></i>Contents</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#getting-started"><i class="bi bi-rocket"></i> Getting Started</a>
                        <a class="nav-link" href="#dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        <a class="nav-link" href="#sales"><i class="bi bi-cart3"></i> Sales Module</a>
                        <a class="nav-link" href="#purchase"><i class="bi bi-bag"></i> Purchase Module</a>
                        <a class="nav-link" href="#inventory"><i class="bi bi-box-seam"></i> Inventory</a>
                        <a class="nav-link" href="#customers"><i class="bi bi-people"></i> Customers</a>
                        <a class="nav-link" href="#reports"><i class="bi bi-graph-up"></i> Reports</a>
                        <a class="nav-link" href="#shortcuts"><i class="bi bi-keyboard"></i> Keyboard Shortcuts</a>
                        <a class="nav-link" href="#settings"><i class="bi bi-gear"></i> Settings</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Getting Started -->
                <div class="doc-card" id="getting-started">
                    <h2><i class="bi bi-rocket me-2"></i>Getting Started</h2>
                    <p>Welcome to {{ $settings['company_name'] ?? 'InvoiceLab' }} - a comprehensive billing and inventory management solution designed for pharmacies and retail businesses.</p>
                    
                    <div class="feature-grid">
                        <div class="feature-item">
                            <i class="bi bi-receipt"></i>
                            <h4>GST Invoicing</h4>
                            <p>GST compliant billing</p>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-box-seam"></i>
                            <h4>Inventory</h4>
                            <p>Stock management</p>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-people"></i>
                            <h4>CRM</h4>
                            <p>Customer management</p>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-graph-up"></i>
                            <h4>Reports</h4>
                            <p>Business analytics</p>
                        </div>
                    </div>

                    <h3>Quick Setup</h3>
                    <ol class="step-list">
                        <li>Login with your admin credentials</li>
                        <li>Complete your business profile in Settings → Profile</li>
                        <li>Add your products in Inventory → Products</li>
                        <li>Add customers and suppliers</li>
                        <li>Start creating invoices!</li>
                    </ol>

                    <div class="tip-box">
                        <strong><i class="bi bi-lightbulb me-1"></i>Pro Tip:</strong> Use keyboard shortcuts for faster navigation. Press <kbd>?</kbd> anywhere to see available shortcuts.
                    </div>
                </div>

                <!-- Dashboard -->
                <div class="doc-card" id="dashboard">
                    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
                    <p>The dashboard provides an overview of your business at a glance:</p>
                    <ul>
                        <li><strong>Today's Sales:</strong> Total sales amount for the current day</li>
                        <li><strong>Monthly Revenue:</strong> Revenue generated this month</li>
                        <li><strong>Pending Receipts:</strong> Outstanding customer payments</li>
                        <li><strong>Low Stock Alerts:</strong> Products running low on inventory</li>
                        <li><strong>Expiry Alerts:</strong> Products nearing expiry date</li>
                        <li><strong>Recent Transactions:</strong> Latest sales and purchase activity</li>
                    </ul>
                </div>

                <!-- Sales Module -->
                <div class="doc-card" id="sales">
                    <h2><i class="bi bi-cart3 me-2"></i>Sales Module</h2>
                    
                    <h3>Creating an Invoice</h3>
                    <ol class="step-list">
                        <li>Go to Sales → Create Invoice or press <kbd>Ctrl+I</kbd></li>
                        <li>Select or add a customer</li>
                        <li>Add products by searching or scanning barcode</li>
                        <li>Adjust quantities and discounts if needed</li>
                        <li>Review the invoice and click Save</li>
                        <li>Print or share the invoice</li>
                    </ol>

                    <h3>Sales Features</h3>
                    <ul>
                        <li><strong>Quick Billing:</strong> Fast checkout for cash customers</li>
                        <li><strong>Credit Sales:</strong> Track customer dues</li>
                        <li><strong>Returns:</strong> Process sales returns and credit notes</li>
                        <li><strong>Discounts:</strong> Apply item-wise or bill-wise discounts</li>
                        <li><strong>Multiple Payment:</strong> Split payment across cash/card/UPI</li>
                    </ul>

                    <div class="warning-box">
                        <strong><i class="bi bi-exclamation-triangle me-1"></i>Important:</strong> Always verify customer details before creating credit invoices.
                    </div>
                </div>

                <!-- Purchase Module -->
                <div class="doc-card" id="purchase">
                    <h2><i class="bi bi-bag me-2"></i>Purchase Module</h2>
                    
                    <h3>Recording Purchases</h3>
                    <ol class="step-list">
                        <li>Navigate to Purchase → Create Purchase</li>
                        <li>Select the supplier</li>
                        <li>Enter invoice number and date</li>
                        <li>Add products with batch details</li>
                        <li>Enter MRP, purchase rate, and expiry dates</li>
                        <li>Save to update inventory automatically</li>
                    </ol>

                    <h3>Batch Management</h3>
                    <p>Each purchase creates a new batch with:</p>
                    <ul>
                        <li>Unique batch number</li>
                        <li>Manufacturing and expiry dates</li>
                        <li>Purchase rate and MRP</li>
                        <li>Quantity tracking</li>
                    </ul>
                </div>

                <!-- Inventory -->
                <div class="doc-card" id="inventory">
                    <h2><i class="bi bi-box-seam me-2"></i>Inventory Management</h2>
                    
                    <h3>Products</h3>
                    <ul>
                        <li><strong>Add Product:</strong> Enter product details including HSN code and GST rate</li>
                        <li><strong>Categories:</strong> Organize products into categories</li>
                        <li><strong>Units:</strong> Define measurement units (Pcs, Box, Strip, etc.)</li>
                        <li><strong>Pricing:</strong> Set MRP, purchase rate, and sale rate</li>
                    </ul>

                    <h3>Stock Management</h3>
                    <ul>
                        <li>View current stock levels</li>
                        <li>Track stock by batch</li>
                        <li>Stock adjustments for damage/loss</li>
                        <li>Stock transfer between locations</li>
                        <li>Low stock alerts</li>
                    </ul>
                </div>

                <!-- Customers -->
                <div class="doc-card" id="customers">
                    <h2><i class="bi bi-people me-2"></i>Customer Management</h2>
                    
                    <ul>
                        <li><strong>Customer Profiles:</strong> Store contact and billing information</li>
                        <li><strong>Credit Limits:</strong> Set maximum credit allowed</li>
                        <li><strong>Due Tracking:</strong> Monitor outstanding payments</li>
                        <li><strong>Payment Receipts:</strong> Record customer payments</li>
                        <li><strong>Customer Ledger:</strong> Complete transaction history</li>
                        <li><strong>Special Rates:</strong> Customer-specific pricing</li>
                    </ul>
                </div>

                <!-- Reports -->
                <div class="doc-card" id="reports">
                    <h2><i class="bi bi-graph-up me-2"></i>Reports</h2>
                    
                    <h3>Available Reports</h3>
                    <ul>
                        <li><strong>Sales Reports:</strong> Daily, monthly, yearly sales analysis</li>
                        <li><strong>Purchase Reports:</strong> Purchase summary and details</li>
                        <li><strong>GST Reports:</strong> GSTR-1, GSTR-3B, HSN summary</li>
                        <li><strong>Stock Reports:</strong> Current stock, movement, valuation</li>
                        <li><strong>Expiry Reports:</strong> Products expiring soon</li>
                        <li><strong>Customer Reports:</strong> Outstanding dues, ledger</li>
                        <li><strong>Profit & Loss:</strong> Business profitability analysis</li>
                    </ul>

                    <div class="tip-box">
                        <strong><i class="bi bi-lightbulb me-1"></i>Export:</strong> All reports can be exported to Excel or PDF format.
                    </div>
                </div>

                <!-- Keyboard Shortcuts -->
                <div class="doc-card" id="shortcuts">
                    <h2><i class="bi bi-keyboard me-2"></i>Keyboard Shortcuts</h2>
                    <p>Use these shortcuts to navigate faster:</p>
                    
                    <table class="shortcut-table">
                        <thead>
                            <tr>
                                <th>Shortcut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>I</kbd></td>
                                <td>Create New Invoice</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>P</kbd></td>
                                <td>Create New Purchase</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>N</kbd></td>
                                <td>Add New Product</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>S</kbd></td>
                                <td>Save Current Form</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>F</kbd></td>
                                <td>Global Search</td>
                            </tr>
                            <tr>
                                <td><kbd>F2</kbd></td>
                                <td>Edit Selected Row</td>
                            </tr>
                            <tr>
                                <td><kbd>Delete</kbd></td>
                                <td>Delete Selected Row</td>
                            </tr>
                            <tr>
                                <td><kbd>Esc</kbd></td>
                                <td>Close Modal/Cancel</td>
                            </tr>
                            <tr>
                                <td><kbd>?</kbd></td>
                                <td>Show Help/Shortcuts</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Settings -->
                <div class="doc-card" id="settings">
                    <h2><i class="bi bi-gear me-2"></i>Settings</h2>
                    
                    <h3>Profile Settings</h3>
                    <ul>
                        <li>Update business name and contact details</li>
                        <li>Add GST number and drug license details</li>
                        <li>Upload company logo</li>
                        <li>Set invoice footer text</li>
                    </ul>

                    <h3>System Settings</h3>
                    <ul>
                        <li>Invoice numbering format</li>
                        <li>Default tax rates</li>
                        <li>Low stock threshold</li>
                        <li>Backup settings</li>
                        <li>User management</li>
                    </ul>

                    <h3>User Roles</h3>
                    <ul>
                        <li><strong>Admin:</strong> Full system access</li>
                        <li><strong>Manager:</strong> Most features except settings</li>
                        <li><strong>Cashier:</strong> Sales and basic reports only</li>
                        <li><strong>Viewer:</strong> Read-only access</li>
                    </ul>
                </div>

                <div class="text-center mt-4 mb-5">
                    <a href="{{ route('pages.support') }}" class="btn-primary-solid me-2">
                        <i class="bi bi-headset"></i> Need Help?
                    </a>
                    <a href="{{ url()->previous() }}" class="btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">© {{ $settings['copyright_year'] ?? '2025' }} {{ $settings['company_name'] ?? 'InvoiceLab' }}. All rights reserved. | 
                <a href="{{ route('pages.privacy') }}">Privacy</a> · 
                <a href="{{ route('pages.terms') }}">Terms</a> · 
                <a href="{{ route('pages.support') }}">Support</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for sidebar links
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Update active state
                    document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Highlight active section on scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.doc-card[id]');
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                if (window.pageYOffset >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
