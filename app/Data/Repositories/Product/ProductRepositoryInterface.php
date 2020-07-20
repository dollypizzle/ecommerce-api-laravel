<?php


namespace App\Data\Repositories\Product;

use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function index();

    public function store();

    public function show($id);

    public function update(Request $request, $id);

    public function destroy($id);
}
