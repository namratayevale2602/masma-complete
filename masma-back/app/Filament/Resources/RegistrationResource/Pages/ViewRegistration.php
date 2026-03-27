<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Resources\RegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewRegistration extends ViewRecord
{
    protected static string $resource = RegistrationResource::class;

   
    public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            // Personal Information Section
            Components\Section::make('Personal Information')
                ->schema([
                    Components\Split::make([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('applicant_name')
                                    ->label('Full Name')
                                    ->weight('bold')
                                    ->size(Components\TextEntry\TextEntrySize::Large),
                                
                                Components\TextEntry::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->date('d M Y'),
                            ]),
                        
                        // Applicant Photo
                        Components\ImageEntry::make('applicant_photo_url')
                            ->label('Applicant Photo')
                            ->disk('public')
                            ->height(200)
                            ->width(200)
                            ->square()
                            ->extraAttributes([
                                'class' => 'rounded-lg border border-gray-200 cursor-pointer hover:opacity-80',
                            ])
                            ->defaultImageUrl(url('https://via.placeholder.com/150?text=No+Photo')),
                    ])->from('lg'),
                ]),
            
            // Contact Information Section
            Components\Section::make('Contact Information')
                ->schema([
                    Components\Grid::make(4)
                        ->schema([
                            Components\TextEntry::make('mobile')
                                ->label('Mobile')
                                ->copyable()
                                ->icon('heroicon-o-phone'),
                            Components\TextEntry::make('phone')
                                ->label('Phone')
                                ->copyable(),
                            Components\TextEntry::make('whatsapp_no')
                                ->label('WhatsApp')
                                ->copyable(),
                            Components\TextEntry::make('office_email')
                                ->label('Email')
                                ->copyable()
                                ->icon('heroicon-o-envelope'),
                        ]),
                ]),
            
            // Visiting Card Section
            Components\Section::make('Visiting Card')
                ->schema([
                    Components\ImageEntry::make('visiting_card_url')
                        ->label('')
                        ->disk('public')
                        ->height(400)
                        ->width(300)
                        ->extraAttributes([
                            'class' => 'rounded-lg border border-gray-200 cursor-pointer hover:opacity-80',
                        ])
                        ->defaultImageUrl(url('https://via.placeholder.com/300x200?text=No+Visiting+Card')),
                ])
                ->visible(fn ($record) => !is_null($record->visiting_card_path))
                ->collapsible(),
            
            // Address Information Section
            Components\Section::make('Address Information')
                ->schema([
                    Components\Grid::make(3)
                        ->schema([
                            Components\TextEntry::make('city'),
                            Components\TextEntry::make('town'),
                            Components\TextEntry::make('village'),
                        ]),
                ]),
            
            // Business Information Section
            Components\Section::make('Business Information')
                ->schema([
                    Components\Grid::make(2)
                        ->schema([
                            Components\TextEntry::make('organization')
                                ->label('Organization Name'),
                            Components\TextEntry::make('website')
                                ->label('Website')
                                ->url(fn ($state) => $state)
                                ->openUrlInNewTab(),
                            Components\TextEntry::make('organization_type_display')
                                ->label('Organization Type'),
                            Components\TextEntry::make('business_category_display')
                                ->label('Business Category'),
                            Components\TextEntry::make('date_of_incorporation')
                                ->label('Date of Incorporation')
                                ->date('d M Y'),
                            Components\TextEntry::make('pan_number')
                                ->label('PAN Number'),
                            Components\TextEntry::make('gst_number')
                                ->label('GST Number'),
                        ]),
                    Components\TextEntry::make('about_service')
                        ->label('About Service')
                        ->columnSpanFull(),
                ]),
            
            // Membership References Section
            Components\Section::make('Membership References')
                ->schema([
                    Components\Grid::make(2)
                        ->schema([
                            Components\TextEntry::make('membership_reference_1'),
                            Components\TextEntry::make('membership_reference_2'),
                        ]),
                ]),
            
            // Registration Details Section
            Components\Section::make('Registration Details')
                ->schema([
                    Components\Grid::make(3)
                        ->schema([
                            Components\TextEntry::make('registration_type_display')
                                ->label('Registration Type')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'Student' => 'success',
                                    'Installer' => 'info',
                                    'EPC Classic' => 'primary',
                                    default => 'gray',
                                }),
                            Components\TextEntry::make('registration_amount')
                                ->label('Amount')
                                ->money('INR'),
                            Components\IconEntry::make('declaration')
                                ->label('Declaration Accepted')
                                ->boolean(),
                        ]),
                ]),
            
            // Payment Details Section
            Components\Section::make('Payment Details')
                ->schema([
                    Components\Grid::make(2)
                        ->schema([
                            Components\TextEntry::make('payment_mode_display')
                                ->label('Payment Mode'),
                            Components\TextEntry::make('transaction_reference')
                                ->label('Transaction Reference')
                                ->copyable(),
                            Components\IconEntry::make('payment_verified')
                                ->label('Payment Status')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-badge')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),
                            Components\TextEntry::make('payment_verified_at')
                                ->label('Verified At')
                                ->dateTime('d M Y H:i:s')
                                ->visible(fn ($record) => $record->payment_verified),
                            Components\TextEntry::make('payment_remarks')
                                ->label('Remarks')
                                ->visible(fn ($state) => !empty($state)),
                        ]),
                    
                    // Payment Screenshot
                   // Payment Screenshot - Fixed Version
        Components\Section::make('Payment Screenshot')
            ->schema([
                
                
                // Thumbnail preview
                Components\ImageEntry::make('payment_screenshot_url')
                    ->label('')
                    ->disk('public')
                    ->height(600)
                    ->width(500)
                    ->extraAttributes([
                        'class' => 'rounded-lg border border-gray-200 cursor-pointer hover:opacity-80 mx-auto',
                    ])
                    ->defaultImageUrl(url('https://via.placeholder.com/400x300?text=No+Screenshot')),
            ])
            ->visible(fn ($record) => !is_null($record->payment_screenshot_path))
            ->collapsible(),
    ]),
            
            // Credentials Status Section
            Components\Section::make('Credentials Status')
                ->schema([
                    Components\Grid::make(3)
                        ->schema([
                            Components\IconEntry::make('credentials_sent')
                                ->label('Credentials Sent')
                                ->boolean(),
                            Components\TextEntry::make('credentials_sent_at')
                                ->label('Sent At')
                                ->dateTime('d M Y H:i:s'),
                            Components\TextEntry::make('generated_password')
                                ->label('Password')
                                ->formatStateUsing(fn ($state) => $state ? '********' : 'Not set')
                                ->copyable(false),
                        ]),
                ]),
        ]);
}                               

    protected function getHeaderActions(): array
{
    return [
        Actions\EditAction::make(),
        Actions\DeleteAction::make(),
        
        // View Form in New Tab
        Actions\Action::make('view_form')
            ->label('View Registration Form')
            ->icon('heroicon-o-document-text')
            ->color('success')
            ->url(fn ($record) => route('registration.view-form', $record))
            ->openUrlInNewTab(),
        
        Actions\Action::make('back')
            ->label('Back to List')
            ->url(route('filament.admin.resources.registrations.index'))
            ->color('gray'),
    ];
}
}