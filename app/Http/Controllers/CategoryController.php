<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        //TODO:: return the related data needed for categories such as number of products
        return Inertia::render('Categories', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        Category::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
    }

    public function update(Request $request, Category $category)
    {
        //TODO: update category
    }

    public function delete(Category $category)
    {
        //TODO: check if category has products
        //TODO: delete category
    }
}
