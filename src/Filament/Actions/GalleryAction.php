<?php

namespace Lyre\Content\Filament\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;

class GalleryAction extends Action
{
    public $selectedFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn($record) => 'Deactivate');

        $this->icon(fn($record) => 'heroicon-o-check-badge');

        $this->modalHeading(fn($record): string => 'Deactivate');

        $this->modalSubmitActionLabel($this->label);

        $this->successNotificationTitle(fn($record): string => 'Deactivate');

        $this->color(fn($record) => 'danger');

        // $this->requiresConfirmation(fn() => true)
        //     ->modalDescription(function ($record) {
        //         return "Are you sure you want to this ";
        //     });

        $this->form(function () {});

        $this->action(function ($livewire): void {
            try {
                // $record->{$this->modelColumn} = ! $record->{$this->modelColumn};
                // $record->save();
                dd($livewire);
            } catch (\Exception $e) {
                \Log::error("RESET PASSWORD ERROR", [$e->getMessage()]);
                $this->halt();
            }
        });

        $this->extraModalFooterActions(fn($action): array => [
            $action->makeModalSubmitAction('createAnother', arguments: ['another' => true]),
        ]);

        $this->modalContent(function ($record, $action): View {
            $fileRepository = app(\Lyre\Content\Repositories\Contracts\FileRepositoryInterface::class);
            $files = $fileRepository->paginate(9)->all();
            \Log::info("FILES", $files);
            return view(
                'filament.resources.content.file-resource.pages.gallery',
                ['files' => $files, 'action' => $action],
            );
        });

        $this->modalWidth('7xl');
    }

    #[On("handle-selected-files")]
    public function handleSelectedFiles(array $selectedFileIds)
    {
        \Log::info('Selected file IDs:', $selectedFileIds);

        // Do something: maybe store them temporarily, attach to a record, etc.
        // You can also close the modal:
        // $this->dispatchBrowserEvent('close-modal');
    }
}
