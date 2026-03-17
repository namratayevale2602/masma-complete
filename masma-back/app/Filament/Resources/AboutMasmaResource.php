<?php
// app/Filament/Resources/AboutMasmaResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutMasmaResource\Pages;
use App\Models\AboutMasma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AboutMasmaResource extends Resource
{
    protected static ?string $model = AboutMasma::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'About MASMA';

    protected static ?string $navigationGroup = 'About Us';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255)
                            ->default('Welcome To Our Association')
                            ->placeholder('Enter section title'),
                    ]),

                Forms\Components\Section::make('President Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('president_name')
                                    ->maxLength(255)
                                    ->placeholder('e.g., Mr. Amit Kulkarni'),
                                
                                Forms\Components\TextInput::make('president_title')
                                    ->maxLength(255)
                                    ->placeholder('e.g., President'),
                            ]),
                        
                        Forms\Components\FileUpload::make('president_image')
                            ->label('President Photo')
                            ->image()
                            ->disk('uploads')
                            ->directory('about-masma')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->maxSize(5120)
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_president_' . $file->getClientOriginalName();
                            })
                            ->helperText('Upload president photo (max 5MB)')
                            ->imageResizeMode(null)
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(null)
                            ->imageResizeTargetHeight(null)
                            ->imageEditor(false)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('president_message')
                            ->label('President Message (Paragraph 1)')
                            ->maxLength(65535)
                            ->rows(4)
                            ->placeholder('Enter first paragraph of president message...')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('president_message_2')
                            ->label('President Message (Paragraph 2)')
                            ->maxLength(65535)
                            ->rows(4)
                            ->placeholder('Enter second paragraph of president message...')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('president_message_3')
                            ->label('President Message (Paragraph 3)')
                            ->maxLength(65535)
                            ->rows(4)
                            ->placeholder('Enter third paragraph of president message...')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('stats_1_label')
                                    ->label('Stat 1 Label')
                                    ->maxLength(100)
                                    ->default('Years of Experience'),
                                
                                Forms\Components\TextInput::make('stats_1_value')
                                    ->label('Stat 1 Value')
                                    ->maxLength(50)
                                    ->default('20+'),
                            ]),
                        
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('stats_2_label')
                                    ->label('Stat 2 Label')
                                    ->maxLength(100)
                                    ->default('Member Companies'),
                                
                                Forms\Components\TextInput::make('stats_2_value')
                                    ->label('Stat 2 Value')
                                    ->maxLength(50)
                                    ->default('500+'),
                            ]),
                        
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('stats_3_label')
                                    ->label('Stat 3 Label')
                                    ->maxLength(100)
                                    ->default('Projects Completed'),
                                
                                Forms\Components\TextInput::make('stats_3_value')
                                    ->label('Stat 3 Value')
                                    ->maxLength(50)
                                    ->default('1000+'),
                            ]),
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
                                    ->default(true)
                                    ->helperText('Only one about masma section can be active at a time'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('president_image')
                    ->label('President')
                    ->circular()
                    ->size(50)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('president_name')
                    ->label('President')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('stats_1_value')
                    ->label('Stats')
                    ->formatStateUsing(function ($record) {
                        return "{$record->stats_1_value} {$record->stats_1_label}";
                    })
                    ->limit(20),
                
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
                Tables\Actions\Action::make('toggleActive')
                    ->label('Toggle Active')
                    ->icon('heroicon-o-power')
                    ->color(fn (AboutMasma $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (AboutMasma $record) {
                        if (!$record->is_active) {
                            AboutMasma::where('is_active', true)->update(['is_active' => false]);
                        }
                        $record->is_active = !$record->is_active;
                        $record->save();
                    })
                    ->requiresConfirmation(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (AboutMasma $record) {
                        if ($record->president_image) {
                            Storage::disk('uploads')->delete($record->president_image);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->president_image) {
                                    Storage::disk('uploads')->delete($record->president_image);
                                }
                            }
                        }),
                ]),
            ]);
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
            'index' => Pages\ListAboutMasmas::route('/'),
            'create' => Pages\CreateAboutMasma::route('/create'),
            'edit' => Pages\EditAboutMasma::route('/{record}/edit'),
        ];
    }
}