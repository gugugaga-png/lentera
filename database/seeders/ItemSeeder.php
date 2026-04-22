<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan category sudah ada
        $category = Category::first();
        
        if (!$category) {
            $category = Category::create(['name' => 'Electronics']);
        }
        
        Item::create([
            'name' => 'Laptop Asus',
            'code' => 'ITM-LAPTOP-001',
            'category_id' => $category->id,
            'stock' => 50,
            'available_stock' => 50,
            'condition' => 'good',
            'description' => 'High performance laptop',
        ]);
        
        Item::create([
            'name' => 'Projector Epson',
            'code' => 'ITM-PROJ-001',
            'category_id' => $category->id,
            'stock' => 10,
            'available_stock' => 10,
            'condition' => 'good',
            'description' => 'LCD Projector',
        ]);
        
        Item::create([
            'name' => 'Whiteboard',
            'code' => 'ITM-BOARD-001',
            'category_id' => $category->id,
            'stock' => 5,
            'available_stock' => 5,
            'condition' => 'good',
        ]);
    }
}