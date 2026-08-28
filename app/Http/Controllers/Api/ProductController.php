<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with(['category', 'suppliers'])
            ->paginate(10);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $supplierIds = $data['supplier_ids'] ?? [];

        unset($data['supplier_ids']);

        $product = Product::create($data);

        if ($supplierIds) {
            $product->suppliers()->sync($supplierIds);
        }

        return response()->json(
            $product->load(['category', 'suppliers']),
            201
        );
    }

    public function show(Product $product)
    {
        return response()->json(
            $product->load(['category', 'suppliers'])
        );
    }

    public function update(UpdateProductRequest $request, Product $product) {
        $data = $request->validated();

        $supplierIds = $data['supplier_ids'] ?? null;

        unset($data['supplier_ids']);

        $product->update($data);

        if ($supplierIds !== null) {
            $product->suppliers()->sync($supplierIds);
        }

        return response()->json(
            $product->load(['category', 'suppliers'])
        );
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
