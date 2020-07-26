<?php


namespace App\Data\Repositories\Product;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRepository implements ProductRepositoryInterface
{
    protected $product;

    public function __construct(Product $product)
    {
       $this->product = $product;
    }

    public function index()
    {
        return $this->product->all();
    }

    public function store($credentials)
    {
        return $this->product->create($credentials);
    }

    public function show(Product $product)
    {
        return $product->get($product);
    }

    public function update($product, $credentials)
    {
        return $product->update($credentials);

    }

    public function destroy($product)
    {
        return $product->delete();
    }

}
