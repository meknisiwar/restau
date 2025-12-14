# Product Images Guide

## ✅ Images Already Added!

All 16 products now have images from Unsplash. Visit http://localhost:8000/products to see them!

## 📸 How to Add/Change Product Images

### **Option 1: Use External Image URLs (Current Method)**

This is the easiest method - just use image URLs from the internet.

#### Update via SQL:
```sql
UPDATE product SET image = 'https://example.com/image.jpg' WHERE name = 'Product Name';
```

#### Good Free Image Sources:
- **Unsplash**: https://unsplash.com (Free high-quality images)
- **Pexels**: https://pexels.com (Free stock photos)
- **Pixabay**: https://pixabay.com (Free images)

**Example:**
```sql
UPDATE product 
SET image = 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=400' 
WHERE name = 'Margherita Pizza';
```

---

### **Option 2: Upload Local Images (Recommended for Production)**

For a real restaurant, you'll want to upload your own product photos.

#### Step 1: Create Upload Directory
```bash
mkdir public/uploads
mkdir public/uploads/products
```

#### Step 2: Install VichUploaderBundle (Optional but Recommended)
```bash
composer require vich/uploader-bundle
```

#### Step 3: Or Use Simple File Upload

Create a simple upload form in your admin panel:

**Controller Example:**
```php
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

public function uploadProductImage(Request $request, Product $product, SluggerInterface $slugger)
{
    /** @var UploadedFile $imageFile */
    $imageFile = $request->files->get('image');
    
    if ($imageFile) {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

        try {
            $imageFile->move(
                $this->getParameter('products_directory'), // public/uploads/products
                $newFilename
            );
            
            $product->setImage('/uploads/products/' . $newFilename);
            $entityManager->flush();
            
        } catch (FileException $e) {
            // Handle exception
        }
    }
}
```

**Add to config/services.yaml:**
```yaml
parameters:
    products_directory: '%kernel.project_dir%/public/uploads/products'
```

---

### **Option 3: Use Placeholder Images**

For testing, you can use placeholder services:

```sql
-- Using placeholder.com
UPDATE product SET image = 'https://via.placeholder.com/400x300?text=Pizza' WHERE name = 'Margherita Pizza';

-- Using picsum.photos (random images)
UPDATE product SET image = 'https://picsum.photos/400/300' WHERE id = 1;
```

---

## 🔧 Quick SQL Commands

### View all products with images:
```sql
SELECT id, name, image FROM product;
```

### Remove all images:
```sql
UPDATE product SET image = NULL;
```

### Update specific product image:
```sql
UPDATE product SET image = 'YOUR_IMAGE_URL' WHERE id = 1;
```

### Update multiple products at once:
```sql
UPDATE product SET image = 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=400' WHERE name = 'Margherita Pizza';
UPDATE product SET image = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400' WHERE name = 'Cheeseburger';
```

---

## 📝 Creating an Admin Panel for Image Upload

If you want to create an admin interface to upload images:

### 1. Install EasyAdmin (Recommended):
```bash
composer require easycorp/easyadmin-bundle
```

### 2. Or Create Custom Admin Forms:

**Form Type:**
```php
use Symfony\Component\Form\Extension\Core\Type\FileType;

$builder
    ->add('name')
    ->add('description')
    ->add('price')
    ->add('imageFile', FileType::class, [
        'label' => 'Product Image',
        'mapped' => false,
        'required' => false,
    ]);
```

---

## 🎨 Image Best Practices

1. **Size**: Recommended 800x600px or 1200x900px
2. **Format**: JPG for photos, PNG for graphics with transparency
3. **Optimization**: Compress images before uploading (use TinyPNG.com)
4. **Naming**: Use descriptive names: `pizza-margherita.jpg` not `IMG_1234.jpg`
5. **Alt Text**: Always add alt text for accessibility

---

## 🚀 Current Setup

Your products now use Unsplash images:
- ✅ All 16 products have images
- ✅ Images are responsive (400px width)
- ✅ Images display in product cards
- ✅ Fallback icon shows if image fails to load

**To see the images:**
Visit http://localhost:8000/products

**To change an image:**
1. Find a new image URL
2. Run SQL: `UPDATE product SET image = 'NEW_URL' WHERE name = 'Product Name';`
3. Refresh the page

---

## 📱 Responsive Images

The current template already handles responsive images:
```twig
{% if product.image %}
<img src="{{ product.image }}" class="card-img-top" alt="{{ product.name }}" 
     style="height: 200px; object-fit: cover;">
{% else %}
<div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
     style="height: 200px;">
    <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
</div>
{% endif %}
```

This ensures:
- Fixed height (200px) for consistent card layout
- `object-fit: cover` maintains aspect ratio
- Fallback icon if no image is set

