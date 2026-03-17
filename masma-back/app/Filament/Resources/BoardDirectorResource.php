<?php
// app/Filament/Resources/BoardDirectorResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\BoardDirectorResource\Pages;
use App\Models\BoardDirector;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class BoardDirectorResource extends Resource
{
    protected static ?string $model = BoardDirector::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Board of Directors';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Director Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter full name'),
                        
                        Forms\Components\TextInput::make('place')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Nashik, Pune, Kolhapur'),
                        
                        Forms\Components\TextInput::make('designation')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., President, Vice President, Secretary'),
                        
                        Forms\Components\TextInput::make('education')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., BE, MBA, LLB, PhD pursuing'),
                        
                        Forms\Components\TextInput::make('experience')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., 17+ years in RE Sector'),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Director Photo')
                            ->image()
                            ->disk('uploads')
                            ->directory('directors')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->maxSize(5120)
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_director_' . $file->getClientOriginalName();
                            })
                            ->helperText('Upload director photo (max 5MB)')
                            ->imageResizeMode(null)
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(null)
                            ->imageResizeTargetHeight(null)
                            ->imageEditor(false),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Display order (lower numbers appear first)'),
                                
                                Forms\Components\TextInput::make('year')
                                    ->maxLength(20)
                                    ->default('2025-26')
                                    ->helperText('e.g., 2025-26, 2024-25')
                                    ->required(),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive directors will not be shown on the website'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Photo')
                    ->circular()
                    ->size(50)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('designation')
                    ->searchable()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('place')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('year')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(fn () => BoardDirector::distinct()->pluck('year', 'year')->toArray()),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (BoardDirector $record) {
                        if ($record->image) {
                            Storage::disk('uploads')->delete($record->image);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->image) {
                                    Storage::disk('uploads')->delete($record->image);
                                }
                            }
                        }),
                ]),
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->defaultSort('year', 'desc');
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
            'index' => Pages\ListBoardDirectors::route('/'),
            'create' => Pages\CreateBoardDirector::route('/create'),
            'edit' => Pages\EditBoardDirector::route('/{record}/edit'),
        ];
    }
}