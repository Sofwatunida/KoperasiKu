<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['P001', 'Pulpen Standard', 1200, 2000, 50, 'pcs'],
            ['P002', 'Buku Tulis 38 Lbr', 3800, 5000, 30, 'pcs'],
            ['P003', 'Pensil 2B', 1500, 2500, 25, 'pcs'],
            ['P004', 'Penghapus Joyko', 900, 1500, 40, 'pcs'],
            ['P005', 'Penggaris 30cm', 1800, 3000, 20, 'pcs'],
            ['P006', 'Spidol Board Marker', 4200, 6000, 15, 'pcs'],
            ['P007', 'Kertas A4 70gr', 32000, 40000, 10, 'rim'],
            ['P008', 'Map Plastik', 1100, 2000, 35, 'pcs'],
        ];

        foreach ($products as [$code, $name, $purchasePrice, $sellingPrice, $stock, $unit]) {
            Product::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'purchase_price' => $purchasePrice,
                    'selling_price' => $sellingPrice,
                    'stock' => $stock,
                    'unit' => $unit,
                ],
            );
        }
    }
}