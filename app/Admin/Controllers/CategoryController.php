<?php

namespace App\Admin\Controllers;

use App\Core\View;
use App\Models\Category;
use App\Models\ActivityLog;
use App\Helpers\Validator;

class CategoryController {
    public function index(): void {
        $categories = Category::all();
        View::render('admin/categories/index', [
            'title' => 'Categories | Admin',
            'categories' => $categories
        ]);
    }

    public function create(): void {
        View::render('admin/categories/form', [
            'title' => 'Create Category | Admin',
            'category' => null
        ]);
    }

    public function store(): void {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:2'
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/categories/create');
            return;
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'])));
        $catId = Category::create([
            'name' => trim($_POST['name']),
            'slug' => $slug,
            'description' => trim($_POST['description'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);

        ActivityLog::log('create_category', "Created category '{$_POST['name']}'", 'category', $catId);
        flash('success', "Category '{$_POST['name']}' created successfully!");
        redirect('/admin/categories');
    }

    public function edit(string $id): void {
        $category = Category::findById((int)$id);
        if (!$category) {
            flash('error', 'Category not found.');
            redirect('/admin/categories');
            return;
        }

        View::render('admin/categories/form', [
            'title' => "Edit Category: {$category['name']} | Admin",
            'category' => $category
        ]);
    }

    public function update(string $id): void {
        $catId = (int)$id;
        $category = Category::findById($catId);
        if (!$category) {
            flash('error', 'Category not found.');
            redirect('/admin/categories');
            return;
        }

        Category::update($catId, [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);

        ActivityLog::log('update_category', "Updated category '{$category['name']}'", 'category', $catId);
        flash('success', 'Category updated successfully!');
        redirect('/admin/categories');
    }

    public function delete(string $id): void {
        $catId = (int)$id;
        $category = Category::findById($catId);
        if ($category) {
            Category::delete($catId);
            ActivityLog::log('delete_category', "Deleted category '{$category['name']}'", 'category', $catId);
            flash('success', 'Category deleted.');
        }
        redirect('/admin/categories');
    }
}
