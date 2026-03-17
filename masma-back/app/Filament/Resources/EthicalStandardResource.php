<?php
// app/Filament/Resources/EthicalStandardResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\EthicalStandardResource\Pages;
use App\Models\EthicalStandard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EthicalStandardResource extends Resource
{
    protected static ?string $model = EthicalStandard::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Ethical Standards';

    protected static ?string $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ethical Standard Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Transparency, Fair Pricing, Ethical Competition'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(65535)
                            ->rows(4)
                            ->placeholder('Enter ethical standard description...'),
                        
                        Forms\Components\Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'FaAward' => 'Award',
                                'FaShieldAlt' => 'Shield Alt',
                                'FaBalanceScale' => 'Balance Scale',
                                'FaHandshake' => 'Handshake',
                                'FaEye' => 'Eye',
                                'FaFlag' => 'Flag',
                                'FaGlobe' => 'Globe',
                                'FaLightbulb' => 'Lightbulb',
                                'FaUsers' => 'Users',
                                'FaChartLine' => 'Chart Line',
                            ])
                            ->searchable()
                            ->required(),
                        
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
                                    ->helperText('Inactive standards will not be shown on the website'),
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
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('icon')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable()
                    ->alignRight(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEthicalStandards::route('/'),
            'create' => Pages\CreateEthicalStandard::route('/create'),
            'edit' => Pages\EditEthicalStandard::route('/{record}/edit'),
        ];
    }
}