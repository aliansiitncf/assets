<?php

namespace App\Exports;

use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class VendorsExport implements FromView, WithEvents, ShouldAutoSize
{
    use Exportable;

    protected $vendors;

    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $filterJenis = null
    ) {
        $this->vendors = Vendor::query()
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->when($this->filterJenis, function ($q) {
                if ($this->filterJenis === 'supplier') {
                    $q->where('is_supplier', true);
                } elseif ($this->filterJenis === 'service') {
                    $q->where('is_service', true);
                }
            })
            ->orderBy('name', 'asc')
            ->get();
    }

    public function view(): View
    {
        return view('exports.vendor-excel', [
            'vendors' => $this->vendors,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->vendors->count() + 1;

                // Style header row
                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Border seluruh tabel
                if ($lastRow > 1) {
                    $sheet->getStyle("A1:F{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'D1D5DB'],
                            ],
                        ],
                    ]);
                }

                // Center kolom No
                $sheet->getStyle("A2:A{$lastRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Set row height header
                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }
}
