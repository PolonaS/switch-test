<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Product::create(["name" => "Headphones", "price" => 14.89]);
        \App\Product::create(["name" => "Watch", "price" => 33.29]);
        \App\Product::create(["name" => "TV", "price" => 214.99]);
        \App\Product::create(["name" => "Laptop", "price" => 599]);
    }
}
