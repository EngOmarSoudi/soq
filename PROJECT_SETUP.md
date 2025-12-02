# EcommStore - Modern E-commerce Platform

## Project Overview

EcommStore is a modern, fully-featured e-commerce platform built with Laravel 12, Filament 4, Tailwind CSS, and JavaScript. The platform includes:

### Key Features Implemented

#### 1. **Frontend Features**
- ✅ Responsive homepage with hero section
- ✅ Product listing with advanced filtering (by category, price, brand)
- ✅ Search functionality for products
- ✅ Shopping cart with add/remove/update operations
- ✅ Wishlist functionality
- ✅ User authentication (login/register)
- ✅ User profile management
- ✅ Order history and order status tracking
- ✅ Address management with map integration support
- ✅ Language switcher (English/Arabic) with i18n support
- ✅ Dark/Light theme toggle with localStorage persistence
- ✅ Checkout process with payment method selection
- ✅ Product reviews and ratings

#### 2. **Admin Panel Features (Filament 4)**
- ✅ Product Management
  - Add/Edit/Delete products
  - Support for local and online suppliers
  - Multi-language product names and descriptions
  - Stock inventory tracking
  - Featured products marking
  - Product attributes management
  
- ✅ Category Management
  - Create hierarchical categories
  - Multi-language support
  - Category images
  - Sort order management

- ✅ Review Management
  - View all reviews
  - Approve/Reject reviews
  - Manage review status (pending/approved/rejected)

- ✅ Order Management
  - View all orders
  - Update order status (pending/processing/shipped/delivered/cancelled/returned)
  - Track payment status
  - Manage shipping and billing addresses

- ✅ Coupon Management
  - Create promotional coupons
  - Set discount type (percentage/fixed)
  - Configure usage limits
  - Set validity periods
  - Multi-language coupon descriptions

- ✅ User Management
  - View all users
  - Manage user roles (customer/admin)
  - Track user status

- ✅ Analytics Dashboard
  - Order statistics
  - Revenue tracking
  - User metrics

#### 3. **Database Schema**
Tables implemented:
- `users` - User accounts with role management
- `categories` - Product categories with hierarchy
- `products` - Product catalog
- `product_attributes` - Product specifications
- `reviews` - Product reviews
- `wishlists` - User wishlists
- `cart_items` - Shopping cart items
- `addresses` - User addresses with coordinates
- `orders` - Order history
- `order_items` - Order item details
- `coupons` - Promotional coupons

#### 4. **Localization**
- English (en) and Arabic (ar) support
- Language preference stored in user preferences
- JSON-based translatable fields for products, categories, and coupons

#### 5. **Theme Management**
- Dark and Light modes
- User preferences saved in localStorage
- Admin preferences saved to database
- Automatic theme switching

## Project Structure

```
ecommerce/
├── app/
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Review.php
│   │   ├── Order.php
│   │   ├── Coupon.php
│   │   └── ... (other models)
│   ├── Http/Controllers/
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── ProfileController.php
│   │   ├── LocaleController.php
│   │   └── ThemeController.php
│   ├── Filament/Resources/
│   │   ├── Categories/CategoryResource.php
│   │   ├── Products/ProductResource.php
│   │   ├── Reviews/ReviewResource.php
│   │   ├── Orders/OrderResource.php
│   │   ├── Coupons/CouponResource.php
│   │   └── Users/UserResource.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── Filament/AdminPanelProvider.php
├── resources/
│   ├── css/app.css (Tailwind CSS)
│   ├── views/
│   │   ├── layouts/app.blade.php
│   │   ├── home.blade.php
│   │   ├── products/
│   │   ├── cart/
│   │   ├── checkout/
│   │   └── profile/
│   └── js/app.js
├── database/
│   └── migrations/
│       ├── *_create_categories_table.php
│       ├── *_create_products_table.php
│       ├── *_create_reviews_table.php
│       ├── *_create_orders_table.php
│       └── ... (other migrations)
├── routes/
│   └── web.php
├── tailwind.config.js
├── postcss.config.js
└── vite.config.js
```

## Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite (or MySQL)

### Steps to Setup

1. **Navigate to project**
```bash
cd c:\Users\hp\Desktop\work\last\ecommerce
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Run migrations**
```bash
php artisan migrate
```

5. **Build frontend assets**
```bash
npm run build  # Production
npm run dev    # Development with hot reload
```

6. **Start development server**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Admin Access
- Visit `http://localhost:8000/admin`
- Use Filament's built-in authentication

## Key Technologies Used

- **Backend**: Laravel 12
- **Admin Panel**: Filament 4
- **Frontend Framework**: Tailwind CSS + Blade Templates
- **Database**: SQLite (default), supports MySQL/PostgreSQL
- **Localization**: Laravel's i18n with JSON translation files
- **Icons**: Heroicons (via Blade Icon Kit)
- **State Management**: Laravel Session + localStorage

## API Endpoints

### Public Routes
- `GET /` - Homepage
- `GET /products` - Product listing with filters
- `GET /products/{slug}` - Product details
- `GET /category/{slug}` - Category products
- `POST /set-locale/{locale}` - Set language
- `POST /set-theme` - Set theme preference

### Protected Routes (Auth Required)
- `GET /cart` - View cart
- `POST /cart/add` - Add to cart
- `POST /cart/remove/{id}` - Remove from cart
- `POST /cart/update/{id}` - Update cart item quantity
- `GET /checkout` - Checkout page
- `POST /order/place` - Place order
- `GET /profile` - User profile
- `GET /profile/orders` - User orders
- `POST /profile/update` - Update profile
- `GET /profile/addresses` - User addresses
- `POST /profile/address/add` - Add new address
- `POST /profile/address/{id}/delete` - Delete address

### Admin Routes
- `GET /admin` - Admin dashboard
- `GET /admin/resources/categories` - Manage categories
- `GET /admin/resources/products` - Manage products
- `GET /admin/resources/reviews` - Manage reviews
- `GET /admin/resources/orders` - Manage orders
- `GET /admin/resources/coupons` - Manage coupons
- `GET /admin/resources/users` - Manage users

## Features Still to Implement

The following features are planned for future development:
- [ ] Full payment gateway integration (Stripe, PayPal)
- [ ] Advanced map integration for delivery location selection
- [ ] Email notifications for orders
- [ ] Product image gallery with carousel
- [ ] Advanced analytics dashboard
- [ ] Inventory low-stock alerts
- [ ] Seller/Vendor management
- [ ] Product variants and options
- [ ] Customer reviews with images
- [ ] Social sharing features

## Database Relationships

```
User
├── Orders
├── CartItems
├── Wishlists
├── Reviews
└── Addresses

Product
├── Category
├── Reviews
├── ProductAttributes
├── CartItems
└── OrderItems

Order
├── User
├── Coupon
├── OrderItems
├── ShippingAddress
└── BillingAddress

Category
├── Products
└── Children (subcategories)
```

## File Upload Configuration

Upload directories (ensure these exist and are writable):
- `storage/app/public/products/` - Product images
- `storage/app/public/categories/` - Category images
- `storage/app/public/users/` - User profile images

Link storage:
```bash
php artisan storage:link
```

## Security Features Implemented

- ✅ CSRF token protection
- ✅ Password hashing (bcrypt)
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade escaping)
- ✅ Authentication & Authorization
- ✅ Role-based access control
- ✅ Secure session management

## Performance Optimizations

- Tailwind CSS purging for production
- Database query optimization with eager loading
- Asset bundling with Vite
- Pagination for large datasets
- Efficient JSON field queries

## Testing

Run tests:
```bash
php artisan test
```

## Troubleshooting

### Port Already in Use
Use a different port:
```bash
php artisan serve --port=8080
```

### Storage Permissions
Ensure storage directory is writable:
```bash
chmod -R 775 storage bootstrap/cache
```

### Database Issues
Reset database:
```bash
php artisan migrate:refresh --seed
```

## Support & Documentation

- Laravel Docs: https://laravel.com/docs
- Filament Docs: https://filamentphp.com/docs
- Tailwind CSS: https://tailwindcss.com/docs

## License

This project is built for educational and commercial purposes.

## Future Enhancements

1. **Mobile App**: React Native mobile application
2. **Microservices**: Decouple services (payments, notifications)
3. **AI Features**: Product recommendations, chatbot support
4. **Advanced Analytics**: Customer behavior tracking, sales forecasting
5. **Multi-vendor**: Marketplace functionality
6. **Blockchain**: NFT products and loyalty programs
