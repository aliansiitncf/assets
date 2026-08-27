<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class AssetRepairChartsExport implements WithDrawings, WithTitle
{
    protected $poinImg;
    protected $biayaImg;

    public function __construct($poinImg, $biayaImg)
    {
        $this->poinImg = $poinImg;
        $this->biayaImg = $biayaImg;
    }

    public function drawings()
    {
        $drawings = [];

        if ($this->poinImg) {
            $poinResource = imagecreatefromstring(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->poinImg)));
            $drawing1 = new MemoryDrawing();
            $drawing1->setName('Poin Service Chart');
            $drawing1->setDescription('Poin Service Chart');
            $drawing1->setImageResource($poinResource);
            $drawing1->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
            $drawing1->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing1->setHeight(300);
            $drawing1->setCoordinates('A1');
            $drawings[] = $drawing1;
        }

        if ($this->biayaImg) {
            $biayaResource = imagecreatefromstring(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->biayaImg)));
            $drawing2 = new MemoryDrawing();
            $drawing2->setName('Biaya Perbaikan Chart');
            $drawing2->setDescription('Biaya Perbaikan Chart');
            $drawing2->setImageResource($biayaResource);
            $drawing2->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
            $drawing2->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing2->setHeight(300);
            $drawing2->setCoordinates('A17');
            $drawings[] = $drawing2;
        }

        return $drawings;
    }

    public function title(): string
    {
        return 'Grafik Repair';
    }
}
