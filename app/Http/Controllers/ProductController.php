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

        return response($products, 200);
    }

    public function store()
    {
        $product = $this->product_repository_interface->store();

        $response = [
            'success' => true,
            'data' => $product,
            'message' => 'Product created successfully.',
        ];

        return response()->json($response, 200);
    }

    public function show($id)
    {

        $product = $this->product_repository_interface->show($id);

        if (is_null($product)) {
            $response = [
                'success' => false,
                'message' => 'Product not found.'
            ];
            return response()->json($response, 404);
        }

        $response = [
            'success' => true,
            'data' => $product,
            'message' => 'Product retrieved successfully.'
        ];

        return response()->json($response, 200);
    }

    public function update(Request $request, $id)
    {
        $product = $this->product_repository_interface->update($request, $id);
        $product->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $product,
            "message" => "Product updated successfully"
        ], 200);
    }

    public function destroy($id)
    {
       $product = $this->product_repository_interface->destroy($id);

        return response()->json([
            'success' => true,
            "message" => "Product deleted successfully"
        ], 200);
    }
}
