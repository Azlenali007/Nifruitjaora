<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ActivityLog;

class HomepageController {
    public function index(): void {
        $carouselProducts = Product::getCarouselProducts();
        $allProducts = Product::allAdmin(['is_active' => 1]);
        $settings = Setting::all();

        View::render('admin/homepage/index', [
            'title' => 'Homepage & Carousel Management | Admin',
            'carouselProducts' => $carouselProducts,
            'allProducts' => $allProducts,
            'settings' => $settings
        ]);
    }

    public function update(): void {
        $heroHeading = trim($_POST['hero_heading'] ?? '');
        $heroSubheading = trim($_POST['hero_subheading'] ?? '');
        $noticeBanner = trim($_POST['notice_banner'] ?? '');
        $isStoreOpen = !empty($_POST['is_store_open']) ? '1' : '0';

        if (!empty($heroHeading)) {
            Setting::set('hero_heading', $heroHeading, 'string', 'Homepage hero heading');
        }
        if (!empty($heroSubheading)) {
            Setting::set('hero_subheading', $heroSubheading, 'string', 'Homepage hero subheading');
        }
        Setting::set('notice_banner', $noticeBanner, 'string', 'Top notice banner');
        Setting::set('is_store_open', $isStoreOpen, 'boolean', 'Store open/closed status');

        // Update carousel products order
        if (!empty($_POST['carousel_products']) && is_array($_POST['carousel_products'])) {
            // First clear all carousel flags
            \App\Core\Database::getConnection()->exec("UPDATE `products` SET `is_carousel` = 0, `carousel_order` = 0");

            $order = 1;
            foreach ($_POST['carousel_products'] as $prodId) {
                Product::update((int)$prodId, [
                    'is_carousel' => 1,
                    'carousel_order' => $order++
                ]);
            }
        }

        ActivityLog::log('update_homepage', 'Updated homepage banner and carousel settings');
        flash('success', 'Homepage settings updated successfully!');
        redirect('/admin/homepage');
    }
}
