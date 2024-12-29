<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function store()
    {
        $categoryData = [
            'title' => 'Some Category 1',
        ];

        return Category::create($categoryData);
    }

    public function update(Category $category)
    {
        $categoryData = [
            'title' => 'Some Category 1 Edit',
        ];

        $category->update($categoryData);

        return $category;
    }

    public function destroy(Category $category)
    {

        $category->delete();

        return response(['message' => 'Category has been deleted']);
    }
}
