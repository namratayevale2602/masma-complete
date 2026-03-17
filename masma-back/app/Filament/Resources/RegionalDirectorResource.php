<?php
// app/Filament/Resources/RegionalDirectorResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionalDirectorResource\Pages;
use App\Models\RegionalDirector;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class RegionalDirectorResource extends Resource
{
    protected static ?string $model = RegionalDirector::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Regional Directors';

    protected static ?string $navigationGroup = 'Team Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->schema([
                        Forms\Components\Select::make('category_title')
                            ->options([
                                'Regional Director' => 'Regional Director',
                                'District Director' => 'District Director',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('category_icon')
                            ->options([
                                'FaUserTie' => 'User Tie',
                                'FaCrown' => 'Crown',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('category_order')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Member Information')
                    ->schema([
                        Forms\Components\TextInput::make('member_name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('member_city')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('member_region')
                            ->label('Region/Position')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., West Maharashtra, North Maharashtra'),
                        
                        Forms\Components\FileUpload::make('member_image')
                            ->image()
                            ->disk('uploads')
                            ->directory('regional-directors')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_director_' . $file->getClientOriginalName();
                            })
                            ->imageResizeMode(null)
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(null)
                            ->imageResizeTargetHeight(null)
                            ->imageEditor(false),
                        
                        Forms\Components\TextInput::make('member_order')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('member_image')
                    ->label('Photo')
                    ->circular()
                    ->size(50)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('category_title')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('member_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('member_region')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('member_city')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_title')
                    ->options([
                        'Regional Director' => 'Regional Director',
                        'District Director' => 'District Director',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (RegionalDirector $record) {
                        if ($record->member_image) {
                            Storage::disk('uploads')->delete($record->member_image);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->member_image) {
                                    Storage::disk('uploads')->delete($record->member_image);
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('category_order')
            ->defaultSort('member_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegionalDirectors::route('/'),
            'create' => Pages\CreateRegionalDirector::route('/create'),
            'edit' => Pages\EditRegionalDirector::route('/{record}/edit'),
        ];
    }
}