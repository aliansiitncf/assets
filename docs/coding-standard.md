# Livewire Coding Standard

### 1. Naming Convention
| Type | Pattern | Example |
|------|---------|--------|
| Page component (full route) | `[Entity]Page` | `CategoryPage`, `VendorPage`, `AssetPage` |
| CRUD form page | `[Entity][Action]` | `AssetCreate`, `AssetEdit` |
| Shared reusable modal | `[Entity]Modal` | `ComponentModal`, `VendorModal` |
| Detail/sub-component | `[Entity][Role]` | `AssetDetail`, `AssetScanner` |
| Directory names | Singular | `Category/`, `Component/`, `User/` |
| Shared components dir | `Shared/` | For cross-module reusable components |
| Blade views | Auto-resolved kebab-case, all lowercase | `category-page.blade.php` |
| Property names | camelCase only | `$categoryId`, `$isSupplier` |

### 2. Modal Pattern
One DaisyUI pattern everywhere:
```blade
@if ($showModal)
<div class="modal modal-open">
    <div class="modal-box">
        <button wire:click="closeModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
        {{-- content --}}
    </div>
    <div class="modal-backdrop" wire:click="closeModal"></div>
</div>
@endif
```
- State prop: `$showModal` for page modals, `$isOpen` for reusable modals, `$showModalPdf` for PDF modals

### 3. Table & Pagination
- Always use `WithPagination` trait
- Typed properties: `public string $search = ''; public string $sortField = 'created_at'; public string $sortDirection = 'asc'; public int $perPage = 10;`
- Search debounce: `300ms`
- Per-page options: `[10, 25, 50]`
- Pagination links: default `->links()`
- Sort method signature: `public function sortBy(string $field): void`
- Empty state: `@forelse / @empty`
- Table headers: `<x-table-header>` component

### 4. Forms
- Submit: `<form wire:submit="store">` (no `.prevent`, no `method="POST"`, no `@csrf`)
- Binding: `wire:model` (no `.defer` — that's LW2 syntax)
- Validation: inline in save/store method via `$this->validate([...])`
- Reset: `public function resetInputFields(): void`

### 5. Events & Notifications  
- Use LW3 `#[On('event-name')]` attributes (not `$listeners` array)
- Notifications: `$this->dispatch('swal', icon: '...', title: '...', text: '...')`
- Keep session flash only for redirect scenarios

### 6. Authorization
- Use `HasAuthorization` trait
- Use `$this->requirePermission('...')` in `mount()`
- Use `@can('...')` in Blade for conditional UI
