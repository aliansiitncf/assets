@if ($showModalPDF)
<div class="modal modal-open">
    <div class="modal-box">
        <button wire:click="closeModalPDF" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
        <h3 class="font-bold text-lg mb-4">Download PDF</h3>
        <form wire:submit="downloadPdf">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="startDate" class="label">Start Date</label>
                    <input name="startDate" type="date" class="input input-bordered w-full" wire:model="startDate"/>
                </div>

                <div>
                    <label for="endDate" class="label">End Date</label>
                    <input name="endDate" type="date" class="input input-bordered w-full" wire:model="endDate"/>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" wire:click="closeModalPDF" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Download</button>
            </div>
        </form>
    </div>
    <div class="modal-backdrop" wire:click="closeModalPDF"></div>
</div>
@endif