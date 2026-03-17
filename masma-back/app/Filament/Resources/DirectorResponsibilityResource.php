<?php
// app/Filament/Resources/DirectorResponsibilityResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectorResponsibilityResource\Pages;
use App\Models\DirectorResponsibility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DirectorResponsibilityResource extends Resource
{
    protected static ?string $model = DirectorResponsibility::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Director Responsibilities';

    protected static ?string $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Responsibility Details')
                    ->schema([
                        Forms\Components\Textarea::make('task')
                            ->required()
                            ->maxLength(65535)
                            ->rows(3)
                            ->placeholder('Enter director responsibility task...'),
                        
                        Forms\Components\Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'FaUserTie' => 'User Tie',
                                'FaClipboardCheck' => 'Clipboard Check',
                                'FaBuilding' => 'Building',
                                'FaChartLine' => 'Chart Line',
                                'FaSun' => 'Sun',
                                'FaGraduationCap' => 'Graduation Cap',
                                'FaMoneyCheck' => 'Money Check',
                                'FaShieldAlt' => 'Shield Alt',
                                'FaHandshake' => 'Handshake',
                                'FaUsers' => 'Users',
                                'FaBullhorn' => 'Bullhorn',
                                'FaCog' => 'Cog',
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
                                    ->helperText('Inactive responsibilities will not be shown on the website'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('icon')
                    ->badge()
                    ->color('warning'),
                
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
            'index' => Pages\ListDirectorResponsibilities::route('/'),
            'create' => Pages\CreateDirectorResponsibility::route('/create'),
            'edit' => Pages\EditDirectorResponsibility::route('/{record}/edit'),
        ];
    }
}