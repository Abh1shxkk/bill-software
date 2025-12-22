<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - {{ $settings['company_name'] ?? 'Medi BillSuite' }}</title>
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
        .support-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            height: 100%;
            text-align: center;
            transition: all 0.2s;
        }
        .support-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .support-card .icon {
            width: 60px;
            height: 60px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            color: var(--primary-color);
        }
        .support-card .icon i {
            font-size: 1.75rem;
        }
        .support-card h3 {
            color: var(--text-dark);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .support-card p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        .support-card .btn-support {
            background: white;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .support-card .btn-support:hover {
            background: var(--primary-color);
            color: white;
        }
        .faq-section {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            margin-top: 2rem;
        }
        .faq-section h2 {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .accordion-item {
            border: 1px solid var(--border-color);
            margin-bottom: 0.5rem;
            border-radius: 6px !important;
            overflow: hidden;
        }
        .accordion-button {
            color: var(--text-dark);
            background: white;
            font-weight: 500;
            padding: 1rem 1.25rem;
            box-shadow: none !important;
        }
        .accordion-button:not(.collapsed) {
            color: var(--primary-color);
            background: var(--primary-light);
        }
        .accordion-body {
            color: var(--text-muted);
            font-size: 0.95rem;
            padding: 1.25rem;
            line-height: 1.6;
        }
        .contact-section {
            background: #1e293b;
            border-radius: 8px;
            padding: 2.5rem;
            margin-top: 2rem;
            color: white;
        }
        .contact-section h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        .contact-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding: 0.75rem;
            background: rgba(255,255,255,0.05);
            border-radius: 6px;
        }
        .contact-info i {
            font-size: 1.25rem;
            color: #818cf8;
        }
        .contact-info span {
            color: #e2e8f0;
            font-size: 0.95rem;
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
        .hours-badge {
            background: #f1f5f9;
            color: var(--text-muted);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 0.75rem;
            border: 1px solid var(--border-color);
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
                    <li class="breadcrumb-item active">Support</li>
                </ol>
            </nav>
            <h1><i class="bi bi-headset me-2"></i>{{ $settings['support_page_title'] ?? 'Support Center' }}</h1>
            <p>{{ $settings['support_page_subtitle'] ?? 'We\'re here to help you succeed with Medi BillSuite' }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="container">
        <!-- Support Options -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="support-card">
                    <div class="icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <h3>{{ $settings['support_email_title'] ?? 'Email Support' }}</h3>
                    <p>{{ $settings['support_email_text'] ?? 'Send us your queries and we\'ll respond within 24 hours' }}</p>
                    <a href="mailto:{{ $settings['company_email'] ?? 'support@medibillsuite.com' }}" class="btn-support">
                        <i class="bi bi-send me-1"></i> Send Email
                    </a>
                    <br>
                    <div class="hours-badge">Response: {{ $settings['support_email_response'] ?? '24 Hours' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="support-card">
                    <div class="icon">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <h3>{{ $settings['support_phone_title'] ?? 'Phone Support' }}</h3>
                    <p>{{ $settings['support_phone_text'] ?? 'Talk to our support team for immediate assistance' }}</p>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['company_phone'] ?? '+911234567890') }}" class="btn-support">
                        <i class="bi bi-telephone me-1"></i> Call Us
                    </a>
                    <br>
                    <div class="hours-badge">{{ $settings['support_phone_hours'] ?? 'Mon-Sat: 9AM - 6PM' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="support-card">
                    <div class="icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h3>{{ $settings['support_chat_title'] ?? 'Live Chat' }}</h3>
                    <p>{{ $settings['support_chat_text'] ?? 'Chat with our support team in real-time' }}</p>
                    <a href="#" class="btn-support" onclick="alert('Live chat coming soon!')">
                        <i class="bi bi-chat me-1"></i> Start Chat
                    </a>
                    <br>
                    <div class="hours-badge">{{ $settings['support_chat_status'] ?? 'Coming Soon' }}</div>
                </div>
            </div>
        </div>

        <!-- Quick Help -->
        <div class="row g-4 mt-3">
            <div class="col-md-6">
                <div class="support-card d-flex flex-row align-items-center text-start p-3">
                    <div class="icon mb-0 me-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi bi-book" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="font-size: 1rem;">{{ $settings['support_docs_title'] ?? 'Documentation' }}</h3>
                        <p class="mb-2" style="font-size: 0.85rem;">{{ $settings['support_docs_text'] ?? 'Browse our comprehensive documentation' }}</p>
                        <a href="{{ route('pages.documentation') }}" class="text-primary text-decoration-none fw-medium" style="font-size: 0.85rem;">
                            View Docs <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="support-card d-flex flex-row align-items-center text-start p-3">
                    <div class="icon mb-0 me-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi bi-play-circle" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="font-size: 1rem;">{{ $settings['support_video_title'] ?? 'Video Tutorials' }}</h3>
                        <p class="mb-2" style="font-size: 0.85rem;">{{ $settings['support_video_text'] ?? 'Watch step-by-step video guides' }}</p>
                        <a href="#" class="text-primary text-decoration-none fw-medium" style="font-size: 0.85rem;" onclick="alert('Video tutorials coming soon!')">
                            Watch Now <i class="bi bi-play ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2><i class="bi bi-question-circle me-2"></i>{{ $settings['faq_section_title'] ?? 'Frequently Asked Questions' }}</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            {{ $settings['faq_1_question'] ?? 'How do I create a new invoice?' }}
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($settings['faq_1_answer'] ?? 'Go to Sales → Create Invoice or press Ctrl+I. Fill in customer details, add products, and click Save.')) !!}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            {{ $settings['faq_2_question'] ?? 'How do I add a new product to inventory?' }}
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($settings['faq_2_answer'] ?? 'Navigate to Inventory → Products → Add Product. Enter product details including HSN code, GST rate, and pricing.')) !!}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            {{ $settings['faq_3_question'] ?? 'How can I generate GST reports?' }}
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($settings['faq_3_answer'] ?? 'Go to Reports → GST Reports. You can generate GSTR-1, GSTR-3B, and other GST reports.')) !!}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            {{ $settings['faq_4_question'] ?? 'How do I backup my data?' }}
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($settings['faq_4_answer'] ?? 'Go to Administration → Database Backup. You can create manual backups or schedule automatic daily backups.')) !!}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            {{ $settings['faq_5_question'] ?? 'How do I manage multiple users?' }}
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($settings['faq_5_answer'] ?? 'Go to Administration → Users to add new users. You can assign roles and permissions to control access to different modules.')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="contact-section">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h2><i class="bi bi-building me-2"></i>{{ $settings['support_contact_title'] ?? 'Contact Information' }}</h2>
                    <p class="opacity-75 mb-4">{{ $settings['support_contact_text'] ?? 'Reach out to us through any of these channels' }}</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-info">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    <div class="small opacity-75">Headquarters</div>
                                    <span>{{ $settings['company_address'] ?? 'Your Business Address, City' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="contact-info">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <div class="small opacity-75">Phone</div>
                                    <span>{{ $settings['company_phone'] ?? '+91 123 456 7890' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="contact-info">
                                <i class="bi bi-envelope"></i>
                                <div>
                                    <div class="small opacity-75">Email</div>
                                    <span>{{ $settings['company_email'] ?? 'support@medibillsuite.com' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="contact-info">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <div class="small opacity-75">Working Hours</div>
                                    <span>{{ $settings['support_hours'] ?? 'Mon-Sat: 9AM - 6PM' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-center d-none d-md-block">
                    <div style="background: rgba(255,255,255,0.05); border-radius: 12px; padding: 2rem; border: 1px solid rgba(255,255,255,0.1);">
                        <i class="bi bi-headset" style="font-size: 5rem; color: #818cf8;"></i>
                        <h4 class="mt-3 text-white">We're here to help!</h4>
                        <p class="text-white-50 small mb-0">Our support team is available during working hours to assist you.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5">
            <a href="{{ url()->previous() }}" class="btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Go Back
            </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
