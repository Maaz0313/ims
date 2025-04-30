<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('products')->select(
            'id',
            'name',
            'sku',
            'category_id',
            'price',
            'created_at',
            'updated_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Category ID',
            'Price',
            'Created At',
            'Updated At'
        ];
    }
}
