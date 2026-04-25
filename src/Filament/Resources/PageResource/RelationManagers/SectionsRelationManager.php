<?php

namespace Lyre\Content\Filament\Resources\PageResource\RelationManagers;

use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Lyre\Content\Filament\Resources\SectionResource;

use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    // TODO: Kigathi - June 12 2025 - Understand this function, and extract it for reusability
    protected function getResourceSchemaComponents(string $resourceClass): array
    {
        $fake = new class extends \Filament\Forms\Components\Component implements \Filament\Forms\Contracts\HasForms {
            use \Filament\Forms\Concerns\InteractsWithForms;
        };

        $container = \Filament\Schemas\Schema::make($fake);
        return $resourceClass::form($container)->getComponents();
    }

    public function form(Schema $form): Schema
    {
        $schema = $this->getResourceSchemaComponents(SectionResource::class);
        return $form->schema([...$schema, Forms\Components\TextInput::make('order')->numeric()]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([...SectionResource::table($table)->getColumns(), Tables\Columns\TextColumn::make('order')])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(
                        function (Builder $query, $livewire) {
                            $prefix = config('lyre.table_prefix');
                            return  $query
                                ->select("{$prefix}sections.id", "{$prefix}sections.slug", "{$prefix}sections.name", "{$prefix}page_sections.order");
                        }
                    )
                    ->form(fn(\Filament\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('order')
                            ->numeric(),
                    ])
                    ->action(function (array $arguments, array $data, Form $form, Table $table, $action): void {
                        /** @var BelongsToMany $relationship */
                        $relationship = Relation::noConstraints(fn() => $table->getRelationship());

                        $isMultiple = is_array($data['recordId']);

                        $record = $relationship->getRelated()
                            ->{$isMultiple ? 'whereIn' : 'where'}($relationship->getQualifiedRelatedKeyName(), $data['recordId'])
                            ->{$isMultiple ? 'get' : 'first'}();

                        if ($record instanceof Model) {
                            $action->record($record);
                        }

                        $action->process(function () use ($data, $record, $relationship) {
                            $relationship->attach(
                                $record,
                                Arr::only($data, $relationship->getPivotColumns()),
                            );
                        }, [
                            'relationship' => $relationship,
                        ]);

                        if ($arguments['another'] ?? false) {
                            $action->callAfter();
                            $action->sendSuccessNotification();

                            $action->record(null);

                            $form->fill();

                            $action->halt();

                            return;
                        }

                        $action->success();
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                Action::make('view')
                    ->label('View')
                    ->icon('gmdi-visibility')
                    ->color('info')
                    ->url(fn($record) => route('filament.admin.resources.sections.edit', $record->id)),
                \Filament\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('sections.created_at', 'desc');
    }
}
