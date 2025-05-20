<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Resources\InteractionResource\Pages;
use Lyre\Content\Filament\Resources\InteractionResource\RelationManagers;
use Lyre\Content\Models\Interaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InteractionResource extends Resource
{
    protected static ?string $model = Interaction::class;

    protected static ?string $navigationIcon = 'gmdi-touch-app';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 19;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('link')
                    ->maxLength(255),
                TiptapEditor::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('interaction_type_id')
                    ->relationship('interactionType', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\Textarea::make('content')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('entity_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('entity_id')
                    ->required()
                    ->numeric(),
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
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('interactionType.name')
                    ->badge()
                    ->color(function (string $state): string {
                        static $colors = null;

                        if (! $colors) {
                            // Retrieve roles once and cache them to avoid multiple queries
                            $roles = \Illuminate\Support\Facades\Cache::remember('role_colors', now()->addMinutes(10), function () {
                                $availableColors = ['success', 'danger', 'warning', 'info', 'gray'];

                                shuffle($availableColors);

                                // Retrieve roles from database
                                $dbInteractionTypes = \Illuminate\Support\Facades\DB::table('interaction_types')->pluck('name')->toArray();

                                $roles = collect($dbInteractionTypes)->mapWithKeys(function ($role, $index) use ($availableColors) {
                                    return [$role => $availableColors[$index % count($availableColors)]];
                                })->toArray();

                                return $roles;
                            });

                            $colors = $roles;
                        }

                        return $colors[$state] ?? 'gray';
                    }),
                Tables\Columns\TextColumn::make('entity.title')
                    // NOTE: Kigathi - May 18 2025 - This assumes only interactions are made on articles
                    ->label('Article')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'published',
                        'danger' => 'deleted',
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
            ]);
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
            'index' => Pages\ListInteractions::route('/'),
            'create' => Pages\CreateInteraction::route('/create'),
            'edit' => Pages\EditInteraction::route('/{record}/edit'),
        ];
    }
}
