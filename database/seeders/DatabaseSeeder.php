<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::factory(10)->create();

        $suppliers = Supplier::factory(10)->create();

        Product::factory(50)
            ->recycle($categories)
            ->create()
            ->each(function (Product $product) use ($suppliers) {
                $product->suppliers()->attach(
                    $suppliers->random(rand(1, 3))->pluck('id')
                );
            });
    }
}
