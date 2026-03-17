<?php
// app/Filament/Resources/ContactMessageResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $navigationGroup = 'Contact Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Message Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('phone')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('subject')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('message')
                            ->disabled()
                            ->rows(5),
                        
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Received At')
                            ->disabled(),
                        
                        Forms\Components\Toggle::make('is_read')
                            ->disabled(),
                        
                        Forms\Components\Toggle::make('is_replied')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('reply_message')
                            ->label('Reply Message')
                            ->rows(3)
                            ->visible(fn ($record) => $record->is_replied),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_read')
                    ->boolean()
                    ->label('Read'),
                
                Tables\Columns\IconColumn::make('is_replied')
                    ->boolean()
                    ->label('Replied'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read'),
                Tables\Filters\TernaryFilter::make('is_replied'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('reply')
                            ->required()
                            ->rows(5)
                            ->label('Reply Message'),
                    ])
                    ->action(function (ContactMessage $record, array $data) {
                        // Send reply email logic here
                        $record->markAsReplied($data['reply']);
                        
                        // You can add email sending logic here
                        // Mail::to($record->email)->send(new ContactReply($record, $data['reply']));
                    })
                    ->visible(fn ($record) => !$record->is_replied),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
        ];
    }
}