INSERT INTO user (email, roles, password, first_name, last_name, phone, address, created_at) VALUES
('admin@example.com','["ROLE_ADMIN"]','','Admin','User',NULL,NULL,NOW()),
('alice@example.com','["ROLE_USER"]','','Alice','Smith',NULL,NULL,NOW()),
('bob@example.com','["ROLE_USER"]','','Bob','Jones',NULL,NULL,NOW());

INSERT INTO service (name, description, price, icon, active, created_at) VALUES
('Delivery','Fast delivery to your door',5.00,NULL,1,NOW()),
('Catering','Full-service catering for events',100.00,NULL,1,NOW()),
('Cleaning','On-site cleaning service',20.00,NULL,1,NOW());

INSERT INTO reservation (reservation_date, number_of_guests, status, special_requests, table_number, created_at, user_id) VALUES
(DATE_ADD(NOW(), INTERVAL 2 DAY),4,'confirmed','Window seat','12',NOW(),(SELECT id FROM user WHERE email='alice@example.com')),
(DATE_ADD(NOW(), INTERVAL 5 DAY),2,'pending','Near bar','3',NOW(),(SELECT id FROM user WHERE email='bob@example.com'));

INSERT INTO cart (created_at, updated_at, active, user_id) VALUES (NOW(),NULL,1,(SELECT id FROM user WHERE email='alice@example.com'));

INSERT INTO cart_item (quantity, price, created_at, cart_id, product_id) VALUES
(2,12.99,NOW(),(SELECT id FROM cart WHERE user_id=(SELECT id FROM user WHERE email='alice@example.com') ORDER BY id DESC LIMIT 1),(SELECT id FROM product WHERE name='Margherita Pizza' LIMIT 1)),
(1,5.99,NOW(),(SELECT id FROM cart WHERE user_id=(SELECT id FROM user WHERE email='alice@example.com') ORDER BY id DESC LIMIT 1),(SELECT id FROM product WHERE name='Craft Beer' LIMIT 1));

INSERT INTO order (status, total_amount, delivery_address, phone, notes, created_at, user_id) VALUES
('pending',25.98,'123 Main St','1234567890','Leave at door',NOW(),(SELECT id FROM user WHERE email='alice@example.com'));

INSERT INTO order_item (quantity, price, product_name, order_ref_id, product_id) VALUES
(2,12.99,'Margherita Pizza',(SELECT id FROM order WHERE user_id=(SELECT id FROM user WHERE email='alice@example.com') ORDER BY id DESC LIMIT 1),(SELECT id FROM product WHERE name='Margherita Pizza' LIMIT 1));
