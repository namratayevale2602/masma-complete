<?php
// app/Filament/Resources/VisionMissionGoalResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\VisionMissionGoalResource\Pages;
use App\Models\VisionMissionGoal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class VisionMissionGoalResource extends Resource
{
    protected static ?string $model = VisionMissionGoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'Vision, Mission & Goals';

    protected static ?string $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Type Selection')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'vision' => 'Vision',
                                'mission' => 'Mission',
                                'goal' => 'Goal',
                            ])
                            ->required()
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'vision') {
                                    $set('title', 'Our Vision');
                                } elseif ($state === 'mission') {
                                    $set('title', 'Our Mission');
                                } elseif ($state === 'goal') {
                                    $set('title', 'Our Goals');
                                }
                            }),
                    ]),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(65535)
                            ->rows(4),
                        
                        Forms\Components\Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'FaEye' => 'Eye (Vision)',
                                'FaBullseye' => 'Bullseye (Mission)',
                                'FaFlag' => 'Flag (Goals)',
                                'FaRocket' => 'Rocket',
                                'FaGlobe' => 'Globe',
                                'FaLightbulb' => 'Lightbulb',
                                'FaStar' => 'Star',
                                'FaAward' => 'Award',
                            ])
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\KeyValue::make('items')
                            ->label('Items / Highlights / Points')
                            ->helperText('Add key-value pairs for highlights, points, or categories')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                                
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
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'vision' => 'success',
                        'mission' => 'warning',
                        'goal' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('icon')
                    ->badge()
                    ->color('secondary'),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'vision' => 'Vision',
                        'mission' => 'Mission',
                        'goal' => 'Goal',
                    ]),
                
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
            ->defaultSort('type', 'asc')
            ->defaultSort('order', 'asc');
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
            'index' => Pages\ListVisionMissionGoals::route('/'),
            'create' => Pages\CreateVisionMissionGoal::route('/create'),
            'edit' => Pages\EditVisionMissionGoal::route('/{record}/edit'),
        ];
    }
}