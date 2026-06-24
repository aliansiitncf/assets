<?php

namespace App\Livewire\Audit;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Title('Audit Logs')]
#[Layout('components.layouts.app')]
class AuditLog extends Component
{
    use WithPagination;
    public $perPage = 10;
    public $showModalPDF = false;
    public $startDate, $endDate;

    public function openModalPDF()
    {
        $this->showModalPDF = true;
    }

    public function downloadPdf()
    {
        $logs = Activity::Filter(
            $this->startDate,
            $this->endDate
        )->get();

        $pdf = Pdf::loadView('exports.audit-log-export', [
            'logs' => $logs,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
        $this->reset([
            'startDate',
            'endDate'
        ]);
        $filename = 'audit-logs-' . Carbon::now()->format('d-m-Y') . '.pdf';
        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }
    public function closeModalPDF()
    {
        $this->showModalPDF = false;
    }

    public function refresh()
    {
        $this->resetPage();
    }
    public function render()
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate($this->perPage);
        return view('livewire.audit.audit-log', [
            'logs' => $logs,
        ]);
    }
}
