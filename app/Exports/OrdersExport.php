<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('orders')->select(
            'id',
            'customer_name',
            'order_number',
            'grand_total',
            'status',
            'created_at',
            'updated_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Order Number',
            'Grand Total',
            'Status',
            'Created At',
            'Updated At'
        ];
    }
}
