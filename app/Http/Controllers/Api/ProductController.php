<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with(['category', 'suppliers'])
            ->paginate(10);
    }

    public function store(Request $request)
    {
        $product = Product::create($request->all());

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json(
            $product->load(['category', 'suppliers'])
        );
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->all());

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
