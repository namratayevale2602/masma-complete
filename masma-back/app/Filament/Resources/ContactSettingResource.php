<?php
// app/Filament/Resources/ContactSettingResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactSettingResource\Pages;
use App\Models\ContactSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactSettingResource extends Resource
{
    protected static ?string $model = ContactSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = 'Contact Settings';

    protected static ?string $navigationGroup = 'Contact Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Settings')
                    ->schema([
                        Forms\Components\TextInput::make('page_title')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('page_description')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\Textarea::make('office_address')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('working_hours_weekdays')
                                    ->maxLength(255)
                                    ->placeholder('Monday - Friday: 9:00 AM - 6:00 PM'),
                                
                                Forms\Components\TextInput::make('working_hours_saturday')
                                    ->maxLength(255)
                                    ->placeholder('Saturday: 9:00 AM - 2:00 PM'),
                            ]),
                    ]),

                Forms\Components\Section::make('Map Settings')
                    ->schema([
                        Forms\Components\Textarea::make('map_embed_url')
                            ->rows(3)
                            ->maxLength(65535)
                            ->helperText('Enter Google Maps embed URL'),
                    ]),

                Forms\Components\Section::make('Form Settings')
                    ->schema([
                        Forms\Components\TextInput::make('form_title')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('form_description')
                            ->rows(2)
                            ->maxLength(65535),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page_title')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactSettings::route('/'),
            'create' => Pages\CreateContactSetting::route('/create'),
            'edit' => Pages\EditContactSetting::route('/{record}/edit'),
        ];
    }
}