## Status: [Phase 4 - Refactor Bertahap Per Modul]

## Selesai
- [x] Phase 1: Fix permission checks, missing traits, wrong titles, dead listeners.
- [x] Phase 2: Remove dead code, orphaned views, unused properties (sudah diverifikasi, include modals bukan orphaned).
- [x] Phase 3: Tentukan/dokumentasikan pola standar di docs/coding-standard.md.

## Belum
- [ ] Phase 4: Refactor bertahap per modul ikuti pola standar (tanpa ubah behavior).
  - [ ] Asset Module
  - [x] Category Module
  - [x] Component Module
  - [x] Location Module
  - [x] Technician Module
  - [ ] Vendor Module
  - [ ] User/Role/Permission Module
  - [ ] Audit Log Module

## Catatan
- `docs/coding-standard.md` sudah ada.
- View modal yang terlihat seperti "orphan" (misal `asset-damage-modal.blade.php`) sebenarnya di `@include` oleh page view-nya. Bukan orphan, tetap dipertahankan unless we want to make them full Livewire components (YAGNI, biarkan saja kalau hanya dipakai di satu tempat).

## Next steps
- Refactor module Asset atau Category sesuai pola standar.
