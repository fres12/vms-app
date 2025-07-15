<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitorExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function map($visitor): array
    {
        $idCardUrl = $visitor->id_card_photo ? url('storage/' . $visitor->id_card_photo) : '';
        $selfPhotoUrl = $visitor->self_photo ? url('storage/' . $visitor->self_photo) : '';

        return [
            $visitor->id,
            $visitor->full_name,
            $visitor->nik,
            $visitor->company,
            $visitor->phone,
            $visitor->department_purpose,
            $visitor->section_purpose,
            \Illuminate\Support\Carbon::parse($visitor->visit_datetime)->format('Y-m-d'),
            \Illuminate\Support\Carbon::parse($visitor->visit_datetime)->format('H:i'),
            $idCardUrl ? '=HYPERLINK("'.$idCardUrl.'","View ID Card Photo")' : '',
            $selfPhotoUrl ? '=HYPERLINK("'.$selfPhotoUrl.'","View Self Photo")' : '',
            $visitor->created_at,
            $visitor->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the highest row number
        $highestRow = $sheet->getHighestRow();
        
        // Style for columns J and K (ID Card Photo and Self Photo)
        for ($row = 2; $row <= $highestRow; $row++) {
            $sheet->getStyle('J'.$row)->applyFromArray([
                'font' => [
                    'color' => ['rgb' => '0563C1'],
                    'underline' => true,
                ],
            ]);
            
            $sheet->getStyle('K'.$row)->applyFromArray([
                'font' => [
                    'color' => ['rgb' => '0563C1'],
                    'underline' => true,
                ],
            ]);
        }

        return [
            1 => ['font' => ['bold' => true]], // Header style
        ];
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