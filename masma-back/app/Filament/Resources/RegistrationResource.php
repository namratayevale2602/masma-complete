<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use App\Mail\UserCredentials;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Members';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('applicant_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->required(),
                        Forms\Components\FileUpload::make('applicant_photo_path')
                            ->label('Applicant Photo')
                            ->image()
                            ->directory('applicant-photos'),
                    ])->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('mobile')
                            ->required()
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->nullable()
                            ->default(null),
                        Forms\Components\TextInput::make('whatsapp_no')
                            ->tel()
                            ->maxLength(20)
                            ->nullable()
                            ->default(null),
                        Forms\Components\TextInput::make('office_email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Address Information')
                    ->schema([
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100)
                            ->nullable()
                            ->default(null),
                        Forms\Components\TextInput::make('town')
                            ->maxLength(100)
                            ->nullable()
                            ->default(null),
                        Forms\Components\TextInput::make('village')
                            ->maxLength(100)
                            ->nullable()
                            ->default(null),
                    ])->columns(3),

                Forms\Components\Section::make('Business Information')
                    ->schema([
                        Forms\Components\TextInput::make('organization')
                            ->maxLength(255)
                            ->nullable()
                            ->default(null),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255)
                            ->nullable()
                            ->default(null),
                        Forms\Components\Select::make('organization_type')
                            ->options([
                                'sole_proprietorship' => 'Sole Proprietorship',
                                'partnership' => 'Partnership',
                                'limited_liability_partnership' => 'Limited Liability Partnership (LLP)',
                                'private_limited_company' => 'Private Limited Company',
                                'public_limited_company' => 'Public Limited Company',
                                'one_person_company' => 'One Person Company (OPC)',
                                'other' => 'Other',
                            ])
                            ->nullable()
                            ->default(null),
                        Forms\Components\Select::make('business_category')
                            ->options([
                                'student' => 'Student',
                                'plumber' => 'Plumber',
                                'electrician' => 'Electrician',
                                'installer_solar_pv' => 'Installer Solar PV',
                                'solar_water_heater' => 'Solar Water Heater',
                                'supplier' => 'Supplier',
                                'dealer' => 'Dealer',
                                'distributor' => 'Distributor',
                                'associate_member' => 'Associate Member',
                                'manufacturer' => 'Manufacturer',
                            ])
                            ->nullable()
                            ->default(null),
                        Forms\Components\DatePicker::make('date_of_incorporation')
                            ->nullable(),
                        Forms\Components\TextInput::make('pan_number')
                            ->maxLength(20)
                            ->nullable()
                            ->default(null),
                        Forms\Components\TextInput::make('gst_number')
                            ->maxLength(20)
                            ->nullable()
                            ->default(null),
                        Forms\Components\Textarea::make('about_service')
                            ->rows(3)
                            ->nullable()
                            ->default(null),
                    ])->columns(2),

                Forms\Components\Section::make('Membership References')
                    ->schema([
                        Forms\Components\TextInput::make('membership_reference_1')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('membership_reference_2')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Registration Details')
                    ->schema([
                        Forms\Components\Select::make('registration_type')
                            ->required()
                            ->options([
                                'renew_epc_classic' => 'Renew EPC Classic',
                                'student' => 'Student',
                                'installer' => 'Installer',
                                'epc_classic' => 'EPC Classic',
                                'epc_lifetime' => 'EPC Lifetime',
                                'dealer_distributor' => 'Dealer/Distributor',
                                'silver_corporate' => 'Silver Corporate',
                                'gold_corporate' => 'Gold Corporate',
                                'masma_associates' => 'MASMA Associates',
                            ]),
                        Forms\Components\TextInput::make('registration_amount')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->default(0),
                        Forms\Components\Toggle::make('declaration')
                            ->required()
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Payment & Verification Status')
                    ->schema([
                        Forms\Components\Placeholder::make('payment_status')
                            ->label('Payment Status')
                            ->content(function (Registration $record) {
                                return $record->payment_verified 
                                    ? "✅ Payment Verified" 
                                    : "⏳ Payment Pending Verification";
                            }),
                        
                        Forms\Components\Placeholder::make('credentials_status')
                            ->label('Credentials Status')
                            ->content(function (Registration $record) {
                                if ($record->credentials_sent && $record->credentials_sent_at) {
                                    return "✅ Credentials sent on " . $record->credentials_sent_at->format('d-m-Y H:i:s');
                                } elseif ($record->payment_verified) {
                                    return "🔄 Credentials pending - Use table action to send";
                                } else {
                                    return "⏳ Waiting for payment verification";
                                }
                            }),
                        
                        Forms\Components\TextInput::make('generated_password_display')
                            ->label('Password Status')
                            ->disabled()
                            ->visible(fn (Registration $record) => $record->credentials_sent)
                            ->helperText('Password has been set and sent to user.')
                            ->default('********'),
                        
                        Forms\Components\DateTimePicker::make('credentials_sent_at')
                            ->label('Credentials Sent At')
                            ->disabled()
                            ->visible(fn (Registration $record) => $record->credentials_sent)
                            ->nullable(),
                    ])
                    ->extraAttributes(['class' => 'bg-gray-50 p-4 rounded-lg'])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: 'N/A'),
                Tables\Columns\TextColumn::make('office_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'student' => 'success',
                        'installer' => 'info',
                        'epc_classic' => 'primary',
                        'epc_lifetime' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ?: 'Not Set'),
                Tables\Columns\ImageColumn::make('payment_screenshot_path')
    ->label('Payment Screenshot')
    ->disk('public')
    ->height(50)
    ->width(50)
    ->square()
    ->toggleable(),
    
                Tables\Columns\TextColumn::make('registration_amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('payment_verified')
                    ->label('Payment')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (Registration $record): string => 
                        $record->payment_verified ? 'Payment Verified' : 'Payment Pending'
                    ),
                Tables\Columns\IconColumn::make('credentials_sent')
                    ->label('Credentials')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (Registration $record): string => 
                        $record->credentials_sent && $record->credentials_sent_at
                            ? 'Credentials sent on ' . $record->credentials_sent_at->format('d-m-Y H:i:s')
                            : ($record->payment_verified ? 'Ready to send' : 'Waiting for payment verification')
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Submitted'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('registration_type')
                    ->options([
                        'renew_epc_classic' => 'Renew EPC Classic',
                        'student' => 'Student',
                        'installer' => 'Installer',
                        'epc_classic' => 'EPC Classic',
                        'epc_lifetime' => 'EPC Lifetime',
                        'dealer_distributor' => 'Dealer/Distributor',
                        'silver_corporate' => 'Silver Corporate',
                        'gold_corporate' => 'Gold Corporate',
                        'masma_associates' => 'MASMA Associates',
                    ]),
                Tables\Filters\SelectFilter::make('business_category')
                    ->options([
                        'student' => 'Student',
                        'plumber' => 'Plumber',
                        'electrician' => 'Electrician',
                        'installer_solar_pv' => 'Installer Solar PV',
                        'solar_water_heater' => 'Solar Water Heater',
                        'supplier' => 'Supplier',
                        'dealer' => 'Dealer',
                        'distributor' => 'Distributor',
                        'associate_member' => 'Associate Member',
                        'manufacturer' => 'Manufacturer',
                    ]),
                Tables\Filters\Filter::make('payment_verified')
                    ->label('Payment Verified')
                    ->query(fn ($query) => $query->where('payment_verified', true)),
                Tables\Filters\Filter::make('credentials_sent')
                    ->label('Credentials Sent')
                    ->query(fn ($query) => $query->where('credentials_sent', true)),
                Tables\Filters\Filter::make('payment_pending')
                    ->label('Payment Pending')
                    ->query(fn ($query) => $query->where('payment_verified', false)),
                Tables\Filters\Filter::make('credentials_pending')
                    ->label('Credentials Pending')
                    ->query(fn ($query) => $query->where('payment_verified', true)->where('credentials_sent', false)),
            ])
            ->actions([
                // Toggle Payment Status directly in table
                Tables\Actions\Action::make('togglePayment')
                    ->label(fn (Registration $record) => $record->payment_verified ? 'Unverify Payment' : 'Verify Payment')
                    ->icon(fn (Registration $record) => $record->payment_verified ? 'heroicon-o-x-circle' : 'heroicon-o-check-badge')
                    ->color(fn (Registration $record) => $record->payment_verified ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Registration $record) => $record->payment_verified ? 'Unverify Payment?' : 'Verify Payment?')
                    ->modalSubheading(function (Registration $record) {
                        if ($record->payment_verified) {
                            return 'Are you sure you want to unverify payment for ' . $record->applicant_name . '?';
                        } else {
                            return 'Are you sure you want to verify payment for ' . $record->applicant_name . '? This will NOT send credentials automatically.';
                        }
                    })
                    ->action(function (Registration $record) {
                        $newStatus = !$record->payment_verified;
                        
                        if ($newStatus) {
                            // ONLY verify payment, DON'T send credentials
                            $record->update([
                                'payment_verified' => true,
                            ]);
                            
                            Notification::make()
                                ->title('Payment Verified!')
                                ->body('Payment verified for ' . $record->applicant_name . '. Use "Send Credentials" action to send login details.')
                                ->success()
                                ->send();
                                
                        } else {
                            // If unverifying payment
                            $record->update([
                                'payment_verified' => false,
                            ]);
                            
                            Notification::make()
                                ->title('Payment Unverified')
                                ->body('Payment status has been reset for ' . $record->applicant_name)
                                ->warning()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                
                // Send credentials (only show if payment is verified but credentials not sent)
                Tables\Actions\Action::make('sendCredentials')
                    ->label('Send Credentials')
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Send Login Credentials')
                    ->modalSubheading('Are you sure you want to send login credentials? This will generate a new password.')
                    ->action(function (Registration $record) {
                        try {
                            // Generate plain password
                            $plainPassword = self::generateSecurePassword();
                            
                            // Hash it for storage
                            $hashedPassword = Hash::make($plainPassword);
                            
                            // Send email with PLAINTEXT password
                            Mail::to($record->office_email)
                                ->send(new UserCredentials($record, $plainPassword));
                            
                            // Update record with HASHED password
                            $record->update([
                                'generated_password' => $hashedPassword, // Store HASHED
                                'credentials_sent' => true,
                                'credentials_sent_at' => now(),
                            ]);
                            
                            \Log::info('Credentials sent via Filament - Email: ' . $record->office_email);
                            \Log::info('Plain password: ' . $plainPassword);
                            \Log::info('Hashed password stored: ' . $hashedPassword);
                            
                            Notification::make()
                                ->title('Credentials Sent Successfully!')
                                ->body('Login credentials have been sent to ' . $record->office_email)
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Log::error('Failed to send credentials via Filament: ' . $e->getMessage());
                            Notification::make()
                                ->title('Failed to Send Credentials')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Registration $record) => $record->payment_verified && !$record->credentials_sent),
                
                // Resend credentials (only show if credentials already sent)
                Tables\Actions\Action::make('resendCredentials')
                    ->label('Resend Credentials')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Resend Login Credentials')
                    ->modalSubheading('Are you sure you want to resend login credentials? A new password will be generated.')
                    ->action(function (Registration $record) {
                        try {
                            // Generate new plain password
                            $plainPassword = self::generateSecurePassword();
                            
                            // Hash it for storage
                            $hashedPassword = Hash::make($plainPassword);
                            
                            // Send email with PLAINTEXT password
                            Mail::to($record->office_email)
                                ->send(new UserCredentials($record, $plainPassword, true));
                            
                            // Update record with HASHED password
                            $record->update([
                                'generated_password' => $hashedPassword, // Store HASHED
                                'credentials_sent_at' => now(),
                            ]);
                            
                            \Log::info('Credentials resent via Filament - Email: ' . $record->office_email);
                            \Log::info('New plain password: ' . $plainPassword);
                            \Log::info('New hashed password stored: ' . $hashedPassword);
                            
                            Notification::make()
                                ->title('Credentials Resent Successfully!')
                                ->body('New login credentials have been sent to ' . $record->office_email)
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Log::error('Failed to resend credentials via Filament: ' . $e->getMessage());
                            Notification::make()
                                ->title('Failed to Resend Credentials')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Registration $record) => $record->payment_verified && $record->credentials_sent),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * Generate secure password with all character types
     */
    public static function generateSecurePassword($length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $allCharacters = $uppercase . $lowercase . $numbers . $symbols;
        $password = '';
        
        // Ensure at least one character from each set
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Fill the rest with random characters
        for ($i = 4; $i < $length; $i++) {
            $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }
        
        // Shuffle the password to randomize character positions
        return str_shuffle($password);
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
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }
}