<?php

namespace Lyre\Content\Filament\Resources\PageResource\RelationManagers;

use Lyre\Content\Filament\Resources\SectionResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

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
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('order')
                            ->numeric(),
                    ]),
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
            ]);
    }
}
