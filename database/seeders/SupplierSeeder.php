<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $suppliers = [
            'Supplier 1',
            'Supplier 2',
            'Supplier 3',
            'Supplier 4',
            'Supplier 5',
            'Supplier 6',
            'Supplier 7',
            'Supplier 8',
        ];

        foreach ($suppliers as $supplier) {

            Supplier::create([
                'code' => randomCode(),
                'name' => $supplier,
            ]);
        }
    }
}
