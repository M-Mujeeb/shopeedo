<?php

namespace App\Exports;

use App\Models\DeliveryBoy;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeliveryBoyExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return DeliveryBoy::with('user','address')->get();
    }

    public function map($deliveryBoy): array
    {
        // dd($deliveryBoy);
        return [
            $deliveryBoy->id,
            $deliveryBoy->user->name ?? 'N/A',
            $deliveryBoy->user->email ?? 'N/A',
            $deliveryBoy->user->phone ?? 'N/A',
            $deliveryBoy->address ? $deliveryBoy->address->address : 'N/A',
            $deliveryBoy->total_earning == 0.00 ? '0' : (string) $deliveryBoy->total_earning,
            $deliveryBoy->total_collection == 0.00 ? '0' : (string) $deliveryBoy->total_collection,
            $deliveryBoy->bank_name ?? 'N/A',
            $deliveryBoy->bank_acc_name ?? 'N/A',
            $deliveryBoy->bank_acc_no ?? 'N/A',
            $deliveryBoy->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Phone', 'Address', 'Earning', 'Collection', 'Bank Account', 'Account Name', 'Account Number', 'Created At'];
    }
}
