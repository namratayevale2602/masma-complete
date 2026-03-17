<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitorResource\Pages;
use App\Models\Visitor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class VisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Visitors';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('visitor_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bussiness_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('mobile')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('whatsapp_no')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Address Information')
                    ->schema([
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('town')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('village')
                            ->maxLength(100),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('remark')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('QR Code')
                    ->schema([
                        Forms\Components\Placeholder::make('qr_code')
                            ->label('QR Code')
                            ->content(function ($record) {
                                if ($record && $record->qr_code_url) {
                                    return view('filament.components.qr-code-view', [
                                        'url' => $record->qr_code_url,
                                        'downloadUrl' => $record->qr_code_download_url,
                                    ])->render();
                                }
                                return 'No QR code generated yet.';
                            }),
                    ])
                    ->hidden(fn ($record) => $record === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visitor_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bussiness_name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('qr_code_path')
                    ->label('QR Code')
                    ->boolean()
                    ->trueIcon('heroicon-o-qr-code')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_qr_code')
                    ->label('Has QR Code')
                    ->query(fn ($query) => $query->whereNotNull('qr_code_path')),
            ])
            ->actions([
                Tables\Actions\Action::make('view_qr')
                    ->label('View QR')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('Visitor QR Code')
                    ->modalContent(fn ($record) => view('filament.components.qr-code-modal', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Tables\Actions\Action::make('download_qr')
                    ->label('Download QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn ($record) => response()->download(
                        storage_path('app/public/' . $record->qr_code_path),
                        'visitor-' . $record->id . '-qr-code.png'
                    ))
                    ->hidden(fn ($record) => empty($record->qr_code_path)),
                Tables\Actions\Action::make('send_qr_email')
                    ->label('Resend QR Email')
                    ->icon('heroicon-o-envelope')
                    ->action(function ($record) {
                        try {
                            \Mail::to($record->email)->send(new \App\Mail\VisitorQrCodeMail($record));
                            \Filament\Notifications\Notification::make()
                                ->title('Email Sent Successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed to send email')
                                ->danger()
                                ->send();
                        }
                    })
                    ->hidden(fn ($record) => empty($record->qr_code_path)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export_qr_codes')
                        ->label('Export QR Codes')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $zip = new \ZipArchive();
                            $zipFileName = 'visitor-qr-codes-' . time() . '.zip';
                            $zipPath = storage_path('app/public/' . $zipFileName);
                            
                            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                                foreach ($records as $record) {
                                    if ($record->qr_code_path && file_exists(storage_path('app/public/' . $record->qr_code_path))) {
                                        $zip->addFile(
                                            storage_path('app/public/' . $record->qr_code_path),
                                            'visitor-' . $record->id . '-' . $record->visitor_name . '.png'
                                        );
                                    }
                                }
                                $zip->close();
                                
                                return response()->download($zipPath)->deleteFileAfterSend(true);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListVisitors::route('/'),
            'create' => Pages\CreateVisitor::route('/create'),
            'edit' => Pages\EditVisitor::route('/{record}/edit'),
        ];
    }
}