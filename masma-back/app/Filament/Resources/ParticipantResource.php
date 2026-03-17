<?php
// app/Filament/Resources/ParticipantResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Participants';

    protected static ?string $navigationGroup = 'Home';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Participant Details')
                    ->schema([
                        Forms\Components\Select::make('row')
                            ->options([
                                1 => 'Row 1 (Top Row - Larger Images)',
                                2 => 'Row 2 (Bottom Row)',
                            ])
                            ->required()
                            ->default(1)
                            ->native(false),
                        
                       
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Participant Image')
                            ->image()
                            ->disk('uploads')
                            ->directory('participants')
                            ->visibility('public')
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateParticipant)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'])
                            ->maxSize(10240)
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_' . $file->getClientOriginalName();
                            })
                            ->helperText('Original image will be stored without any modification. Max size: 10MB')
                            ->imageResizeMode(null)
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(null)
                            ->imageResizeTargetHeight(null)
                            ->imageEditor(false)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->maxLength(255)
                            ->placeholder('Describe the image for SEO and accessibility'),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required(),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive images will not be shown on the website'),
                            ]),
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
                    ->size(80)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('row')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '2' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => $state == 1 ? 'Row 1 (Top)' : 'Row 2 (Bottom)'),
                
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('row')
                    ->options([
                        1 => 'Row 1 (Top)',
                        2 => 'Row 2 (Bottom)',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Participant $record) {
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
            ])
            ->reorderable('order')
            ->defaultSort('row', 'asc')
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
            'index' => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'edit' => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }
}