<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseOrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('purchase_orders')->select(
            'id',
            'supplier_id',
            'order_date',
            'expected_delivery_date',
            'total_amount',
            'status',
            'created_at',
            'updated_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Supplier ID',
            'Order Date',
            'Expected Delivery Date',
            'Total Amount',
            'Status',
            'Created At',
            'Updated At'
        ];
    }
}
