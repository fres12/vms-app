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
        // Column map considering the current export view structure
        // For master admin:
        // A No, B Full Name, C Email, D NIK, E Company, F Phone, G Dept, H Purpose,
        // I Start, J End, K Equipment, L Brand, M Submit, N Status,
        // O ID Card, P Self Photo, Q Barcode, R Approved Date, S Ticket Number
        // For non-master admin (no Dept):
        // A No, B Full Name, C Email, D NIK, E Company, F Phone, G Purpose,
        // H Start, I End, J Equipment, K Brand, L Submit, M Status,
        // N ID Card, O Self Photo, P Barcode, Q Approved Date, R Ticket Number
        if ($this->isMasterAdmin) {
            return [
                'D' => NumberFormat::FORMAT_TEXT,   // NIK as text
                'O' => NumberFormat::FORMAT_TEXT,   // ID Card hyperlink text
                'P' => NumberFormat::FORMAT_TEXT,   // Self Photo hyperlink text
                'Q' => NumberFormat::FORMAT_TEXT,   // QR hyperlink text
            ];
        }

        return [
            'D' => NumberFormat::FORMAT_TEXT,       // NIK as text
            'N' => NumberFormat::FORMAT_TEXT,       // ID Card hyperlink text
            'O' => NumberFormat::FORMAT_TEXT,       // Self Photo hyperlink text
            'P' => NumberFormat::FORMAT_TEXT,       // QR hyperlink text
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Determine hyperlink columns
        if ($this->isMasterAdmin) {
            $idCardCol = 'O';
            $selfPhotoCol = 'P';
            $qrCol = 'Q';
        } else {
            $idCardCol = 'N';
            $selfPhotoCol = 'O';
            $qrCol = 'P';
        }
        
        // Apply styles to hyperlink columns (underline + blue color)
        for ($row = 2; $row <= $lastRow; $row++) {
            foreach ([$idCardCol, $selfPhotoCol, $qrCol] as $col) {
                $cell = $sheet->getCell("{$col}{$row}");
                if ($cell->getValue() !== '-') {
                    $sheet->getStyle("{$col}{$row}")->applyFromArray([
                        'font' => [
                            'underline' => true,
                            'color' => ['rgb' => '0563C1']
                        ]
                    ]);
                }
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