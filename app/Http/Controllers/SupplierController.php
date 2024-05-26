<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{

    public function index()
    {
        $this->authorize('see all suppliers');
        return Inertia::render('Suppliers', [
            'suppliers' => Supplier::all()
        ]);
    }


    public function store(Request $request)
    {
        $this->authorize('add supplier');
        try {
            $supplier = Supplier::create([
                'code'=> randomCode(),
                'name' => $request->name,
                'phone' => $request->phone
            ]);
            $request->session()->flash('severity', 'success');
            $request->session()->flash('message', $supplier->name . ' was created successfully');
        } catch (Exception $exception) {
            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }


    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('edit supplier');
        try {
            $supplier->update([
                'name' => $request->name,
                'phone' => $request->phone_number
            ]);
            $request->session()->flash('severity', 'success');
            $request->session()->flash('message', 'Supplier was updated successfully');
        } catch (Exception $exception) {
            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }

    public function destroy(Request $request,$id)
    {
        $this->authorize('delete supplier');
        try {
            $ids = explode(',', $id);
            Supplier::whereIn('id', $ids)->delete();
            $request->session()->flash('severity', 'success');
            $request->session()->flash('message', 'Suppliers/Supplier deleted successfully');
        } catch (Exception $exception) {
            $request->session()->flash('severity', 'error');
            $request->session()->flash('message', 'An Error occurred, try again');
        }
    }
}
