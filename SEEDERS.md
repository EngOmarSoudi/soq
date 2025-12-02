# Database Seeders Documentation

This document explains the comprehensive database seeders for the e-commerce application.

## Overview

The seeders populate the database with realistic test data to fully test application functionality:

1. **Categories**: 8 product categories (Electronics, Clothing, Home & Garden, etc.)
2. **Products**: 26 products across all categories with pricing and stock
3. **John User**: Primary test user (john@example.com) with multiple addresses
4. **Cart Items**: Sample items in John's shopping cart
5. **Order History**: 6 orders with various statuses (pending, processing, shipped, delivered, cancelled)

## Test Accounts

### Admin Account
- Email: `admin@example.com`
- Password: `password123`
- Role: Admin
- Access: Admin panel at `/admin`

### Regular Customers
- jane@example.com (Chicago, IL)
- ahmed@example.com (Riyadh, Saudi Arabia)
- sara@example.com (Houston, TX)
- michael@example.com (Phoenix, AZ)
- john@example.com (Los Angeles, CA) - **Primary test user with full data**

All regular customers use password: `password123`

## Running the Seeders

### Fresh Database Reset
To reset the database and run all seeders:
```bash
php artisan migrate:fresh --seed
```

This command:
1. Drops all tables
2. Re-creates all tables from migrations
3. Runs all seeders in order

### Individual Seeders

Run specific seeders if needed:

```bash
# Run all default seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductSeeder
php artisan db:seed --class=JohnUserSeeder
php artisan db:seed --class=CartItemSeeder
php artisan db:seed --class=OrderSeeder

# Run custom seeder class
php artisan db:seed --class=Database\\Seeders\\CategorySeeder
```

## Seeder Details

### 1. DatabaseSeeder (Main Seeder)
**File**: `database/seeders/DatabaseSeeder.php`

Creates:
- 1 Admin user with default address
- 4 Regular customers (Jane, Ahmed, Sara, Michael) with default addresses
- Calls all individual seeders in order

### 2. CategorySeeder
**File**: `database/seeders/CategorySeeder.php`

Creates 8 product categories:
1. Electronics
2. Clothing
3. Home & Garden
4. Sports & Outdoors
5. Books & Media
6. Beauty & Personal Care
7. Food & Beverages
8. Toys & Games

All categories are bilingual (English & Arabic) with slugs for routing.

### 3. ProductSeeder
**File**: `database/seeders/ProductSeeder.php`

Creates 26 products:
- 4 Electronics products (Headphones, Charger, Smart Watch, Power Bank)
- 3 Clothing products (T-Shirt, Jeans, Jacket)
- 3 Home & Garden products (Desk Lamp, Cutting Board Set, Tool Kit)
- 2 Sports & Outdoors products (Yoga Mat, Sports Watch)
- 1 Books & Media product (Code Book)
- 2 Beauty & Personal Care products (Face Moisturizer, Shampoo Combo)
- 2 Food & Beverages products (Coffee Beans, Green Tea)
- 2 Toys & Games products (STEM Puzzle, Board Games)

Each product includes:
- Bilingual name and description
- SKU (Stock Keeping Unit)
- Price and cost price
- Stock quantity
- Average rating (4.3 - 4.9)
- Featured flag for some products

### 4. JohnUserSeeder
**File**: `database/seeders/JohnUserSeeder.php`

Creates john@example.com with:
- 3 shipping addresses:
  - Home: 456 Main Avenue, Los Angeles, CA 90001 (Default)
  - Office: 789 Business Park, Los Angeles, CA 90002
  - Parent's House: 321 Oak Street, San Francisco, CA 94102

### 5. CartItemSeeder
**File**: `database/seeders/CartItemSeeder.php`

Adds items to John's cart:
- 3 cart items with different quantities
- References actual products from database
- Includes correct pricing

### 6. OrderSeeder
**File**: `database/seeders/OrderSeeder.php`

Creates 6 orders for john@example.com with:

**Order 1 - Pending** (5 days ago)
- Status: Pending
- Payment Status: Pending
- Payment Method: Bank Transfer
- 2 items

**Order 2 - Processing** (10 days ago)
- Status: Processing
- Payment Status: Completed
- Payment Method: Credit Card
- 2 items

**Order 3 - Shipped** (15 days ago)
- Status: Shipped
- Payment Status: Completed
- Payment Method: Credit Card
- Shipped 12 days ago
- 3 items

**Order 4 - Delivered** (25 days ago)
- Status: Delivered
- Payment Status: Completed
- Payment Method: Bank Transfer
- Shipped 22 days ago
- Delivered 20 days ago
- 3 items

**Order 5 - Delivered** (30 days ago)
- Status: Delivered
- Payment Status: Completed
- Payment Method: Credit Card
- Shipped 28 days ago
- Delivered 26 days ago
- 3 items

**Order 6 - Cancelled** (35 days ago)
- Status: Cancelled
- Payment Status: Refunded
- Payment Method: Credit Card
- 1 item

All orders include:
- Generated order numbers (ORD-YYYYMMDD-XXXXXX format)
- Calculated subtotal, shipping, tax, and total
- Associated order items with pricing
- Shipping and billing addresses

## Testing the Seeders

After running migrations with seeders:

### 1. Test Login
```
Email: john@example.com
Password: password123
```

### 2. Test Admin Panel
```
Email: admin@example.com
Password: password123
Navigate to: /admin
```

### 3. Verify Data
- Browse products in different categories
- View John's cart items
- Check John's order history with various statuses
- View John's saved addresses
- Test checkout with John's addresses

## Notes

- All products have bilingual names and descriptions (English & Arabic)
- All categories are marked as active (`is_active = true`)
- Prices are set realistically with cost prices for profit margin calculation
- Stock quantities range from 25 to 200 items
- Order timestamps are set relative to current date (using Carbon::now())
- All seeders use `WithoutModelEvents` to skip event firing during seeding
- Dependencies between seeders are handled through queries (e.g., finding John user before adding orders)

## Troubleshooting

### Seeder Fails to Run
- Ensure all migrations are run: `php artisan migrate`
- Check database connection in `.env` file
- Verify all model classes exist and have correct namespaces

### Data Not Appearing
- Clear application cache: `php artisan cache:clear`
- Check database connection
- Verify foreign key constraints are satisfied

### Duplicate Data After Re-running
- Use `php artisan migrate:fresh --seed` to reset database first
- Or manually truncate tables before running seeders
