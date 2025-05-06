<?php

namespace Lyre\Content\Filament\Resources\SectionResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Lyre\Content\Filament\Resources\DataResource;

class DataRelationManager extends RelationManager
{
    protected static string $relationship = 'data';

    public function form(Form $form): Form
    {
        return DataResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(DataResource::table($table)->getColumns())
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['section_id'] = static::getOwnerRecord()->id;
        return $data;
    }
}
