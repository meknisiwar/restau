-- Insert missing users (only if not exists)
INSERT INTO user (email, roles, password, first_name, last_name, phone, address, created_at)
SELECT 'admin@example.com','["ROLE_ADMIN"]','$2y$10$2Iy4wXvjkxj2iQwI7BsPQeQoFNIe9nHdoXOTdCZspa7WjvyvVnawO','Admin','User',NULL,NULL,NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM user WHERE email='admin@example.com');

INSERT INTO user (email, roles, password, first_name, last_name, phone, address, created_at)
SELECT 'alice@example.com','["ROLE_USER"]','$2y$10$2Iy4wXvjkxj2iQwI7BsPQeQoFNIe9nHdoXOTdCZspa7WjvyvVnawO','Alice','Smith',NULL,NULL,NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM user WHERE email='alice@example.com');

INSERT INTO user (email, roles, password, first_name, last_name, phone, address, created_at)
SELECT 'bob@example.com','["ROLE_USER"]','$2y$10$2Iy4wXvjkxj2iQwI7BsPQeQoFNIe9nHdoXOTdCZspa7WjvyvVnawO','Bob','Jones',NULL,NULL,NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM user WHERE email='bob@example.com');

-- Services
INSERT INTO service (name, description, price, icon, active, created_at)
SELECT 'Delivery','Fast delivery to your door',5.00,NULL,1,NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM service WHERE name='Delivery');
INSERT INTO service (name, description, price, icon, active, created_at)
SELECT 'Catering','Full-service catering for events',100.00,NULL,1,NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM service WHERE name='Catering');
INSERT INTO service (name, description, price, icon, active, created_at)
SELECT 'Cleaning','On-site cleaning service',20.00,NULL,1,NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM service WHERE name='Cleaning');

-- Reservations
INSERT INTO reservation (reservation_date, number_of_guests, status, special_requests, table_number, created_at, user_id)
SELECT DATE_ADD(NOW(), INTERVAL 2 DAY),4,'confirmed','Window seat','12',NOW(),(SELECT id FROM user WHERE email='alice@example.com')
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM reservation r JOIN user u ON r.user_id=u.id WHERE u.email='alice@example.com' AND r.number_of_guests=4);

INSERT INTO reservation (reservation_date, number_of_guests, status, special_requests, table_number, created_at, user_id)
SELECT DATE_ADD(NOW(), INTERVAL 5 DAY),2,'pending','Near bar','3',NOW(),(SELECT id FROM user WHERE email='bob@example.com')
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM reservation r JOIN user u ON r.user_id=u.id WHERE u.email='bob@example.com' AND r.number_of_guests=2);

-- Cart for Alice
INSERT INTO cart (created_at, updated_at, active, user_id)
SELECT NOW(),NULL,1,(SELECT id FROM user WHERE email='alice@example.com') FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM cart WHERE user_id=(SELECT id FROM user WHERE email='alice@example.com'));

-- Ensure an order for Alice (pending)
INSERT INTO order (status, total_amount, delivery_address, phone, notes, created_at, user_id)
SELECT 'pending',0,'123 Main St','1234567890','Leave at door',NOW(),(SELECT id FROM user WHERE email='alice@example.com')
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM order WHERE user_id=(SELECT id FROM user WHERE email='alice@example.com') AND status='pending');

-- Insert order items if missing and update total
SET @uid = (SELECT id FROM user WHERE email='alice@example.com');
SET @oid = (SELECT id FROM order WHERE user_id=@uid AND status='pending' ORDER BY id DESC LIMIT 1);

INSERT INTO order_item (quantity, price, product_name, order_ref_id, product_id)
SELECT 2,12.99,'Margherita Pizza',@oid,(SELECT id FROM product WHERE name='Margherita Pizza' LIMIT 1)
FROM DUAL WHERE @oid IS NOT NULL AND NOT EXISTS (SELECT 1 FROM order_item WHERE order_ref_id=@oid AND product_name='Margherita Pizza');

INSERT INTO order_item (quantity, price, product_name, order_ref_id, product_id)
SELECT 1,5.99,'Craft Beer',@oid,(SELECT id FROM product WHERE name='Craft Beer' LIMIT 1)
FROM DUAL WHERE @oid IS NOT NULL AND NOT EXISTS (SELECT 1 FROM order_item WHERE order_ref_id=@oid AND product_name='Craft Beer');

-- Update order total
UPDATE order SET total_amount = (SELECT IFNULL(SUM(price*quantity),0) FROM order_item WHERE order_ref_id=@oid) WHERE id=@oid;

-- Show counts
SELECT COUNT(*) AS users FROM user;
SELECT COUNT(*) AS services FROM service;
SELECT COUNT(*) AS reservations FROM reservation;
SELECT COUNT(*) AS carts FROM cart;
SELECT COUNT(*) AS cart_items FROM cart_item;
SELECT COUNT(*) AS orders FROM order;
SELECT COUNT(*) AS order_items FROM order_item;
