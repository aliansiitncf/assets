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
    public $showModalPDF = false;
    public $startDate, $endDate;
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    private function getQuery()
    {
        return Activity::query()
            ->when($this->search, fn($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

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
        $logs = $this->getQuery();
        return view('livewire.audit.audit-log', [
            'logs' => $logs,
        ]);
    }
}
