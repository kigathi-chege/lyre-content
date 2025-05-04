<?php

namespace Lyre\Content\Filament\Resources\SectionResource\RelationManagers;

use Lyre\Content\Filament\Resources\ButtonResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ButtonsRelationManager extends RelationManager
{
    protected static string $relationship = 'buttons';

    public function form(Form $form): Form
    {
        return ButtonResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(ButtonResource::table($table)->getColumns())
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
