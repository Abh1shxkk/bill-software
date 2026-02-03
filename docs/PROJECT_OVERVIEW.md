# Bill Software - Comprehensive Project Overview

## 🏢 Project Description

**Bill Software** is a comprehensive **Laravel-based Billing & Inventory Management System** designed for wholesale medical stores, pharmacies, and similar retail businesses. It provides end-to-end management of sales, purchases, inventory, customers, suppliers, and financial transactions with advanced features like AI-powered OCR receipt scanning.

---

## 🎯 Core Purpose

This software serves as a complete **ERP (Enterprise Resource Planning)** solution for medical/pharmaceutical businesses, enabling:

- **Sales Management** - Point of sale transactions, invoicing, and customer management
- **Purchase Management** - Supplier orders, purchase tracking, and inventory replenishment
- **Inventory Control** - Stock tracking, batch management, expiry monitoring, breakage handling
- **Financial Management** - Ledgers, vouchers, receipts, payments, and comprehensive reporting
- **AI-Enhanced OCR** - Smart receipt scanning with Gemini AI for automatic item matching
- **Multi-location Support** - Godown/warehouse management and stock transfers
- **Customer Relationship Management** - Customer ledgers, special rates, discounts, prescriptions
- **Compliance & Reporting** - GST reports, financial statements, audit logs

---

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 12.x (PHP 8.2+)
- **Database**: MySQL (bill_software)
- **Server**: Apache (XAMPP)
- **PDF Generation**: barryvdh/laravel-dompdf

### Frontend
- **CSS Framework**: Tailwind CSS 4.0
- **JavaScript**: Vanilla JS with Axios
- **Build Tool**: Vite 7.0
- **UI Components**: Blade Templates

### AI & Services
- **Google Gemini AI** (gemini-2.5-flash) - Advanced OCR and receipt analysis
- **OCR.space API** - Alternative OCR service
- **Location APIs** - Country/State/City data

### DevOps
- **Testing**: Pest PHP
- **Queue System**: Database-based queues
- **Cache**: Database cache
- **Email**: SMTP (Gmail)

---

## 📁 Project Structure

```
bill-software/
├── app/
│   ├── Console/           # Artisan commands
│   ├── Helpers/           # Helper functions
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/     # ~100+ business module controllers
│   │       ├── Api/       # API controllers (OCR, etc.)
│   │       └── Auth/      # Authentication
│   ├── Mail/             # Email notifications
│   ├── Models/           # ~130 Eloquent models
│   ├── Notifications/    # Push notifications
│   ├── Observers/        # Model observers
│   ├── Services/         # Business logic services
│   │   ├── GeminiOCRService.php      # AI-powered OCR
│   │   ├── ImageDeskewService.php    # Image straightening
│   │   ├── DatabaseBackupService.php
│   │   ├── LicenseService.php
│   │   └── ...
│   └── Traits/           # Reusable traits
│
├── config/               # Configuration files
├── database/
│   ├── migrations/       # ~172 migration files
│   ├── seeders/          # Database seeders
│   └── factories/        # Model factories
│
├── docs/                 # Project documentation
│   ├── OCR_IMPROVEMENTS.md
│   ├── RECEIPT_OCR_FEATURE.md
│   ├── GEMINI_OCR_IMPLEMENTATION.md
│   └── GEMINI_MODEL_SELECTION.md
│
├── public/               # Public assets
├── resources/
│   └── views/
│       ├── admin/        # ~977 Blade templates
│       ├── auth/         # Login/registration
│       └── layouts/      # Layout templates
│
├── routes/
│   ├── web.php          # Web routes (~1390 lines)
│   ├── api.php          # API routes
│   └── console.php      # Console routes
│
├── storage/             # File storage
│   ├── app/
│   │   └── public/
│   │       └── receipts/  # OCR receipt images
│   └── logs/
│
└── scanner-service/     # Custom scanner integration
```

---

## 🎨 Key Features & Modules

### 1. **Sales Module** 🛒
- **Sale Transactions** - POS interface with barcode scanning
- **Sale Returns** - Return handling with adjustments
- **Sale Challans** - Delivery notes
- **Invoicing** - Multiple print formats
- **Quotations** - Price quotes for customers
- **Pending Orders** - Order management

### 2. **Purchase Module** 📦
- **Purchase Transactions** - Supplier order processing
- **Purchase Returns** - Return to supplier
- **Purchase Challans** - Goods receipt notes
- **Purchase Vouchers** - Payment vouchers

### 3. **Customer Management** 👥
- **Customer Master** - Customer database
- **Customer Ledger** - Transaction history
- **Customer Receipts** - Payment collection
- **Customer Dues** - Outstanding tracking
- **Special Rates** - Customer-specific pricing
- **Discounts** - Customer discounts
- **Prescriptions** - Prescription management
- **Customer Copy Discounts** - Copy-based pricing

### 4. **Supplier Management** 🏭
- **Supplier Master** - Supplier database
- **Supplier Payments** - Payment processing
- **Supplier Ledger** - Transaction tracking

### 5. **Inventory Management** 📊
- **Item Master** - Product database with HSN codes
- **Batch Management** - Batch tracking
- **Stock Ledger** - Real-time stock tracking
- **Stock Adjustments** - Manual adjustments
- **Breakage/Expiry** - Damaged/expired stock handling
- **Stock Transfers** - Inter-location transfers
- **Godown Management** - Warehouse management
- **Label Generation** - Barcode/label printing

### 6. **Advanced Inventory** 🔄
- **Sample Issued/Received** - Free sample tracking
- **Replacement Notes** - Replacement processing
- **Claim to Supplier** - Supplier claims
- **Breakage to Supplier** - Return damaged goods
- **Godown Breakage/Expiry** - Warehouse-specific handling

### 7. **Financial Management** 💰
- **General Ledger** - All accounts
- **Cash/Bank Book** - Cash flow tracking
- **Customer Ledger** - Receivables
- **Supplier Ledger** - Payables
- **Credit Notes** - Customer credits
- **Debit Notes** - Supplier debits
- **Voucher Entry** - Journal entries
- **Income/Purchase Vouchers** - Voucher management
- **Multi-Voucher** - Batch voucher entry
- **Deposit Slips** - Bank deposits
- **Cheque Returns** - Bounced cheque handling

### 8. **Reporting & Analytics** 📈
- **Sales Reports** - Detailed sales analysis
- **Purchase Reports** - Purchase tracking
- **Inventory Reports** - Stock reports
- **Financial Reports** - P&L, Balance Sheet
- **GST Reports** - Tax compliance
- **Management Reports** - Executive dashboards
- **Customer Reports** - Customer analytics
- **Expiry Reports** - Near-expiry alerts
- **Audit Logs** - Complete audit trail

### 9. **AI-Enhanced OCR** 🤖 (Star Feature!)
- **Receipt Scanning** - Upload or scan receipts
- **Automatic Image Straightening** - Auto-correct tilted images
- **Multi-Receipt Detection** - Handle multiple receipts in one scan
- **Smart Item Matching** - AI-powered item recognition
- **Gemini AI Integration** - Advanced OCR with Google Gemini
- **Quality Analysis** - Image quality assessment
- **Interactive Preview** - Zoom, pan, area selection
- **Batch Processing** - Process multiple receipts

### 10. **User & Organization Management** 👤
- **Multi-User Support** - Role-based access
- **User Permissions** - Granular module access
- **Organization Settings** - Company profile
- **Branding/White-Label** - Custom branding
- **Email OTP** - Email verification
- **Hotkeys** - Keyboard shortcuts
- **Module Access Control** - Feature restrictions

### 11. **System Features** ⚙️
- **Auto Backup** - Automated daily backups
- **Database Backup/Restore** - Manual backup
- **License Management** - Software licensing
- **Personal Directory** - Contact management
- **General Reminders** - Notification system
- **General Notebook** - Notes/memos
- **Country/State/City Manager** - Location data
- **Area/Route Management** - Sales territory

---

## 🔑 Advanced OCR Implementation

### Gemini AI Integration

The system uses **Google Gemini 2.5 Flash** for intelligent receipt processing:

#### Features:
1. **Text Extraction** - Accurate OCR from receipts
2. **Auto-Straightening** - Corrects tilted/skewed images
3. **Multi-Receipt Detection** - Processes stacked receipts
4. **Item Recognition** - Matches items with inventory
5. **Smart Search** - 4+ character matching (filters noise like "No", "Pcs")
6. **Unlimited Results** - No artificial limits on matched items

#### API Configuration (.env):
```env
GEMINI_API_KEY=AIzaSyCyF0f4_1mN1wT46KwmXMJdDwdPFRPSXAM
GEMINI_MODEL=gemini-2.5-flash

SPACE_OCR_URL=https://api.ocr.space/parse/image
SPACE_OCR_KEY=K83698965288957
```

#### Key Services:
- **GeminiOCRService.php** - Main AI service
- **ImageDeskewService.php** - Image correction
- **PythonDeskewService.php** - Python-based straightening
- **OCRController.php** - API endpoints

#### API Endpoints:
- `POST /admin/api/ocr/extract` - Extract text from image
- `POST /admin/api/ocr/search-items` - Search inventory items
- `POST /admin/api/ocr/straighten-image` - Auto-straighten receipt
- `POST /admin/api/ocr/analyze-with-gemini` - Full AI analysis
- `POST /admin/api/ocr/detect-multiple-receipts` - Multi-receipt detection
- `GET /admin/api/ocr/status` - Check OCR service status

---

## 🗄️ Database Schema

**Database Name**: `bill_software`

### Key Tables (130+ models):
- **users** - User accounts with permissions
- **organizations** - Company/organization data
- **customers** - Customer master
- **suppliers** - Supplier master
- **items** - Product/medicine inventory (~10,550 bytes model)
- **batches** - Batch tracking
- **companies** - Company/manufacturer data
- **item_categories** - Product categories
- **hsn_codes** - HSN/tax codes

### Transaction Tables:
- **sale_transactions** + **sale_transaction_items**
- **purchase_transactions** + **purchase_transaction_items**
- **stock_ledgers** - Real-time stock tracking
- **customer_ledgers** - Customer accounts
- **invoices** + **invoice_items**
- **credit_notes**, **debit_notes**
- **breakage_expiry_transactions**
- **stock_transfer_*_transactions**
- And ~100+ more transaction tables...

### Financial Tables:
- **general_ledgers**
- **cash_bank_books**
- **vouchers**, **multi_vouchers**
- **bank_transactions**
- **deposit_slips**
- **cheque_returns**

### System Tables:
- **licenses** + **license_logs**
- **audit_logs**
- **auto_backup_logs**
- **backup_schedules**
- **email_otps**
- **permissions** + **user_permissions**
- **sessions**, **cache**

---

## 🚀 Application Workflow

### Typical Sales Flow:
1. **Create/Select Customer** → Customer master
2. **Scan Receipt** (Optional) → AI OCR extracts items
3. **Add Items** → Manual or from OCR matches
4. **Apply Discounts** → Customer-specific rates
5. **Generate Invoice** → PDF invoice
6. **Record Payment** → Customer receipt
7. **Update Inventory** → Stock ledger updated
8. **Email Invoice** → Send to customer

### Purchase Workflow:
1. **Select Supplier** → Supplier master
2. **Create Purchase** → Add items with batches
3. **Record Payment** → Supplier payment
4. **Update Stock** → Stock increase
5. **Generate Report** → Purchase report

### OCR Workflow:
1. **Upload Receipt** → Scanner or file upload
2. **Preview & Select** → Zoom and select text area
3. **Extract Text** → Gemini AI processes image
4. **Auto-Straighten** → Corrects tilt (if needed)
5. **Match Items** → Smart search inventory
6. **Review Matches** → Top matches displayed
7. **Add to Sale** → Select items to add
8. **Complete Transaction** → Process sale

---

## 🔐 Security Features

- **License-based Access** - Software licensing system
- **Role-based Permissions** - Module-level access control
- **Audit Logging** - Complete activity tracking
- **Email OTP Verification** - Secure email confirmation
- **CSRF Protection** - Form security
- **Encrypted Passwords** - Bcrypt hashing
- **Session Management** - Database sessions
- **Auto Backup** - Daily automated backups

---

## 📧 Email Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=abhishekchauhan.gms@gmail.com
MAIL_ENCRYPTION=tls
```

**Features:**
- Invoice emailing with PDF attachments
- Receipt image attachments
- OTP verification emails
- Notification emails

---

## 🎨 User Interface

### Design Philosophy:
- **Blade Templates** - Server-side rendering
- **Tailwind CSS** - Utility-first styling
- **Responsive Design** - Mobile-friendly
- **Module Shortcuts** - Quick navigation
- **Hotkey Support** - Keyboard shortcuts (e.g., Ctrl+I for past invoices)
- **Interactive Modals** - Modern UI patterns

### Key UI Features:
- **Dashboard** - Overview with stats and charts
- **Data Tables** - Sortable, searchable tables
- **Form Validation** - Real-time validation
- **Toast Notifications** - Success/error messages
- **Dark Mode** - (If implemented)
- **Print Formats** - Multiple invoice templates

---

## 🧪 Testing & Quality

- **Pest PHP** - Modern testing framework
- **Feature Tests** - Business logic testing
- **Unit Tests** - Component testing
- **Test Coverage** - Comprehensive test suite

---

## 📊 Reporting Capabilities

### Sales Reports:
- Daily/Monthly/Yearly sales
- Customer-wise sales
- Item-wise sales
- Salesman performance
- Profit analysis

### Financial Reports:
- Profit & Loss
- Balance Sheet
- Cash Flow
- Outstanding reports
- Aging analysis

### Inventory Reports:
- Stock summary
- Stock movement
- Expiry reports
- Breakage reports
- Batch reports
- Godown stock

### GST Reports:
- GSTR-1 (Sales)
- GSTR-2 (Purchases)
- Tax summary
- HSN-wise summary

---

## 🔧 Configuration Files

### Key Environment Variables:
```env
APP_NAME=Laravel
APP_URL=http://localhost/bill-software
DB_DATABASE=bill_software

# OCR Services
GEMINI_API_KEY=***
GEMINI_MODEL=gemini-2.5-flash
SPACE_OCR_KEY=***

# Location APIs
LOCATION_API_KEY=***
COUNTRY_STATE_CITY_API_KEY=***

# Email
MAIL_USERNAME=abhishekchauhan.gms@gmail.com
MAIL_PASSWORD=***
```

---

## 📱 Current Open Files (Context)

Based on your current session:
1. **config/database.php** - Database configuration
2. **docs/OCR_IMPROVEMENTS.md** - OCR enhancement documentation
3. **app/Http/Controllers/Api/OCRController.php** - OCR API logic
4. **database/migrations/2026_01_21_132657_create_email_otps_table.php** - Email OTP table
5. **resources/views/layouts/partials/module-shortcuts.blade.php** - Navigation shortcuts
6. **app/Http/Controllers/ProfileController.php** - User profile management

---

## 🎯 Recent Development Focus

Based on conversation history:
1. **OCR Enhancements** - Multi-receipt support, auto-straightening
2. **Item Matching** - Unlimited results, 4+ character filtering
3. **Image Quality** - Auto-alignment, tilt correction
4. **Email System** - OTP verification, invoice attachments
5. **Past Invoice Modal** - Invoice selection (Ctrl+I)
6. **Database Stability** - Memory optimization, error fixing

---

## 🚀 Getting Started

### Prerequisites:
- PHP 8.2+
- MySQL
- Composer
- Node.js & npm
- XAMPP (Apache + MySQL)

### Installation:
```bash
# Clone repository
cd c:\xampp\htdocs\bill-software

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Build assets
npm run build

# Start development server
composer run dev
```

### Access:
- **URL**: http://localhost/bill-software
- **Admin Dashboard**: /admin/dashboard

---

## 🎓 Key Strengths

1. ✅ **Comprehensive ERP** - All-in-one business solution
2. ✅ **AI-Powered OCR** - Cutting-edge receipt scanning with Gemini
3. ✅ **Medical/Pharma Focus** - Industry-specific features
4. ✅ **Multi-location Support** - Godown/warehouse management
5. ✅ **Extensive Reporting** - 100+ report types
6. ✅ **Robust Architecture** - Laravel best practices
7. ✅ **Scalable** - Handles large inventories
8. ✅ **Modern Stack** - Latest Laravel, Tailwind, Vite

---

## 📖 Documentation Files

- **RECEIPT_OCR_FEATURE.md** - OCR feature guide
- **OCR_IMPROVEMENTS.md** - Recent OCR enhancements
- **GEMINI_OCR_IMPLEMENTATION.md** - Gemini AI integration
- **GEMINI_MODEL_SELECTION.md** - Model selection guide

---

## 🏆 Summary

**Bill Software** is a **production-ready, enterprise-grade billing and inventory management system** specifically designed for medical/pharmaceutical wholesale businesses. It combines traditional ERP modules (sales, purchase, inventory, finance) with modern AI-powered features like **Gemini OCR receipt scanning**, making it a comprehensive solution for retail operations.

The system is built on **Laravel 12** with a **Tailwind CSS** frontend, featuring **130+ database models**, **100+ controllers**, **977+ Blade views**, and **172+ migrations**, demonstrating significant scale and maturity.

**Current Focus**: Enhancing the AI OCR capabilities for multi-receipt processing and intelligent item matching.

---

**Project Location**: `c:\xampp\htdocs\bill-software`  
**Database**: `bill_software` (MySQL)  
**Tech Stack**: Laravel 12 + Tailwind CSS 4 + Gemini AI + XAMPP  
**Primary Module**: Medical Wholesale ERP with AI-Enhanced OCR
