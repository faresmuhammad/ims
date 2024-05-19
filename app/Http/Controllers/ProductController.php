<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewProductItemResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StockItemResource;
use App\Imports\ProductsImport;
use App\Models\Stock;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
        $product = new ProductResource(Product::where('slug', $slug)->with(['category:id,name', 'stocks'])->first());
        return Inertia::render('ProductInfo', [
            'product' => $product,
            'categories' => Category::select('id', 'name')->get(),
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

    public function getProduct(string $code, ?string $for = 'customer')
    {
        $product = Product::where('code', $code)->first();
        $stock = Stock::where('code', $code)->first();
        if ($for == 'customer') {
            return $product ? new NewProductItemResource(
                $product->load(['stocks' => function (Builder $query) {
                    $query->available()->orderByDesc('expire_date');
                }
                ])) : ($stock ? new StockItemResource($stock->load('product')) : response(['message' => 'No Product or Stock Found'], 404));
        } elseif ($for == 'return') {
            return $product ? new NewProductItemResource(
                $product->load(['stocks' => function (Builder $query) {
                    $query->orderByDesc('expire_date');
                }])
            ) : ($stock ? new StockItemResource($stock->load('product')) : response(['message' => 'No Product or Stock Found'], 404));
        } else {
            return $product ? new NewProductItemResource($product) : response(['message' => 'No Product Found'], 404);
        }
        //TODO:suggestion: modify new stock generated code to be related to product code for faster searching
    }
}
