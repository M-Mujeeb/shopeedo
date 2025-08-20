<?php

namespace App\Exports;

use App\Models\User; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AppliedDeliveryBoyExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::where('user_type', 'delivery_boy')->where('email_verified_at', null)->where('password', null)->get();
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name ?? 'N/A',
            $user->email ?? 'N/A',
            $user->phone ?? 'N/A',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Phone', 'Created At'];
    }
}
