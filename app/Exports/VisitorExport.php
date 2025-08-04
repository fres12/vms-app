<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitorExport implements FromView, ShouldAutoSize, WithStyles, WithProperties, WithColumnFormatting
{
    protected $visitors;
    protected $isMasterAdmin;
    protected $isDeptAdmin;

    public function __construct($visitors, $isMasterAdmin = false, $isDeptAdmin = false)
    {
        $this->visitors = $visitors;
        $this->isMasterAdmin = $isMasterAdmin;
        $this->isDeptAdmin = $isDeptAdmin;
    }

    public function view(): View
    {
        return view('exports.visitors', [
            'visitors' => $this->visitors,
            'isMasterAdmin' => $this->isMasterAdmin,
            'isDeptAdmin' => $this->isDeptAdmin
        ]);
    }

    public function properties(): array
    {
        return [
            'creator'        => 'VMS System',
            'lastModifiedBy' => 'VMS System',
            'title'         => 'Visitor Export',
            'description'   => 'Visitor list export with photo links',
            'subject'       => 'Visitor',
            'keywords'      => 'visitors,export',
            'category'      => 'Visitor Data',
            'manager'       => 'VMS',
            'company'       => 'Hyundai',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER, // NIK column
            // Adjust column letters for ID Card Photo and Self Photo
            // Assuming columns: No, Full Name, Email, NIK, Company, Phone, (Department), Visit Purpose, Equipment Type, Brand, Start Date, End Date, Status, Submit Date, ID Card Photo, Self Photo
            // If master admin: ID Card Photo = O, Self Photo = P; else: N, O
            // We'll keep both for compatibility
            'O' => NumberFormat::FORMAT_TEXT, // ID Card Photo (with department)
            'P' => NumberFormat::FORMAT_TEXT, // Self Photo (with department)
            'N' => NumberFormat::FORMAT_TEXT, // ID Card Photo (without department)
            'O' => NumberFormat::FORMAT_TEXT, // Self Photo (without department)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Find ID Card Photo and Self Photo columns based on master admin status
        $idCardCol = $this->isMasterAdmin ? 'O' : 'N';
        $selfPhotoCol = $this->isMasterAdmin ? 'P' : 'O';
        
        // Apply styles to photo columns
        for ($row = 2; $row <= $lastRow; $row++) {
            // Style ID Card Photo column
            $idCardCell = $sheet->getCell("{$idCardCol}{$row}");
            if ($idCardCell->getValue() !== '-') {
                $sheet->getStyle("{$idCardCol}{$row}")->applyFromArray([
                    'font' => [
                        'underline' => true,
                        'color' => ['rgb' => '0563C1']
                    ]
                ]);
            }

            // Style Self Photo column
            $selfPhotoCell = $sheet->getCell("{$selfPhotoCol}{$row}");
            if ($selfPhotoCell->getValue() !== '-') {
                $sheet->getStyle("{$selfPhotoCol}{$row}")->applyFromArray([
                    'font' => [
                        'underline' => true,
                        'color' => ['rgb' => '0563C1']
                    ]
                ]);
            }
        }

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '003368']
                ]
            ],
            'A2:' . $lastColumn . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ]
            ]
        ];
    }
}