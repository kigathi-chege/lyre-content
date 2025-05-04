{{-- <x-filament-panels::page> --}}

<div x-data="{
    selectedFiles: @entangle('selectedFiles'),
    toggleSelection(fileId) {
        if (this.selectedFiles.includes(fileId)) {
            this.selectedFiles = this.selectedFiles.filter(id => id !== fileId);
        } else {
            this.selectedFiles.push(fileId);
        }
    },
    submitSelection() {
        $wire.call('handleSelectedFiles', this.selectedFiles);
    }
}" class="flex flex-col space-y-4">
    <div class="grid lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 md:grid-cols-2 gap-4 justify-center items-center">
        @foreach ($files['data'] as $file)
            <div class="rounded-md h-64 relative cursor-pointer border-2"
                :class="selectedFiles.includes({{ $file->id }}) ? 'border-blue-500' : 'border-transparent'"
                @click="toggleSelection({{ $file->id }})">
                <div x-show="selectedFiles.includes({{ $file->id }})"
                    class="absolute top-0 right-0 w-0 h-0 border-t-[40px] border-t-green-500 border-l-[40px] border-l-transparent">
                </div>
                <div class="absolute inset-0 hover:bg-black hover:opacity-25 rounded-md"
                    :class="selectedFiles.includes({{ $file->id }}) ? 'bg-green-600 opacity-25' : 'bg-transparent'">
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

{{-- </x-filament-panels::page> --}}
