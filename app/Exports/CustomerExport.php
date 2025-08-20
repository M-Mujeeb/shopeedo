<?php

namespace App\Exports;

use App\Models\User; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::where('user_type', 'customer')->get();
    }

    public function map($user): array
    {
        return [
            'ID'          => $user->id,
            'Name'        => $user->name ?? 'N/A',
            'Email'       => $user->email ?? 'N/A',
            'Phone'       => $user->phone ?? 'N/A',
            'Wallet Balance'     => $user->balance ?? 'N/A',
            'Created At'  => $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Phone', 'Wallet Balance', 'Created At'];
    }
}
