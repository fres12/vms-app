<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VisitorExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($visitor, $i) {
            return [
                $i + 1,
                $visitor->full_name,
                $visitor->nik,
                $visitor->company,
                $visitor->phone,
                $visitor->department_purpose,
                $visitor->section_purpose,
                \Illuminate\Support\Carbon::parse($visitor->visit_datetime)->format('Y-m-d'),
                \Illuminate\Support\Carbon::parse($visitor->visit_datetime)->format('H:i'),
                $visitor->id_card_photo ? url('storage/' . $visitor->id_card_photo) : '',
                $visitor->self_photo ? url('storage/' . $visitor->self_photo) : '',
                $visitor->created_at,
                $visitor->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Full Name',
            'NIK',
            'Company',
            'Phone',
            'Department Purpose',
            'Section Purpose',
            'Visit Date',
            'Visit Time',
            'ID Card Photo',
            'Self Photo',
            'Created At',
            'Status',
        ];
    }
} 