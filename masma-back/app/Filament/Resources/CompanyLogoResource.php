<?php
// app/Filament/Resources/CompanyLogoResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyLogoResource\Pages;
use App\Models\CompanyLogo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CompanyLogoResource extends Resource
{
    protected static ?string $model = CompanyLogo::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Company Logos';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Logo Details')
                    ->schema([
                       
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Company Logo')
                            ->image()
                            ->disk('uploads')
                            ->directory('company-logos')
                            ->visibility('public')
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateCompanyLogo)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'])
                            ->maxSize(5120)
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_logo_' . $file->getClientOriginalName();
                            })
                            ->helperText('Upload company logo (max 5MB)')
                            ->imageResizeMode(null)
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(null)
                            ->imageResizeTargetHeight(null)
                            ->imageEditor(false)
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Display order (lower numbers appear first)'),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive logos will not be shown on the website'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Logo')
                    ->square()
                    ->size(60)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (CompanyLogo $record) {
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
            'index' => Pages\ListCompanyLogos::route('/'),
            'create' => Pages\CreateCompanyLogo::route('/create'),
            'edit' => Pages\EditCompanyLogo::route('/{record}/edit'),
        ];
    }
}