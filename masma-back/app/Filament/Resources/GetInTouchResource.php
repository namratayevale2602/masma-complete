<?php
// app/Filament/Resources/GetInTouchResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\GetInTouchResource\Pages;
use App\Models\GetInTouch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class GetInTouchResource extends Resource
{
    protected static ?string $model = GetInTouch::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Get In Touch';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Get In Touch Content')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Small Title')
                                    ->maxLength(255)
                                    ->default('Get in Touch')
                                    ->placeholder('e.g., Get in Touch')
                                    ->required(),
                                
                                Forms\Components\TextInput::make('main_title')
                                    ->label('Main Title')
                                    ->maxLength(255)
                                    ->default("Let's Work Together!")
                                    ->placeholder('e.g., Let\'s Work Together!')
                                    ->required(),
                            ]),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(65535)
                            ->rows(4)
                            ->placeholder('Enter description text...')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('background_image')
                            ->label('Background Image')
                            ->image()
                            ->disk('uploads')
                            ->directory('get-in-touch')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->maxSize(10240)
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_getintouch_' . $file->getClientOriginalName();
                            })
                            ->helperText('Upload background image (recommended size: 1920x1080px, max 10MB)')
                            ->imageResizeMode(null)
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(null)
                            ->imageResizeTargetHeight(null)
                            ->imageEditor(false)
                            ->columnSpanFull(),
                        
                        Forms\Components\Section::make('Button Configuration')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('button_text')
                                            ->label('Button Text')
                                            ->maxLength(100)
                                            ->default('Became A Member')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('button_link')
                                            ->label('Button Link')
                                            ->maxLength(255)
                                            ->default('/bemember')
                                            ->required(),
                                        
                                        Forms\Components\Select::make('button_icon')
                                            ->label('Button Icon')
                                            ->options([
                                                'FaUserPlus' => 'User Plus',
                                                'FaArrowRight' => 'Arrow Right',
                                                'FaCalendarAlt' => 'Calendar',
                                                'FaMapMarkerAlt' => 'Map Marker',
                                                'FaUsers' => 'Users',
                                                'FaEnvelope' => 'Envelope',
                                                'FaPhone' => 'Phone',
                                            ])
                                            ->default('FaUserPlus')
                                            ->required(),
                                    ]),
                            ]),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Only one get in touch section can be active at a time')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('background_image')
                    ->label('Background')
                    ->square()
                    ->size(60)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Small Title')
                    ->searchable()
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('main_title')
                    ->label('Main Title')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('button_text')
                    ->label('Button')
                    ->badge()
                    ->color('success'),
                
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
                    ->color(fn (GetInTouch $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (GetInTouch $record) {
                        if (!$record->is_active) {
                            GetInTouch::where('is_active', true)->update(['is_active' => false]);
                        }
                        $record->is_active = !$record->is_active;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Toggle Active Status')
                    ->modalDescription(fn (GetInTouch $record): string => 
                        $record->is_active 
                            ? 'Are you sure you want to deactivate this section?' 
                            : 'Are you sure you want to activate this section? This will deactivate any other active section.'
                    ),
                Tables\Actions\DeleteAction::make()
                    ->before(function (GetInTouch $record) {
                        if ($record->background_image) {
                            Storage::disk('uploads')->delete($record->background_image);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->background_image) {
                                    Storage::disk('uploads')->delete($record->background_image);
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
            'index' => Pages\ListGetInTouches::route('/'),
            'create' => Pages\CreateGetInTouch::route('/create'),
            'edit' => Pages\EditGetInTouch::route('/{record}/edit'),
        ];
    }
}