-- Add Sample Products to Restaurant Platform
-- Run this in HeidiSQL, phpMyAdmin, or MySQL command line

USE restaurant_platform;

-- First, add product categories
INSERT INTO product_category (name, description) VALUES
('Food', 'Delicious food items from our kitchen'),
('Drinks', 'Refreshing beverages and cocktails');

-- Add Sample Food Products
INSERT INTO product (name, description, price, image, available, category_id, created_at) VALUES
('Margherita Pizza', 'Classic pizza with tomato sauce, mozzarella, and fresh basil', 12.99, NULL, 1, 1, NOW()),
('Cheeseburger', 'Juicy beef patty with cheddar cheese, lettuce, tomato, and special sauce', 10.99, NULL, 1, 1, NOW()),
('Caesar Salad', 'Fresh romaine lettuce with parmesan cheese, croutons, and Caesar dressing', 8.99, NULL, 1, 1, NOW()),
('Spaghetti Carbonara', 'Creamy pasta with bacon, eggs, and parmesan cheese', 13.99, NULL, 1, 1, NOW()),
('Grilled Salmon', 'Fresh Atlantic salmon with lemon butter sauce and vegetables', 18.99, NULL, 1, 1, NOW()),
('Chicken Wings', 'Crispy wings with your choice of sauce (BBQ, Buffalo, or Honey Garlic)', 9.99, NULL, 1, 1, NOW()),
('Beef Tacos', 'Three soft tacos with seasoned beef, lettuce, cheese, and salsa', 11.99, NULL, 1, 1, NOW()),
('Vegetable Stir Fry', 'Fresh vegetables stir-fried with soy sauce and served with rice', 10.99, NULL, 1, 1, NOW());

-- Add Sample Drink Products
INSERT INTO product (name, description, price, image, available, category_id, created_at) VALUES
('Coca Cola', 'Classic Coca Cola (330ml)', 2.99, NULL, 1, 2, NOW()),
('Fresh Orange Juice', 'Freshly squeezed orange juice', 4.99, NULL, 1, 2, NOW()),
('Iced Coffee', 'Cold brew coffee with ice', 3.99, NULL, 1, 2, NOW()),
('Mojito', 'Refreshing cocktail with rum, mint, lime, and soda', 8.99, NULL, 1, 2, NOW()),
('Red Wine', 'House red wine (glass)', 6.99, NULL, 1, 2, NOW()),
('Craft Beer', 'Local craft beer on tap', 5.99, NULL, 1, 2, NOW()),
('Lemonade', 'Homemade fresh lemonade', 3.49, NULL, 1, 2, NOW()),
('Espresso', 'Double shot espresso', 2.99, NULL, 1, 2, NOW());

-- Verify the data
SELECT 'Product Categories:' as Info;
SELECT * FROM product_category;

SELECT 'Products:' as Info;
SELECT p.id, p.name, p.price, pc.name as category, p.available 
FROM product p 
JOIN product_category pc ON p.category_id = pc.id
ORDER BY pc.name, p.name;

