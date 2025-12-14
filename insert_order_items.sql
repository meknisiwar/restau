-- Insert missing order items for Alice and update order total
SET @oid = (SELECT id FROM `order` WHERE user_id=(SELECT id FROM `user` WHERE email='alice@example.com') ORDER BY id DESC LIMIT 1);
INSERT INTO order_item (quantity, price, product_name, order_ref_id, product_id) VALUES
(2, 12.99, 'Margherita Pizza', @oid, (SELECT id FROM product WHERE name='Margherita Pizza' LIMIT 1)),
(1, 5.99, 'Craft Beer', @oid, (SELECT id FROM product WHERE name='Craft Beer' LIMIT 1));

UPDATE `order` SET total_amount = (SELECT IFNULL(SUM(price*quantity),0) FROM order_item WHERE order_ref_id=@oid) WHERE id=@oid;

SELECT COUNT(*) AS order_items_total FROM order_item WHERE order_ref_id=@oid;
SELECT id AS order_id, total_amount FROM `order` WHERE id=@oid;
