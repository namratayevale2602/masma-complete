<?php
// app/Filament/Resources/CtaCardResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\CtaCardResource\Pages;
use App\Models\CtaCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CtaCardResource extends Resource
{
    protected static ?string $model = CtaCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'CTA Cards';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('CTA Card Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Exhibitor, Visitor, Member'),
                        
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(65535)
                            ->rows(3)
                            ->placeholder('Enter card description...'),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('icon')
                                    ->label('Icon')
                                    ->options([
                                        'FaIndustry' => 'Industry (Exhibitor)',
                                        'FaUsers' => 'Users (Visitor)',
                                        'FaUserTie' => 'User Tie (Member)',
                                        'FaBuilding' => 'Building',
                                        'FaAward' => 'Award',
                                        'FaHandshake' => 'Handshake',
                                        'FaStar' => 'Star',
                                    ])
                                    ->default('FaIndustry')
                                    ->helperText('Select an icon for this card'),
                                
                                Forms\Components\ColorPicker::make('color')
                                    ->label('Card Color')
                                    ->default('#005aa8')
                                    ->helperText('Select background color for the card'),
                            ]),
                        
                        Forms\Components\TextInput::make('stats')
                            ->maxLength(100)
                            ->placeholder('e.g., 500+ Booths, 15,000+ Visitors, 200+ Speakers'),
                        
                        Forms\Components\TextInput::make('link')
                            ->maxLength(255)
                            ->placeholder('e.g., https://masmaexpo.in/exhibitor or /bemember'),
                        
                        Forms\Components\TextInput::make('button_text')
                            ->maxLength(50)
                            ->default('Register')
                            ->placeholder('e.g., Register, Join Now, Book Now'),
                        
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
                                    ->helperText('Inactive cards will not be shown on the website'),
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
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Exhibitor' => 'success',
                        'Visitor' => 'warning',
                        'Member' => 'info',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('stats')
                    ->searchable()
                    ->badge()
                    ->color('secondary'),
                
                Tables\Columns\TextColumn::make('button_text')
                    ->label('Button')
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('title')
                    ->options([
                        'Exhibitor' => 'Exhibitor',
                        'Visitor' => 'Visitor',
                        'Member' => 'Member',
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
            'index' => Pages\ListCtaCards::route('/'),
            'create' => Pages\CreateCtaCard::route('/create'),
            'edit' => Pages\EditCtaCard::route('/{record}/edit'),
        ];
    }
}