<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        //TODO:: return the related data needed for categories such as number of products
        
        return Inertia::render('Categories', [
            'categories' => Category::where('parent_id', null)->with('subcategories')->get()
        ]);
    }

    public function store(Request $request)
    {
        try {

            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ]);

            if ($request->parent_id) {
                $category->parent()->associate($request->parent_id);
                $category->save();
            }
            $request->session()->flash('severity', 'success');
            $request->session()->flash('message', $category->name . ' was created successfully');
        } catch (Exception $exception) {
            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description
            ]);
            $request->session()->flash('severity', 'success');
            $request->session()->flash('message', 'Category was updated successfully');
        } catch (Exception $exception) {
            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }

    public function destroy(Request $request, $id)
    {
        //TODO: check if category has products

        try {
            $ids = explode(',', $id);
            Category::whereIn('id', $ids)->delete();
            $request->session()->flash('severity', 'success');
            $request->session()->flash('message', 'Categories/Category deleted successfully');
        } catch (Exception $exception) {

            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }
}
