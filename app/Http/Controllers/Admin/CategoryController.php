<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category; // ✅ NewsCategory → Category
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::orderBy('order')->get(); // ✅ 'order' column
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255', // ✅ name_hi → display_name
            'order' => 'nullable|integer', // ✅ sort_order → order
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'parent_id' => 'nullable|exists:news_categories,id',
            'is_active' => 'boolean',
        ]);

        $displayName = $request->display_name ?? $request->name; // ✅ Auto-fill display_name

        Category::create([
            'name' => $request->name,
            'display_name' => $displayName,
            'slug' => Str::slug($request->name),
            'order' => $request->order ?? 0, // ✅ 'order' column
            'icon' => $request->icon,
            'color' => $request->color,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', '✅ Category added successfully!');
    }

    /**
     * Toggle active status.
     */
    public function toggle(Category $category) // ✅ Category model
    {
        $category->update(['is_active' => !$category->is_active]);
        $status = $category->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.categories.index')
            ->with('success', "✅ Category {$status} successfully!");
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category) // ✅ Category model
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'parent_id' => 'nullable|exists:news_categories,id',
            'is_active' => 'boolean',
        ]);

        $displayName = $request->display_name ?? $category->display_name ?? $request->name;

        $category->update([
            'name' => $request->name,
            'display_name' => $displayName,
            'slug' => Str::slug($request->name),
            'order' => $request->order ?? 0,
            'icon' => $request->icon,
            'color' => $request->color,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', '✅ Category updated successfully!');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category) // ✅ Category model
    {
        // Check if category has news
        if ($category->news()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', '❌ Cannot delete category. It has news. First move or delete news in this category.');
        }
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', '✅ Category deleted successfully!');
    }
}