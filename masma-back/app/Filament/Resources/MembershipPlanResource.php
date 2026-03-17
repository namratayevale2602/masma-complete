<?php
// app/Filament/Resources/MembershipPlanResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipPlanResource\Pages;
use App\Models\MembershipPlan;
use App\Models\MembershipFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\KeyValue;

class MembershipPlanResource extends Resource
{
    protected static ?string $model = MembershipPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Membership Plans';

    protected static ?string $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('type')
                            ->maxLength(100)
                            ->placeholder('e.g., student, classic, dealer, silver, gold'),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('membership_fee')
                                    ->label('Membership Fee')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('e.g., ₹1000, ₹2500'),
                                
                                Forms\Components\TextInput::make('registration_charges')
                                    ->label('Registration Charges')
                                    ->maxLength(50)
                                    ->placeholder('e.g., ₹500 or leave empty'),
                                
                                Forms\Components\TextInput::make('duration')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('e.g., Economic Year'),
                            ]),
                        
                        Forms\Components\Toggle::make('is_highlighted')
                            ->label('Highlight as Popular')
                            ->default(false),
                    ]),

                Forms\Components\Section::make('Features')
                    ->schema([
                        KeyValue::make('features')
                            ->keyLabel('Feature Key')
                            ->valueLabel('Feature Value')
                            ->addable(true)
                            ->editableKeys(true)
                            ->deletable(true)
                            ->columnSpanFull()
                            ->helperText('Enter feature values like "Yes", "No", "3/Month", "Add Charges", etc.'),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Students' => 'gray',
                        'EPC Classic' => 'info',
                        'Dealer /Distributor' => 'warning',
                        'Corporate Silver' => 'secondary',
                        'Corporate Gold' => 'success',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('membership_fee')
                    ->label('Fee')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('duration')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_highlighted')
                    ->label('Popular')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star'),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_highlighted')
                    ->label('Popular'),
                
                Tables\Filters\TernaryFilter::make('is_active'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembershipPlans::route('/'),
            'create' => Pages\CreateMembershipPlan::route('/create'),
            'edit' => Pages\EditMembershipPlan::route('/{record}/edit'),
        ];
    }
}