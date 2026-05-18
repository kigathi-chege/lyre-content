<?php

namespace Lyre\Content\Filament\Resources\SectionResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Lyre\Content\Filament\Resources\DataResource;

class DataRelationManager extends RelationManager
{
    protected static string $relationship = 'data';

    public function form(Schema $form): Schema
    {
        return DataResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(DataResource::table($table)->getColumns())
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('created_at', 'desc');
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['section_id'] = static::getOwnerRecord()->id;
        return $data;
    }
}
