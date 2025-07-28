<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Resources\InteractionTypeResource\Pages;
use Lyre\Content\Filament\Resources\InteractionTypeResource\RelationManagers;
use Lyre\Content\Models\InteractionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class InteractionTypeResource extends Resource
{
    protected static ?string $model = InteractionType::class;

    protected static ?string $navigationIcon = 'gmdi-gesture';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('antonym_id')
                    ->relationship('antonym', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('icon_id')
                    ->relationship('icon', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                Forms\Components\TextInput::make('link')
                    ->maxLength(255),
                TiptapEditor::make('description')
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('antonym.name'),
                Tables\Columns\TextColumn::make('icon')
                    ->formatStateUsing(fn(InteractionType $record): HtmlString => $record->icon ? new HtmlString($record->icon->content) : ''),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInteractionTypes::route('/'),
            'create' => Pages\CreateInteractionType::route('/create'),
            'edit' => Pages\EditInteractionType::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // $permissions = config('filament-shield.permission_prefixes.resource');
        // TODO: Kigathi - May 4 2025 - Users should only view this navigation if they have at least one more permission than view and viewAny
        $usingSpatieRoles = in_array(\Spatie\Permission\Traits\HasRoles::class, class_uses(\App\Models\User::class));
        return $usingSpatieRoles ? Auth::user()->can('update', new InteractionType) : true;
    }
}
