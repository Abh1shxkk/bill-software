# Requirements Document

## Introduction

The Quotation module enables users to create quotations/estimates for customers without affecting inventory or batch quantities. Unlike sale transactions, quotations are purely informational documents that can be printed and shared with customers for pricing purposes. This module provides a streamlined invoice-like interface for creating quotations with items, quantities, rates, discounts, and terms.

## Glossary

- **Quotation**: A document providing estimated pricing for items to a customer without affecting stock levels
- **Quotation_System**: The software module responsible for managing quotation creation, modification, and retrieval
- **Item**: A product from the item master that can be added to a quotation
- **Customer**: A party to whom the quotation is addressed
- **Net_Amount**: The final calculated amount after applying discounts and taxes
- **T.No (Transaction Number)**: A unique sequential identifier for each quotation
- **Series**: A prefix identifier for quotation numbering (e.g., "QT")

## Requirements

### Requirement 1

**User Story:** As a sales user, I want to create new quotations for customers, so that I can provide them with pricing estimates without affecting inventory.

#### Acceptance Criteria

1. WHEN a user opens the quotation transaction page THEN the Quotation_System SHALL display a form with date, customer name, discount percentage, remarks, and terms fields
2. WHEN a user saves a quotation THEN the Quotation_System SHALL generate a unique sequential transaction number (T.No)
3. WHEN a quotation is saved THEN the Quotation_System SHALL NOT modify any batch quantities or stock levels
4. WHEN a user enters customer details THEN the Quotation_System SHALL store the customer name and discount percentage

### Requirement 2

**User Story:** As a sales user, I want to add multiple items to a quotation with pricing details, so that I can provide comprehensive pricing information.

#### Acceptance Criteria

1. WHEN a user adds an item to the quotation THEN the Quotation_System SHALL capture item code, item name, quantity, rate, MRP, and calculate the amount
2. WHEN a user modifies quantity or rate THEN the Quotation_System SHALL recalculate the line item amount as quantity multiplied by rate
3. WHEN a user adds multiple items THEN the Quotation_System SHALL display all items in a tabular grid format
4. WHEN a user clicks "Insert Item" THEN the Quotation_System SHALL add a new empty row to the items grid
5. WHEN a user clicks "Delete Item" THEN the Quotation_System SHALL remove the selected item row from the grid

### Requirement 3

**User Story:** As a sales user, I want the quotation to calculate totals automatically, so that I can see accurate pricing without manual calculations.

#### Acceptance Criteria

1. WHEN items are added or modified THEN the Quotation_System SHALL calculate and display the Net total amount
2. WHEN a discount percentage is applied THEN the Quotation_System SHALL apply the discount to the total amount calculation
3. WHEN the quotation is displayed THEN the Quotation_System SHALL show Pack, Unit, Cl.Qty (closing quantity), Comp (company), and Lctn (location) information in the footer area

### Requirement 4

**User Story:** As a sales user, I want to modify existing quotations, so that I can update pricing or items as needed.

#### Acceptance Criteria

1. WHEN a user opens the modification page THEN the Quotation_System SHALL display a list of existing quotations with search capability
2. WHEN a user selects a quotation for editing THEN the Quotation_System SHALL load all quotation details including header and items
3. WHEN a user updates quotation details THEN the Quotation_System SHALL save the changes and maintain the original transaction number
4. WHEN a user clicks "Cancel Quotation" THEN the Quotation_System SHALL mark the quotation as cancelled without deleting the record

### Requirement 5

**User Story:** As a sales user, I want to view quotation details, so that I can review or print quotations for customers.

#### Acceptance Criteria

1. WHEN a user views a quotation THEN the Quotation_System SHALL display all header information including date, customer, remarks, and terms
2. WHEN a user views a quotation THEN the Quotation_System SHALL display all line items with their quantities, rates, and amounts
3. WHEN a user requests to print THEN the Quotation_System SHALL generate a printable format of the quotation

### Requirement 6

**User Story:** As a system administrator, I want quotation data to be persisted reliably, so that quotation records are maintained for future reference.

#### Acceptance Criteria

1. WHEN a quotation is saved THEN the Quotation_System SHALL persist the quotation header and all items to the database
2. WHEN retrieving a quotation THEN the Quotation_System SHALL return the complete quotation with all associated items
3. IF a database error occurs during save THEN the Quotation_System SHALL rollback all changes and display an error message
