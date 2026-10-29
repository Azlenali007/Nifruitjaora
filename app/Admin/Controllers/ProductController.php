<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\ActivityLog;
use App\Services\ImageUploadService;
use App\Helpers\Validator;

class ProductController {
    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';

        $filters = ['search' => $search];
        if ($status !== '') {
            $filters['is_active'] = (int)$status;
        }

        $products = Product::allAdmin($filters);

        View::render('admin/products/index', [
            'title' => 'Product Management | Admin',
            'products' => $products,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function create(): void {
        $categories = Category::all();
        View::render('admin/products/create', [
            'title' => 'Add New Coconut Product | Admin',
            'categories' => $categories
        ]);
    }

    public function store(): void {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:2',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/products/create');
            return;
        }

        $imageUrl = '/assets/images/nariyal-pani.webp';
        if (!empty($_FILES['image']['tmp_name'])) {
            try {
                $imageUrl = ImageUploadService::upload($_FILES['image'], 'products');
            } catch (\Exception $e) {
                flash('error', 'Image upload failed: ' . $e->getMessage());
                redirect('/admin/products/create');
                return;
            }
        } elseif (!empty($_POST['image_url'])) {
            $imageUrl = trim($_POST['image_url']);
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'])));
        // Ensure unique slug
        $existing = Product::findBySlug($slug);
        if ($existing) {
            $slug .= '-' . rand(100, 999);
        }

        $productId = Product::create([
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'name' => trim($_POST['name']),
            'slug' => $slug,
            'price' => (float)$_POST['price'],
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'stock_quantity' => (int)$_POST['stock_quantity'],
            'is_in_stock' => (int)$_POST['stock_quantity'] > 0 ? 1 : 0,
            'is_featured' => !empty($_POST['is_featured']) ? 1 : 0,
            'is_carousel' => !empty($_POST['is_carousel']) ? 1 : 0,
            'carousel_order' => (int)($_POST['carousel_order'] ?? 0),
            'image_url' => $imageUrl,
            'short_description' => trim($_POST['short_description'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);

        // Add variants if provided
        if (!empty($_POST['variant_names']) && is_array($_POST['variant_names'])) {
            for ($i = 0; $i < count($_POST['variant_names']); $i++) {
                $vName = trim($_POST['variant_names'][$i]);
                $vPrice = (float)($_POST['variant_prices'][$i] ?? 0);
                $vStock = (int)($_POST['variant_stocks'][$i] ?? 50);
                if (!empty($vName) && $vPrice > 0) {
                    ProductVariant::create([
                        'product_id' => $productId,
                        'name' => $vName,
                        'sku' => strtoupper(substr($slug, 0, 3)) . '-' . rand(100, 999),
                        'price' => $vPrice,
                        'stock_quantity' => $vStock
                    ]);
                }
            }
        }

        ActivityLog::log('create_product', "Created product '{$_POST['name']}' (ID: {$productId})", 'product', $productId);
        flash('success', "Product '{$_POST['name']}' created successfully!");
        redirect('/admin/products');
    }

    public function edit(string $id): void {
        $product = Product::findById((int)$id);
        if (!$product) {
            flash('error', 'Product not found.');
            redirect('/admin/products');
            return;
        }

        $categories = Category::all();
        $variants = ProductVariant::getByProductId($product['id']);

        View::render('admin/products/edit', [
            'title' => "Edit Product: {$product['name']} | Admin",
            'product' => $product,
            'categories' => $categories,
            'variants' => $variants
        ]);
    }

    public function update(string $id): void {
        $productId = (int)$id;
        $product = Product::findById($productId);
        if (!$product) {
            flash('error', 'Product not found.');
            redirect('/admin/products');
            return;
        }

        $imageUrl = $product['image_url'];
        if (!empty($_FILES['image']['tmp_name'])) {
            try {
                $imageUrl = ImageUploadService::upload($_FILES['image'], 'products');
            } catch (\Exception $e) {
                flash('error', 'Image upload failed: ' . $e->getMessage());
                redirect("/admin/products/{$productId}/edit");
                return;
            }
        } elseif (!empty($_POST['image_url'])) {
            $imageUrl = trim($_POST['image_url']);
        }

        Product::update($productId, [
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'name' => trim($_POST['name']),
            'price' => (float)$_POST['price'],
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'stock_quantity' => (int)$_POST['stock_quantity'],
            'is_in_stock' => (int)$_POST['stock_quantity'] > 0 ? 1 : 0,
            'is_featured' => !empty($_POST['is_featured']) ? 1 : 0,
            'is_carousel' => !empty($_POST['is_carousel']) ? 1 : 0,
            'carousel_order' => (int)($_POST['carousel_order'] ?? 0),
            'image_url' => $imageUrl,
            'short_description' => trim($_POST['short_description'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);

        // Manage variants
        ProductVariant::deleteByProductId($productId);
        if (!empty($_POST['variant_names']) && is_array($_POST['variant_names'])) {
            for ($i = 0; $i < count($_POST['variant_names']); $i++) {
                $vName = trim($_POST['variant_names'][$i]);
                $vPrice = (float)($_POST['variant_prices'][$i] ?? 0);
                $vStock = (int)($_POST['variant_stocks'][$i] ?? 50);
                if (!empty($vName) && $vPrice > 0) {
                    ProductVariant::create([
                        'product_id' => $productId,
                        'name' => $vName,
                        'price' => $vPrice,
                        'stock_quantity' => $vStock
                    ]);
                }
            }
        }

        ActivityLog::log('update_product', "Updated product '{$product['name']}'", 'product', $productId);
        flash('success', 'Product updated successfully!');
        redirect('/admin/products');
    }

    public function delete(string $id): void {
        $productId = (int)$id;
        $product = Product::findById($productId);
        if ($product) {
            Product::delete($productId);
            ActivityLog::log('delete_product', "Deleted product '{$product['name']}'", 'product', $productId);
            flash('success', 'Product deleted successfully.');
        }
        redirect('/admin/products');
    }
}
