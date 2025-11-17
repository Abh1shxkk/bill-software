# Design Document: Purchase Return Credit Adjustment

## Overview

This feature implements Purchase Return Transaction with Credit Note Adjustment functionality. It follows the same pattern as the existing Sale Return Credit Adjustment but works with suppliers and purchase invoices instead of customers and sale invoices.

The system allows users to:
1. Create purchase return transactions recording goods returned to suppliers
2. Adjust the return amount against supplier's outstanding purchase invoices
3. Track all returns and adjustments for accounting purposes

## Architecture

The feature follows the existing Laravel MVC architecture:

```
┌─────────────────────────────────────────────────────────────────┐
│                        Frontend (Blade)                          │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │ Transaction Form│  │ Adjustment Modal│  │ Invoice List    │  │
│  └────────┬────────┘  └────────┬────────┘  └────────┬────────┘  │
└───────────┼─────────────────────┼─────────────────────┼──────────┘
            │                     │                     │
            ▼                     ▼                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Controller Layer                              │
│  ┌─────────────────────────────────────────────────────────────┐│
│  │              PurchaseReturnController                        ││
│  │  - transaction()      - store()                              ││
│  │  - getSupplierInvoices()  - saveAdjustment()                ││
│  └─────────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────────┘
            │                     │                     │
            ▼                     ▼                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Model Layer                                 │
│  ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐ │
│  │PurchaseReturn    │ │PurchaseReturn    │ │PurchaseReturn    │ │
│  │Transaction       │ │TransactionItem   │ │Adjustment        │ │
│  └──────────────────┘ └──────────────────┘ └──────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
            │                     │                     │
            ▼                     ▼                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Database Layer                              │
│  ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐ │
│  │purchase_return_  │ │purchase_return_  │ │purchase_return_  │ │
│  │transactions      │ │transaction_items │ │adjustments       │ │
│  └──────────────────┘ └──────────────────┘ └──────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### 1. Database Migrations

#### purchase_return_transactions
Main table storing purchase return header information.

#### purchase_return_transaction_items
Table storing line items for each purchase return.

#### purchase_return_adjustments
Table storing credit note adjustments against purchase invoices.

### 2. Models

#### PurchaseReturnTransaction
- Relationships: belongsTo(Supplier), hasMany(PurchaseReturnTransactionItem), hasMany(PurchaseReturnAdjustment)
- Methods: generatePRNumber(), calculateTotals()

#### PurchaseReturnTransactionItem
- Relationships: belongsTo(PurchaseReturnTransaction), belongsTo(Item), belongsTo(Batch)

#### PurchaseReturnAdjustment
- Relationships: belongsTo(PurchaseReturnTransaction), belongsTo(PurchaseTransaction)

### 3. Controller Methods

#### PurchaseReturnController
- `transaction()` - Display transaction form
- `store(Request $request)` - Save purchase return transaction
- `getSupplierInvoices($supplierId)` - Get outstanding purchase invoices for supplier
- `saveAdjustment(Request $request)` - Save credit note adjustments

### 4. Frontend Components

#### Transaction Form (existing, enhanced)
- Add "Credit Note Adjustment" button
- Add adjustment modal similar to Sale Return

#### Adjustment Modal
- Display supplier's outstanding purchase invoices
- Allow entering adjustment amounts
- Validate totals and balances
- Submit adjustments with transaction

## Data Models

### purchase_return_transactions

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| pr_no | varchar | Unique PR number (PR0001, PR0002...) |
| series | varchar(10) | Series code (default: PR) |
| return_date | date | Date of return |
| supplier_id | bigint FK | Reference to suppliers table |
| supplier_name | varchar | Supplier name (denormalized) |
| invoice_no | varchar | Supplier's invoice number |
| invoice_date | date | Supplier's invoice date |
| gst_vno | varchar | GST voucher number |
| tax_flag | char(1) | Tax applicable (Y/N) |
| rate_diff_flag | char(1) | Rate difference flag |
| nt_amount | decimal(15,2) | Net taxable amount |
| sc_amount | decimal(15,2) | Special charge amount |
| dis_amount | decimal(15,2) | Discount amount |
| scm_amount | decimal(15,2) | Scheme amount |
| tax_amount | decimal(15,2) | Total tax amount |
| net_amount | decimal(15,2) | Net return amount |
| tcs_amount | decimal(15,2) | TCS amount |
| remarks | text | Remarks |
| status | varchar(20) | Status (active/cancelled) |
| created_by | bigint FK | User who created |
| updated_by | bigint FK | User who updated |
| created_at | timestamp | Created timestamp |
| updated_at | timestamp | Updated timestamp |

### purchase_return_transaction_items

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| purchase_return_transaction_id | bigint FK | Reference to parent transaction |
| item_id | bigint FK | Reference to items table |
| batch_id | bigint FK | Reference to batches table |
| item_code | varchar | Item code |
| item_name | varchar | Item name |
| batch_no | varchar | Batch number |
| expiry_date | date | Expiry date |
| qty | decimal(10,3) | Return quantity |
| free_qty | decimal(10,3) | Free quantity |
| pur_rate | decimal(10,2) | Purchase rate |
| dis_percent | decimal(10,3) | Discount percentage |
| ft_rate | decimal(10,2) | Final taxable rate |
| ft_amount | decimal(10,2) | Final taxable amount |
| mrp | decimal(10,2) | MRP |
| cgst_percent | decimal(10,3) | CGST percentage |
| sgst_percent | decimal(10,3) | SGST percentage |
| cess_percent | decimal(10,3) | Cess percentage |
| cgst_amount | decimal(10,2) | CGST amount |
| sgst_amount | decimal(10,2) | SGST amount |
| cess_amount | decimal(10,2) | Cess amount |
| tax_amount | decimal(10,2) | Total tax amount |
| net_amount | decimal(10,2) | Net amount |
| hsn_code | varchar | HSN code |
| packing | varchar | Packing |
| unit | varchar | Unit |
| company_name | varchar | Company name |
| row_order | int | Row order |
| created_at | timestamp | Created timestamp |
| updated_at | timestamp | Updated timestamp |

### purchase_return_adjustments

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| purchase_return_id | bigint FK | Reference to purchase_return_transactions |
| purchase_transaction_id | bigint FK | Reference to purchase_transactions (invoice being adjusted) |
| adjusted_amount | decimal(15,2) | Amount adjusted against this invoice |
| adjustment_date | date | Date of adjustment |
| created_by | varchar | User who created |
| created_at | timestamp | Created timestamp |
| updated_at | timestamp | Updated timestamp |

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Transaction Number Uniqueness
*For any* set of purchase return transactions, all PR numbers SHALL be unique across the system.
**Validates: Requirements 1.2**

### Property 2: Calculation Correctness
*For any* purchase return item with quantity Q, rate R, and discount D%, the F.T. Rate SHALL equal R - (R * D / 100) and F.T. Amount SHALL equal Q * F.T. Rate.
**Validates: Requirements 1.3**

### Property 3: Tax Calculation Correctness
*For any* purchase return item, the tax amounts (CGST, SGST, Cess) SHALL be calculated correctly based on the taxable amount and respective percentages.
**Validates: Requirements 1.3**

### Property 4: Adjustment Total Validation
*For any* set of adjustments on a purchase return, the sum of all adjusted amounts SHALL NOT exceed the return's net amount.
**Validates: Requirements 2.3**

### Property 5: Invoice Balance Validation
*For any* adjustment against a purchase invoice, the adjusted amount SHALL NOT exceed the invoice's remaining balance (total - already adjusted).
**Validates: Requirements 2.4**

### Property 6: Adjustment Record Integrity
*For any* saved adjustment, there SHALL exist a valid purchase return and a valid purchase invoice linked to it.
**Validates: Requirements 2.5**

### Property 7: Cascade Delete Integrity
*For any* deleted purchase return transaction, all related items and adjustments SHALL also be deleted.
**Validates: Requirements 4.4**

## Error Handling

1. **Validation Errors**
   - Invalid supplier selection
   - Missing required fields
   - Adjustment amount exceeds return amount
   - Adjustment amount exceeds invoice balance

2. **Database Errors**
   - Foreign key constraint violations
   - Duplicate PR number (retry with new number)
   - Transaction rollback on partial failure

3. **Business Logic Errors**
   - Attempting to return more than purchased quantity
   - Adjusting against already fully adjusted invoice

## Testing Strategy

### Unit Tests
- Model relationship tests
- Calculation method tests
- Validation rule tests

### Property-Based Tests
Using a property-based testing library (e.g., PHPUnit with data providers or a dedicated PBT library):

1. **Property 1**: Generate multiple transactions and verify all PR numbers are unique
2. **Property 2**: Generate random items with various rates/discounts and verify calculations
3. **Property 3**: Generate items with various tax percentages and verify tax calculations
4. **Property 4**: Generate adjustments and verify total doesn't exceed return amount
5. **Property 5**: Generate adjustments and verify each doesn't exceed invoice balance
6. **Property 6**: Verify all saved adjustments have valid foreign key references
7. **Property 7**: Create transaction with items/adjustments, delete, verify cascade

### Integration Tests
- Full transaction flow with adjustment
- Stock ledger updates
- Batch quantity updates
