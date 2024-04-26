<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('Products', [
            'products' => Product::all(),
            'categories' => Category::select('id', 'name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Product::create([
                'code' => $request->code,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'price' => $request->price,
                'stock_offset' => $request->stock_offset,
                'description' => $request->description,
                'category_id' => $request->category_id,
            ]);

            $request->session()->flash('severity', 'success');
            $request->session()->flash('message', 'Product was created successfully');
        } catch (Exception $exception) {
            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        return Inertia::render('ProductInfo', [
            'product' => Product::where('slug', $slug)->with('category:id,name')->first(),
            'categories' => Category::select('id', 'name')->get()
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'stock_offset' => $request->stock_offset
            ]);
            $request->session()->flash('severity', 'success');
            $request->session()->flash('message', 'Product was updated successfully');
        } catch (Exception $exception) {
            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        try {
            $product->delete();
            return redirect('/products');
        } catch (Exception $exception) {
            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }


    public function import(Request $request)
    {
        Excel::import(new ProductsImport(), $request->file('products'));
    }

    public function getProduct(Product $product)
    {
        return $product;
    }
}
