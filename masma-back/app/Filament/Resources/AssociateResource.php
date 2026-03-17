<?php
// app/Filament/Resources/AssociateResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\AssociateResource\Pages;
use App\Models\Associate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AssociateResource extends Resource
{
    protected static ?string $model = Associate::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Associate Companies';

    protected static ?string $navigationGroup = 'Team Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('industry')
                            ->maxLength(255)
                            ->placeholder('e.g., Solar Panel Manufacturing, Solar Installation'),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->rows(4)
                            ->placeholder('Enter company description...'),
                        
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->disk('uploads')
                            ->directory('associates')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_associate_' . $file->getClientOriginalName();
                            })
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
                                    ->default(0),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->square()
                    ->size(60)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('industry')
                    ->searchable()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Associate $record) {
                        if ($record->logo) {
                            Storage::disk('uploads')->delete($record->logo);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->logo) {
                                    Storage::disk('uploads')->delete($record->logo);
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
            'index' => Pages\ListAssociates::route('/'),
            'create' => Pages\CreateAssociate::route('/create'),
            'edit' => Pages\EditAssociate::route('/{record}/edit'),
        ];
    }
}