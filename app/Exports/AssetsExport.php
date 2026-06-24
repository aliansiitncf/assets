<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class AssetsExport implements FromView, WithDrawings, WithEvents
{
    use Exportable;

    protected $assets;

    public function __construct()
    {
        $this->assets = Asset::with(['category', 'components', 'latestLocation.location'])
            ->get();
    }
    public function view() :View
    {
        return view('exports.asset-excel', [
            'assets' => $this->assets
        ]);
    }

    public function drawings()
    {
        $drawings = [];
        $row = 2;

        foreach ($this->assets as $asset) {

            $imagePath = storage_path('app/public/' . $asset->image_path);

            if ($asset->image_path && file_exists($imagePath)) {

                $drawing = new Drawing();
                $drawing->setName($asset->name);
                $drawing->setDescription('Asset Image');
                $drawing->setPath($imagePath);
                $drawing->setHeight(80);
                $drawing->setCoordinates('B' . $row);

                $drawings[] = $drawing;
            }

            $row++;
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $row = 2;
                foreach ($this->assets as $asset) {

                    $event->sheet->getDelegate()
                        ->getRowDimension($row)
                        ->setRowHeight(80);

                    $row++;
                }

                $event->sheet->getDelegate()
                    ->getColumnDimension('B')
                    ->setWidth(20);
            },
        ];
    }
}
