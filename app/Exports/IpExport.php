<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Ipaddress;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class IpExport implements FromCollection,
 WithCustomStartCell,
 ShouldAutoSize,
 WithHeadings,
 WithStyles,
 WithDrawings
{

    protected $start_date;
    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date;
        $this->end_date;
    }

    public function collection()
    {
         return Ipaddress::whereBetween('created_at', [$this->start_date, $this->end_date])
                ->select('ipaddress')
                ->get();
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return ['IP Address','Heading 2'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A5' => [
                'font' => [
                    'size' => 12,
                    'bold' => true
                ]
            ]
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/image/banner.png'));
        $drawing->setHeight(81);
        $drawing->setCoordinates('A1');

        return $drawing;
    }
}