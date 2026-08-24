<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\AssetRepair;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetRepairExport implements FromView, WithEvents, ShouldAutoSize
{
    use Exportable;

    protected $asset;
    protected $repairs;

    public function __construct(protected int $assetId)
    {
        $this->asset = Asset::with(['details', 'damages'])->findOrFail($assetId);

        $this->repairs = AssetRepair::where('asset_id', $assetId)
            ->with([
                'damage',
                'components',
            ])
            ->orderBy('started_at', 'desc')
            ->get();

        // Eager load vendor & technician on pivot
        $this->repairs->each(function ($repair) {
            $repair->components->each(function ($component) {
                $component->pivot->load('vendor', 'technician');
            });
        });
    }

    public function view(): View
    {
        return view('exports.asset-repair-excel', [
            'asset' => $this->asset,
            'repairs' => $this->repairs,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Detail asset header section (rows 1-3) — bold labels
                $sheet->getStyle('A1:B3')->getFont()->setBold(true);
                $sheet->getStyle('A1:B3')->getFont()->setSize(11);

                // 2 baris info (Kode + Nama) + 1 baris detail (jika ada) + 1 baris separator
                $detailRows = $this->asset->details->count() > 0 ? 1 : 0;
                $headerRow = 2 + $detailRows + 1 + 1;
                $lastCol = 'M';

                // Style header tabel
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 10,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getRowDimension($headerRow)->setRowHeight(30);

                // Hitung total data rows
                $totalDataRows = 0;
                foreach ($this->repairs as $repair) {
                    $componentCount = $repair->components->count();
                    $totalDataRows += max($componentCount, 1);
                }

                $lastDataRow = $headerRow + $totalDataRows;

                // Border seluruh tabel
                if ($totalDataRows > 0) {
                    $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastDataRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'D1D5DB'],
                            ],
                        ],
                    ]);
                }

                // Center kolom No
                $sheet->getStyle("A" . ($headerRow + 1) . ":A{$lastDataRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Format angka (Qty, Price)
                $sheet->getStyle("H" . ($headerRow + 1) . ":I{$lastDataRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Format kolom Price
                $sheet->getStyle("I" . ($headerRow + 1) . ":I{$lastDataRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');
            },
        ];
    }
}
