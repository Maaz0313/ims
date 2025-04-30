<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuppliersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('suppliers')->select(
            'id',
            'name',
            'contact_person',
            'email',
            'phone',
            'created_at',
            'updated_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Contact Person',
            'Email',
            'Phone',
            'Created At',
            'Updated At'
        ];
    }
}
