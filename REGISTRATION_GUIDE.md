# Registration & Authentication Guide

## ✅ **Fixes Applied**

I've fixed the registration system with the following improvements:

### 1. **Enhanced Error Handling**
- Added try-catch block to catch any registration errors
- Added flash messages to show success/error messages
- Added form validation error display

### 2. **Test Users Created**
Two test users have been created for you to test the system:

**Admin User:**
- Email: `admin@test.com`
- Password: `admin123`
- Role: ROLE_ADMIN
- Access: Full admin dashboard access

**Regular User:**
- Email: `user@test.com`
- Password: `user123`
- Role: ROLE_USER (default)
- Access: Cart, orders, reservations

---

## 🧪 **Testing the System**

### **Test Login (Recommended First)**
1. Go to http://localhost:8000/login
2. Use one of the test accounts above
3. You should be logged in successfully

### **Test Registration**
1. Go to http://localhost:8000/register
2. Fill in the form with:
   - Email: your-email@example.com
   - First Name: Your Name
   - Last Name: Your Last Name
   - Phone: (optional)
   - Address: (optional)
   - Password: at least 6 characters
   - Check "I agree to terms"
3. Click "Register"
4. You should be automatically logged in

---

## 🔍 **If Registration Still Doesn't Work**

### **Check for Specific Errors:**

1. **Open the registration page** at http://localhost:8000/register
2. **Fill in the form** and try to submit
3. **Look for error messages** that will now appear at the top of the form

### **Common Issues & Solutions:**

#### **Issue 1: "Email already exists"**
**Solution:** Use a different email address or delete the existing user:
```sql
DELETE FROM user WHERE email = 'your-email@example.com';
```

#### **Issue 2: "Password too short"**
**Solution:** Use at least 6 characters for the password

#### **Issue 3: "Must agree to terms"**
**Solution:** Check the "I agree to terms and conditions" checkbox

#### **Issue 4: Form validation errors**
**Solution:** Make sure all required fields are filled:
- Email (must be valid email format)
- First Name (required)
- Last Name (required)
- Password (minimum 6 characters)
- Agree to terms (must be checked)

#### **Issue 5: Database connection error**
**Solution:** Make sure Laragon MySQL is running

---

## 📝 **Manual User Creation (If Needed)**

If registration still doesn't work, you can create users manually:

### **Method 1: Using PHP Script**

Create a file `create_user.php`:
```php
<?php
$pdo = new PDO("mysql:host=127.0.0.1;dbname=restaurant_platform", "root", "");
$password = password_hash('your_password', PASSWORD_BCRYPT);

$stmt = $pdo->prepare("
    INSERT INTO user (email, roles, password, first_name, last_name, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");

$stmt->execute([
    'newemail@example.com',
    json_encode([]),  // or json_encode(['ROLE_ADMIN']) for admin
    $password,
    'First',
    'Last'
]);

echo "User created!\n";
```

Run: `php create_user.php`

### **Method 2: Using HeidiSQL/phpMyAdmin**

1. Open HeidiSQL (in Laragon)
2. Select `restaurant_platform` database
3. Open the `user` table
4. Click "Insert row"
5. Fill in:
   - email: your-email@example.com
   - roles: `[]` (or `["ROLE_ADMIN"]` for admin)
   - password: Use an online bcrypt generator
   - first_name: Your Name
   - last_name: Your Last Name
   - created_at: Current timestamp

---

## 🔐 **Password Hashing**

Symfony uses bcrypt by default. To generate a password hash:

### **Option 1: Symfony Console**
```bash
php bin/console security:hash-password
```

### **Option 2: Online Tool**
Use: https://bcrypt-generator.com/
- Enter your password
- Use cost factor: 13
- Copy the hash

### **Option 3: PHP**
```php
echo password_hash('your_password', PASSWORD_BCRYPT);
```

---

## 🎯 **What to Tell Me**

If registration still doesn't work, please tell me:

1. **What error message do you see?** (should now appear at the top of the form)
2. **What data did you enter?** (email, name, etc.)
3. **Does login work?** (try with admin@test.com / admin123)
4. **Any errors in the browser console?** (F12 → Console tab)

---

## 📊 **Check Current Users**

To see all users in the database:

```sql
SELECT id, email, first_name, last_name, roles, created_at FROM user;
```

Or via PHP:
```bash
php bin/console doctrine:query:sql "SELECT email, first_name, last_name FROM user"
```

---

## 🚀 **Next Steps After Registration Works**

1. **Test the full user flow:**
   - Register → Login → Browse Products → Add to Cart → Checkout

2. **Test admin features:**
   - Login as admin@test.com
   - Access /admin dashboard

3. **Make a reservation:**
   - Login as any user
   - Go to /reservation/new

4. **Place an order:**
   - Add products to cart
   - Complete checkout

---

## 🛠️ **Files Modified**

- `src/Controller/RegistrationController.php` - Added error handling
- `templates/registration/register.html.twig` - Added flash messages and error display

---

## 💡 **Tips**

- Clear browser cache if forms don't update
- Check var/log/dev.log for detailed errors
- Use browser DevTools (F12) to see network errors
- Test with different browsers if issues persist

