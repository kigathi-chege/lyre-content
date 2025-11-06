<?php

namespace Lyre\Content\Filament\Resources\PageResource\RelationManagers;

use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Lyre\Content\Filament\Resources\SectionResource;

use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    // TODO: Kigathi - June 12 2025 - Understand this function, and extract it for reusability
    function getSchema(string $resourceClass): array
    {
        $fake = new class extends \Filament\Forms\Components\Component implements \Filament\Forms\Contracts\HasForms {
            use \Filament\Forms\Concerns\InteractsWithForms;
        };

        $container = \Filament\Forms\Form::make($fake);
        return $resourceClass::form($container)->getComponents();
    }

    public function form(Form $form): Form
    {
        $schema = $this->getSchema(SectionResource::class);
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
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(
                        function (Builder $query, $livewire) {
                            $prefix = config('lyre.table_prefix');
                            return  $query
                                ->select("{$prefix}sections.id", "{$prefix}sections.slug", "{$prefix}sections.name", "{$prefix}page_sections.order");
                        }
                    )
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
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
                Tables\Actions\EditAction::make(),
                Action::make('view')
                    ->label('View')
                    ->icon('gmdi-visibility')
                    ->color('info')
                    ->url(fn($record) => route('filament.admin.resources.sections.edit', $record->id)),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('sections.created_at', 'desc');
    }
}
