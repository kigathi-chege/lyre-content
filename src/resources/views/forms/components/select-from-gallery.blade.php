<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    @php
        $multiple = $getMultiple();
    @endphp

    <div x-data="{
        tempState: [],
        state: $wire.$entangle('{{ $getStatePath() }}'),
        allFiles: @js($getGalleryFiles()['data']),
        selectedFiles: @js($getSelectedFiles()),
        multiple: @js($multiple),
        toggleSelection(fileId) {
            if (this.tempState == null) {
                this.tempState = []
            }
            if (this.tempState?.includes(fileId)) {
                this.tempState = this.tempState.filter(id => id !== fileId);
            } else {
                if (!this.multiple) {
                    this.tempState = []
                }
                this.tempState.push(fileId);
            }
        },
        selectFiles() {
            for (let i = 0; i < this.tempState.length; i++) {
                if (this.state == null) {
                    this.state = []
                }
                if (!this.multiple) {
                    this.state = []
                    this.selectedFiles = []
                }
                this.state.push(this.tempState[i]);
                let file = this.allFiles.find(file => file.id === this.tempState[i]);
                this.selectedFiles.push(file);
            }
        }
    }">
        <x-filament::button color="gray" size="md" class="w-full px-3 py-1/5" alignment="start"
            x-on:click="$dispatch('open-modal', { id: 'select-file' })">
            <div class="inset-0 flex flex-row items-center justify-start px-3 py-1/5 text-gray-950 font-normal">
                <template x-if="selectedFiles.length > 0">
                    <div class="flex flex-row items-center justify-center gap-2 flex-wrap">
                        <template x-for="file in selectedFiles">
                            <img :src="file.link" alt="i" class="h-16 w-16 rounded-md object-cover">
                        </template>
                    </div>
                </template>
                <template x-if="selectedFiles.length == 0">
                    <img src="{{ asset('lyre/content/placeholder.webp') }}" alt="i"
                        class="h-16 w-16 rounded-md object-cover">
                </template>
            </div>
        </x-filament::button>

        <x-filament::modal id="select-file" width="6xl">
            <x-slot name="heading">
                Select File
            </x-slot>
            <div class="flex flex-col space-y-4">
                <div {{-- class="grid lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 md:grid-cols-2 gap-4 justify-center items-center" --}}
                    class="grid lg:grid-cols-4 grid-cols-1 md:grid-cols-2 gap-4 justify-center items-center">
                    @foreach ($getGalleryFiles()['data'] as $file)
                        <div class="rounded-md h-64 relative cursor-pointer border-2"
                            :class="tempState?.includes({{ $file->id }}) ? 'border-blue-500' : 'border-transparent'"
                            @click="toggleSelection({{ $file->id }})">
                            <div x-show="tempState?.includes({{ $file->id }})"
                                class="absolute top-0 right-0 w-0 h-0 border-t-[40px] border-t-green-500 border-l-[40px] border-l-transparent">
                            </div>
                            <div class="absolute inset-0 hover:bg-black hover:opacity-25 rounded-md"
                                :class="tempState?.includes({{ $file->id }}) ? 'bg-green-600 opacity-25' : 'bg-transparent'">
                            </div>
                            @if ($file->mimetype == 'application/pdf')
                                <div class="flex flex-col items-center justify-center gap-4 h-full w-full">
                                    <img src="{{ asset('pdf.png') }}" alt="PDF" class="h-48 w-48">
                                    <p class="">{{ $file->name }}</p>
                                </div>
                            @else
                                <img src="{{ $file->link }}" alt="{{ $file->name }}"
                                    class="w-full h-full object-cover rounded-md">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <x-slot name="footer">
                <div class="flex flex-row w-full items-center justify-end gap-4">
                    <x-filament::button @click="selectFiles; $dispatch('close-modal', { id: 'select-file' })">
                        Select Files
                    </x-filament::button>
                    <x-filament::button
                        @click="tempstate = []; state = []; $dispatch('close-modal', { id: 'select-file' })"
                        color="gray" x-on:click="tempState=null">
                        Cancel
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>
</x-dynamic-component>
