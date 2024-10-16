<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Edit Category
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    // Update Category
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bg_color' => 'required|string',
            'img_url' => 'required|url',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->only('name', 'bg_color', 'img_url'));

        return redirect()->route('lesson.list')->with('success', 'Category updated successfully!');
    }

    // Delete Category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('lesson.list')->with('success', 'Category deleted successfully!');
    }
}
