<?php
// app/Filament/Resources/CircularResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\CircularResource\Pages;
use App\Models\Circular;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CircularResource extends Resource
{
    protected static ?string $model = Circular::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Circulars & Documents';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->options([
                                'Important Circular' => 'Important Circular',
                                'Important Documents' => 'Important Documents',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('subcategory')
                            ->maxLength(255)
                            ->placeholder('e.g., Circulars, Procedures & Forms'),
                        
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Document File')
                            ->disk('uploads')
                            ->directory('circulars')
                            ->visibility('public')
                            ->required()
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240)
                            ->getUploadedFileNameForStorageUsing(function ($file, $livewire) {
                                $title = $livewire->data['title'] ?? 'document';
                                return time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $title) . '.' . $file->getClientOriginalExtension();
                            })
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('subcategory')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('file_type')
                    ->badge()
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Important Circular' => 'Important Circular',
                        'Important Documents' => 'Important Documents',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Circular $record) {
                        if ($record->file_path) {
                            Storage::disk('uploads')->delete($record->file_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->file_path) {
                                    Storage::disk('uploads')->delete($record->file_path);
                                }
                            }
                        }),
                ]),
            ])
            ->reorderable('order')
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCirculars::route('/'),
            'create' => Pages\CreateCircular::route('/create'),
            'edit' => Pages\EditCircular::route('/{record}/edit'),
        ];
    }
}