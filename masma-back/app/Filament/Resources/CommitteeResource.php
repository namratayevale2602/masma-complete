<?php
// app/Filament/Resources/CommitteeResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\CommitteeResource\Pages;
use App\Models\Committee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CommitteeResource extends Resource
{
    protected static ?string $model = Committee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Committees';

    protected static ?string $navigationGroup = 'Team Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->schema([
                        Forms\Components\TextInput::make('category_title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Public Relations Committee, Women Entrepreneur\'s Committee'),
                        
                        Forms\Components\Select::make('category_icon')
                            ->options([
                                'FaUserTie' => 'User Tie',
                                'FaUsers' => 'Users',
                                'FaCrown' => 'Crown',
                                'FaUserShield' => 'User Shield',
                                'FaUserGraduate' => 'User Graduate',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('category_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order for category grouping'),
                    ])->columns(2),

                Forms\Components\Section::make('Member Information')
                    ->schema([
                        Forms\Components\TextInput::make('member_name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('member_city')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('member_position')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\FileUpload::make('member_image')
                            ->image()
                            ->disk('uploads')
                            ->directory('committees')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                return time() . '_committee_' . $file->getClientOriginalName();
                            })
                            ->imageResizeMode(null)
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(null)
                            ->imageResizeTargetHeight(null)
                            ->imageEditor(false),
                        
                        Forms\Components\TextInput::make('member_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order within category'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('member_image')
                    ->label('Photo')
                    ->circular()
                    ->size(50)
                    ->disk('uploads'),
                
                Tables\Columns\TextColumn::make('category_title')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('member_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('member_position')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('member_city')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('category_order')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_title')
                    ->options(fn () => Committee::pluck('category_title', 'category_title')->toArray()),
                
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Committee $record) {
                        if ($record->member_image) {
                            Storage::disk('uploads')->delete($record->member_image);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->member_image) {
                                    Storage::disk('uploads')->delete($record->member_image);
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('category_order')
            ->defaultSort('member_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommittees::route('/'),
            'create' => Pages\CreateCommittee::route('/create'),
            'edit' => Pages\EditCommittee::route('/{record}/edit'),
        ];
    }
}