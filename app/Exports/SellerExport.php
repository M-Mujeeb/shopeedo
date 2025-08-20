<?php

namespace App\Exports;

use App\Models\Shop;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SellerExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Shop::with(['user', 'user.products'])->get();
    }

    public function map($shop): array
    {
        return [
            'ID'          => $shop->id,
            'Name'        => $shop->user->name ?? 'N/A',
            'Email'       => $shop->user->email ?? 'N/A',
            'Phone'       => $shop->user->phone ?? 'N/A',
            'Verification Info'     => $shop->verification_status ?? 'N/A',
            'Num. of Products'     => $shop->user!=null ? $shop->user->products->count() : 0,
            'Due to Seller'  => $shop->admin_to_pay,
            'Bank Account' => $shop->bank_name != null ? $shop->bank_name : 'N/A',
            'Account Name' => $shop->bank_acc_name != null ? $shop->bank_acc_name : 'N/A',
            'Account Number' => $shop->bank_acc_no != null ? $shop->bank_acc_no : 'N/A',
            'Created At'  => $shop->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Phone', 'Verification Info', 'Num. of Products', 'Due to Seller', 'Bank Account', 'Account Name', 'Account Number',  'Created At'];
    }
}
