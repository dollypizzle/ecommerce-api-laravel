<?php

namespace App\Http\Controllers;

use App\Data\Repositories\Product\ProductRepositoryInterface ;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $product_repository_interface;

    public function __construct(ProductRepositoryInterface $product_repository_interface)
    {
        $this->product_repository_interface = $product_repository_interface;

        $this->middleware('auth:api')->except(['index', 'show']);
    }

    public function index()
    {
        $products = $this->product_repository_interface->index();

        return response()->json([
            'products' => $products,
        ], 200);
    }

    public function store()
    {
        $credentials = request()->validate([
            'name' => 'required|string',
            'brand' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|string',
            'description' => 'required|string'
        ]);

        $credentials['user_id'] = auth()->id();

        $product = $this->product_repository_interface->store($credentials);

        $response = [
            'success' => true,
            'data' => $product,
            'message' => 'Product created successfully.',
        ];

        return response()->json($response, 201);
    }

    public function show(Product $product)
    {
        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function update(Product $product)
    {
        $this->authorize('update', $product);

        $credentials = request()->validate([
            'name' => 'required|string',
            'brand' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|string',
            'description' => 'required|string'
        ]);

        $this->product_repository_interface->update($product, $credentials);

        return response()->json([
            'success' => true,
            'product' => $product,
            "message" => "Product updated successfully"
        ], 200);
    }

    public function destroy(Product $product)
    {
       $product = $this->product_repository_interface->destroy($product);

        return response()->json([
            'success' => true,
            'product' => $product,
            "message" => "Product deleted successfully"
        ], 200);
    }
}
