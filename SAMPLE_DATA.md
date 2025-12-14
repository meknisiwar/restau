# Sample Data for Testing

## Quick SQL Script to Add Sample Data

Run these SQL commands in your MySQL database to add sample data:

```sql
-- Use the database
USE restaurant_platform;

-- Add Product Categories
INSERT INTO product_category (name, description) VALUES
('Food', 'Delicious food items from our kitchen'),
('Drinks', 'Refreshing beverages and cocktails');

-- Add Sample Products (Food)
INSERT INTO product (name, description, price, image, available, category_id, created_at) VALUES
('Margherita Pizza', 'Classic pizza with tomato sauce, mozzarella, and fresh basil', 12.99, NULL, 1, 1, NOW()),
('Cheeseburger', 'Juicy beef patty with cheddar cheese, lettuce, tomato, and special sauce', 10.99, NULL, 1, 1, NOW()),
('Caesar Salad', 'Fresh romaine lettuce with parmesan cheese, croutons, and Caesar dressing', 8.99, NULL, 1, 1, NOW()),
('Spaghetti Carbonara', 'Creamy pasta with bacon, eggs, and parmesan cheese', 13.99, NULL, 1, 1, NOW()),
('Grilled Salmon', 'Fresh Atlantic salmon with lemon butter sauce and vegetables', 18.99, NULL, 1, 1, NOW()),
('Chicken Wings', 'Crispy wings with your choice of sauce (BBQ, Buffalo, or Honey Garlic)', 9.99, NULL, 1, 1, NOW());

-- Add Sample Products (Drinks)
INSERT INTO product (name, description, price, image, available, category_id, created_at) VALUES
('Coca Cola', 'Classic Coca Cola (330ml)', 2.99, NULL, 1, 2, NOW()),
('Fresh Orange Juice', 'Freshly squeezed orange juice', 4.99, NULL, 1, 2, NOW()),
('Iced Coffee', 'Cold brew coffee with ice', 3.99, NULL, 1, 2, NOW()),
('Mojito', 'Refreshing cocktail with rum, mint, lime, and soda', 8.99, NULL, 1, 2, NOW()),
('Red Wine', 'House red wine (glass)', 6.99, NULL, 1, 2, NOW()),
('Craft Beer', 'Local craft beer on tap', 5.99, NULL, 1, 2, NOW());

-- Add Sample Admin User (password: admin123)
-- Note: You'll need to hash the password properly using Symfony's password hasher
-- This is just a placeholder - use the registration form or Symfony console command instead
-- INSERT INTO user (email, roles, password, first_name, last_name, phone, address, created_at) VALUES
-- ('admin@restaurant.com', '["ROLE_ADMIN"]', '$2y$13$hashed_password_here', 'Admin', 'User', '555-0100', '123 Admin St', NOW());

-- Add Sample Client User (password: client123)
-- INSERT INTO user (email, roles, password, first_name, last_name, phone, address, created_at) VALUES
-- ('client@example.com', '["ROLE_USER"]', '$2y$13$hashed_password_here', 'John', 'Doe', '555-0101', '456 Client Ave', NOW());
```

## Creating Users via Registration

Instead of inserting users directly, it's recommended to:

1. **Register via the web interface** at http://localhost:8000/register
2. **Promote to admin** using SQL:
   ```sql
   UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'your-email@example.com';
   ```

## Sample Service Data

```sql
-- Add Sample Services
INSERT INTO service (name, description, price, icon, active, created_at) VALUES
('Catering Service', 'Full catering service for events and parties', 500.00, 'truck', 1, NOW()),
('Private Dining', 'Private dining room for special occasions', 200.00, 'door-closed', 1, NOW()),
('Delivery', 'Home delivery service within 5km radius', 5.00, 'bicycle', 1, NOW()),
('Event Planning', 'Complete event planning and coordination', 1000.00, 'calendar-event', 1, NOW());
```

## Testing Workflow

After adding sample data:

1. **Browse Products**: Visit http://localhost:8000/products
2. **Filter by Category**: Click on "Food" or "Drinks" buttons
3. **View Product Details**: Click on any product
4. **Register/Login**: Create an account or login
5. **Add to Cart**: Add products to your cart
6. **Checkout**: Complete an order
7. **Make Reservation**: Book a table
8. **Admin Access**: Login as admin and visit /admin

## Useful SQL Queries for Testing

```sql
-- View all products with categories
SELECT p.id, p.name, p.price, pc.name as category 
FROM product p 
JOIN product_category pc ON p.category_id = pc.id;

-- View all orders with user info
SELECT o.id, u.email, o.status, o.total_amount, o.created_at 
FROM `order` o 
JOIN user u ON o.user_id = u.id 
ORDER BY o.created_at DESC;

-- View all reservations
SELECT r.id, u.email, r.reservation_date, r.number_of_guests, r.status 
FROM reservation r 
JOIN user u ON r.user_id = u.id 
ORDER BY r.reservation_date DESC;

-- Count products by category
SELECT pc.name, COUNT(p.id) as product_count 
FROM product_category pc 
LEFT JOIN product p ON pc.id = p.category_id 
GROUP BY pc.id;

-- View cart items with product details
SELECT c.id as cart_id, u.email, p.name, ci.quantity, ci.price 
FROM cart c 
JOIN user u ON c.user_id = u.id 
JOIN cart_item ci ON ci.cart_id = c.id 
JOIN product p ON ci.product_id = p.id 
WHERE c.active = 1;
```

## Reset Database (if needed)

```sql
-- Clear all data (use with caution!)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE cart_item;
TRUNCATE TABLE cart;
TRUNCATE TABLE order_item;
TRUNCATE TABLE `order`;
TRUNCATE TABLE reservation;
TRUNCATE TABLE product;
TRUNCATE TABLE product_category;
TRUNCATE TABLE service;
TRUNCATE TABLE user;
SET FOREIGN_KEY_CHECKS = 1;
```

Then re-run the sample data SQL above.

