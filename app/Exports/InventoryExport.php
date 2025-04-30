<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('inventory')->select(
            'id',
            'product_id',
            'quantity',
            'reorder_level',
            'location',
            'created_at',
            'updated_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Product ID',
            'Quantity',
            'Reorder Level',
            'Location',
            'Created At',
            'Updated At'
        ];
    }
}
