## Status: [Phase 4 - Refactor Bertahap Per Modul]

## Selesai
- [x] Phase 1: Fix permission checks, missing traits, wrong titles, dead listeners.
- [x] Phase 2: Remove dead code, orphaned views, unused properties (sudah diverifikasi, include modals bukan orphaned).
- [x] Phase 3: Tentukan/dokumentasikan pola standar di docs/coding-standard.md.
- [x] Phase 4: Refactor bertahap per modul ikuti pola standar (tanpa ubah behavior).
  - [x] Asset Module
  - [x] Category Module
  - [x] Component Module
  - [x] Location Module
  - [x] Technician Module
  - [x] Vendor Module
  - [x] User/Role/Permission Module
  - [x] Audit Log Module

## Belum
- Semua phase standarization sudah selesai!

## Catatan
- `docs/coding-standard.md` digunakan sebagai acuan.
- Modal orphan yang berupa `@include` (contoh: `asset-damage-modal.blade.php`) dipertahankan karena bukan orphan sungguhan.
- Modal components seperti `ModalVendor.php` dan `ModalTechnician.php` sudah direname menyesuaikan konvensi (`VendorModal.php`). Dead code `ModalTechnician` sudah dihapus.
- Asset Module, User/Role/Permission, dan Audit Log refactor selesai. Standarisasi alert diganti ke Swal dan form directive `.prevent` & `.defer` dihapus.

## Next steps
- Review seluruh aplikasi, pastikan tidak ada fungsionalitas yang patah. Codebase sudah seragam.
