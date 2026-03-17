<?php
// app/Filament/Resources/ObjectiveResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ObjectiveResource\Pages;
use App\Models\Objective;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ObjectiveResource extends Resource
{
    protected static ?string $model = Objective::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationLabel = 'Objectives';

    protected static ?string $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Objective Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Industry Unification, Government Coordination'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(65535)
                            ->rows(4)
                            ->placeholder('Enter objective description...'),
                        
                        Forms\Components\Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'FaUsers' => 'Users',
                                'FaHandshake' => 'Handshake',
                                'FaGraduationCap' => 'Graduation Cap',
                                'FaBullhorn' => 'Bullhorn',
                                'FaCog' => 'Cog',
                                'FaSun' => 'Sun',
                                'FaChartLine' => 'Chart Line',
                                'FaShieldAlt' => 'Shield Alt',
                                'FaBalanceScale' => 'Balance Scale',
                                'FaAward' => 'Award',
                                'FaRocket' => 'Rocket',
                                'FaBuilding' => 'Building',
                                'FaMoneyCheck' => 'Money Check',
                                'FaUserTie' => 'User Tie',
                                'FaClipboardCheck' => 'Clipboard Check',
                                'FaEye' => 'Eye',
                                'FaBullseye' => 'Bullseye',
                                'FaFlag' => 'Flag',
                                'FaGlobe' => 'Globe',
                                'FaLightbulb' => 'Lightbulb',
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
                                    ->helperText('Inactive objectives will not be shown on the website'),
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
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('icon')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable()
                    ->alignRight(),
                
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
            'index' => Pages\ListObjectives::route('/'),
            'create' => Pages\CreateObjective::route('/create'),
            'edit' => Pages\EditObjective::route('/{record}/edit'),
        ];
    }
}