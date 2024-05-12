<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{



    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        print_r($row);
        return new Product([
            'code' => $row['id'],
            'name' => $row['name'],
            'slug' => Str::slug($row['name']),
            'price' => $row['sales_price'],
            'description' => '',
            'parts_per_unit' => 3
        ]);
    }
}
