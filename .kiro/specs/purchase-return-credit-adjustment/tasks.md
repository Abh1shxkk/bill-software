# Implementation Plan

## Phase 1: Database Setup

- [x] 1. Create database migrations



  - [x] 1.1 Create purchase_return_transactions migration


    - Create table with all columns as per design (pr_no, supplier_id, amounts, flags, etc.)
    - Add foreign keys and indexes
    - _Requirements: 4.1_

  - [x] 1.2 Create purchase_return_transaction_items migration

    - Create table with item details (qty, rates, tax amounts, etc.)
    - Add foreign key to purchase_return_transactions with cascade delete
    - _Requirements: 4.2_

  - [x] 1.3 Create purchase_return_adjustments migration

    - Create table linking returns to purchase invoices
    - Add foreign keys with appropriate delete behavior



    - _Requirements: 4.3_




  - [ ] 1.4 Run migrations
    - Execute `php artisan migrate`
    - Verify tables are created correctly
    - _Requirements: 4.1, 4.2, 4.3_

## Phase 2: Models

- [x] 2. Create Eloquent models

  - [x] 2.1 Create PurchaseReturnTransaction model


    - Define fillable fields, casts, and relationships
    - Add relationship to Supplier, Items, Adjustments
    - _Requirements: 1.1, 4.1_

  - [ ] 2.2 Create PurchaseReturnTransactionItem model
    - Define fillable fields, casts, and relationships
    - Add relationship to parent transaction, Item, Batch

    - _Requirements: 1.1, 4.2_
  - [ ] 2.3 Create PurchaseReturnAdjustment model
    - Define fillable fields, casts, and relationships
    - Add relationship to PurchaseReturnTransaction and PurchaseTransaction
    - _Requirements: 2.5, 4.3_

## Phase 3: Controller Methods

- [-] 3. Implement controller methods

  - [x] 3.1 Add store() method to PurchaseReturnController


    - Validate request data
    - Generate unique PR number
    - Save transaction and items in database transaction
    - Update stock ledger (OUT entry)
    - Update batch quantities
    - _Requirements: 1.1, 1.2, 1.4, 1.5_
  - [ ] 3.2 Write property test for PR number uniqueness
    - **Property 1: Transaction Number Uniqueness**
    - **Validates: Requirements 1.2**
  - [ ] 3.3 Write property test for calculation correctness
    - **Property 2: Calculation Correctness**
    - **Validates: Requirements 1.3**

  - [ ] 3.4 Add getSupplierInvoices() method
    - Fetch all purchase transactions for supplier
    - Calculate already adjusted amounts
    - Return invoice list with balance amounts

    - _Requirements: 2.1, 2.2_
  - [ ] 3.5 Add saveAdjustment() method
    - Validate adjustment amounts
    - Save adjustment records
    - Link to purchase return and purchase invoices
    - _Requirements: 2.3, 2.4, 2.5_
  - [ ] 3.6 Write property test for adjustment validation
    - **Property 4: Adjustment Total Validation**
    - **Property 5: Invoice Balance Validation**
    - **Validates: Requirements 2.3, 2.4**

- [ ] 4. Checkpoint - Make sure all tests are passing
  - Ensure all tests pass, ask the user if questions arise.


## Phase 4: Routes



- [ ] 5. Add routes for purchase return
  - [ ] 5.1 Add routes to web.php
    - POST route for store()
    - GET route for getSupplierInvoices()

    - POST route for saveAdjustment()


    - _Requirements: 1.1, 2.1, 2.5_


## Phase 5: Frontend - Credit Adjustment Modal

- [ ] 6. Implement credit adjustment modal
  - [ ] 6.1 Add Credit Note Adjustment button to transaction form
    - Add button next to Save button

    - Button should be enabled only when items exist
    - _Requirements: 2.1_
  - [ ] 6.2 Create adjustment modal HTML/CSS
    - Modal backdrop and container

    - Table showing invoices with columns: Invoice No, Date, Amount, Adjusted, Balance, Adjust Amount
    - Summary row showing totals
    - Save and Cancel buttons
    - _Requirements: 2.1, 2.2_

  - [ ] 6.3 Implement openAdjustmentModal() function
    - Validate items exist
    - Fetch supplier invoices via AJAX
    - Display modal with invoice data
    - _Requirements: 2.1_
  - [x] 6.4 Implement adjustment amount validation

    - Real-time validation as user enters amounts

    - Highlight errors if amount exceeds balance
    - Update remaining amount display
    - _Requirements: 2.3, 2.4_
  - [x] 6.5 Implement saveAdjustment() function

    - Collect adjustment data
    - Submit to backend
    - Handle success/error responses
    - _Requirements: 2.5_

## Phase 6: Save Transaction with Adjustments

- [x] 7. Integrate adjustment with transaction save


  - [x] 7.1 Modify store() to handle adjustments

    - Accept adjustments array in request
    - Save adjustments after transaction is saved
    - Use database transaction for atomicity

    - _Requirements: 1.1, 2.5_
  - [ ] 7.2 Update frontend to submit with adjustments
    - Modify form submission to include adjustment data
    - Handle both save-only and save-with-adjustment flows
    - _Requirements: 2.5_

- [ ] 8. Checkpoint - Make sure all tests are passing
  - Ensure all tests pass, ask the user if questions arise.

## Phase 7: Stock and Batch Updates

- [ ] 9. Implement stock and batch updates
  - [ ] 9.1 Add stock ledger entry on save
    - Create OUT entry in stock_ledgers table
    - Include batch_id, quantity, transaction reference
    - _Requirements: 1.4_
  - [ ] 9.2 Update batch quantities
    - Decrease batch qty by return qty
    - Handle free_qty if applicable
    - _Requirements: 1.5_

## Phase 8: View and History

- [x] 10. Implement view functionality
  - [x] 10.1 Add show() method to controller
    - Fetch transaction with items and adjustments
    - Return view with all data
    - _Requirements: 3.1_
  - [x] 10.2 Create show view
    - Display transaction details
    - Display items table
    - Display adjustments table
    - _Requirements: 3.1_
  - [x] 10.3 Create index view (listing page)
    - Display all purchase return transactions with pagination
    - Search/filter by supplier, PR no, invoice no
    - Date range filter
    - Actions: View, Edit, Delete
  - [x] 10.4 Add index() method to controller
    - Fetch all transactions with filters
    - Paginate results
  - [x] 10.5 Add destroy() method to controller
    - Restore batch quantities on delete
    - Remove stock ledger entries
    - Soft delete transaction

- [ ] 11. Final Checkpoint - Make sure all tests are passing
  - Ensure all tests pass, ask the user if questions arise.
