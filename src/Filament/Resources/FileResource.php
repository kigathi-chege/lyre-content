<?php

namespace Lyre\Content\Filament\Resources;

use Lyre\Content\Filament\Actions\GalleryAction;
use Lyre\Content\Filament\Resources\FileResource\Pages;
use Lyre\Content\Filament\Resources\FileResource\RelationManagers;
use Illuminate\Database\Eloquent\Collection;
use Lyre\Content\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Contracts\View\View;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'gmdi-folder-open';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 17;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file')
                    ->imagePreviewHeight('250')
                    ->loadingIndicatorPosition('left')
                    ->panelAspectRatio('3:1')
                    ->panelLayout('integrated')
                    ->removeUploadedFileButtonPosition('right')
                    ->uploadButtonPosition('left')
                    ->uploadProgressIndicatorPosition('left')
                    ->previewable(true)
                    ->multiple(false)
                    ->imageEditor()
                    ->columnSpanFull()
                    // ->rules(['mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,gif,svg,webp,mp4,mp3,txt'])
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/*',
                        'video/*',
                        'audio/*',
                        'text/*',

                        // Microsoft Office documents
                        'application/msword',                    // .doc
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                        'application/vnd.ms-excel',              // .xls
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        'application/vnd.ms-powerpoint',         // .ppt
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx

                        // text
                        'text/csv',
                        'text/plain',

                        // Archives
                        'application/zip',
                        'application/x-zip-compressed',
                        'multipart/x-zip',
                    ])
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ]),

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
                Tables\Columns\ImageColumn::make('file')
                    ->defaultImageUrl(fn($record) => $record->link),
                Tables\Columns\TextColumn::make('name')
                    ->copyable()
                    ->copyableState(fn(File $record): string => $record->link)
                    ->copyMessage('File link copied!')
                    ->copyMessageDuration(1500)
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable()
                    ->copyable()
                    ->copyableState(fn(File $record): string => $record->link)
                    ->copyMessage('File link copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('mimetype')
                    ->searchable()
                    ->copyable()
                    ->copyableState(fn(File $record): string => $record->link)
                    ->copyMessage('File link copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('usagecount')
                    ->numeric()
                    ->sortable()
                    ->copyable()
                    ->copyableState(fn(File $record): string => $record->link)
                    ->copyMessage('File link copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('viewed_at')
                    ->dateTime()
                    ->sortable()
                    ->copyable()
                    ->copyableState(fn(File $record): string => $record->link)
                    ->copyMessage('File link copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('storage')
                    ->searchable()
                    ->copyable()
                    ->copyableState(fn(File $record): string => $record->link)
                    ->copyMessage('File link copied!')
                    ->copyMessageDuration(1500),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records, $action): void {
                            collect($records)->each(fn(File $record) => $record->attachments()->delete());

                            $action->process(static fn(Collection $records) => $records->each(fn(Model $record) => $record->delete()));

                            foreach ($records as $record) {
                                unlink(storage_path('app/private/' . $record->path));
                                unlink(storage_path('app/private/' . $record->path_sm));
                                unlink(storage_path('app/private/' . $record->path_md));
                                unlink(storage_path('app/private/' . $record->path_lg));
                            }

                            $action->success();
                        }),
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
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }
}
