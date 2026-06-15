# PetCare Store - PHP Backend Implementation

## Overview
This is a complete PHP backend implementation for the PetCare e-commerce store. The system includes user authentication, product management, shopping cart functionality, order processing, and contact form handling.

## Features Implemented

### 1. **User Authentication**
- User registration with email validation
- Secure login with password hashing (bcrypt)
- Session management
- User profile information storage

### 2. **Product Management**
- Database-driven product catalog
- Product images and descriptions
- Price management
- Stock tracking

### 3. **Shopping Cart**
- Add/remove items from cart
- Update quantity
- Real-time cart total calculation
- User-specific cart (stored in database)

### 4. **Order Management**
- Create orders from cart
- Multiple payment method support (Card, UPI, Net Banking, COD)
- Order history tracking
- Order status management

### 5. **Contact Form**
- Message submission
- Email validation
- Database storage of inquiries

### 6. **Security Features**
- Password hashing with bcrypt
- SQL injection prevention (prepared statements)
- XSS protection (htmlspecialchars)
- CSRF protection via session management

## File Structure

```
project/
├── config.php              # Database configuration
├── auth.php                # Authentication functions
├── api.php                 # AJAX API endpoints
├── db_setup.php            # Database initialization script
├── index.php               # Home page
├── products.php            # Products page
├── login.php               # Login/Registration page
├── cart.php                # Shopping cart page
├── contact.php             # Contact page
├── logout.php              # Logout handler
├── style.css               # CSS styling
└── images/                 # Product images
```

## Database Tables

### users
- user_id (Primary Key)
- username (Unique)
- email (Unique)
- password_hash
- full_name
- phone
- address
- created_at, updated_at

### products
- product_id (Primary Key)
- name
- description
- price
- image_url
- stock
- created_at, updated_at

### cart
- cart_id (Primary Key)
- user_id (Foreign Key)
- product_id (Foreign Key)
- quantity
- added_at

### orders
- order_id (Primary Key)
- user_id (Foreign Key)
- total_amount
- payment_method
- order_status
- created_at, updated_at

### order_items
- order_item_id (Primary Key)
- order_id (Foreign Key)
- product_id (Foreign Key)
- quantity
- price

### contacts
- contact_id (Primary Key)
- name
- email
- message
- created_at
- status

## Setup Instructions

### Step 1: Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB server running
- Local development server (Apache, Nginx, or PHP built-in server)

### Step 2: Database Setup

1. Create a new database or use an existing one:
```sql
CREATE DATABASE petcare_store;
```

2. Update database credentials in `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'petcare_store');
```

3. Run the setup script by accessing:
```
http://localhost/project/db_setup.php
```

This will automatically:
- Create the database (if it doesn't exist)
- Create all necessary tables
- Insert sample products

### Step 3: Configure Server

**For Apache (with mod_rewrite):**
Create `.htaccess` in the project root:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

**For PHP Built-in Server:**
```bash
php -S localhost:8000
```

**For Nginx:**
Add to your server block:
```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

### Step 4: Start Using

1. Access the application:
```
http://localhost/project/index.php
```

2. Register a new account on the login page
3. Browse products
4. Add items to cart
5. Proceed to checkout
6. Submit contact inquiries

## API Endpoints

All API endpoints are accessed via `api.php` using POST/GET requests:

### Get Products
```
GET /api.php?action=get_products
Response: { success: true, products: [...] }
```

### Get Cart
```
GET /api.php?action=get_cart
Requires: User logged in
Response: { success: true, cart: [...] }
```

### Add to Cart
```
POST /api.php
Data: action=add_to_cart&product_id=1&quantity=1
Requires: User logged in
```

### Remove from Cart
```
POST /api.php
Data: action=remove_from_cart&cart_id=1
Requires: User logged in
```

### Update Quantity
```
POST /api.php
Data: action=update_quantity&cart_id=1&quantity=2
Requires: User logged in
```

### Checkout
```
POST /api.php
Data: action=checkout&payment_method=Card
Requires: User logged in
Response: { success: true, order_id: 1, total_amount: 99.99 }
```

### Submit Contact
```
POST /api.php
Data: action=submit_contact&name=John&email=john@email.com&message=Hello
```

## User Registration

Users can register via the login.php page with:
- Full Name
- Username
- Email
- Password

Passwords are automatically hashed using bcrypt for security.

## Payment Methods Supported

- Credit/Debit Card
- UPI (Unified Payments Interface)
- Net Banking
- Cash on Delivery (COD)

*Note: This is a demo implementation. Real payment gateway integration would be needed for production.*

## Session Management

- Session timeout: 24 minutes (PHP default)
- Sessions stored on server
- Session ID in cookies

To modify session timeout, add to `auth.php`:
```php
ini_set('session.gc_maxlifetime', 3600); // 1 hour
session_set_cookie_params(3600);
```

## Security Considerations

1. **Password Security**: All passwords are hashed using bcrypt
2. **SQL Injection Prevention**: All queries use prepared statements
3. **XSS Protection**: Output is escaped with htmlspecialchars()
4. **CSRF Protection**: Session-based token validation
5. **HTTPS**: Use HTTPS in production
6. **Database Backups**: Regular backups recommended

## Configuration Tips

### Change Database Credentials
Edit `config.php`:
```php
define('DB_HOST', 'your_host');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
```

### Add New Products
Insert directly in database or via admin interface (not yet implemented)

### Modify Session Timeout
Edit `auth.php`:
```php
ini_set('session.gc_maxlifetime', 7200);
```

## Troubleshooting

### Database Connection Error
- Check if MySQL/MariaDB is running
- Verify credentials in `config.php`
- Run `db_setup.php` to initialize database

### "Page not found" errors
- Ensure PHP files are in correct directory
- Check file permissions (755 for directories, 644 for files)
- Verify web server is serving PHP files

### Cart not saving
- Ensure user is logged in
- Check database connection
- Verify cart table exists

### Contact form not working
- Check that `api.php` is accessible
- Verify form fields match API expectations
- Check browser console for JavaScript errors

## Future Enhancements

- Admin dashboard for product management
- Payment gateway integration (Stripe, PayPal)
- Email notifications for orders
- User profile management
- Order tracking
- Product reviews and ratings
- Search and filtering
- Product recommendations
- Inventory management
- Admin panel

## Support

For issues or questions, ensure:
1. PHP version is 7.4 or higher
2. MySQL/MariaDB is running
3. Database is properly initialized
4. All files are in the correct directory
5. Web server has proper permissions

## License

This project is open source and available for modification and distribution.

---

**Last Updated**: May 14, 2026
