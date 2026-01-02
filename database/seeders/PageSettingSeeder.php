<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PageSetting;

class PageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // ========== GENERAL SETTINGS ==========
            ['key' => 'company_name', 'value' => 'InvoiceLab', 'group' => 'general', 'label' => 'Company Name', 'type' => 'text'],
            ['key' => 'company_email', 'value' => 'support@invoicelab.com', 'group' => 'general', 'label' => 'Support Email', 'type' => 'email'],
            ['key' => 'legal_email', 'value' => 'legal@invoicelab.com', 'group' => 'general', 'label' => 'Legal Email', 'type' => 'email'],
            ['key' => 'company_phone', 'value' => '+91 123 456 7890', 'group' => 'general', 'label' => 'Phone Number', 'type' => 'text'],
            ['key' => 'company_address', 'value' => 'Your Business Address, City - 000000', 'group' => 'general', 'label' => 'Company Address', 'type' => 'textarea'],
            ['key' => 'support_hours', 'value' => 'Monday - Saturday: 9:00 AM - 6:00 PM', 'group' => 'general', 'label' => 'Support Hours', 'type' => 'text'],
            ['key' => 'copyright_year', 'value' => '2025', 'group' => 'general', 'label' => 'Copyright Year', 'type' => 'text'],
            
            // ========== PRIVACY POLICY PAGE ==========
            ['key' => 'privacy_last_updated', 'value' => 'December 2025', 'group' => 'privacy', 'label' => 'Last Updated Date', 'type' => 'text'],
            ['key' => 'privacy_page_title', 'value' => 'Privacy Policy', 'group' => 'privacy', 'label' => 'Page Title', 'type' => 'text'],
            ['key' => 'privacy_page_subtitle', 'value' => 'How we collect, use, and protect your information', 'group' => 'privacy', 'label' => 'Page Subtitle', 'type' => 'text'],
            ['key' => 'privacy_intro_title', 'value' => 'Introduction', 'group' => 'privacy', 'label' => 'Introduction Section Title', 'type' => 'text'],
            ['key' => 'privacy_intro', 'value' => 'InvoiceLab ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our billing and inventory management software.', 'group' => 'privacy', 'label' => 'Introduction Text', 'type' => 'textarea'],
            ['key' => 'privacy_collect_title', 'value' => 'Information We Collect', 'group' => 'privacy', 'label' => 'Collection Section Title', 'type' => 'text'],
            ['key' => 'privacy_collect_intro', 'value' => 'We collect information that you provide directly to us, including:', 'group' => 'privacy', 'label' => 'Collection Intro Text', 'type' => 'text'],
            ['key' => 'privacy_collect_item1', 'value' => 'Account Information: Name, email address, username, password, and business details', 'group' => 'privacy', 'label' => 'Collection Item 1', 'type' => 'text'],
            ['key' => 'privacy_collect_item2', 'value' => 'Business Data: Customer details, supplier information, inventory data, sales transactions, and purchase records', 'group' => 'privacy', 'label' => 'Collection Item 2', 'type' => 'text'],
            ['key' => 'privacy_collect_item3', 'value' => 'Financial Information: GST numbers, TIN numbers, drug license details, and banking information', 'group' => 'privacy', 'label' => 'Collection Item 3', 'type' => 'text'],
            ['key' => 'privacy_collect_item4', 'value' => 'Usage Data: Log files, device information, and how you interact with our software', 'group' => 'privacy', 'label' => 'Collection Item 4', 'type' => 'text'],
            ['key' => 'privacy_use_title', 'value' => 'How We Use Your Information', 'group' => 'privacy', 'label' => 'Usage Section Title', 'type' => 'text'],
            ['key' => 'privacy_use_intro', 'value' => 'We use the collected information to:', 'group' => 'privacy', 'label' => 'Usage Intro Text', 'type' => 'text'],
            ['key' => 'privacy_use_item1', 'value' => 'Provide, maintain, and improve our billing software services', 'group' => 'privacy', 'label' => 'Usage Item 1', 'type' => 'text'],
            ['key' => 'privacy_use_item2', 'value' => 'Process transactions and send related information', 'group' => 'privacy', 'label' => 'Usage Item 2', 'type' => 'text'],
            ['key' => 'privacy_use_item3', 'value' => 'Generate invoices, reports, and business analytics', 'group' => 'privacy', 'label' => 'Usage Item 3', 'type' => 'text'],
            ['key' => 'privacy_use_item4', 'value' => 'Send technical notices, updates, and support messages', 'group' => 'privacy', 'label' => 'Usage Item 4', 'type' => 'text'],
            ['key' => 'privacy_use_item5', 'value' => 'Respond to your comments, questions, and customer service requests', 'group' => 'privacy', 'label' => 'Usage Item 5', 'type' => 'text'],
            ['key' => 'privacy_use_item6', 'value' => 'Ensure compliance with GST and other regulatory requirements', 'group' => 'privacy', 'label' => 'Usage Item 6', 'type' => 'text'],
            ['key' => 'privacy_security_title', 'value' => 'Data Security', 'group' => 'privacy', 'label' => 'Security Section Title', 'type' => 'text'],
            ['key' => 'privacy_security_intro', 'value' => 'We implement appropriate technical and organizational measures to protect your personal information, including:', 'group' => 'privacy', 'label' => 'Security Intro Text', 'type' => 'text'],
            ['key' => 'privacy_security_item1', 'value' => 'Encrypted data transmission using SSL/TLS protocols', 'group' => 'privacy', 'label' => 'Security Item 1', 'type' => 'text'],
            ['key' => 'privacy_security_item2', 'value' => 'Secure password hashing and authentication', 'group' => 'privacy', 'label' => 'Security Item 2', 'type' => 'text'],
            ['key' => 'privacy_security_item3', 'value' => 'Regular security audits and updates', 'group' => 'privacy', 'label' => 'Security Item 3', 'type' => 'text'],
            ['key' => 'privacy_security_item4', 'value' => 'Access controls and user permission management', 'group' => 'privacy', 'label' => 'Security Item 4', 'type' => 'text'],
            ['key' => 'privacy_security_item5', 'value' => 'Secure backup and disaster recovery procedures', 'group' => 'privacy', 'label' => 'Security Item 5', 'type' => 'text'],
            ['key' => 'privacy_sharing_title', 'value' => 'Information Sharing', 'group' => 'privacy', 'label' => 'Sharing Section Title', 'type' => 'text'],
            ['key' => 'privacy_sharing_intro', 'value' => 'We do not sell, trade, or rent your personal information to third parties. We may share information only in the following circumstances:', 'group' => 'privacy', 'label' => 'Sharing Intro Text', 'type' => 'textarea'],
            ['key' => 'privacy_sharing_item1', 'value' => 'With your consent or at your direction', 'group' => 'privacy', 'label' => 'Sharing Item 1', 'type' => 'text'],
            ['key' => 'privacy_sharing_item2', 'value' => 'To comply with legal obligations or government requests', 'group' => 'privacy', 'label' => 'Sharing Item 2', 'type' => 'text'],
            ['key' => 'privacy_sharing_item3', 'value' => 'To protect our rights, privacy, safety, or property', 'group' => 'privacy', 'label' => 'Sharing Item 3', 'type' => 'text'],
            ['key' => 'privacy_sharing_item4', 'value' => 'In connection with a merger, acquisition, or sale of assets', 'group' => 'privacy', 'label' => 'Sharing Item 4', 'type' => 'text'],
            ['key' => 'privacy_rights_title', 'value' => 'Your Rights', 'group' => 'privacy', 'label' => 'Rights Section Title', 'type' => 'text'],
            ['key' => 'privacy_rights_intro', 'value' => 'You have the right to:', 'group' => 'privacy', 'label' => 'Rights Intro Text', 'type' => 'text'],
            ['key' => 'privacy_rights_item1', 'value' => 'Access, update, or delete your personal information', 'group' => 'privacy', 'label' => 'Rights Item 1', 'type' => 'text'],
            ['key' => 'privacy_rights_item2', 'value' => 'Export your business data in standard formats', 'group' => 'privacy', 'label' => 'Rights Item 2', 'type' => 'text'],
            ['key' => 'privacy_rights_item3', 'value' => 'Opt-out of marketing communications', 'group' => 'privacy', 'label' => 'Rights Item 3', 'type' => 'text'],
            ['key' => 'privacy_rights_item4', 'value' => 'Request information about data processing activities', 'group' => 'privacy', 'label' => 'Rights Item 4', 'type' => 'text'],
            ['key' => 'privacy_contact_title', 'value' => 'Contact Us', 'group' => 'privacy', 'label' => 'Contact Section Title', 'type' => 'text'],
            ['key' => 'privacy_contact_text', 'value' => 'If you have any questions about this Privacy Policy, please contact us at:', 'group' => 'privacy', 'label' => 'Contact Text', 'type' => 'text'],
            
            // ========== TERMS OF SERVICE PAGE ==========
            ['key' => 'terms_last_updated', 'value' => 'December 2025', 'group' => 'terms', 'label' => 'Last Updated Date', 'type' => 'text'],
            ['key' => 'terms_page_title', 'value' => 'Terms of Service', 'group' => 'terms', 'label' => 'Page Title', 'type' => 'text'],
            ['key' => 'terms_page_subtitle', 'value' => 'Please read these terms carefully before using our services', 'group' => 'terms', 'label' => 'Page Subtitle', 'type' => 'text'],
            ['key' => 'terms_acceptance_title', 'value' => 'Acceptance of Terms', 'group' => 'terms', 'label' => 'Acceptance Section Title', 'type' => 'text'],
            ['key' => 'terms_acceptance_text', 'value' => 'By accessing and using InvoiceLab billing and inventory management software, you accept and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.', 'group' => 'terms', 'label' => 'Acceptance Text', 'type' => 'textarea'],
            ['key' => 'terms_acceptance_note', 'value' => 'These terms constitute a legally binding agreement between you and InvoiceLab.', 'group' => 'terms', 'label' => 'Acceptance Important Note', 'type' => 'text'],
            ['key' => 'terms_use_title', 'value' => 'Use of Service', 'group' => 'terms', 'label' => 'Use Section Title', 'type' => 'text'],
            ['key' => 'terms_use_intro', 'value' => 'InvoiceLab provides a comprehensive billing and inventory management solution. You agree to use the service only for:', 'group' => 'terms', 'label' => 'Use Intro Text', 'type' => 'textarea'],
            ['key' => 'terms_use_item1', 'value' => 'Legitimate business purposes related to billing, invoicing, and inventory management', 'group' => 'terms', 'label' => 'Use Item 1', 'type' => 'text'],
            ['key' => 'terms_use_item2', 'value' => 'Recording accurate business transactions and maintaining proper records', 'group' => 'terms', 'label' => 'Use Item 2', 'type' => 'text'],
            ['key' => 'terms_use_item3', 'value' => 'Generating GST-compliant invoices and reports', 'group' => 'terms', 'label' => 'Use Item 3', 'type' => 'text'],
            ['key' => 'terms_use_item4', 'value' => 'Managing customer and supplier relationships', 'group' => 'terms', 'label' => 'Use Item 4', 'type' => 'text'],
            ['key' => 'terms_account_title', 'value' => 'Account Responsibilities', 'group' => 'terms', 'label' => 'Account Section Title', 'type' => 'text'],
            ['key' => 'terms_account_intro', 'value' => 'As an account holder, you are responsible for:', 'group' => 'terms', 'label' => 'Account Intro Text', 'type' => 'text'],
            ['key' => 'terms_account_item1', 'value' => 'Account Security: Maintaining the confidentiality of your login credentials', 'group' => 'terms', 'label' => 'Account Item 1', 'type' => 'text'],
            ['key' => 'terms_account_item2', 'value' => 'Authorized Access: Ensuring only authorized personnel access your account', 'group' => 'terms', 'label' => 'Account Item 2', 'type' => 'text'],
            ['key' => 'terms_account_item3', 'value' => 'Data Accuracy: Providing accurate and up-to-date business information', 'group' => 'terms', 'label' => 'Account Item 3', 'type' => 'text'],
            ['key' => 'terms_account_item4', 'value' => 'Compliance: Ensuring your use complies with applicable laws and regulations', 'group' => 'terms', 'label' => 'Account Item 4', 'type' => 'text'],
            ['key' => 'terms_account_item5', 'value' => 'Activity Monitoring: Monitoring and being responsible for all activities under your account', 'group' => 'terms', 'label' => 'Account Item 5', 'type' => 'text'],
            ['key' => 'terms_prohibited_title', 'value' => 'Prohibited Activities', 'group' => 'terms', 'label' => 'Prohibited Section Title', 'type' => 'text'],
            ['key' => 'terms_prohibited_intro', 'value' => 'You agree not to:', 'group' => 'terms', 'label' => 'Prohibited Intro Text', 'type' => 'text'],
            ['key' => 'terms_prohibited_item1', 'value' => 'Use the service for any illegal or unauthorized purpose', 'group' => 'terms', 'label' => 'Prohibited Item 1', 'type' => 'text'],
            ['key' => 'terms_prohibited_item2', 'value' => 'Attempt to gain unauthorized access to any part of the service', 'group' => 'terms', 'label' => 'Prohibited Item 2', 'type' => 'text'],
            ['key' => 'terms_prohibited_item3', 'value' => 'Interfere with or disrupt the service or servers', 'group' => 'terms', 'label' => 'Prohibited Item 3', 'type' => 'text'],
            ['key' => 'terms_prohibited_item4', 'value' => 'Transmit viruses, malware, or any malicious code', 'group' => 'terms', 'label' => 'Prohibited Item 4', 'type' => 'text'],
            ['key' => 'terms_prohibited_item5', 'value' => 'Reverse engineer or attempt to extract source code', 'group' => 'terms', 'label' => 'Prohibited Item 5', 'type' => 'text'],
            ['key' => 'terms_prohibited_item6', 'value' => 'Use the service to generate fraudulent invoices or records', 'group' => 'terms', 'label' => 'Prohibited Item 6', 'type' => 'text'],
            ['key' => 'terms_prohibited_item7', 'value' => 'Share your account credentials with unauthorized parties', 'group' => 'terms', 'label' => 'Prohibited Item 7', 'type' => 'text'],
            ['key' => 'terms_data_title', 'value' => 'Data Ownership', 'group' => 'terms', 'label' => 'Data Section Title', 'type' => 'text'],
            ['key' => 'terms_data_intro', 'value' => 'You retain ownership of all business data you enter into InvoiceLab, including:', 'group' => 'terms', 'label' => 'Data Intro Text', 'type' => 'text'],
            ['key' => 'terms_data_item1', 'value' => 'Customer and supplier information', 'group' => 'terms', 'label' => 'Data Item 1', 'type' => 'text'],
            ['key' => 'terms_data_item2', 'value' => 'Product and inventory data', 'group' => 'terms', 'label' => 'Data Item 2', 'type' => 'text'],
            ['key' => 'terms_data_item3', 'value' => 'Sales and purchase transactions', 'group' => 'terms', 'label' => 'Data Item 3', 'type' => 'text'],
            ['key' => 'terms_data_item4', 'value' => 'Financial records and reports', 'group' => 'terms', 'label' => 'Data Item 4', 'type' => 'text'],
            ['key' => 'terms_data_note', 'value' => 'You grant us a limited license to process this data solely for the purpose of providing our services to you.', 'group' => 'terms', 'label' => 'Data License Note', 'type' => 'textarea'],
            ['key' => 'terms_payment_title', 'value' => 'Payment Terms', 'group' => 'terms', 'label' => 'Payment Section Title', 'type' => 'text'],
            ['key' => 'terms_payment_intro', 'value' => 'If applicable to your subscription:', 'group' => 'terms', 'label' => 'Payment Intro Text', 'type' => 'text'],
            ['key' => 'terms_payment_item1', 'value' => 'Fees are billed in advance on a monthly or annual basis', 'group' => 'terms', 'label' => 'Payment Item 1', 'type' => 'text'],
            ['key' => 'terms_payment_item2', 'value' => 'All fees are non-refundable unless otherwise specified', 'group' => 'terms', 'label' => 'Payment Item 2', 'type' => 'text'],
            ['key' => 'terms_payment_item3', 'value' => 'We reserve the right to modify pricing with 30 days notice', 'group' => 'terms', 'label' => 'Payment Item 3', 'type' => 'text'],
            ['key' => 'terms_payment_item4', 'value' => 'Failure to pay may result in service suspension', 'group' => 'terms', 'label' => 'Payment Item 4', 'type' => 'text'],
            ['key' => 'terms_liability_title', 'value' => 'Limitation of Liability', 'group' => 'terms', 'label' => 'Liability Section Title', 'type' => 'text'],
            ['key' => 'terms_liability_intro', 'value' => 'To the maximum extent permitted by law:', 'group' => 'terms', 'label' => 'Liability Intro Text', 'type' => 'text'],
            ['key' => 'terms_liability_item1', 'value' => 'InvoiceLab is provided "as is" without warranties of any kind', 'group' => 'terms', 'label' => 'Liability Item 1', 'type' => 'text'],
            ['key' => 'terms_liability_item2', 'value' => 'We are not liable for any indirect, incidental, or consequential damages', 'group' => 'terms', 'label' => 'Liability Item 2', 'type' => 'text'],
            ['key' => 'terms_liability_item3', 'value' => 'Our total liability shall not exceed the amount paid by you in the past 12 months', 'group' => 'terms', 'label' => 'Liability Item 3', 'type' => 'text'],
            ['key' => 'terms_liability_item4', 'value' => 'We are not responsible for data loss due to circumstances beyond our control', 'group' => 'terms', 'label' => 'Liability Item 4', 'type' => 'text'],
            ['key' => 'terms_termination_title', 'value' => 'Termination', 'group' => 'terms', 'label' => 'Termination Section Title', 'type' => 'text'],
            ['key' => 'terms_termination_intro', 'value' => 'Either party may terminate this agreement:', 'group' => 'terms', 'label' => 'Termination Intro Text', 'type' => 'text'],
            ['key' => 'terms_termination_item1', 'value' => 'You may cancel your account at any time through the settings', 'group' => 'terms', 'label' => 'Termination Item 1', 'type' => 'text'],
            ['key' => 'terms_termination_item2', 'value' => 'We may suspend or terminate accounts that violate these terms', 'group' => 'terms', 'label' => 'Termination Item 2', 'type' => 'text'],
            ['key' => 'terms_termination_item3', 'value' => 'Upon termination, you may export your data within 30 days', 'group' => 'terms', 'label' => 'Termination Item 3', 'type' => 'text'],
            ['key' => 'terms_termination_item4', 'value' => 'We may retain certain data as required by law', 'group' => 'terms', 'label' => 'Termination Item 4', 'type' => 'text'],
            ['key' => 'terms_changes_title', 'value' => 'Changes to Terms', 'group' => 'terms', 'label' => 'Changes Section Title', 'type' => 'text'],
            ['key' => 'terms_changes_text', 'value' => 'We may update these Terms of Service from time to time. We will notify you of significant changes via email or through the software. Your continued use after changes constitutes acceptance of the new terms.', 'group' => 'terms', 'label' => 'Changes Text', 'type' => 'textarea'],
            ['key' => 'terms_contact_title', 'value' => 'Contact Information', 'group' => 'terms', 'label' => 'Contact Section Title', 'type' => 'text'],
            ['key' => 'terms_contact_text', 'value' => 'For questions about these Terms of Service, contact us at:', 'group' => 'terms', 'label' => 'Contact Text', 'type' => 'text'],

            // ========== SUPPORT PAGE ==========
            ['key' => 'support_page_title', 'value' => 'Support Center', 'group' => 'support', 'label' => 'Page Title', 'type' => 'text'],
            ['key' => 'support_page_subtitle', 'value' => 'We\'re here to help you succeed with InvoiceLab', 'group' => 'support', 'label' => 'Page Subtitle', 'type' => 'text'],
            ['key' => 'support_email_title', 'value' => 'Email Support', 'group' => 'support', 'label' => 'Email Card Title', 'type' => 'text'],
            ['key' => 'support_email_text', 'value' => 'Send us your queries and we\'ll respond within 24 hours', 'group' => 'support', 'label' => 'Email Card Text', 'type' => 'text'],
            ['key' => 'support_email_response', 'value' => 'Response: 24 Hours', 'group' => 'support', 'label' => 'Email Response Time', 'type' => 'text'],
            ['key' => 'support_phone_title', 'value' => 'Phone Support', 'group' => 'support', 'label' => 'Phone Card Title', 'type' => 'text'],
            ['key' => 'support_phone_text', 'value' => 'Talk to our support team for immediate assistance', 'group' => 'support', 'label' => 'Phone Card Text', 'type' => 'text'],
            ['key' => 'support_phone_hours', 'value' => 'Mon-Sat: 9AM - 6PM', 'group' => 'support', 'label' => 'Phone Support Hours', 'type' => 'text'],
            ['key' => 'support_chat_title', 'value' => 'Live Chat', 'group' => 'support', 'label' => 'Chat Card Title', 'type' => 'text'],
            ['key' => 'support_chat_text', 'value' => 'Chat with our support team in real-time', 'group' => 'support', 'label' => 'Chat Card Text', 'type' => 'text'],
            ['key' => 'support_chat_status', 'value' => 'Coming Soon', 'group' => 'support', 'label' => 'Live Chat Status', 'type' => 'text'],
            ['key' => 'support_docs_title', 'value' => 'Documentation', 'group' => 'support', 'label' => 'Docs Card Title', 'type' => 'text'],
            ['key' => 'support_docs_text', 'value' => 'Browse our comprehensive documentation and user guides', 'group' => 'support', 'label' => 'Docs Card Text', 'type' => 'text'],
            ['key' => 'support_video_title', 'value' => 'Video Tutorials', 'group' => 'support', 'label' => 'Video Card Title', 'type' => 'text'],
            ['key' => 'support_video_text', 'value' => 'Watch step-by-step video guides for all features', 'group' => 'support', 'label' => 'Video Card Text', 'type' => 'text'],
            ['key' => 'support_contact_title', 'value' => 'Contact Information', 'group' => 'support', 'label' => 'Contact Section Title', 'type' => 'text'],
            ['key' => 'support_contact_text', 'value' => 'Reach out to us through any of these channels', 'group' => 'support', 'label' => 'Contact Section Text', 'type' => 'text'],
            ['key' => 'privacy_intro_text', 'value' => 'InvoiceLab ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our billing and inventory management software.', 'group' => 'privacy', 'label' => 'Introduction Content', 'type' => 'textarea'],

            // ========== FAQ SETTINGS ==========
            ['key' => 'faq_section_title', 'value' => 'Frequently Asked Questions', 'group' => 'faq', 'label' => 'FAQ Section Title', 'type' => 'text'],
            ['key' => 'faq_1_question', 'value' => 'How do I create a new invoice?', 'group' => 'faq', 'label' => 'FAQ 1 Question', 'type' => 'text'],
            ['key' => 'faq_1_answer', 'value' => 'Go to Sales → Create Invoice or press Ctrl+I. Fill in customer details, add products, and click Save. The invoice will be automatically numbered and ready for printing.', 'group' => 'faq', 'label' => 'FAQ 1 Answer', 'type' => 'textarea'],
            ['key' => 'faq_2_question', 'value' => 'How do I add a new product to inventory?', 'group' => 'faq', 'label' => 'FAQ 2 Question', 'type' => 'text'],
            ['key' => 'faq_2_answer', 'value' => 'Navigate to Inventory → Products → Add Product. Enter product details including HSN code, GST rate, and pricing. You can also import products in bulk using CSV.', 'group' => 'faq', 'label' => 'FAQ 2 Answer', 'type' => 'textarea'],
            ['key' => 'faq_3_question', 'value' => 'How can I generate GST reports?', 'group' => 'faq', 'label' => 'FAQ 3 Question', 'type' => 'text'],
            ['key' => 'faq_3_answer', 'value' => 'Go to Reports → GST Reports. You can generate GSTR-1, GSTR-3B, and other GST reports. Select the date range and export in Excel or PDF format.', 'group' => 'faq', 'label' => 'FAQ 3 Answer', 'type' => 'textarea'],
            ['key' => 'faq_4_question', 'value' => 'How do I backup my data?', 'group' => 'faq', 'label' => 'FAQ 4 Question', 'type' => 'text'],
            ['key' => 'faq_4_answer', 'value' => 'Go to Administration → Database Backup. You can create manual backups or schedule automatic daily backups. We recommend storing backups in multiple locations.', 'group' => 'faq', 'label' => 'FAQ 4 Answer', 'type' => 'textarea'],
            ['key' => 'faq_5_question', 'value' => 'How do I manage multiple users?', 'group' => 'faq', 'label' => 'FAQ 5 Question', 'type' => 'text'],
            ['key' => 'faq_5_answer', 'value' => 'Go to Administration → Users to add new users. You can assign roles and permissions to control access to different modules. Each user gets their own login credentials.', 'group' => 'faq', 'label' => 'FAQ 5 Answer', 'type' => 'textarea'],
        ];

        foreach ($settings as $setting) {
            PageSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
