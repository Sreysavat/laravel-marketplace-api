<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

//get all categories with children
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();

        return response()->json($categories);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',

        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

         $category->update([
            'name' => $request->name ?? $category->name,
            'slug' => $request->name ? Str::slug($request->name) : $category->slug,
            'parent_id' => $request->parent_id,
        ]);


        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Category deleted'
        ]);
    }
}