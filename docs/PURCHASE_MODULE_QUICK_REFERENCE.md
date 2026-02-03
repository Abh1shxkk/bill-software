# Purchase Module - Quick Reference Guide

## New Keyboard Shortcuts

### Code Field
| Action | Result |
|--------|--------|
| Press Enter (empty field) | Opens Item Selection Modal |
| Press Enter (with barcode) | Fetches item → Opens Batch Modal → Populates row |

### Discount Field (dis_percent)
| Action | Result |
|--------|--------|
| Press Enter (value changed) | Shows discount options modal |
| Press Enter (value unchanged) | Calculates GST → Moves to next row's Code field |

## New Features

### 1. Item Selection Modal
- Search items by name, code, or company
- View stock levels and rates
- Automatically opens Batch Modal after selection

### 2. Batch Selection Modal
- View all available batches for selected item
- See batch details: expiry, quantity, rates
- Shows supplier and cost information
- Allows selection of existing batch or creation of new batch

### 3. Automatic Row Population
- Item name becomes readonly after selection
- All relevant fields populated automatically
- Focus moves to quantity field for quick entry

### 4. Smart Navigation
- Discount field Enter key now moves to next row
- New rows created automatically when needed
- Seamless flow from one item to the next

## Workflow Examples

### Quick Entry Workflow
```
1. Enter barcode in Code field → Press Enter
2. Select batch from modal
3. Enter quantity
4. Enter free quantity (optional)
5. Verify/adjust purchase rate
6. Enter discount → Press Enter
7. Automatically moves to next row's Code field
8. Repeat for next item
```

### Manual Selection Workflow
```
1. Press Enter in empty Code field
2. Search for item in Item Selection Modal
3. Select item
4. Select batch from Batch Modal
5. Enter quantity and other details
6. Enter discount → Press Enter
7. Automatically moves to next row
```

## Tips

- **Barcode Scanning**: Just scan and press Enter - the system handles the rest
- **Quick Navigation**: Use Enter key to move through fields efficiently
- **Readonly Fields**: Item name is protected after selection to prevent errors
- **Batch Creation**: Can create new batches during purchase entry
- **GST Calculation**: Automatically triggered when moving to next row

## Compatibility

- Works with both Purchase Transaction and Purchase Modification
- Maintains all existing calculation logic
- Compatible with existing discount options modal
- Preserves MRP details modal functionality
