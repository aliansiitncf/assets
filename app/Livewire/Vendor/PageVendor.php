<?php

namespace App\Livewire\Vendor;

use App\Enums\AuditEvent;
use App\Exports\VendorsExport;
use App\Models\Vendor;
use App\Services\AuditService;
use App\Traits\HasAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;


#[Title('Vendor')]
#[Layout('components.layouts.app')]
class PageVendor extends Component
{
    use WithPagination, HasAuthorization;

    public $showModal = false;
    public $modalMode = 'create';
    public $name, $address, $phone, $vendorId;

    public $is_supplier = false;
    public $is_service = false;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public $filterJenis = '';

    // Export properties
    public $showExportModal = false;
    public $exportStartDate = '';
    public $exportEndDate = '';
    public $exportFilterJenis = '';


    private function getQuery()
    {
        return Vendor::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterJenis, function ($q) {
                if ($this->filterJenis === 'supplier') {
                    $q->where('is_supplier', true);
                } elseif ($this->filterJenis === 'service') {
                    $q->where('is_service', true);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    private function resetPageIfEmpty()
    {
        if ($this->getQuery()->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJenis(): void
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

    public function openModal($mode = 'create', $id = null)
    {
        $this->resetInputFields();
        $this->modalMode = $mode;
        if ($mode === 'edit' && $id) {
            $this->edit($id);
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    // --- Export Methods ---

    public function openExportModal()
    {
        $this->resetExportFields();
        $this->showExportModal = true;
    }

    public function closeExportModal()
    {
        $this->showExportModal = false;
    }

    public function resetExportFields()
    {
        $this->exportStartDate = '';
        $this->exportEndDate = '';
        $this->exportFilterJenis = '';
    }

    public function exportVendor()
    {
        $this->validate([
            'exportStartDate' => 'nullable|date',
            'exportEndDate'   => 'nullable|date|after_or_equal:exportStartDate',
        ], [
            'exportEndDate.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        ]);

        $startDate = $this->exportStartDate ?: null;
        $endDate = $this->exportEndDate ?: null;
        $filterJenis = $this->exportFilterJenis ?: null;

        $this->closeExportModal();

        $filename = 'vendors-' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new VendorsExport($startDate, $endDate, $filterJenis),
            $filename
        );
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->address = '';
        $this->phone = '';
        $this->is_supplier = false;
        $this->is_service = false;
        $this->vendorId = null;
    }

    // --------------------------------------------

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3|unique:vendors,name,' . ($this->vendorId ?: 'NULL') . ',id',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|numeric',
            'is_supplier' => 'required_without:is_service',
            'is_service' => 'required_without:is_supplier',
        ], [
            'is_supplier.required_without' => 'Pilih minimal satu jenis vendor.',
            'is_service.required_without' => 'Pilih minimal satu jenis vendor.',
        ]);

        $message = null;
        $isEdit = $this->modalMode === 'edit' && $this->vendorId;

        // Ambil data vendor lama SEBELUM diupdate (untuk keperluan audit)
        $oldVendorModel = $isEdit ? Vendor::find($this->vendorId) : null;
        $oldVendorData = $oldVendorModel ? [
            'name'    => $oldVendorModel->name,
            'address' => $oldVendorModel->address,
            'phone'   => $oldVendorModel->phone,
            'is_supplier' => $oldVendorModel->is_supplier,
            'is_service' => $oldVendorModel->is_service,
        ] : null;

        $vendor = Vendor::updateOrCreate(
            ['id' => $this->vendorId],
            [
                'name'    => $this->name,
                'address' => $this->address,
                'phone'   => $this->phone,
                'is_supplier' => $this->is_supplier,
                'is_service' => $this->is_service,
            ]
        );

        if ($isEdit && $oldVendorData) {
            // Bandingkan field lama vs baru, hanya simpan yang benar-benar berubah
            $changes = [];
            foreach ($oldVendorData as $field => $oldValue) {
                $newValue = $vendor->{$field};
                if ($oldValue != $newValue) {
                    $changes[$field] = [
                        'before' => $oldValue,
                        'after'  => $newValue,
                    ];
                }
            }

            $message = 'Data vendor berhasil diperbarui.';

            AuditService::log(
                AuditEvent::VENDOR_UPDATED,
                'vendor_updated',
                $vendor,
                ['changes' => $changes]
            );
        } else {
            $message = 'Data vendor berhasil ditambahkan.';

            AuditService::log(
                AuditEvent::VENDOR_CREATED,
                'vendor_created',
                $vendor,
                ['name' => $this->name]
            );
        }

        $this->resetInputFields();
        $this->closeModal();

        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: $message);
    }


    // edit vendor
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        $this->vendorId = $id;
        $this->name = $vendor->name;
        $this->address = $vendor->address;
        $this->phone = $vendor->phone;
        $this->is_supplier = $vendor->is_supplier;
        $this->is_service = $vendor->is_service;
    }

    public function delete($id)
    {
        $this->requirePermission('hapus vendor');
        $vendor = Vendor::find($id);
        $vendor->delete();
        $this->resetPageIfEmpty();

        AuditService::log(
            AuditEvent::VENDOR_DELETED,
            'vendor_deleted',
            $vendor,
            ['name' => $vendor->name]
        );

        $this->dispatch('swal', icon: 'success', title: 'Berhasil', text: 'Data vendor berhasil dihapus.');
    }

    public function render()
    {
        $arr['vendors'] = $this->getQuery();
        return view('livewire.vendor.page-vendor', $arr);
    }
}
