<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - {{ $settings['company_name'] ?? 'Medi BillSuite' }}</title>
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
        .content-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .content-card h2 {
            color: var(--text-dark);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        .content-card h2 i {
            color: var(--primary-color);
        }
        .content-card p, .content-card li {
            color: #475569;
            line-height: 1.7;
            font-size: 0.95rem;
        }
        .content-card ul, .content-card ol {
            padding-left: 1.5rem;
        }
        .content-card ul li, .content-card ol li {
            margin-bottom: 0.5rem;
        }
        .highlight-box {
            background: #fffbeb;
            border: 1px solid #f59e0b;
            padding: 1rem 1.25rem;
            border-radius: 6px;
            margin: 1.5rem 0;
            color: #92400e;
        }
        .highlight-box strong {
            color: #b45309;
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
        .last-updated {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Terms of Service</li>
                </ol>
            </nav>
            <h1><i class="bi bi-file-text me-2"></i>{{ $settings['terms_page_title'] ?? 'Terms of Service' }}</h1>
            <p>{{ $settings['terms_page_subtitle'] ?? 'Please read these terms carefully before using our services' }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <p class="last-updated mb-4"><i class="bi bi-calendar3 me-1"></i>Last Updated: {{ $settings['terms_last_updated'] ?? 'December 2025' }}</p>

                <div class="content-card">
                    <h2><i class="bi bi-hand-thumbs-up me-2"></i>{{ $settings['terms_acceptance_title'] ?? 'Acceptance of Terms' }}</h2>
                    <p>{{ $settings['terms_acceptance_text'] ?? 'By accessing and using Medi BillSuite billing and inventory management software, you accept and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.' }}</p>
                    <div class="highlight-box">
                        <strong>Important:</strong> {{ $settings['terms_acceptance_note'] ?? 'These terms constitute a legally binding agreement between you and Medi BillSuite.' }}
                    </div>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-laptop me-2"></i>{{ $settings['terms_use_title'] ?? 'Use of Service' }}</h2>
                    <p>{{ $settings['terms_use_intro'] ?? 'Medi BillSuite provides a comprehensive billing and inventory management solution. You agree to use the service only for:' }}</p>
                    <ul>
                        <li>{{ $settings['terms_use_item1'] ?? 'Legitimate business purposes related to billing, invoicing, and inventory management' }}</li>
                        <li>{{ $settings['terms_use_item2'] ?? 'Recording accurate business transactions and maintaining proper records' }}</li>
                        <li>{{ $settings['terms_use_item3'] ?? 'Generating GST-compliant invoices and reports' }}</li>
                        <li>{{ $settings['terms_use_item4'] ?? 'Managing customer and supplier relationships' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-person-badge me-2"></i>{{ $settings['terms_account_title'] ?? 'Account Responsibilities' }}</h2>
                    <p>{{ $settings['terms_account_intro'] ?? 'As an account holder, you are responsible for:' }}</p>
                    <ol>
                        <li>{!! $settings['terms_account_item1'] ?? '<strong>Account Security:</strong> Maintaining the confidentiality of your login credentials' !!}</li>
                        <li>{!! $settings['terms_account_item2'] ?? '<strong>Authorized Access:</strong> Ensuring only authorized personnel access your account' !!}</li>
                        <li>{!! $settings['terms_account_item3'] ?? '<strong>Data Accuracy:</strong> Providing accurate and up-to-date business information' !!}</li>
                        <li>{!! $settings['terms_account_item4'] ?? '<strong>Compliance:</strong> Ensuring your use complies with applicable laws and regulations' !!}</li>
                        <li>{!! $settings['terms_account_item5'] ?? '<strong>Activity Monitoring:</strong> Monitoring and being responsible for all activities under your account' !!}</li>
                    </ol>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-shield-exclamation me-2"></i>{{ $settings['terms_prohibited_title'] ?? 'Prohibited Activities' }}</h2>
                    <p>{{ $settings['terms_prohibited_intro'] ?? 'You agree not to:' }}</p>
                    <ul>
                        <li>{{ $settings['terms_prohibited_item1'] ?? 'Use the service for any illegal or unauthorized purpose' }}</li>
                        <li>{{ $settings['terms_prohibited_item2'] ?? 'Attempt to gain unauthorized access to any part of the service' }}</li>
                        <li>{{ $settings['terms_prohibited_item3'] ?? 'Interfere with or disrupt the service or servers' }}</li>
                        <li>{{ $settings['terms_prohibited_item4'] ?? 'Transmit viruses, malware, or any malicious code' }}</li>
                        <li>{{ $settings['terms_prohibited_item5'] ?? 'Reverse engineer or attempt to extract source code' }}</li>
                        <li>{{ $settings['terms_prohibited_item6'] ?? 'Use the service to generate fraudulent invoices or records' }}</li>
                        <li>{{ $settings['terms_prohibited_item7'] ?? 'Share your account credentials with unauthorized parties' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-database me-2"></i>{{ $settings['terms_data_title'] ?? 'Data Ownership' }}</h2>
                    <p>{{ $settings['terms_data_intro'] ?? 'You retain ownership of all business data you enter into Medi BillSuite, including:' }}</p>
                    <ul>
                        <li>{{ $settings['terms_data_item1'] ?? 'Customer and supplier information' }}</li>
                        <li>{{ $settings['terms_data_item2'] ?? 'Product and inventory data' }}</li>
                        <li>{{ $settings['terms_data_item3'] ?? 'Sales and purchase transactions' }}</li>
                        <li>{{ $settings['terms_data_item4'] ?? 'Financial records and reports' }}</li>
                    </ul>
                    <p>{{ $settings['terms_data_note'] ?? 'You grant us a limited license to process this data solely for the purpose of providing our services to you.' }}</p>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-credit-card me-2"></i>{{ $settings['terms_payment_title'] ?? 'Payment Terms' }}</h2>
                    <p>{{ $settings['terms_payment_intro'] ?? 'If applicable to your subscription:' }}</p>
                    <ul>
                        <li>{{ $settings['terms_payment_item1'] ?? 'Fees are billed in advance on a monthly or annual basis' }}</li>
                        <li>{{ $settings['terms_payment_item2'] ?? 'All fees are non-refundable unless otherwise specified' }}</li>
                        <li>{{ $settings['terms_payment_item3'] ?? 'We reserve the right to modify pricing with 30 days notice' }}</li>
                        <li>{{ $settings['terms_payment_item4'] ?? 'Failure to pay may result in service suspension' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-exclamation-triangle me-2"></i>{{ $settings['terms_liability_title'] ?? 'Limitation of Liability' }}</h2>
                    <p>{{ $settings['terms_liability_intro'] ?? 'To the maximum extent permitted by law:' }}</p>
                    <ul>
                        <li>{{ $settings['terms_liability_item1'] ?? 'Medi BillSuite is provided "as is" without warranties of any kind' }}</li>
                        <li>{{ $settings['terms_liability_item2'] ?? 'We are not liable for any indirect, incidental, or consequential damages' }}</li>
                        <li>{{ $settings['terms_liability_item3'] ?? 'Our total liability shall not exceed the amount paid by you in the past 12 months' }}</li>
                        <li>{{ $settings['terms_liability_item4'] ?? 'We are not responsible for data loss due to circumstances beyond our control' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-x-circle me-2"></i>{{ $settings['terms_termination_title'] ?? 'Termination' }}</h2>
                    <p>{{ $settings['terms_termination_intro'] ?? 'Either party may terminate this agreement:' }}</p>
                    <ul>
                        <li>{{ $settings['terms_termination_item1'] ?? 'You may cancel your account at any time through the settings' }}</li>
                        <li>{{ $settings['terms_termination_item2'] ?? 'We may suspend or terminate accounts that violate these terms' }}</li>
                        <li>{{ $settings['terms_termination_item3'] ?? 'Upon termination, you may export your data within 30 days' }}</li>
                        <li>{{ $settings['terms_termination_item4'] ?? 'We may retain certain data as required by law' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-arrow-repeat me-2"></i>{{ $settings['terms_changes_title'] ?? 'Changes to Terms' }}</h2>
                    <p>{{ $settings['terms_changes_text'] ?? 'We may update these Terms of Service from time to time. We will notify you of significant changes via email or through the software. Your continued use after changes constitutes acceptance of the new terms.' }}</p>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-envelope me-2"></i>{{ $settings['terms_contact_title'] ?? 'Contact Information' }}</h2>
                    <p>{{ $settings['terms_contact_text'] ?? 'For questions about these Terms of Service, contact us at:' }}</p>
                    <p class="mb-0">
                        <strong>Email:</strong> {{ $settings['legal_email'] ?? 'legal@medibillsuite.com' }}<br>
                        <strong>Support:</strong> {{ $settings['company_email'] ?? 'support@medibillsuite.com' }}
                    </p>
                </div>

                <div class="text-center mt-4 mb-5">
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
            <p class="mb-0">© {{ $settings['copyright_year'] ?? '2025' }} {{ $settings['company_name'] ?? 'Medi BillSuite' }}. All rights reserved. | 
                <a href="{{ route('pages.privacy') }}">Privacy</a> · 
                <a href="{{ route('pages.terms') }}">Terms</a> · 
                <a href="{{ route('pages.support') }}">Support</a>
            </p>
        </div>
    </footer>
</body>
</html>
