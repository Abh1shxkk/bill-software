# Bill Software - Standard Operating Procedure (SOP)

---

## 1. Project Overview

### 1.1 What is Bill Software?

**Bill Software** ek comprehensive **Laravel-based Billing & Inventory Management System** hai jo wholesale medical stores, pharmacies, aur similar retail businesses ke liye designed kiya gaya hai. Ye software sales, purchases, inventory, customers, suppliers, aur financial transactions ko manage karne mein help karta hai.

### 1.2 Key Capabilities

| Feature | Description |
|---------|-------------|
| **Sales Management** | Point of sale transactions, invoicing, customer management |
| **Purchase Management** | Supplier orders, purchase tracking, inventory replenishment |
| **Inventory Control** | Stock tracking, batch management, expiry monitoring, breakage handling |
| **Financial Management** | Ledgers, vouchers, receipts, payments, comprehensive reporting |
| **AI-Enhanced OCR** | Smart receipt scanning with Gemini AI for automatic item matching |
| **Multi-location Support** | Godown/warehouse management and stock transfers |
| **Customer Relationship** | Customer ledgers, special rates, discounts, prescriptions |
| **Compliance & Reporting** | GST reports, financial statements, audit logs |

### 1.3 Technology Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 12.x (PHP 8.2+) |
| **Database** | MySQL (Database: `bill_software`) |
| **Server** | Apache (XAMPP) |
| **Frontend** | Tailwind CSS 4.0, Blade Templates |
| **AI/OCR** | Google Gemini AI 2.5 Flash, OCR.space API |
| **PDF** | barryvdh/laravel-dompdf |
| **Build Tool** | Vite 7.0 |

---

## 2. Table of Contents - All Modules

### 📊 MASTER DATA MODULES
| Module | Description |
|--------|-------------|
| **Item Master** | Products/medicines ka database management |
| **Company Master** | Manufacturers/companies ka management |
| **Item Category** | Product categories ka management |
| **HSN Code Master** | GST HSN codes ka management |
| **Batch Master** | Medicine batch tracking |
| **Supplier Master** | Suppliers database management |
| **Customer Master** | Customers database management |

### 👥 USER & ORGANIZATION MODULES
| Module | Description |
|--------|-------------|
| **User Management** | Users aur permissions management |
| **Organization Settings** | Company profile aur branding |
| **Role Management** | User roles aur access control |
| **Hotkeys/Shortcuts** | Keyboard shortcuts configuration |

### 🏢 LOCATION & TERRITORY MODULES
| Module | Description |
|--------|-------------|
| **Country Manager** | Countries ka management |
| **State Manager** | States ka management |
| **Area Manager** | Sales areas ka management |
| **Route Management** | Delivery routes ka management |
| **Regional Manager** | Regional territories |
| **Divisional Manager** | Divisional territories |
| **General Manager** | GM level management |
| **Marketing Manager** | Marketing territories |
| **Salesman Management** | Sales team management |

### 🛒 SALES MODULES
| Module | Description |
|--------|-------------|
| **Sale Transaction** | Main sales/POS interface |
| **Sale Return** | Customer returns handling |
| **Sale Challan** | Delivery notes management |
| **Sale Voucher** | Sales payment vouchers |
| **Pending Orders** | Pending order items tracking |
| **Quotation** | Price quotes generation |

### 📦 PURCHASE MODULES
| Module | Description |
|--------|-------------|
| **Purchase Transaction** | Purchase order processing |
| **Purchase Return** | Return to supplier |
| **Purchase Challan** | Goods receipt notes |
| **Purchase Voucher** | Purchase payment vouchers |
| **Purchase Return Voucher** | Return payment vouchers |

### 📊 INVENTORY MODULES
| Module | Description |
|--------|-------------|
| **Stock Ledger** | Real-time stock tracking |
| **Stock Adjustment** | Manual stock adjustments |
| **Godown Management** | Warehouse management |
| **Stock Transfer - Outgoing** | Godown se transfer |
| **Stock Transfer - Incoming** | Godown mein receive |
| **Stock Transfer Return - Outgoing** | Return transfers out |
| **Stock Transfer Return - Incoming** | Return transfers in |
| **Breakage/Expiry** | Damaged/expired stock |
| **Godown Breakage/Expiry** | Godown-specific breakage |
| **Label Generation** | Barcode/label printing |

### 🔄 ADVANCED INVENTORY MODULES
| Module | Description |
|--------|-------------|
| **Sample Issued** | Free samples given out |
| **Sample Received** | Free samples received |
| **Replacement Note** | Replacement challans |
| **Replacement Received** | Replacements received |
| **Claim to Supplier** | Supplier claim processing |
| **Breakage to Supplier** | Return damaged goods |
| **Pending Order Item** | Pending orders management |

### 💰 FINANCIAL MODULES
| Module | Description |
|--------|-------------|
| **General Ledger** | All accounts ledger |
| **Cash/Bank Book** | Cash flow tracking |
| **Customer Ledger** | Customer accounts |
| **Supplier Ledger** | Supplier accounts |
| **Purchase Ledger** | Purchase accounts |
| **Sale Ledger** | Sales accounts |
| **All Ledger** | Combined ledger view |

### 📝 VOUCHER MODULES
| Module | Description |
|--------|-------------|
| **Voucher Entry** | Journal vouchers |
| **Credit Note** | Customer credit notes |
| **Debit Note** | Supplier debit notes |
| **Income Voucher** | Income transactions |
| **Purchase Voucher** | Purchase vouchers |
| **Multi Voucher** | Batch voucher entry |
| **Sale Return Voucher** | Sale return vouchers |
| **Purchase Return Voucher** | Purchase return vouchers |

### 💳 PAYMENT MODULES
| Module | Description |
|--------|-------------|
| **Customer Receipt** | Payment collection from customers |
| **Supplier Payment** | Payments to suppliers |
| **Deposit Slip** | Bank deposit slips |
| **Cheque Return** | Bounced cheques handling |
| **Bank Transaction** | Bank transactions |

### 📈 REPORTING MODULES

#### Sales Reports
| Report | Description |
|--------|-------------|
| **Sales Book** | Complete sales register |
| **Sales Summary** | Day/period wise summary |
| **Day Sales Summary (Item-wise)** | Daily item-wise sales |
| **Sales Return Book** | Returns register |
| **Sales Stock Summary** | Stock movement summary |
| **Sales Bills Printing** | Bulk bill printing |
| **Local/Central Sale Register** | GST register |
| **Dispatch Sheet** | Delivery dispatch |
| **Sale Sheet** | Sales summary sheet |
| **Customer Visit Status** | Customer visit tracking |
| **Shortage Report** | Stock shortage analysis |

#### Purchase Reports
| Report | Description |
|--------|-------------|
| **Purchase Book** | Complete purchase register |
| **Day Purchase Summary** | Daily purchase summary |
| **Monthly Purchase Summary** | Monthly summary |
| **Party-wise Purchase** | Supplier-wise analysis |
| **Purchase Return List** | Returns register |
| **Local/Central Register** | GST purchase register |
| **Debit/Credit Note** | Notes register |
| **Short Expiry Received** | Near-expiry stock |
| **Purchase Voucher Detail** | Voucher analysis |

#### Inventory Reports
| Report | Description |
|--------|-------------|
| **Stock Ledger** | Complete stock movement |
| **Godown-wise Stock** | Location-wise stock |
| **Expiry Report** | Expiry tracking |
| **Breakage Report** | Breakage analysis |
| **Batch-wise Stock** | Batch tracking |
| **Item-wise Stock** | Item analysis |

#### Financial Reports
| Report | Description |
|--------|-------------|
| **Balance Sheet** | Financial position |
| **Profit & Loss** | Income statement |
| **Trial Balance** | Account balances |
| **Cash Flow** | Cash movement |
| **Fund Flow** | Fund movement |

#### GST Reports
| Report | Description |
|--------|-------------|
| **GSTR-1** | Outward supplies |
| **GSTR-2** | Inward supplies |
| **GSTR-3B** | Monthly return |
| **GST Set-off** | Input credit tracking |
| **HSN-wise Summary** | HSN code analysis |

#### Receipt-Payment Reports
| Report | Description |
|--------|-------------|
| **Receipt from Customer** | Customer payments |
| **Payment to Supplier** | Supplier payments |
| **Cash Collection Summary** | Cash summary |
| **Cash/Cheque Collection** | Collection details |
| **Post-dated Cheques** | PDC tracking |
| **Returned Cheques** | Bounced cheques |
| **Payment History** | Payment tracking |
| **Pay-in Slip** | Deposit details |
| **Currency Detail** | Currency-wise collection |
| **Receipt Customer Month-wise** | Monthly receipts |

#### Management Reports
| Report | Description |
|--------|-------------|
| **Customer List** | Complete customer database |
| **Customer-Supplier List** | Combined list |
| **Doctor-wise Customers** | Doctor referral analysis |
| **List of Masters** | All master data |
| **Mailing Labels** | Address labels |
| **Company-wise Discount** | Discount analysis |

#### Miscellaneous Reports
| Category | Reports |
|----------|---------|
| **Customer-wise Sale** | All-customer, Bill-wise, Company-wise, Item-wise, Quantity-wise, Area-wise, Month-wise |
| **Item-wise Sale** | All-item, Bill-wise, Customer-wise, Area-wise, Below-cost, Summary |
| **Route-wise Sale** | All-route, Area-wise |
| **Discount-wise Sale** | All-discount, Item-wise |
| **Supplier-wise Purchase** | All-supplier, Bill-wise, Invoice-item-wise, Item-wise |
| **Item-wise Purchase** | All-item, Bill-wise |
| **Company-wise Purchase** | All-company, Item-wise, Party-wise |
| **Schemed Received** | Free-received, Free-schemed, Half-schemed |

### 🔧 SYSTEM MODULES
| Module | Description |
|--------|-------------|
| **Dashboard** | Main dashboard with KPIs |
| **Audit Log** | Complete audit trail |
| **Auto Backup** | Automated backups |
| **Database Backup** | Manual backup/restore |
| **License Management** | Software licensing |
| **Personal Directory** | Contact management |
| **General Reminder** | Notifications |
| **General Notebook** | Notes/memos |
| **Page Settings** | Page configuration |
| **Branding** | White-label settings |

### 🤖 AI/OCR MODULES
| Module | Description |
|--------|-------------|
| **Receipt OCR** | Receipt scanning |
| **OCR Preview** | Image preview & editing |
| **Batch OCR** | Multiple receipts |
| **Item Matching** | AI-powered matching |

---

## 3. Module-wise Detailed SOP

*Each module SOP covers the following sections:*
- **Purpose** — What the module does
- **Access Path** — How to navigate to it
- **Pre-requisites** — What must exist before using it
- **Main Features** — Key functionalities
- **Step-by-Step Process** — Detailed usage steps
- **Validation Rules** — Field validations and constraints
- **Reports Generated** — Reports available from this module
- **Common Issues & Solutions** — Troubleshooting guide
- **Keyboard Shortcuts** — Available keyboard shortcuts

---

### 3.1 Item Master

#### Purpose
The Item Master module is the central product/medicine database for the entire billing system. It stores all details about every product — pricing, tax rates, stock levels, schemes, and configuration flags. All sale, purchase, and inventory transactions reference items from this master database.

#### Access Path
- **Menu:** Administration → Items (left sidebar)
- **Direct URL:** `/items`

#### Pre-requisites
Before adding items, ensure the following master data exists:
1. **Company Master** — At least one company/manufacturer must exist (items require a company)
2. **HSN Code Master** — HSN codes for GST tax mapping (optional but recommended)
3. **User Permission** — Logged-in user must have Item module access rights

#### Main Features

| Feature | Description |
|---------|-------------|
| **Complete Item Database** | 70+ configurable fields covering all product details |
| **Flexible Pricing** | Sale rate, MRP, wholesale rate, special rate — all configurable |
| **GST Tax Management** | CGST, SGST, IGST, CESS, and VAT percentage fields |
| **Scheme Support** | Date-based promotional schemes with plus/minus scheme values |
| **Batch Tracking** | Each item can have multiple batches with individual expiry dates |
| **Stock Monitoring** | Minimum/maximum stock level alerts |
| **Expiry Management** | Expiry flag to track near-expiry and expired stock |
| **Barcode Support** | Barcode field for scanner-based lookup |
| **Pending Orders** | Track and manage back-order/pending order quantities |
| **Stock Ledger** | Full transaction history (in/out) for every item |
| **Multi-category** | Two category fields plus division and UPC classification |
| **Soft Delete** | Deleted items are hidden but data is preserved |
| **Bulk Operations** | Select multiple items and delete in one action |
| **Advanced Search** | Search by name, barcode, location, packing, MRP, HSN, batch code |

#### Step-by-Step Process

**A. Viewing the Item List**
1. Navigate to **Administration → Items** from the left sidebar.
2. The item list page shows a table with columns: ID, Item Name, HSN Code, Packing, Company, Quantity.
3. Use the **search bar** at the top to filter items. Select the search type from the dropdown (All Fields, Item Name, BarCode, Location, Packing, MRP, Batch Code, HSN).
4. Click any item row to highlight it. Use **Arrow Up / Arrow Down** keys to navigate rows.

**B. Creating a New Item**
1. Click the **"New Item"** button (or press **F9**) on the item list page.
2. Fill in the form sections:

   **Section 1 — Basic Information**

   | Field | Description | Required |
   |-------|-------------|----------|
   | Item Name | Full product/medicine name | Yes |
   | Company | Select manufacturer/company | Yes |
   | Packing | Pack size (e.g., "10 Tab", "100ml") | No |
   | Unit | Unit type (default: 1) | No |
   | Location | Shelf/rack location in store | No |
   | Status | Active / Inactive | No |
   | Schedule | Drug schedule (H, X, etc.) | No |
   | Box Qty | Quantity per box | No |
   | Case Qty | Quantity per case | No |
   | Bar Code | Barcode for scanner lookup | No |
   | Division | Product division/category | No |
   | Min Level | Minimum stock alert level | No |
   | Max Level | Maximum stock level | No |

   **Section 2 — Sale Details**

   | Field | Description |
   |-------|-------------|
   | Sale Rate (S_Rate) | Default selling price per unit |
   | MRP | Maximum Retail Price |
   | WS Rate | Wholesale rate |
   | Special Rate | Special customer rate |
   | Scheme Plus | Free units on sale (e.g., 10+1) |
   | Scheme Minus | Discount percentage on sale |
   | Min GP | Minimum gross profit percentage |

   **Section 3 — Purchase Details**

   | Field | Description |
   |-------|-------------|
   | Purchase Rate | Cost price from supplier |
   | Cost | Landed cost |
   | Purchase Scheme Plus | Free units received from supplier |
   | Purchase Scheme Minus | Purchase discount percentage |
   | Net Rate (NR) | Net purchase rate after scheme |

   **Section 4 — GST / Tax Details**

   | Field | Description |
   |-------|-------------|
   | HSN Code | GST Harmonized System Nomenclature code |
   | CGST % | Central GST percentage |
   | SGST % | State GST percentage |
   | IGST % | Integrated GST percentage (inter-state) |
   | CESS % | Additional CESS percentage |
   | VAT % | VAT percentage (legacy use) |

   **Section 5 — Configuration Flags**

   | Flag | Values | Description |
   |------|--------|-------------|
   | Expiry Flag | Y / N | Track batch expiry dates for this item |
   | Inclusive Flag | Y / N | MRP is tax-inclusive |
   | Generic Flag | Y / N | Mark as generic medicine |
   | Lock Sale | Y / N | Prevent selling this item |
   | Narcotic Flag | Y / N | Controlled substance (schedule) |
   | Barcode Flag | Y / N | Enable barcode identification |

3. Click **Save** to create the item.

**C. Editing an Existing Item**
1. On the item list, click the **Edit** icon on the item row, or select the row and press **F3**.
2. The edit form opens with all current values pre-filled.
3. If batches exist, the latest batch rates are automatically loaded as a reference (shown in an info alert).
4. Make the required changes and click **Update**.

**D. Viewing Item Details**
1. Click the **View** (eye) icon on any item row.
2. The detail page shows all fields, the latest batch information, and quick links to:
   - Batches list
   - Stock Ledger
   - Pending Orders
   - Expiry Ledger

**E. Managing Batches**
1. On the item list, click the **Batches** icon, or press **F5** on a selected row.
2. View all batches for that item with batch code, expiry date, quantity, and rates.
3. Batches are created automatically when purchase transactions are saved — they do not need to be manually created here.

**F. Viewing Stock Ledger**
1. On the item list, click the **Stock Ledger** icon, or press **F10** on a selected row.
2. View the complete transaction history: date, transaction type (purchase/sale/adjustment), in/out quantities, and running balance.
3. For a complete ledger with amounts and values, use the **Complete Stock Ledger** option from the item detail page.

**G. Managing Pending Orders**
1. Click the **Pending Orders** icon, or press **F7** on a selected row.
2. Add, edit, or delete pending order quantities for the item.
3. Pending orders are visible in the Sale Transaction module for reference during billing.

**H. Deleting Items**
- **Single Delete:** Click the **Delete** icon on the item row, or press **Delete** after selecting a row. Confirm the prompt.
- **Bulk Delete:** Check multiple items using checkboxes (press **Enter** to toggle selection on a highlighted row), then press **F8** or click "Delete Selected".
- **Note:** Deletion uses soft delete — the item is hidden (`is_deleted = 1`) but its data and transaction history are preserved.

#### Validation Rules

| Field | Rule |
|-------|------|
| Item Name | Required, maximum 255 characters |
| Company | Required, must exist in Company Master |
| HSN Code | Optional, maximum 20 characters |
| Bar Code | Optional, maximum 50 characters |
| Sale Rate, MRP, Purchase Rate, etc. | Numeric, accepts decimals, defaults to 0 if left blank |
| CGST %, SGST %, IGST %, CESS %, VAT % | Numeric decimal, typically 0–28 for GST |
| Min Level, Max Level | Decimal values |
| Box Qty, Case Qty | Integer values |

**System Default Values Applied on Save:**

| Field | Default |
|-------|---------|
| is_deleted | 0 (active) |
| unit | 1 |
| locks_flag | N |
| narcotic_flag | N |
| expiry_flag | N |
| inclusive_flag | N |
| generic_flag | N |
| lock_sale_flag | N |
| current_scheme_flag | N |
| max_min_flag | 1 |

#### Reports Generated

| Report | Location | Description |
|--------|----------|-------------|
| **Item List** | Reports → Inventory | Complete item list with all details, PDF printable |
| **Item List – Tax/MRP Range** | Reports → Inventory | Items grouped by tax slab and MRP range |
| **Margin-wise Items** | Reports → Inventory | Items grouped by profit margin percentage |
| **Vat-wise Items** | Reports → Inventory | Items grouped by VAT percentage |
| **Item List with Salts** | Reports → Inventory | Items with salt/composition information |
| **Min/Max Level Items** | Reports → Inventory | Items below minimum or above maximum stock level |
| **Non-Moving Items** | Reports → Management | Items with no sales movement in the selected period |
| **Slow-Moving Items** | Reports → Management | Items with low sale velocity |
| **Expiry Report** | Reports → Inventory | Items nearing expiry or already expired |
| **Stock Ledger** | Item → Stock Ledger | Transaction-wise in/out history per item |
| **Label Generation** | Reports → Labels | Barcode and price labels for printing |
| **Item-wise Sale Analysis** | Reports → Sale | Sale analysis per item (bill-wise, customer-wise, area-wise, month-wise) |
| **Item-wise Purchase Analysis** | Reports → Purchase | Purchase analysis per item (bill-wise, supplier-wise) |
| **Item Ledger Printing** | Reports → Inventory | Complete date-range transaction history for an item |

#### Common Issues & Solutions

| Issue | Likely Cause | Solution |
|-------|-------------|----------|
| Item not found in sale/purchase search | Item Status is Inactive | Edit item → set Status to **Active** |
| Item not searchable by barcode | Barcode field is empty or Barcode Flag is N | Edit item → enter the barcode and set **Barcode Flag = Y** |
| Wrong GST rate applied on invoice | CGST/SGST/IGST percentages are incorrect | Edit item → update the GST percentage fields correctly |
| Sale rate not auto-filling during billing | Sale Rate and MRP are both 0 | Edit item → set Sale Rate and MRP; or ensure latest batch has rates |
| Cannot delete an item | Item has linked transactions in the system | Items can only be soft-deleted; contact admin if a hard delete is needed |
| Expiry dates not being tracked | Expiry Flag is N | Edit item → set **Expiry Flag = Y** |
| Item appears under wrong company | Wrong company selected during creation | Edit item → re-select the correct company |
| Promotional scheme not applying on sale | current_scheme_flag is N, or scheme date range has expired | Edit item → enable **Current Scheme Flag** and verify From/To dates |
| Stock showing 0 after purchase entry | A different item was selected in the purchase transaction | Open the purchase entry → verify the correct item was selected |
| Batch rates not loading on edit form | No purchase transactions exist for this item yet | Enter the rates manually in the edit form fields |

#### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| **F9** | Create a new item |
| **F3** | Edit the selected item |
| **Delete** | Delete the selected item |
| **F8** | Bulk delete all checked items |
| **F5** | View batches for the selected item |
| **F10** | View stock ledger for the selected item |
| **F7** | View pending orders for the selected item |
| **Arrow Up** | Move highlight up in the item list |
| **Arrow Down** | Move highlight down in the item list |
| **Enter** | Toggle checkbox selection on the highlighted row |
| **ESC** | Go back / exit current screen |

---

#### Screenshots Reference

| Figure No. | Section | Description |
|------------|---------|-------------|
| **Figure 1.1** | Module Overview | Item Master: Full List Page (Index View) |
| **Figure 1.2** | Item List & Navigation | Item List: Table View with Columns (ID, Name, HSN, Packing, Company, Quantity) |
| **Figure 1.3** | Item List & Navigation | Item List: Search Bar with Filter Type Dropdown |
| **Figure 1.4** | Item List & Navigation | Item List: Row Highlight and Keyboard Navigation |
| **Figure 1.5** | Item List & Navigation | Item List: Action Icons (Edit, Delete, Batches, Stock Ledger, Pending Orders, View) |
| **Figure 1.6** | Creating an Item | New Item Form: Section 1 – Basic Information |
| **Figure 1.7** | Creating an Item | New Item Form: Section 2 – Sale Details |
| **Figure 1.8** | Creating an Item | New Item Form: Section 3 – Purchase Details |
| **Figure 1.9** | Creating an Item | New Item Form: Section 4 – GST / Tax Details |
| **Figure 1.10** | Creating an Item | New Item Form: Section 5 – Configuration Flags |
| **Figure 1.11** | Editing an Item | Edit Item Form: Batch Rates Auto-Load Reference (Info Alert) |
| **Figure 1.12** | Item Detail View | Item Detail Page: All Fields & Quick Links |
| **Figure 1.13** | Batch Management | Item Batches List: Batch Code, Expiry Date, Quantity & Rates |
| **Figure 1.14** | Stock Ledger | Stock Ledger: Transaction History (Date, Type, In/Out, Balance) |
| **Figure 1.15** | Stock Ledger | Complete Stock Ledger: Full View with Amounts |
| **Figure 1.16** | Pending Orders | Pending Orders Page: Add / Edit / Delete Order Quantities |
| **Figure 1.17** | Bulk Operations | Bulk Delete: Checkbox Selection & Delete Selected Action |

---

### 3.2 Company Master

#### Purpose
The Company Master module manages the database of all manufacturers, pharmaceutical companies, and brands whose products are stocked and sold. Every item in the system must be linked to a company. Company records hold contact details, financial configurations (surcharge, discount, tax settings), and business flags that influence how transactions are processed for that company's products.

#### Access Path
- **Menu:** Companies (left sidebar)
- **Direct URL:** `/companies`
- **Global Shortcut:** `Ctrl + Shift + C` from anywhere in the application

> **Figure 2.1 — Company Master: Module Overview (List Page)**

---

#### Pre-requisites
The Company Master has no hard dependencies — it is one of the first modules to be set up. However, ensure the following before adding companies:
1. **Organization Settings** — Organization must be configured (for multi-tenancy)
2. **User Permission** — Logged-in user must have Company module access rights

---

#### Main Features

| Feature | Description |
|---------|-------------|
| **Complete Company Database** | 50+ configurable fields covering identity, contact, and financial settings |
| **Unique Contact Enforcement** | Email, telephone, mobile_1, and mobile_2 are unique across all companies |
| **Financial Configuration** | Per-company surcharge, discount, minimum GP, VAT, and tax settings |
| **Business Flags** | Direct/Indirect supply, Fixed/Maximum discount, AIOCD/IMS locking |
| **Expiry Control** | Per-company expiry tracking flag and disallow-after-months setting |
| **Generic Medicine Flag** | Mark company as generic-only supplier |
| **Advanced Search** | Search by alter code, name, telephone, address, mobile |
| **Bulk Operations** | Select and delete multiple companies at once |
| **AJAX Lookup** | Real-time company lookup by alter code, ID, or short name for transaction screens |
| **Soft Delete** | Companies are hidden on deletion; data and linked records are preserved |
| **Status Management** | Active/Inactive status control |
| **Notes** | Free-text notes field for internal remarks about the company |

> **Figure 2.2 — Company List: Table View with Search & Filter Panel**

---

#### Step-by-Step Process

**A. Viewing the Company List**
1. Navigate to **Companies** from the left sidebar, or press **Ctrl + Shift + C**.
2. The list page displays companies in a table. Use the **search bar** to filter by field type (Alter Code, Name, Telephone, Address, Mobile).
3. Use the **Status filter** to show Active, Inactive, or All companies.
4. Use the **Date Range filter** to find companies added in a specific period.
5. Click any company row to highlight it. Navigate with **Arrow Up / Arrow Down** keys.

> **Figure 2.3 — Company List: Search Filters (Field-wise, Status, Date Range)**

---

**B. Creating a New Company**
1. Click the **"New Company"** button on the list page.
2. Fill in the form sections:

   **Section 1 — Basic Information**

   | Field | Description | Required |
   |-------|-------------|----------|
   | Company Name | Full legal/brand name | Yes |
   | Short Name | Abbreviated name (used in item lists) | No |
   | Alter Code | Unique alternate code for quick lookup | No |
   | Address | Company postal address | Yes |
   | Location | City / area | No |
   | Status | Active / Inactive | No |
   | Notes | Internal remarks | No |

   > **Figure 2.4 — New Company Form: Section 1 – Basic Information**

   **Section 2 — Contact Information**

   | Field | Description | Required |
   |-------|-------------|----------|
   | Email | Company email (must be unique) | Yes |
   | Telephone | Office telephone (unique) | No |
   | Mobile 1 | Primary mobile number (unique) | No |
   | Mobile 2 | Secondary mobile number (unique) | No |
   | Contact Person 1 | Primary contact name | No |
   | Contact Person 2 | Secondary contact name | No |
   | Website | Company website URL | No |

   > **Figure 2.5 — New Company Form: Section 2 – Contact Information**

   **Section 3 — Financial Configuration**

   | Field | Description |
   |-------|-------------|
   | Purchase Surcharge (pur_sc) | Surcharge applied on purchases from this company |
   | Sale Surcharge (sale_sc) | Surcharge applied on sales of this company's items |
   | Discount % on Sale | Default discount percentage on sale |
   | Minimum GP % | Minimum gross profit percentage enforced |
   | Purchase Tax | Tax rate applied on purchase |
   | Sale Tax | Tax rate applied on sale |
   | VAT % | VAT percentage |
   | Disallow Expiry After (Months) | Block items expiring within this many months |

   > **Figure 2.6 — New Company Form: Section 3 – Financial Configuration**

   **Section 4 — Business Flags**

   | Flag | Values | Description |
   |------|--------|-------------|
   | Expiry | Y / N | Track expiry dates for this company's items |
   | Generic | Y / N | Company supplies generic medicines only |
   | Direct / Indirect | D / I | Supply chain type (Direct from manufacturer or Indirect via distributor) |
   | Fixed / Maximum | F / M | Discount type — Fixed amount or Maximum ceiling |
   | Surcharge After Discount | Y / N | Apply surcharge after discount is calculated |
   | Add Surcharge | Y / N | Add surcharge to the bill |
   | Inclusive | Y / N | Prices are tax-inclusive |
   | Lock AIOCD | Y / N | Lock AIOCD (All India Origin Chemists & Druggists) compliance flag |
   | Lock IMS | Y / N | Lock IMS (Intercontinental Marketing Services) tracking flag |
   | Invoice Print Order | — | Defines print ordering for invoices |

   > **Figure 2.7 — New Company Form: Section 4 – Business Flags**

3. Click **Save** to create the company.

---

**C. Editing an Existing Company**
1. Click the **Edit** icon on the company row, or select the row and press **F3**.
2. The edit form opens with all current values pre-filled.
3. Make the required changes and click **Update**.

> **Figure 2.8 — Edit Company Form: Pre-filled Values**

---

**D. Viewing Company Details**
1. Click the **View** (eye) icon on any company row.
2. The detail page displays all fields organized in sections.
3. From this page, you can navigate to all items belonging to this company.

> **Figure 2.9 — Company Detail / View Page**

---

**E. Deleting Companies**
- **Single Delete:** Click the **Delete** icon on the company row. Confirm the prompt.
- **Bulk Delete:** Check multiple companies using checkboxes (press **Enter** to toggle), then click **Delete Selected**.
- **Note:** A company that has items linked to it **cannot be deleted** — you must first reassign or delete those items.
- Deletion uses soft delete — data is preserved with `is_deleted = 1`.

> **Figure 2.10 — Bulk Delete: Checkbox Selection in Company List**

---

#### Validation Rules

| Field | Rule |
|-------|------|
| Company Name | Required, maximum 255 characters |
| Email | Required, valid email format, must be unique across all companies |
| Telephone | Optional, must be unique if provided |
| Mobile 1 | Optional, must be unique if provided |
| Mobile 2 | Optional, must be unique if provided |
| Generic | Optional, accepted values: Y / N |
| Expiry | Optional, accepted values: Y / N |
| Lock AIOCD | Optional, accepted values: Y / N |
| Lock IMS | Optional, accepted values: Y / N |
| Surcharge After Discount | Optional, accepted values: Y / N |
| Add Surcharge | Optional, accepted values: Y / N |
| Inclusive | Optional, accepted values: Y / N |
| Direct / Indirect | Optional, accepted values: D / I |
| Fixed / Maximum | Optional, accepted values: F / M |
| Status | Optional, maximum 5 characters |
| Notes | Optional, free text |

**System Default Values Applied on Save:**

| Field | Default |
|-------|---------|
| discount | 0.00 (locked, cannot be changed) |
| direct_indirect | D (Direct) |
| fixed_maximum | F (Fixed) |
| lock_aiocd | N |
| lock_ims | N |
| is_deleted | 0 (active) |

---

#### Reports Generated

| Report | Location | Description |
|--------|----------|-------------|
| **Company-wise Item List** | Reports → Inventory | All items grouped by company/manufacturer |
| **Company-wise Purchase** | Reports → Purchase | Purchase analysis grouped by company |
| **Company-wise Purchase (Item-wise)** | Reports → Purchase | Item-level breakdown per company purchase |
| **Company-wise Purchase (Party-wise)** | Reports → Purchase | Supplier-wise breakdown per company |
| **Company-wise Discount** | Reports → Management | Discount analysis per company |
| **Company-wise Sale** | Reports → Sale | Sale performance per manufacturer company |
| **GST Reports (Company filter)** | Reports → GST | GST reports filterable by company |
| **Expiry Report (Company filter)** | Reports → Inventory | Expiry tracking filtered by company |
| **Breakage Report (Company filter)** | Reports → Inventory | Breakage analysis filtered by company |
| **Margin-wise Items** | Reports → Inventory | Margin analysis per company's items |

---

#### Common Issues & Solutions

| Issue | Likely Cause | Solution |
|-------|-------------|----------|
| Duplicate email/telephone error on save | Another company already uses that email or number | Use a unique email/telephone; check existing records |
| Company not appearing in item creation dropdown | Company Status is Inactive | Edit company → set Status to **Active** |
| Cannot delete a company | Company has items linked to it | Reassign or delete all items under this company first |
| Surcharge not applying on sale | `add_surcharge_yn` flag is N | Edit company → set **Add Surcharge = Y** |
| Discount not changing from 0 | Discount field is system-locked to 0.00 | This is by design; discount is controlled at item level |
| AIOCD / IMS compliance flag not saving | Value not in accepted Y/N format | Ensure the toggle/checkbox is properly selected on the form |
| Company not found in transaction screen | Company has wrong alter_code or no alter_code | Edit company → add a unique, memorable Alter Code |
| Expiry not tracked for company's items | Expiry flag is N at company level | Edit company → set **Expiry = Y**, then also set Expiry Flag on each item |
| Generic medicines not filtering correctly | Generic flag not set | Edit company → set **Generic = Y** for generic-only suppliers |

---

#### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| **Ctrl + Shift + C** | Open Company Master from anywhere in the application |
| **F9** | Create a new company |
| **F3** | Edit the selected company |
| **Delete** | Delete the selected company |
| **F8** | Bulk delete all checked companies |
| **Arrow Up** | Move highlight up in the company list |
| **Arrow Down** | Move highlight down in the company list |
| **Enter** | Toggle checkbox selection on the highlighted row |
| **ESC** | Go back / exit current screen |

---

#### Screenshots Reference

| Figure No. | Section | Description |
|------------|---------|-------------|
| **Figure 2.1** | Module Overview | Company Master: Full List Page (Index View) |
| **Figure 2.2** | Company List & Navigation | Company List: Table View with Search & Filter Panel |
| **Figure 2.3** | Company List & Navigation | Company List: Search Filters (Field-wise, Status, Date Range) |
| **Figure 2.4** | Creating a Company | New Company Form: Section 1 – Basic Information |
| **Figure 2.5** | Creating a Company | New Company Form: Section 2 – Contact Information |
| **Figure 2.6** | Creating a Company | New Company Form: Section 3 – Financial Configuration |
| **Figure 2.7** | Creating a Company | New Company Form: Section 4 – Business Flags |
| **Figure 2.8** | Editing a Company | Edit Company Form: Pre-filled Values |
| **Figure 2.9** | Company Detail View | Company Detail / View Page: All Fields |
| **Figure 2.10** | Bulk Operations | Bulk Delete: Checkbox Selection in Company List |

---

### 3.3 Item Category

#### Purpose
The Item Category module allows you to organize and classify inventory items into logical groups (e.g., Tablets, Syrups, Surgical, FMCG). Each item can be assigned a category, enabling category-wise stock analysis, reporting, and filtering across the system.

#### Access Path
- **Menu:** Administration → Item Category (left sidebar)
- **Direct URL:** `/item-category`
- **Global Shortcut:** `Alt + C` from anywhere in the application

> **Figure 3.1 — Item Category: Module Overview (List Page)**

---

#### Pre-requisites
No dependencies. Item Category is a standalone master that should be set up before items are created, so categories are available during item creation.

---

#### Main Features

| Feature | Description |
|---------|-------------|
| **Category Database** | Create and manage product categories for classification |
| **Alter Code** | Short alternate code for quick lookup and reference |
| **Status Control** | Active/Inactive status per category |
| **Bulk Delete** | Select and delete multiple categories at once |
| **Infinite Scroll** | List loads more records as you scroll (10 per page) |
| **Search** | Search by Name, Alter Code, or Status |
| **Category-wise Reports** | Filter stock and valuation reports by category |

> **Figure 3.2 — Item Category List: Table View with Search**

---

#### Step-by-Step Process

**A. Viewing the Category List**
1. Navigate to **Administration → Item Category** from the left sidebar.
2. The list shows columns: #, Name, Alter Code, Status, Actions.
3. Use the search bar to filter by Name, Alter Code, or Status.
4. Scroll down to load more categories (infinite scroll).

> **Figure 3.3 — Item Category List: Search & Filter Bar**

**B. Creating a New Category**
1. Click the **"New Category"** button (or press **F9**).
2. Fill in the fields:

   | Field | Description | Required |
   |-------|-------------|----------|
   | Name | Category name (e.g., Tablets, Syrups) | No |
   | Alter Code | Short code for quick reference | No |
   | Status | Active / Inactive | No |

3. Click **Save**.

> **Figure 3.4 — New Category Form**

**C. Editing a Category**
1. Click the **Edit** icon on a category row, or select and press **F3**.
2. Update the fields and click **Update**.

**D. Deleting Categories**
- **Single:** Click the **Delete** icon on the row.
- **Bulk:** Select multiple rows (checkbox / Enter key) and click **Delete Selected** or press **F8**.

> **Figure 3.5 — Bulk Delete: Category Checkbox Selection**

---

#### Validation Rules

| Field | Rule |
|-------|------|
| Name | Optional, maximum 255 characters |
| Alter Code | Optional, maximum 255 characters |
| Status | Optional, maximum 255 characters |

> Note: No field is strictly required — however, Name should always be filled for meaningful categorization.

---

#### Reports Generated

| Report | Location | Description |
|--------|----------|-------------|
| **Category-wise Stock Status** | Reports → Inventory | Stock quantities and values grouped by item category |
| **Category-wise Valuation Closing Stock** | Reports → Inventory | Closing stock valuation per category |

---

#### Common Issues & Solutions

| Issue | Likely Cause | Solution |
|-------|-------------|----------|
| Category not appearing in item form dropdown | Category Status is Inactive | Edit category → set Status to **Active** |
| Category cannot be deleted | Category is linked to active items | Reassign items to another category first |
| Category-wise report showing no data | Items not assigned to categories | Edit items → assign the correct category |

---

#### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| **Alt + C** | Open Item Category Master from anywhere |
| **F9** | Create a new category |
| **F3** | Edit the selected category |
| **Delete** | Delete the selected category |
| **F8** | Bulk delete all checked categories |
| **Arrow Up / Down** | Navigate rows |
| **Enter** | Toggle checkbox on highlighted row |
| **ESC** | Go back / exit |

---

#### Screenshots Reference

| Figure No. | Section | Description |
|------------|---------|-------------|
| **Figure 3.1** | Module Overview | Item Category: Full List Page (Index View) |
| **Figure 3.2** | Category List | Category List: Table View with Columns |
| **Figure 3.3** | Category List | Category List: Search & Filter Bar |
| **Figure 3.4** | Creating a Category | New Category Form: Name, Alter Code, Status |
| **Figure 3.5** | Bulk Operations | Bulk Delete: Checkbox Selection in Category List |

---

### 3.4 HSN Code Master

#### Purpose
The HSN Code Master stores Harmonized System Nomenclature (HSN) codes along with their associated GST tax rates (CGST, SGST, IGST). When an item is linked to an HSN code, the correct tax percentages are automatically applied during billing. This module is essential for GST compliance and accurate tax calculation.

#### Access Path
- **Menu:** HSN Master (left sidebar)
- **Direct URL:** `/hsn-codes`
- **Global Shortcut:** `Ctrl + H` from anywhere in the application

> **Figure 4.1 — HSN Code Master: Module Overview (List Page)**

---

#### Pre-requisites
No dependencies. HSN Code Master should be configured before creating items, so tax rates are available for assignment.

Standard GST tax structures used in India:
- **0% GST:** CGST 0% + SGST 0% + IGST 0%
- **5% GST:** CGST 2.5% + SGST 2.5% + IGST 5%
- **12% GST:** CGST 6% + SGST 6% + IGST 12%
- **18% GST:** CGST 9% + SGST 9% + IGST 18%

---

#### Main Features

| Feature | Description |
|---------|-------------|
| **HSN Code Database** | Store HSN codes with description and all GST rate components |
| **CGST / SGST / IGST** | Individual tax component percentages per HSN code |
| **Total GST %** | Computed total GST percentage |
| **Service Flag** | Mark HSN codes applicable to services (shown as badge in list) |
| **Active / Inactive Status** | Enable or disable HSN codes |
| **Bulk Delete** | Select and delete multiple HSN codes |
| **AJAX Lookup** | HSN codes auto-fetched in Sale Voucher screens |
| **Search** | Search by Name and HSN Code number |
| **GST Reports Integration** | GSTR-1, HSN-wise sale/purchase reports |

> **Figure 4.2 — HSN Code List: Table with GST Rate Columns**

---

#### Step-by-Step Process

**A. Viewing the HSN Code List**
1. Navigate to **HSN Master** from the left sidebar, or press **Ctrl + H**.
2. The list shows: #, Name (with Service badge), HSN Code, CGST%, SGST%, IGST%, Total GST%, Status, Actions.
3. Use the search bar to filter by Name or HSN Code.
4. Use the **Status filter** to show Active, Inactive, or All records.
5. Use **Page Jump** to navigate to a specific page (15 records per page).

> **Figure 4.3 — HSN Code List: Search, Status Filter & Page Jump**

**B. Creating a New HSN Code**
1. Click the **"New HSN Code"** button (or press **F9**).
2. Fill in the form:

   | Field | Description | Required |
   |-------|-------------|----------|
   | Name | HSN description (e.g., "Medicaments - 5% GST") | Yes |
   | HSN Code | The numeric HSN code (e.g., 30049099) | No |
   | CGST % | Central GST rate (e.g., 2.5 for 5% total) | No |
   | SGST % | State GST rate (e.g., 2.5 for 5% total) | No |
   | IGST % | Integrated GST rate (e.g., 5 for inter-state) | No |
   | Total GST % | Total combined GST (e.g., 5) | No |
   | Inactive | Check to mark this code as inactive | No |
   | Service | Check if this HSN applies to services | No |

3. Click **Save**.

> **Figure 4.4 — New HSN Code Form: Tax Rate Fields**

**C. Editing an HSN Code**
1. Click the **Edit** icon on any row, or select and press **F3**.
2. Update the fields and click **Update**.

> **Figure 4.5 — Edit HSN Code Form: Pre-filled Values**

**D. Deleting HSN Codes**
- **Single:** Click the **Delete** icon.
- **Bulk:** Select rows using checkboxes and click **Delete Selected** or press **F8**.

> **Figure 4.6 — Bulk Delete: HSN Code Checkbox Selection**

---

#### Validation Rules

| Field | Rule |
|-------|------|
| Name | Required, maximum 255 characters |
| HSN Code | Optional, maximum 255 characters |
| CGST % | Optional, numeric, 0–100 |
| SGST % | Optional, numeric, 0–100 |
| IGST % | Optional, numeric, 0–100 |
| Total GST % | Optional, numeric, 0–100 |
| Inactive | Optional, boolean |
| Service | Optional, boolean |

---

#### Reports Generated

| Report | Location | Description |
|--------|----------|-------------|
| **HSN-wise Sale Purchase Report** | Reports → GST | Sale and purchase data grouped and filtered by HSN code |
| **GSTR-1** | Reports → GST | GST return report including HSN code columns |
| **Stock Transfer Reports** | Reports → Inventory | Transfer summaries including HSN codes |
| **List of Masters** | Reports → Management | Lists all HSN codes with their tax rates |

---

#### Common Issues & Solutions

| Issue | Likely Cause | Solution |
|-------|-------------|----------|
| Wrong GST rate applying on invoice | Item linked to incorrect HSN code | Edit the item → reassign to the correct HSN code |
| HSN code not found in item form | HSN code is marked Inactive | Edit HSN code → uncheck **Inactive** |
| IGST not applying on inter-state sale | IGST % is 0 in HSN code | Edit HSN code → set the correct IGST percentage |
| Total GST % not matching components | Total GST was entered incorrectly | Total GST % = CGST % + SGST % (intra-state) or = IGST % (inter-state) |
| Service HSN not showing badge | Service checkbox not checked | Edit HSN code → check the **Service** checkbox |

---

#### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| **Ctrl + H** | Open HSN Code Master from anywhere |
| **F9** | Create a new HSN code |
| **F3** | Edit the selected HSN code |
| **Delete** | Delete the selected HSN code |
| **F8** | Bulk delete all checked HSN codes |
| **Arrow Up / Down** | Navigate rows |
| **Enter** | Toggle checkbox on highlighted row |
| **ESC** | Go back / exit |

---

#### Screenshots Reference

| Figure No. | Section | Description |
|------------|---------|-------------|
| **Figure 4.1** | Module Overview | HSN Code Master: Full List Page (Index View) |
| **Figure 4.2** | HSN Code List | HSN Code List: Table with CGST, SGST, IGST, Total GST Columns |
| **Figure 4.3** | HSN Code List | HSN Code List: Search Bar, Status Filter & Page Jump |
| **Figure 4.4** | Creating HSN Code | New HSN Code Form: Name, Code, and Tax Rate Fields |
| **Figure 4.5** | Editing HSN Code | Edit HSN Code Form: Pre-filled Values |
| **Figure 4.6** | Bulk Operations | Bulk Delete: Checkbox Selection in HSN Code List |

---

### 3.5 Batch Master

#### Purpose
The Batch Master stores all inventory batch records. Every purchase transaction creates one or more batches — each batch holds a unique batch number, expiry date, quantity, purchase rate, sale rate, MRP, and full tax details. The Batch Master is the live inventory engine of the system — all stock levels, expiry tracking, and rate lookups during billing are sourced from batch records.

#### Access Path
- **Menu:** Administration → Batches (left sidebar)
- **Direct URL:** `/batches`
- **Via Item:** Item List → Batches icon (F5) → item-specific batch list

> **Figure 5.1 — Batch Master: Module Overview (List Page)**

---

#### Pre-requisites
1. **Item Master** — Items must exist before batches can be created
2. **Purchase Transaction** — Batches are created automatically when a purchase bill is saved. They are not typically created manually.

> **Note:** Do not create batches manually unless correcting data. Always create batches through Purchase Transactions.

---

#### Main Features

| Feature | Description |
|---------|-------------|
| **Automatic Batch Creation** | Batches are auto-generated from purchase transactions |
| **Complete Rate Storage** | Stores purchase rate, MRP, sale rate, wholesale rate, special rate, net rate |
| **Full Tax Details** | CGST %, SGST %, CESS % and computed tax amounts per batch |
| **Expiry Tracking** | Expiry date and manufacturing date per batch |
| **Quantity Management** | Tracks qty, free qty, and total qty separately |
| **Godown Assignment** | Each batch is assigned to a godown/location |
| **Hold / Breakage / Expiry Status** | Batches can be flagged as Hold (H), Breakage (B), or Expiry (E) |
| **Stock Ledger per Batch** | Full in/out transaction history per batch |
| **AJAX Lookup** | Batches are fetched in real-time during sale/purchase entry |
| **Expiry Report** | View batches expiring within a specified number of days |
| **All Batches View** | View all batches including those with zero stock |

> **Figure 5.2 — Batch List: Grouped by Item with Batch Details**

---

#### Step-by-Step Process

**A. Viewing Batches**
1. Navigate to **Administration → Batches** from the sidebar.
2. The default view shows **available batches** (quantity > 0), grouped by item.
3. Columns shown: Batch No, Expiry Date, Qty, Sale Rate, Net Rate, MRP, Company.
4. To view ALL batches including zero-stock: navigate to `/batches/all-batches/view`.
5. To view batches for a specific item: navigate from **Item List → F5 (Batches)**.

> **Figure 5.3 — Batch List: Columns (Batch No, Expiry, Qty, Rates, MRP)**

**B. Editing a Batch**
1. Click the **Edit** icon on any batch row, or select and press **F3**.
2. The edit form shows:

   | Field | Description |
   |-------|-------------|
   | Batch No | Batch number (required) |
   | Qty | Current quantity (required) |
   | Total Qty | Total quantity including free (required) |
   | BC (Bonus Credit) | Y / N |
   | Bill Date | Original purchase bill date |
   | Expiry Date | Expiry month/year (MM/YYYY) |
   | Manufacturing Date | Mfg month/year (MM/YYYY) |
   | Purchase Rate | Cost price (required) |
   | Sale Rate | Selling rate (required) |
   | MRP | Maximum Retail Price (required) |
   | WS Rate | Wholesale rate |
   | Special Rate | Special customer rate |
   | Net Rate | Net rate after discount/scheme |
   | Discount % | Discount percentage (max 100) |
   | SC Amount | Surcharge amount |
   | GST Points | GST points value |
   | Rate Diff | Rate difference |
   | Cost / Cost with Qty | Cost calculations |
   | Hold/Breakage/Expiry | H (Hold) / B (Breakage) / E (Expiry) |

3. Click **Update** to save changes.

> **Figure 5.4 — Edit Batch Form: All Rate & Date Fields**

**C. Viewing Batch Stock Ledger**
1. On the batch list, click the **Stock Ledger** icon on any batch.
2. View all in/out transactions for that specific batch: date, type, qty, balance.

> **Figure 5.5 — Batch Stock Ledger: Transaction History**

**D. Viewing Expiry Report**
1. Navigate to `/batches/expiry/report`.
2. Enter the number of days to check (e.g., 90 for batches expiring within 90 days).
3. View all batches with their expiry dates and current quantities.

> **Figure 5.6 — Batch Expiry Report: Expiring Within N Days**

---

#### Validation Rules

**On Edit/Update:**

| Field | Rule |
|-------|------|
| Batch No | Required, maximum 100 characters |
| Qty | Required, numeric, minimum 0 |
| Purchase Rate | Required, numeric, minimum 0 |
| MRP | Required, numeric, minimum 0 |
| Sale Rate | Required, numeric, minimum 0 |
| Expiry Date | Optional, date format |
| Manufacturing Date | Optional, date format |
| BC | Optional, Y / N |
| Discount % | Optional, numeric, 0–100 |
| Hold/Breakage/Expiry | Optional, H / B / E |
| WS Rate, Special Rate, Net Rate | Optional, numeric, minimum 0 |

---

#### Reports Generated

| Report | Location | Description |
|--------|----------|-------------|
| **Batch-wise Stock Report** | Reports → Inventory | Stock quantities organized by batch number |
| **Item Search by Batch** | Reports → Inventory | Search and find items by batch code |
| **List of Hold Batches** | Reports → Inventory | All batches currently marked as Hold |
| **Expiry Report** | Batches → Expiry Report | Batches expiring within N days |
| **Label Generation from Batches** | Reports → Labels | Generate price/barcode labels from batch data |

---

#### Common Issues & Solutions

| Issue | Likely Cause | Solution |
|-------|-------------|----------|
| Batch not appearing during sale entry | Batch qty is 0 or batch is marked Hold/Breakage/Expiry | Check batch status and quantity; release Hold if required |
| Wrong rate auto-filling in sale | Old batch has different rates from latest purchase | Edit the latest batch → update rates to match current purchase |
| Expiry date not showing correctly | Date format entered incorrectly during purchase | Edit batch → correct the expiry date (MM/YYYY) |
| Batch stock not reducing after sale | Sale transaction not properly saved | Check sale transaction; verify batch was linked correctly |
| Stock ledger showing wrong balance | Manual batch edit changed qty directly | Avoid editing qty directly; use stock adjustments instead |
| Free qty not counted in total | free_qty and total_qty not updated | Edit batch → verify total_qty = qty + free_qty |

---

#### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| **F5** | View batches for selected item (from Item List) |
| **F3** | Edit the selected batch |
| **Arrow Up / Down** | Navigate batch rows |
| **ESC** | Go back / exit |

---

#### Screenshots Reference

| Figure No. | Section | Description |
|------------|---------|-------------|
| **Figure 5.1** | Module Overview | Batch Master: Full List Page (Index View) |
| **Figure 5.2** | Batch List | Batch List: Grouped by Item with Batch Details |
| **Figure 5.3** | Batch List | Batch List: Columns (Batch No, Expiry, Qty, Sale Rate, MRP) |
| **Figure 5.4** | Editing a Batch | Edit Batch Form: All Rate, Date & Status Fields |
| **Figure 5.5** | Stock Ledger | Batch Stock Ledger: In/Out Transaction History |
| **Figure 5.6** | Expiry Report | Batch Expiry Report: Batches Expiring Within N Days |

---

### 3.6 Supplier Master

#### Purpose
The Supplier Master module manages the database of all suppliers, distributors, and stockists from whom products are purchased. Each supplier record stores contact details, financial configuration, compliance documents (GST, DL, PAN), bank details, and transaction settings. All purchase transactions are linked to suppliers, and the system tracks outstanding payments, dues, and pending orders per supplier.

#### Access Path
- **Menu:** Suppliers (left sidebar)
- **Direct URL:** `/suppliers`
- **Global Shortcut:** `Ctrl + F9` from anywhere in the application

> **Figure 6.1 — Supplier Master: Module Overview (List Page)**

---

#### Pre-requisites
No hard dependencies. However, having **State Master** and **Area Master** set up helps when assigning supplier state codes and territories.

---

#### Main Features

| Feature | Description |
|---------|-------------|
| **Complete Supplier Database** | 51 configurable fields covering identity, contact, compliance, and financials |
| **Compliance Document Storage** | GST No, PAN, DL No, TAN No, MSME License, Food License, CST, TIN |
| **Financial Configuration** | Opening balance, credit limit, invoice rounding, scheme type |
| **Bank Details** | Bank name, branch, account number, IFSC code |
| **Tax Settings** | TDS, TCS, net rate, discount settings, scheme configuration |
| **Pending Orders** | Create and track purchase orders per supplier |
| **Supplier Ledger** | View all purchase and payment transactions for a supplier |
| **Dues Tracking** | View outstanding payable amounts per supplier |
| **Bills View** | View all purchase invoices from a supplier |
| **Advanced Search** | Search by name, code, mobile, telephone, address, DL No, GST No, email |
| **Bulk Delete** | Select and delete multiple suppliers |
| **Soft Delete** | Data preserved on deletion |

> **Figure 6.2 — Supplier List: Table View with Search & Filter Panel**

---

#### Step-by-Step Process

**A. Viewing the Supplier List**
1. Navigate to **Suppliers** from the left sidebar, or press **Ctrl + F9**.
2. The list displays suppliers with columns: Name, Code, Mobile, Telephone, Status, Actions.
3. Use the **search bar** to filter by Name, Code, Mobile, Telephone, Address, DL No, GST No, Email.
4. Use the **Status filter** (Active / Inactive / All) and **Date Range filter**.
5. Click action icons to access: View, Edit, Delete, Ledger, Dues, Bills, Pending Orders.

> **Figure 6.3 — Supplier List: Search Filters & Action Icons**

**B. Creating a New Supplier**
1. Click the **"New Supplier"** button (or press **F9**).
2. Fill in the form sections:

   **Section 1 — Basic Information**

   | Field | Description | Required |
   |-------|-------------|----------|
   | Name | Full supplier/firm name | Yes |
   | Code | Supplier code for quick lookup | No |
   | Address | Supplier address | Yes |
   | Status | Active / Inactive | No |
   | Flag | Custom flag | No |
   | T/R Flag | Tax / Retail classification | No |
   | TAN No | Tax Deduction Account Number | No |
   | MSME License | MSME registration number | No |

   > **Figure 6.4 — New Supplier Form: Section 1 – Basic Information**

   **Section 2 — Contact Information**

   | Field | Description | Required |
   |-------|-------------|----------|
   | Telephone | Office telephone (unique) | Yes |
   | Email | Email address (unique) | Yes |
   | Mobile | Primary mobile (unique) | No |
   | Mobile Additional | Secondary mobile (unique) | No |
   | Contact Person 1 | Primary contact name | No |
   | Contact Person 2 | Secondary contact name | No |
   | Fax | Fax number | No |
   | Birthday / Anniversary | Key dates | No |

   > **Figure 6.5 — New Supplier Form: Section 2 – Contact Information**

   **Section 3 — Compliance & Regulatory**

   | Field | Description |
   |-------|-------------|
   | DL No | Drug License number |
   | DL No 1 | Additional Drug License number |
   | Food License | Food safety license |
   | CST No | Central Sales Tax number |
   | TIN No | Tax Identification Number |
   | PAN | Permanent Account Number |
   | GST No | GST registration number |
   | State Code | State for GST classification |
   | Aadhar | Aadhar number |
   | Registration Status | Registered / Unregistered / Composite |
   | Registration Date | Date of GST registration |

   > **Figure 6.6 — New Supplier Form: Section 3 – Compliance & Regulatory**

   **Section 4 — Financial Configuration**

   | Field | Description |
   |-------|-------------|
   | Opening Balance | Opening debit/credit balance |
   | Opening Balance Type | Debit (Dr) / Credit (Cr) |
   | Credit Limit | Maximum credit allowed |
   | Invoice Rounding | Rounding on invoice total |
   | Local / Central | L (Local) / C (Central) supply |
   | Direct / Indirect | D (Direct from manufacturer) / I (Indirect via distributor) |
   | Net Rate | Y / N — apply net rate |
   | Expiry on | M (MRP) / S (Sale Rate) / P (Purchase Rate) — expiry credit basis |
   | Sale Purchase Status | B (Both) / S (Sale only) / P (Purchase only) |
   | TDS | Y / N — apply TDS |
   | TCS Applicable | Y / N — apply TCS |

   > **Figure 6.7 — New Supplier Form: Section 4 – Financial Configuration**

   **Section 5 — Bank Details**

   | Field | Description |
   |-------|-------------|
   | Bank | Bank name |
   | Branch | Branch name |
   | Account No | Bank account number |
   | IFSC Code | Bank IFSC code |

   > **Figure 6.8 — New Supplier Form: Section 5 – Bank Details**

3. Click **Save** to create the supplier.

**C. Editing a Supplier**
1. Click the **Edit** icon, or select and press **F3**.
2. Update the required fields and click **Update**.

> **Figure 6.9 — Edit Supplier Form: Pre-filled Values**

**D. Viewing Supplier Ledger**
1. On the supplier list, click the **Ledger** icon.
2. View all purchase bills and payments in chronological order with running balance.

> **Figure 6.10 — Supplier Ledger: Purchase & Payment Transaction History**

**E. Viewing Supplier Dues**
1. Click the **Dues** icon on the supplier row.
2. View all outstanding payable amounts with invoice references and ageing.

> **Figure 6.11 — Supplier Dues: Outstanding Payable Amounts**

**F. Managing Pending Orders**
1. Click the **Pending Orders** icon on the supplier row.
2. Add a new pending order by selecting items and quantities.
3. Print the pending order as a purchase order document.
4. Edit or delete existing pending orders as needed.

> **Figure 6.12 — Supplier Pending Orders: Add / Edit / Print Order**

---

#### Validation Rules

| Field | Rule |
|-------|------|
| Name | Required, maximum 255 characters |
| Address | Required, free text |
| Email | Required, valid email format, unique across suppliers |
| Telephone | Required, unique across suppliers |
| Mobile | Optional, unique if provided |
| Mobile Additional | Optional, unique if provided |
| Code | Optional, maximum 255 characters |
| Status | Optional, maximum 5 characters |
| Local / Central | Optional, L / C |
| Direct / Indirect | Optional, D / I |
| Expiry On | Optional, M / S / P |
| Sale Purchase Status | Optional, B / S / P |

**System Default Values Applied on Save:**

| Field | Default |
|-------|---------|
| expiry_on_mrp_sale_rate_purchase_rate | M (MRP) |
| sale_purchase_status | B (Both) |
| is_deleted | 0 (active) |

---

#### Reports Generated

| Report | Location | Description |
|--------|----------|-------------|
| **Customer-Supplier List** | Reports → Management | Combined list of all suppliers and customers |
| **Supplier-wise Purchase – All** | Reports → Purchase → Misc | All purchases grouped by supplier |
| **Supplier-wise Purchase – Bill-wise** | Reports → Purchase → Misc | Bill-level breakdown per supplier |
| **Supplier-wise Purchase – Item-wise** | Reports → Purchase → Misc | Item-level breakdown per supplier |
| **Supplier-wise Companies** | Reports → Purchase → Other | Companies supplied by each supplier |
| **Supplier Visit Report** | Reports → Purchase → Other | Supplier visit tracking |
| **Current Stock – Supplier-wise** | Reports → Inventory | Stock levels by supplier |
| **Suppliers Pending Order** | Reports → Management | All pending orders per supplier |
| **Payment to Supplier** | Reports → Receipt-Payment | Payment history per supplier |
| **Replacement from Supplier** | Reports → Breakage/Expiry | Replacement transactions from suppliers |
| **Expiry Return to Supplier** | Reports → Breakage/Expiry | Expiry items returned to suppliers |
| **Gross Profit – Supplier-wise** | Reports → Management | GP analysis per supplier |

---

#### Common Issues & Solutions

| Issue | Likely Cause | Solution |
|-------|-------------|----------|
| Duplicate email/telephone error | Another supplier has the same contact | Use unique email and telephone for each supplier |
| Supplier not appearing in purchase form | Supplier Status is Inactive | Edit supplier → set Status to **Active** |
| Cannot delete a supplier | Supplier has linked purchase transactions | Suppliers with transactions cannot be deleted |
| TDS not applying on purchase | TDS flag is N | Edit supplier → set **TDS = Y** |
| GST not calculating correctly on purchase | GST No or State Code missing | Edit supplier → fill GST No and State Code correctly |
| Ledger not showing recent transactions | Transaction not finalized/saved | Confirm the purchase transaction is saved and posted |
| Pending order not printing | Order not created properly | Re-create the pending order and use Print option |

---

#### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| **Ctrl + F9** | Open Supplier Master from anywhere |
| **F9** | Create a new supplier |
| **F3** | Edit the selected supplier |
| **Delete** | Delete the selected supplier |
| **F8** | Bulk delete all checked suppliers |
| **Arrow Up / Down** | Navigate supplier list rows |
| **Enter** | Toggle checkbox on highlighted row |
| **ESC** | Go back / exit |

---

#### Screenshots Reference

| Figure No. | Section | Description |
|------------|---------|-------------|
| **Figure 6.1** | Module Overview | Supplier Master: Full List Page (Index View) |
| **Figure 6.2** | Supplier List | Supplier List: Table View with Search & Filter Panel |
| **Figure 6.3** | Supplier List | Supplier List: Search Filters & Row Action Icons |
| **Figure 6.4** | Creating a Supplier | New Supplier Form: Section 1 – Basic Information |
| **Figure 6.5** | Creating a Supplier | New Supplier Form: Section 2 – Contact Information |
| **Figure 6.6** | Creating a Supplier | New Supplier Form: Section 3 – Compliance & Regulatory |
| **Figure 6.7** | Creating a Supplier | New Supplier Form: Section 4 – Financial Configuration |
| **Figure 6.8** | Creating a Supplier | New Supplier Form: Section 5 – Bank Details |
| **Figure 6.9** | Editing a Supplier | Edit Supplier Form: Pre-filled Values |
| **Figure 6.10** | Supplier Ledger | Supplier Ledger: Purchase & Payment Transaction History |
| **Figure 6.11** | Supplier Dues | Supplier Dues Page: Outstanding Payable Amounts |
| **Figure 6.12** | Pending Orders | Supplier Pending Orders: Add / Edit / Print Order |

---

### 3.7 Customer Master

#### Purpose
The Customer Master module is the most comprehensive master in the system. It manages all customers — retailers, wholesalers, institutions, and hospitals. Customer records store contact details, compliance documents, financial settings, credit limits, pricing rules, territory assignments, and expiry/breakage policies. All sale transactions, receipts, ledgers, dues, special rates, and prescriptions are linked to customer records.

#### Access Path
- **Menu:** Customers (left sidebar)
- **Direct URL:** `/customers`
- **Global Shortcut:** `Ctrl + F11` from anywhere in the application

> **Figure 7.1 — Customer Master: Module Overview (List Page)**

---

#### Pre-requisites
Before creating customers, set up the following masters:
1. **Salesman Management** — For assigning salesman to customer
2. **Area Manager** — For assigning customer to a sales area
3. **Route Management** — For assigning delivery route
4. **State Manager** — For GST state code assignment
5. **Transport Master** — For eWay bill transport details (optional)

---

#### Main Features

| Feature | Description |
|---------|-------------|
| **131-Field Customer Database** | Complete customer profile across 3 tabs |
| **3-Tab Form** | General Info, Other Details, Locks — organized for easy data entry |
| **Credit Control** | Credit limit, credit days, max outstanding amount, invoice limits |
| **Special Rates** | Per-customer item rate overrides |
| **Customer Discounts** | Breakage/expiry discount policies per customer |
| **Customer Ledger** | Full transaction history — sales, returns, breakage/expiry |
| **Dues Management** | Outstanding amount tracking with ageing |
| **Prescriptions** | Pharmaceutical prescription records linked to customer |
| **Territory Assignment** | Assign Salesman, Area, and Route per customer |
| **GST Compliance** | GST No, state code, registration status (Registered/Unregistered/Composite) |
| **Drug License Tracking** | DL number, DL expiry date, additional DL |
| **Expiry Policy** | Per-customer expiry return rules, lock amounts, monthly limits |
| **Copy Discounts** | Copy discount structure from one customer to another |
| **Advanced Search** | Search by name, code, mobile, telephone, DL No, GST No, city |
| **Pending Challans** | View outstanding delivery challans for a customer |
| **Soft Delete** | Data preserved on deletion |

> **Figure 7.2 — Customer List: Table View with Search & Filter Panel**

---

#### Step-by-Step Process

**A. Viewing the Customer List**
1. Navigate to **Customers** from the left sidebar, or press **Ctrl + F11**.
2. The list displays customers with key columns: Name, Code, Mobile, City, Status, Actions.
3. Use the **search bar** to filter by Name, Code, Mobile, Telephone, Address, DL No, GST No, City.
4. Use action icons to access: View, Edit, Delete, Ledger (F10), Dues (F5), Pending Challans (F8), Special Rates (F4), Expiry Ledger (F11), Bills (F2).

> **Figure 7.3 — Customer List: Search Filters & Action Icons**

**B. Creating a New Customer**

The create form has **3 tabs**:

---

**Tab 1 — General Information**

   **Basic Details:**

   | Field | Description | Required |
   |-------|-------------|----------|
   | Name | Customer firm/person name | Yes |
   | Code | Customer code for quick lookup | No |
   | T/R | Tax / Retail classification | No |
   | Address (Line 1, 2, 3) | Primary address | No |
   | City | City name | No |
   | Pin Code | Postal code | No |
   | Telephone Office | Office number | No |
   | Telephone Residence | Home number | No |
   | Mobile | Primary mobile | No |
   | Email | Email address | No |

   > **Figure 7.4 — New Customer Form: Tab 1 – Basic Details**

   **Contact & Financial:**

   | Field | Description |
   |-------|-------------|
   | Contact Person 1 & 2 | Contact names and mobiles |
   | Fax Number | Fax |
   | Opening Balance | Opening Dr/Cr balance |
   | Balance Type | D (Debit) / C (Credit) |
   | Local / Central | L / C for GST |
   | Anniversary / Birthday | Key dates for relationship management |
   | Status | Active / Inactive |

   > **Figure 7.5 — New Customer Form: Tab 1 – Contact & Financial Details**

   **Compliance (License & GST):**

   | Field | Description |
   |-------|-------------|
   | DL No / DL Expiry | Drug License with expiry date |
   | DL No 1 | Additional Drug License |
   | Food License | Food safety license |
   | CST No / TIN No / PAN | Tax numbers |
   | GST No | GST registration number |
   | State Code (GST) | State for GST filing |
   | Registration Status | U (Unregistered) / R (Registered) / C (Composite) |
   | Aadhar | Aadhar number |

   > **Figure 7.6 — New Customer Form: Tab 1 – Compliance & License Details**

   **Organization & Territory:**

   | Field | Description |
   |-------|-------------|
   | Salesman | Assign salesman from Salesman Master |
   | Area | Assign area from Area Master |
   | Route | Assign route from Route Master |
   | Business Type | R (Retail) / W (Wholesale) / I (Institution) / D (Dept) / O (Others) |
   | Description | Additional notes |
   | Order Required | Y / N |
   | Registration Date / End Date | Account lifecycle dates |

   > **Figure 7.7 — New Customer Form: Tab 1 – Territory & Organization**

---

**Tab 2 — Other Details**

   **Pricing & Rate Configuration:**

   | Field | Description |
   |-------|-------------|
   | Sale Rate Type | 1–8 (Sale Rate, Wholesale, Special Rate, etc.) |
   | Add % | Additional markup percentage |
   | Net Rate | Y / N |
   | Fixed Discount | Fixed discount amount |
   | No. of Items in Bill | Maximum line items per invoice |
   | Invoice Print Order | 0–3 (Default / Company / User / Name ordering) |
   | Invoice Format | Invoice print format number |
   | Cash Sale | Y / N — restrict to cash sales only |
   | SR Replacement | Y / N — sale return replacement allowed |

   > **Figure 7.8 — New Customer Form: Tab 2 – Pricing & Rate Configuration**

   **Expiry & Breakage Policy:**

   | Field | Description |
   |-------|-------------|
   | Expiry On | M (MRP) / S (Sale) / P (Purchase) / W (WS) / L (Special) — credit basis |
   | Expiry RN On | Expiry return note basis |
   | Expiry Repl/Credit Note | C (Credit Note) / R (Replacement) |
   | Dis. After Scheme | Y / N |
   | Dis. On Excise | Y / N / X |
   | Brk./Expiry Msg in Sale | Y / N — show warning during sale |
   | GST Discount % (per slab) | Breakage/expiry discount by GST slab (0%, 5%, 12%, 18%, 28%) |

   > **Figure 7.9 — New Customer Form: Tab 2 – Expiry & Breakage Policy**

   **Tax & Banking:**

   | Field | Description |
   |-------|-------------|
   | TDS | Y / N |
   | TCS Applicable | Y / N / # |
   | BE Incl. | Y / N |
   | Scheme Type | F / H |
   | Sale Purchase Status | S / P / B |
   | Bank / Branch / Reference | Banking details |
   | Series Lock | Invoice series lock value |
   | Transport | eWay bill transport assignment |
   | Distance (KM) | Distance for eWay bill |

   > **Figure 7.10 — New Customer Form: Tab 2 – Tax & Banking Details**

---

**Tab 3 — Locks**

   | Field | Description |
   |-------|-------------|
   | Max O/S Amount | Maximum outstanding amount allowed |
   | Max Limit On | D (Due) / L (Ledger) |
   | Max Invoice Amount | Maximum per-invoice amount |
   | Max No. O/S Invoices | Maximum outstanding invoices count |
   | Follow Conditions Strictly | Y / N |
   | Credit Days Lock | Lock credit after N days |
   | Open Lock Once | Y / N — allow one-time override |
   | Expiry Lock Type | A (Amount) / P (Percentage) |
   | Expiry Lock Value | Lock threshold value |
   | No. of Expiries per Month | Maximum expiry returns per month |
   | TAN No | Tax Deduction Account Number |
   | MSME License | MSME registration number |

   > **Figure 7.11 — New Customer Form: Tab 3 – Credit Locks & Expiry Limits**

3. Click **Save** to create the customer.

> **Figure 7.12 — Saved Customer: Success Confirmation & Detail View**

**C. Editing a Customer**
1. Click the **Edit** icon on the customer row, or select and press **F3**.
2. All 3 tabs are available for editing.
3. Click **Update** to save changes.

**D. Viewing Customer Ledger**
1. On the customer list, click the **Ledger** icon, or select and press **F10**.
2. View all sale, sale return, and breakage/expiry transactions with running balance.

> **Figure 7.13 — Customer Ledger: Sale & Payment Transaction History**

**E. Viewing Customer Dues**
1. Click the **Dues** icon, or press **F5** on the selected customer.
2. View all outstanding invoice amounts with ageing and expiry list.

> **Figure 7.14 — Customer Dues: Outstanding Invoice Amounts**

**F. Managing Special Rates**
1. Click the **Special Rates** icon (F4) on the customer row.
2. Add, edit, or delete item-specific rate overrides for this customer.

> **Figure 7.15 — Customer Special Rates: Per-Item Rate Override**

**G. Managing Prescriptions**
1. Click the **Prescriptions** option from the customer detail page.
2. Add, edit, or delete prescription records for pharmaceutical customers.

> **Figure 7.16 — Customer Prescriptions: Prescription Record Management**

**H. Copying Discounts**
1. From the customer detail page, use **Copy Discounts** to replicate the discount structure from another customer to this one.

---

#### Validation Rules

| Field | Rule |
|-------|------|
| Name | Required, maximum 255 characters |
| Email | Optional, valid email format |
| Mobile | Optional, string |
| PAN Number | Optional, string |
| Opening Balance | Optional, numeric |
| Balance Type | Optional, D / C |
| Local / Central | Optional, L / C |
| Credit Days | Optional, integer |
| Birth Day / DL Expiry / Registration Date / End Date | Optional, valid date |
| Status | Optional, boolean |
| Business Type | Optional, string |
| Aadhar | Optional, string |
| GST Name | Optional, string |
| State Code GST | Optional, string |
| Registration Status | Optional, U / R / C |
| Sales Man Code / Area Code / Route Code / State Code | Optional, string references |

---

#### Reports Generated

| Report | Location | Description |
|--------|----------|-------------|
| **Customer List** | Reports → Management | Complete customer database with all fields |
| **Customer-Supplier List** | Reports → Management | Combined customers and suppliers list |
| **Customer-wise Sale – All** | Reports → Sale → Misc | All sales grouped by customer |
| **Customer-wise Sale – Bill-wise** | Reports → Sale → Misc | Bill-level breakdown per customer |
| **Customer-wise Sale – Item-wise** | Reports → Sale → Misc | Item-level sale analysis per customer |
| **Customer-wise Sale – Month-wise** | Reports → Sale → Misc | Month-by-month sale per customer |
| **Customer-wise Sale – Area-wise** | Reports → Sale → Misc | Area-level customer sale analysis |
| **Customer Visit Status** | Reports → Sale | Customer field visit tracking |
| **Doctor-wise Customers** | Reports → Management | Customers grouped by doctor referral |
| **Customer GST Detail** | Reports → GST | GST filing details per customer |
| **Mailing Labels** | Reports → Management | Address labels for customer mailing |
| **Receipt from Customer** | Reports → Receipt-Payment | Payment receipt history per customer |
| **Expiry Return – Customer-wise** | Reports → Breakage/Expiry | Expiry returns per customer |
| **Customer Pending Orders** | Reports → Management | Outstanding orders per customer |
| **Customer Stock Details** | Reports → Inventory | Stock position at customer location |

---

#### Common Issues & Solutions

| Issue | Likely Cause | Solution |
|-------|-------------|----------|
| Customer not found in sale form search | Customer Status is Inactive | Edit customer → set Status to **Active** |
| Cannot delete a customer | Customer has linked sale transactions | Customers with sales cannot be deleted; use soft delete |
| Credit limit warning appearing | Customer has exceeded credit limit or days | Increase credit limit or collect outstanding payment |
| Wrong GST rate on inter-state invoice | Local/Central flag is wrong | Edit customer → set **Local/Central = C** for central (inter-state) |
| Special rate not applying in sale | Special rate not set for that item | Go to Customer → Special Rates → add the item rate |
| Salesman/Area/Route not showing in dropdown | Master record is Inactive | Activate the Salesman/Area/Route in respective masters |
| Expiry return being blocked | Expiry lock value exceeded or monthly limit reached | Review Locks tab → adjust expiry lock settings |
| DL expiry alert on sale | Drug license has expired | Renew DL → edit customer → update DL Expiry date |
| Customer ledger showing wrong balance | Opening balance entered incorrectly | Edit customer → correct Opening Balance and Balance Type |
| Copy discounts not working | Source customer has no discounts set | First set discounts on source customer, then copy |

---

#### Keyboard Shortcuts

| Key | Action |
|-----|--------|
| **Ctrl + F11** | Open Customer Master from anywhere |
| **F9** | Create a new customer |
| **F3** | Edit the selected customer |
| **Delete** | Delete the selected customer |
| **F8** | View pending challans for selected customer |
| **F10** | View ledger for selected customer |
| **F5** | View dues for selected customer |
| **F4** | View special rates for selected customer |
| **F11** | View expiry ledger for selected customer |
| **F2** | View list of bills for selected customer |
| **Arrow Up / Down** | Navigate customer list rows |
| **Enter** | Toggle checkbox on highlighted row |
| **ESC** | Go back / exit |

---

#### Screenshots Reference

| Figure No. | Section | Description |
|------------|---------|-------------|
| **Figure 7.1** | Module Overview | Customer Master: Full List Page (Index View) |
| **Figure 7.2** | Customer List | Customer List: Table View with Search & Filter Panel |
| **Figure 7.3** | Customer List | Customer List: Search Filters & Row Action Icons |
| **Figure 7.4** | Creating – Tab 1 | New Customer Form: Tab 1 – Basic Details (Name, Address, Contact) |
| **Figure 7.5** | Creating – Tab 1 | New Customer Form: Tab 1 – Contact & Financial Details |
| **Figure 7.6** | Creating – Tab 1 | New Customer Form: Tab 1 – Compliance & License Details |
| **Figure 7.7** | Creating – Tab 1 | New Customer Form: Tab 1 – Territory & Organization |
| **Figure 7.8** | Creating – Tab 2 | New Customer Form: Tab 2 – Pricing & Rate Configuration |
| **Figure 7.9** | Creating – Tab 2 | New Customer Form: Tab 2 – Expiry & Breakage Policy |
| **Figure 7.10** | Creating – Tab 2 | New Customer Form: Tab 2 – Tax & Banking Details |
| **Figure 7.11** | Creating – Tab 3 | New Customer Form: Tab 3 – Credit Locks & Expiry Limits |
| **Figure 7.12** | Saved Customer | Customer Detail Page after Successful Save |
| **Figure 7.13** | Customer Ledger | Customer Ledger: Sale & Payment Transaction History |
| **Figure 7.14** | Customer Dues | Customer Dues: Outstanding Invoice Amounts with Ageing |
| **Figure 7.15** | Special Rates | Customer Special Rates: Per-Item Rate Override Table |
| **Figure 7.16** | Prescriptions | Customer Prescriptions: Prescription Record Management |

---

