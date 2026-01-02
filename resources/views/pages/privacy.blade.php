<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - {{ $settings['company_name'] ?? 'Medi BillSuite' }}</title>
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
        .content-card ul {
            padding-left: 1.5rem;
        }
        .content-card ul li {
            margin-bottom: 0.5rem;
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
                    <li class="breadcrumb-item active">Privacy Policy</li>
                </ol>
            </nav>
            <h1><i class="bi bi-shield-lock me-2"></i>{{ $settings['privacy_page_title'] ?? 'Privacy Policy' }}</h1>
            <p>{{ $settings['privacy_page_subtitle'] ?? 'How we collect, use, and protect your information' }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <p class="last-updated mb-4"><i class="bi bi-calendar3 me-1"></i>Last Updated: {{ $settings['privacy_last_updated'] ?? 'December 2025' }}</p>

                <div class="content-card">
                    <h2><i class="bi bi-info-circle me-2"></i>{{ $settings['privacy_intro_title'] ?? 'Introduction' }}</h2>
                    <p>{{ $settings['privacy_intro_text'] ?? 'Medi BillSuite ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our billing and inventory management software.' }}</p>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-collection me-2"></i>{{ $settings['privacy_collect_title'] ?? 'Information We Collect' }}</h2>
                    <p>{{ $settings['privacy_collect_intro'] ?? 'We collect information that you provide directly to us, including:' }}</p>
                    <ul>
                        <li>{!! $settings['privacy_collect_item1'] ?? '<strong>Account Information:</strong> Name, email address, username, password, and business details' !!}</li>
                        <li>{!! $settings['privacy_collect_item2'] ?? '<strong>Business Data:</strong> Customer details, supplier information, inventory data, sales transactions, and purchase records' !!}</li>
                        <li>{!! $settings['privacy_collect_item3'] ?? '<strong>Financial Information:</strong> GST numbers, TIN numbers, drug license details, and banking information' !!}</li>
                        <li>{!! $settings['privacy_collect_item4'] ?? '<strong>Usage Data:</strong> Log files, device information, and how you interact with our software' !!}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-gear me-2"></i>{{ $settings['privacy_use_title'] ?? 'How We Use Your Information' }}</h2>
                    <p>{{ $settings['privacy_use_intro'] ?? 'We use the collected information to:' }}</p>
                    <ul>
                        <li>{{ $settings['privacy_use_item1'] ?? 'Provide, maintain, and improve our billing software services' }}</li>
                        <li>{{ $settings['privacy_use_item2'] ?? 'Process transactions and send related information' }}</li>
                        <li>{{ $settings['privacy_use_item3'] ?? 'Generate invoices, reports, and business analytics' }}</li>
                        <li>{{ $settings['privacy_use_item4'] ?? 'Send technical notices, updates, and support messages' }}</li>
                        <li>{{ $settings['privacy_use_item5'] ?? 'Respond to your comments, questions, and customer service requests' }}</li>
                        <li>{{ $settings['privacy_use_item6'] ?? 'Ensure compliance with GST and other regulatory requirements' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-lock me-2"></i>{{ $settings['privacy_security_title'] ?? 'Data Security' }}</h2>
                    <p>{{ $settings['privacy_security_intro'] ?? 'We implement appropriate technical and organizational measures to protect your personal information, including:' }}</p>
                    <ul>
                        <li>{{ $settings['privacy_security_item1'] ?? 'Encrypted data transmission using SSL/TLS protocols' }}</li>
                        <li>{{ $settings['privacy_security_item2'] ?? 'Secure password hashing and authentication' }}</li>
                        <li>{{ $settings['privacy_security_item3'] ?? 'Regular security audits and updates' }}</li>
                        <li>{{ $settings['privacy_security_item4'] ?? 'Access controls and user permission management' }}</li>
                        <li>{{ $settings['privacy_security_item5'] ?? 'Secure backup and disaster recovery procedures' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-share me-2"></i>{{ $settings['privacy_sharing_title'] ?? 'Information Sharing' }}</h2>
                    <p>{{ $settings['privacy_sharing_intro'] ?? 'We do not sell, trade, or rent your personal information to third parties. We may share information only in the following circumstances:' }}</p>
                    <ul>
                        <li>{{ $settings['privacy_sharing_item1'] ?? 'With your consent or at your direction' }}</li>
                        <li>{{ $settings['privacy_sharing_item2'] ?? 'To comply with legal obligations or government requests' }}</li>
                        <li>{{ $settings['privacy_sharing_item3'] ?? 'To protect our rights, privacy, safety, or property' }}</li>
                        <li>{{ $settings['privacy_sharing_item4'] ?? 'In connection with a merger, acquisition, or sale of assets' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-person-check me-2"></i>{{ $settings['privacy_rights_title'] ?? 'Your Rights' }}</h2>
                    <p>{{ $settings['privacy_rights_intro'] ?? 'You have the right to:' }}</p>
                    <ul>
                        <li>{{ $settings['privacy_rights_item1'] ?? 'Access, update, or delete your personal information' }}</li>
                        <li>{{ $settings['privacy_rights_item2'] ?? 'Export your business data in standard formats' }}</li>
                        <li>{{ $settings['privacy_rights_item3'] ?? 'Opt-out of marketing communications' }}</li>
                        <li>{{ $settings['privacy_rights_item4'] ?? 'Request information about data processing activities' }}</li>
                    </ul>
                </div>

                <div class="content-card">
                    <h2><i class="bi bi-envelope me-2"></i>{{ $settings['privacy_contact_title'] ?? 'Contact Us' }}</h2>
                    <p>{{ $settings['privacy_contact_text'] ?? 'If you have any questions about this Privacy Policy, please contact us at:' }}</p>
                    <p class="mb-0">
                        <strong>Email:</strong> {{ $settings['legal_email'] ?? $settings['company_email'] ?? 'legal@medibillsuite.com' }}<br>
                        <strong>Address:</strong> {{ $settings['company_address'] ?? 'Your Business Address Here' }}
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
