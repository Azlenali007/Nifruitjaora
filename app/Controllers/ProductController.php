<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Services\CartService;

class ProductController {
    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $categorySlug = trim($_GET['category'] ?? '');
        $sort = trim($_GET['sort'] ?? 'featured');

        $products = Product::allActive([
            'search' => $search,
            'category_slug' => $categorySlug,
            'sort' => $sort
        ]);

        $categories = Category::allActive();
        $cartSummary = CartService::getCartSummary(auth()['id'] ?? null, session_id());

        View::render('products/index', [
            'title' => 'Product Catalogue | N.A Fresh Fruits & Coconuts',
            'products' => $products,
            'categories' => $categories,
            'currentSearch' => $search,
            'currentCategory' => $categorySlug,
            'currentSort' => $sort,
            'cartSummary' => $cartSummary
        ]);
    }

    public function show(string $slug): void {
        $product = Product::findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Product Not Found']);
            return;
        }

        $variants = ProductVariant::getByProductId($product['id']);
        $related = Product::getFeaturedProducts(4);
        $cartSummary = CartService::getCartSummary(auth()['id'] ?? null, session_id());

        View::render('products/show', [
            'title' => "{$product['name']} | N.A Fresh Fruits & Coconuts",
            'product' => $product,
            'variants' => $variants,
            'related' => $related,
            'cartSummary' => $cartSummary
        ]);
    }
}
