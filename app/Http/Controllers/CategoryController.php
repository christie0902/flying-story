<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Add Category
    public function add($request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'bg_color' => 'required|string',
            'img_url' => 'required|url',
        ], [
           'name.required' => 'A name is required.',
            'bg_color.required' => 'A color is required.',
            'img_url.required' => 'An image is required.',


            'name.string' => 'The name must be a string of alpha-numeric characters.',
            'bg_color.string' => 'The background color must be a string of alphanumeric charcters eg. #ff2200.',
            'img_url.string' => 'The image URL must be a string of alphanumeric characters formatted into a URL.',
            
            'name.max' => 'The name may not be greater than 255 characters.',
            'img_url.max' => 'The image URL may not be greater than 255 characters.',
        ]);

        Category::create($request->all());
        return redirect()->route('categories.create')->with('success', 'Category added successfully!');
    }

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
