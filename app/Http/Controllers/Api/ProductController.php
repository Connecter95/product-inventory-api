<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'suppliers'])
            ->filter($request->only([
                'category_id',
                'min_price',
                'max_price',
                'min_stock',
                'max_stock',
            ]))
            ->paginate(10);

        return ProductResource::collection($products);
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

        return new ProductResource(
            $product->load(['category', 'suppliers'])
        );
    }

    public function show(Product $product)
    {
        return new ProductResource(
            $product->load(['category', 'suppliers'])
        );
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $supplierIds = $data['supplier_ids'] ?? null;

        unset($data['supplier_ids']);

        $product->update($data);

        if ($supplierIds !== null) {
            $product->suppliers()->sync($supplierIds);
        }

        return new ProductResource(
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
