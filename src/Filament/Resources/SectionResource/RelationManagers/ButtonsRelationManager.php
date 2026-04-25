<?php

namespace Lyre\Content\Filament\Resources\SectionResource\RelationManagers;

use Lyre\Content\Filament\Resources\ButtonResource;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ButtonsRelationManager extends RelationManager
{
    protected static string $relationship = 'buttons';

    public function form(Schema $form): Schema
    {
        return ButtonResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(ButtonResource::table($table)->getColumns())
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ])
            ->striped()
            ->deferLoading()
            // NOTE: Kigathi - September 5 2025 - When we add custom table names, this will need to be updated
            ->defaultSort('buttons.created_at', 'desc');
    }
}
