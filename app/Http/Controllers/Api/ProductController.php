<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'Get all products',
        tags: ['Products'],
        security: [
            ['bearerAuth' => []]
        ],
        parameters: [
            new OA\Parameter(
                name: 'category_id',
                description: 'Filter products by category ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer'
                )
            ),
            new OA\Parameter(
                name: 'min_price',
                description: 'Minimum product price',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    format: 'float'
                )
            ),
            new OA\Parameter(
                name: 'max_price',
                description: 'Maximum product price',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    format: 'float'
                )
            ),
            new OA\Parameter(
                name: 'min_stock',
                description: 'Minimum stock quantity',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer'
                )
            ),
            new OA\Parameter(
                name: 'max_stock',
                description: 'Maximum stock quantity',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Products retrieved successfully',
                content: new OA\JsonContent(
                    example: [
                        'data' => [
                            [
                                'id' => 11,
                                'name' => 'Iste Aut Eum',
                                'description' => 'Commodi facilis quis dignissimos voluptatum.',
                                'price' => '906.57',
                                'stock' => 336,
                                'category' => [
                                    'id' => 1,
                                    'name' => 'ea',
                                ],
                                'suppliers' => [
                                    [
                                        'id' => 2,
                                        'name' => 'Runolfsson LLC',
                                        'email' => 'lesch.carmel@example.net',
                                        'phone' => '+1-617-917-8684',
                                    ],
                                ],
                                'created_at' => '2026-08-28T14:04:04.000000Z',
                                'updated_at' => '2026-08-28T14:04:04.000000Z',
                            ],
                        ],
                        'links' => [
                            'first' => 'http://127.0.0.1:8000/api/products?page=1',
                            'last' => 'http://127.0.0.1:8000/api/products?page=1',
                            'prev' => null,
                            'next' => null,
                        ],
                        'meta' => [
                            'current_page' => 1,
                            'from' => 1,
                            'last_page' => 1,
                            'per_page' => 10,
                            'to' => 1,
                            'total' => 1,
                        ],
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unauthenticated.'
                        )
                    ]
                )
            )
        ]
    )]
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
