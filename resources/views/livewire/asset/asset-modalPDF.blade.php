@if ($showModalPDF)
<div class="modal modal-open">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Download PDF</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <select class="select w-full" wire:model="filterCategoryDownload">
                    <option value="all">All Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id_category }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select class="select w-full" wire:model="filterLocationDownload">
                    <option value="all">All Location</option>
                    @foreach($filterLocations as $location)
                    <option value="{{ $location->id_location }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <legend for="startDate" class="fieldset-legend">Start Date</legend>
                <input name="startDate" type="date" class="input" wire:model="startDate"/>
            </div>

            <div>
                <legend for="endDate" class="fieldset-legend">End Date</legend>
                <input name="endDate" type="date" class="input" wire:model="endDate"/>
            </div>
        </div>

        <div class="modal-action">
            <button wire:click="closeModalPDF" class="btn btn-outline">Cancel</button>
            <button wire:click="downloadPdf" class="btn btn-primary">Download</button>
        </div>
    </div>
</div>
@endif