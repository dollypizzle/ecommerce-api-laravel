<?php


namespace App\Data\Repositories\Product;

use App\Product;
use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function index();

    public function store($credentials);

    public function show(Product $product);

    public function update($product, $credentials);

    public function destroy($product);
}
