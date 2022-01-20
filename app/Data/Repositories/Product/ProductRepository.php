<?php


namespace App\Data\Repositories\Product;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRepository implements ProductRepositoryInterface
{
    public function index()
    {
        return  Product::get();
    }

    public function store()
    {
        request()->validate([
            'name' => 'required|string',
            'brand' => 'required|string',
            'image' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required|string'
        ]);

        $products = new Product([
            'user_id' => Auth::id(),
            'name' => request('name'),
            'brand' => request('brand'),
            'image' => request('image'),
            'price' => request('price'),
            'description' => request('description')
        ]);

        $products->save();

        return $products;
    }

    public function show($id)
    {
        return Product::find($id);
    }

    public function update(Request $request, $id)
    {
        return Product::find($id);

    }

    public function destroy($id)
    {
        return Product::findOrFail($id)->delete();
    }

}
