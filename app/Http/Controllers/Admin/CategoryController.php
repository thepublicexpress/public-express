<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = NewsCategory::orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_hi' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        NewsCategory::create([
            'name' => $request->name,
            'name_hi' => $request->name_hi,
            'slug' => Str::slug($request->name),
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Category added successfully!');
    }

    public function toggle(NewsCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        $status = $category->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Category {$status} successfully!");
    }

    public function update(Request $request, NewsCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_hi' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $category->update([
            'name' => $request->name,
            'name_hi' => $request->name_hi,
            'slug' => Str::slug($request->name),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Category updated successfully!');
    }

    public function destroy(NewsCategory $category)
    {
        if ($category->news()->count() > 0) {
            return back()->with('error', 'Cannot delete category. It has news. First move or delete news in this category.');
        }
        $category->delete();
        return back()->with('success', 'Category deleted successfully!');
    }
}