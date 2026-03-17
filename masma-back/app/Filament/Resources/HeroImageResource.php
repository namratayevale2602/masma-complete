<?php
// app/Filament/Resources/HeroImageResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroImageResource\Pages;
use App\Models\HeroImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class HeroImageResource extends Resource
{
    protected static ?string $model = HeroImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Hero Images';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hero Image Details')
                    ->schema([
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('desktop_image')
                                    ->label('Desktop Image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080')
                                    ->disk('uploads') // Use the uploads disk
                                    ->directory('hero/desktop')
                                    ->visibility('public')
                                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateHeroImage)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                                    ->maxSize(5120)
                                    ->getUploadedFileNameForStorageUsing(function ($file) {
                                        return time() . '_desktop_' . $file->getClientOriginalName();
                                    })
                                    ->helperText('Recommended size: 1920x1080px. Max size: 5MB'),
                                
                                Forms\Components\FileUpload::make('mobile_image')
                                    ->label('Mobile Image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('9:16')
                                    ->imageResizeTargetWidth('1080')
                                    ->imageResizeTargetHeight('1920')
                                    ->disk('uploads') // Use the uploads disk
                                    ->directory('hero/mobile')
                                    ->visibility('public')
                                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateHeroImage)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                                    ->maxSize(5120)
                                    ->getUploadedFileNameForStorageUsing(function ($file) {
                                        return time() . '_mobile_' . $file->getClientOriginalName();
                                    })
                                    ->helperText('Recommended size: 1080x1920px. Max size: 5MB'),
                            ]),
                        
                        Forms\Components\TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->maxLength(255)
                            ->placeholder('Describe the image for SEO and accessibility'),
                        
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required(),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive images will not be shown on the website'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('desktop_image')
                    ->label('Desktop')
                    ->square()
                    ->size(60)
                    ->disk('uploads') // Specify the disk
                    ->url(fn ($record) => $record->desktop_image_url)
                    ->openUrlInNewTab(),
                
                Tables\Columns\ImageColumn::make('mobile_image')
                    ->label('Mobile')
                    ->square()
                    ->size(60)
                    ->disk('uploads') // Specify the disk
                    ->url(fn ($record) => $record->mobile_image_url)
                    ->openUrlInNewTab(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->placeholder('All'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (HeroImage $record) {
                        // Delete images using the uploads disk
                        if ($record->desktop_image) {
                            Storage::disk('uploads')->delete($record->desktop_image);
                        }
                        if ($record->mobile_image) {
                            Storage::disk('uploads')->delete($record->mobile_image);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->desktop_image) {
                                    Storage::disk('uploads')->delete($record->desktop_image);
                                }
                                if ($record->mobile_image) {
                                    Storage::disk('uploads')->delete($record->mobile_image);
                                }
                            }
                        }),
                ]),
            ])
            ->reorderable('order')
            ->defaultSort('order');
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
            'index' => Pages\ListHeroImages::route('/'),
            'create' => Pages\CreateHeroImage::route('/create'),
            'edit' => Pages\EditHeroImage::route('/{record}/edit'),
        ];
    }
}