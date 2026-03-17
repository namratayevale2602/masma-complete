<?php
// app/Filament/Resources/AboutUsResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutUsResource\Pages;
use App\Models\AboutUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AboutUsResource extends Resource
{
    protected static ?string $model = AboutUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'About Us';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('About Us Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->default('Welcome To Our Association')
                            ->required(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(65535)
                            ->rows(8)
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('About Us Image')
                            ->image()
                            ->disk('uploads')
                            ->directory('about')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->maxSize(5120)
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_about_' . $file->getClientOriginalName();
                            })
                            ->helperText('Upload the about us image (max 5MB)')
                            ->imageResizeMode(null)
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(null)
                            ->imageResizeTargetHeight(null)
                            ->imageEditor(false)
                            ->columnSpanFull(),
                        
                        Forms\Components\Section::make('Badge Information')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('badge_number')
                                            ->label('Badge Number')
                                            ->maxLength(50)
                                            ->default('20')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('badge_label')
                                            ->label('Badge Label')
                                            ->maxLength(100)
                                            ->default('Years')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('badge_subtext')
                                            ->label('Badge Subtext')
                                            ->maxLength(100)
                                            ->default('of Legacy')
                                            ->required(),
                                    ]),
                            ]),
                        
                        Forms\Components\Section::make('Button Configuration')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('button_text')
                                            ->label('Button Text')
                                            ->maxLength(100)
                                            ->default('Read More')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('button_link')
                                            ->label('Button Link')
                                            ->maxLength(255)
                                            ->default('/about-us')
                                            ->required(),
                                    ]),
                            ]),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Only one about us section can be active at a time')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->square()
                    ->size(60)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('badge_number')
                    ->label('Badge'),
                
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
                Tables\Actions\Action::make('toggleActive')
                    ->label('Toggle Active')
                    ->icon('heroicon-o-power')
                    ->color(fn (AboutUs $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (AboutUs $record) {
                        // Deactivate all others if activating
                        if (!$record->is_active) {
                            AboutUs::where('is_active', true)->update(['is_active' => false]);
                        }
                        $record->is_active = !$record->is_active;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Toggle Active Status')
                    ->modalDescription(fn (AboutUs $record): string => 
                        $record->is_active 
                            ? 'Are you sure you want to deactivate this about us section?' 
                            : 'Are you sure you want to activate this about us section? This will deactivate any other active section.'
                    ),
                Tables\Actions\DeleteAction::make()
                    ->before(function (AboutUs $record) {
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
            'index' => Pages\ListAboutUs::route('/'),
            'create' => Pages\CreateAboutUs::route('/create'),
            'edit' => Pages\EditAboutUs::route('/{record}/edit'),
        ];
    }
}