# Requirements Document

## Introduction

This feature implements Purchase Return Transaction functionality with Credit Note Adjustment capability. When a business returns goods to a supplier, the return amount needs to be adjusted against the supplier's outstanding purchase invoices. This is similar to the existing Sale Return Credit Adjustment feature but works in reverse - adjusting against supplier's purchase invoices instead of customer's sale invoices.

## Glossary

- **Purchase Return Transaction**: A transaction recording goods returned to a supplier
- **Credit Note**: A document issued when goods are returned, reducing the amount owed to the supplier
- **Credit Adjustment**: The process of applying the return amount against outstanding purchase invoices
- **Supplier**: The vendor/party from whom goods were originally purchased
- **Purchase Invoice**: The original bill/invoice from the supplier for goods purchased
- **Outstanding Amount**: The unpaid balance on a purchase invoice

## Requirements

### Requirement 1

**User Story:** As a store manager, I want to create purchase return transactions, so that I can record goods being returned to suppliers.

#### Acceptance Criteria

1. WHEN a user creates a purchase return transaction THEN the system SHALL store the transaction with supplier details, return date, items, quantities, rates, and calculated amounts
2. WHEN a purchase return is saved THEN the system SHALL generate a unique transaction number (PR series)
3. WHEN items are added to purchase return THEN the system SHALL calculate F.T. Rate, F.T. Amount, tax amounts (CGST, SGST, Cess), and net amount
4. WHEN a purchase return is saved THEN the system SHALL update the stock ledger to reflect returned quantities (OUT transaction)
5. WHEN a purchase return is saved THEN the system SHALL update batch quantities accordingly

### Requirement 2

**User Story:** As a store manager, I want to adjust the purchase return amount against supplier's outstanding purchase invoices, so that I can properly track credit notes and supplier balances.

#### Acceptance Criteria

1. WHEN a user clicks "Credit Note Adjustment" button THEN the system SHALL display a modal showing all outstanding purchase invoices for the selected supplier
2. WHEN displaying outstanding invoices THEN the system SHALL show invoice number, date, total amount, already adjusted amount, and balance amount
3. WHEN a user enters adjustment amounts THEN the system SHALL validate that total adjustments do not exceed the return amount
4. WHEN a user enters adjustment amount for an invoice THEN the system SHALL validate that it does not exceed the invoice's balance amount
5. WHEN adjustments are saved THEN the system SHALL create adjustment records linking the purchase return to the adjusted purchase invoices

### Requirement 3

**User Story:** As a store manager, I want to view purchase return history and adjustment details, so that I can track all returns and their credit adjustments.

#### Acceptance Criteria

1. WHEN viewing a purchase return THEN the system SHALL display all adjustment records showing which invoices were adjusted
2. WHEN viewing a purchase invoice THEN the system SHALL show any credit adjustments applied from purchase returns
3. WHEN generating reports THEN the system SHALL include purchase return and adjustment data

### Requirement 4

**User Story:** As a system administrator, I want the purchase return data to be properly stored and related, so that data integrity is maintained.

#### Acceptance Criteria

1. THE system SHALL store purchase return transactions in a dedicated table with proper foreign key relationships
2. THE system SHALL store purchase return items in a dedicated table linked to the transaction
3. THE system SHALL store credit adjustments in a dedicated table linking returns to purchase invoices
4. WHEN a purchase return is deleted THEN the system SHALL cascade delete related items and adjustments
5. WHEN a purchase invoice is deleted THEN the system SHALL handle related adjustments appropriately
