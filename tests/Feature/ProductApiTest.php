<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $category = Category::factory()->create();

        $supplier = Supplier::factory()->create();

        Product::factory()
            ->count(3)
            ->create([
                'category_id' => $category->id,
            ])
            ->each(function (Product $product) use ($supplier) {
                $product->suppliers()->attach($supplier);
            });

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $response->assertJsonCount(3, 'data');
    }

    public function test_can_create_product(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $category = Category::factory()->create();

        $supplier1 = Supplier::factory()->create();
        $supplier2 = Supplier::factory()->create();

        $data = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'description' => 'Test product description',
            'price' => 99.90,
            'stock' => 50,
            'supplier_ids' => [
                $supplier1->id,
                $supplier2->id,
            ],
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201);

        $response->assertJsonPath(
            'data.name',
            'Test Product'
        );

        $response->assertJsonPath(
            'data.price',
            99.9
        );

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'price' => 99.90,
            'stock' => 50,
        ]);

        $product = Product::where('name', 'Test Product')->first();

        $this->assertNotNull($product);

        $this->assertCount(
            2,
            $product->suppliers
        );
    }

    public function test_can_show_product(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $category = Category::factory()->create();

        $supplier = Supplier::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Show Test Product',
            'price' => 199.90,
            'stock' => 25,
        ]);

        $product->suppliers()->attach($supplier);

        $response = $this->getJson(
            "/api/products/{$product->id}"
        );

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.id',
            $product->id
        );

        $response->assertJsonPath(
            'data.name',
            'Show Test Product'
        );

        $response->assertJsonPath(
            'data.category.id',
            $category->id
        );

        $response->assertJsonPath(
            'data.suppliers.0.id',
            $supplier->id
        );
    }

    public function test_can_update_product(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Old Product Name',
            'price' => 100,
            'stock' => 10,
        ]);

        $response = $this->putJson(
            "/api/products/{$product->id}",
            [
                'name' => 'Updated Product Name',
                'price' => 150,
                'stock' => 25,
            ]
        );

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.name',
            'Updated Product Name'
        );

        $response->assertJsonPath(
            'data.price',
            150
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 150,
            'stock' => 25,
        ]);
    }

    public function test_can_delete_product(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson(
            "/api/products/{$product->id}"
        );

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Product deleted successfully',
        ]);

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    public function test_cannot_create_product_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/products', [
            'name' => '',
            'price' => -10,
            'stock' => -5,
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'category_id',
            'name',
            'price',
            'stock',
        ]);
    }
}
