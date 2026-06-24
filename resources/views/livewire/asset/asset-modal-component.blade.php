{{-- show modal component --}}
@if($showComponentModal)
<div class="modal modal-open">
    <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg">Components of Asset {{ $name }}</h3>
        <div class="space-y-3 mt-4">
            @foreach($components as $index => $component)
            <ul class="list bg-amber-50 rounded-box shadow-md">
                @error('components.' . $index . '.name_component')
                <span class="text-error text-sm">{{ $message }}</span>
                @enderror
                <li class="list-row flex justify-between">
                    @if ($index == $editIndex)
                    <div class="flex justify-between items-center gap-2">
                        <input type="text" class="input input-xs input-bordered w-full"
                            wire:model="components.{{ $index }}.name_component" />
                        <div class="flex gap-2">
                            <button wire:click="updateComponent({{ $index }})"
                                class="btn btn-xs btn-primary">save</button>
                            <button wire:click="cancelEdit" class="btn btn-xs btn-accent">cancel</button>
                        </div>
                    </div>
                    @else
                    <div class="font-semibold">{{ $component['name_component'] }}</div>
                    @endif
                    <div class="">
                        <button wire:click="editComponent({{ $index }})" class="btn btn-xs btn-warning">Edit</button>
                        <button wire:click="deleteComponent({{ $index }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="btn btn-xs btn-error">Delete</button>
                    </div>
                </li>
            </ul>
            @endforeach
        </div>

        <div class="modal-action">
            <button wire:click="$set('showComponentModal', false)" class="btn">close</button>
        </div>
    </div>
</div>
@endif