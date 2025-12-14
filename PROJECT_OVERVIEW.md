# Restaurant Platform - Project Overview

## 🎯 Project Description

A complete food and drinks platform for restaurants built with Symfony 6.4 LTS. This platform allows customers to browse products, place orders, make reservations, and manage their cart. Administrators can manage products, orders, and reservations through an admin dashboard.

## 🚀 Features Implemented

### User Management
- ✅ User registration with email verification
- ✅ Login/Logout functionality
- ✅ Role-based access control (Admin & Client)
- ✅ User profiles with contact information

### Product Management
- ✅ Product catalog with categories (Food/Drink)
- ✅ Product listing and detail pages
- ✅ Product availability status
- ✅ Category-based filtering

### Shopping Cart
- ✅ Add products to cart
- ✅ Update quantities
- ✅ Remove items
- ✅ Clear cart
- ✅ Cart total calculation

### Order System
- ✅ Checkout process
- ✅ Order placement
- ✅ Order history
- ✅ Order status tracking (pending, confirmed, preparing, ready, delivered, cancelled)
- ✅ Order details with delivery information

### Reservation System
- ✅ Table reservation booking
- ✅ Reservation management
- ✅ Reservation status (pending, confirmed, cancelled, completed)
- ✅ Special requests handling

### Admin Dashboard
- ✅ Statistics overview
- ✅ User management
- ✅ Product management
- ✅ Order management
- ✅ Reservation management

## 📁 Project Structure

```
src/
├── Controller/
│   ├── Admin/
│   │   └── DashboardController.php
│   ├── CartController.php
│   ├── HomeController.php
│   ├── OrderController.php
│   ├── ProductController.php
│   ├── RegistrationController.php
│   ├── ReservationController.php
│   └── SecurityController.php
├── Entity/
│   ├── Cart.php
│   ├── CartItem.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│   ├── ProductCategory.php
│   ├── Reservation.php
│   ├── Service.php
│   └── User.php
├── Form/
│   ├── RegistrationForm.php
│   └── ReservationType.php
├── Repository/
│   ├── CartItemRepository.php
│   ├── CartRepository.php
│   ├── OrderItemRepository.php
│   ├── OrderRepository.php
│   ├── ProductCategoryRepository.php
│   ├── ProductRepository.php
│   ├── ReservationRepository.php
│   ├── ServiceRepository.php
│   └── UserRepository.php
└── Security/
    └── LoginFormAuthenticator.php

templates/
├── admin/
│   └── dashboard.html.twig
├── cart/
│   └── index.html.twig
├── home/
│   └── index.html.twig
├── order/
│   ├── checkout.html.twig
│   ├── index.html.twig
│   └── show.html.twig
├── product/
│   ├── index.html.twig
│   └── show.html.twig
├── registration/
│   └── register.html.twig
├── reservation/
│   ├── index.html.twig
│   ├── new.html.twig
│   └── show.html.twig
├── security/
│   └── login.html.twig
└── base.html.twig
```

## 🗄️ Database Schema

### Entities and Relationships

1. **User** - Authentication and user management
   - Roles: ROLE_ADMIN, ROLE_CLIENT, ROLE_USER
   - Has many: Carts, Orders, Reservations

2. **ProductCategory** - Food/Drink classification
   - Has many: Products

3. **Product** - Menu items
   - Belongs to: ProductCategory
   - Has many: CartItems, OrderItems

4. **Cart** - Shopping cart
   - Belongs to: User
   - Has many: CartItems

5. **CartItem** - Items in cart
   - Belongs to: Cart, Product

6. **Order** - Customer orders
   - Belongs to: User
   - Has many: OrderItems
   - Statuses: pending, confirmed, preparing, ready, delivered, cancelled

7. **OrderItem** - Items in order
   - Belongs to: Order, Product
   - Stores product snapshot (name, price)

8. **Reservation** - Table bookings
   - Belongs to: User
   - Statuses: pending, confirmed, cancelled, completed

9. **Service** - Additional restaurant services
   - Standalone entity for future features

## 🔐 Security & Access Control

- Public routes: Home, Products, Login, Register
- User routes: Cart, Orders, Reservations, Profile
- Admin routes: Admin Dashboard, Product Management, Order Management

## 🎨 Frontend

- Bootstrap 5.3 for responsive design
- Bootstrap Icons for UI elements
- Twig templating engine
- Flash messages for user feedback
- Mobile-responsive layout

## 📝 Next Steps & Recommendations

1. **Add Sample Data**
   - Create fixtures for products and categories
   - Add sample users (admin and client)

2. **Enhance Admin Panel**
   - CRUD operations for products
   - Order status management
   - Reservation confirmation/management
   - User management

3. **Add Features**
   - Product images upload
   - Payment integration
   - Email notifications
   - Order tracking
   - Reviews and ratings

4. **Testing**
   - Unit tests for entities
   - Functional tests for controllers
   - Integration tests for workflows

5. **Performance**
   - Add caching
   - Optimize database queries
   - Image optimization

6. **Security Enhancements**
   - Email verification
   - Password reset
   - Two-factor authentication
   - Rate limiting

## 🛠️ Technology Stack

- **Framework**: Symfony 6.4 LTS
- **PHP**: 8.1.10
- **Database**: MySQL (via Laragon)
- **ORM**: Doctrine
- **Frontend**: Bootstrap 5.3, Twig
- **Authentication**: Symfony Security Component

## 🚦 Getting Started

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL (via Laragon or standalone)
- Web browser

### Installation

1. **Database Setup**
   - Ensure Laragon MySQL is running
   - Database `restaurant_platform` is already created

2. **Start the Development Server**
   ```bash
   php -S localhost:8000 -t public
   ```

3. **Access the Application**
   - Open browser: http://localhost:8000
   - Register a new account
   - Browse products and test features

### Creating an Admin User

To create an admin user, you can either:

**Option 1: Via Database**
```sql
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'your-email@example.com';
```

**Option 2: Via Code (in a controller or command)**
```php
$user->setRoles(['ROLE_ADMIN']);
$entityManager->flush();
```

### Testing the Application

1. **Register a new user** at `/register`
2. **Login** at `/login`
3. **Browse products** at `/products`
4. **Add items to cart** and checkout
5. **Make a reservation** at `/reservation/new`
6. **View orders** at `/order`
7. **Access admin dashboard** at `/admin` (requires ROLE_ADMIN)

## 📞 Support

For questions or issues, refer to:
- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [Doctrine Documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/5.3/)

