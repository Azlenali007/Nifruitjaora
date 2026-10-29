<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Services\CartService;

class HomeController {
    public function index(): void {
        $carouselProducts = Product::getCarouselProducts();
        $featuredProducts = Product::getFeaturedProducts(6);
        $categories = Category::allActive();
        
        $settings = Setting::all();
        $cartSummary = CartService::getCartSummary(auth()['id'] ?? null, session_id());

        View::render('home/index', [
            'title' => 'Fresh Tender Coconuts & Natural Coconut Water | N.A Fresh Fruits',
            'carouselProducts' => $carouselProducts,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'settings' => $settings,
            'cartSummary' => $cartSummary
        ]);
    }
}
