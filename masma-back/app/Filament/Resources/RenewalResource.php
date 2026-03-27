<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RenewalResource\Pages;
use App\Models\Registration;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class RenewalResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Renewal Management';
    protected static ?string $navigationGroup = 'Members';
    protected static ?string $modelLabel = 'Renewal';
    protected static ?string $pluralModelLabel = 'Renewals';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('payment_verified', true)
            ->whereNotNull('member_id');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant_name')
                    ->label('Member Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('member_id')
                    ->label('Member ID')
                    ->searchable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('mobile')
                    ->label('Mobile')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('office_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('registration_type')
                    ->label('Plan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'epc_classic' => 'EPC Classic',
                        'student' => 'Student',
                        'dealer_distributor' => 'Dealer/Distributor',
                        'silver_corporate' => 'Silver Corporate',
                        'gold_corporate' => 'Gold Corporate',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Expiry Date')
                    ->getStateUsing(fn ($record) => $record->expiry_date->format('d M Y'))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('created_at', $direction);
                    })
                    ->color(fn ($record) => static::getExpiryColor($record)),
                    
                Tables\Columns\TextColumn::make('days_left')
                    ->label('Days Left')
                    ->getStateUsing(fn ($record) => $record->days_left)
                    ->badge()
                    ->color(fn ($state) => $state <= 0 ? 'danger' : ($state <= 8 ? 'danger' : ($state <= 30 ? 'warning' : 'success'))),
                    
                Tables\Columns\IconColumn::make('has_mobile')
                    ->label('WhatsApp')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record) => !empty($record->mobile)),
            ])
            ->defaultSort('created_at', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('expiry_status')
                    ->label('Membership Status')
                    ->options([
                        'expiring_30' => 'Expiring in 30 Days',
                        'expiring_15' => 'Expiring in 15 Days',
                        'expiring_8' => 'Expiring in 8 Days',
                        'expiring_7' => 'Expiring in 7 Days',
                        'expiring_5' => 'Expiring in 5 Days',
                        'expiring_3' => 'Expiring in 3 Days',
                        'expiring_1' => 'Expiring Tomorrow',
                        'expired' => 'Expired',
                        'active' => 'Active (>30 days)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) return $query;
                        
                        switch ($data['value']) {
                            case 'expiring_30':
                                $expiryDate = now()->addDays(30)->startOfDay();
                                return $query->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
                            case 'expiring_15':
                                $expiryDate = now()->addDays(15)->startOfDay();
                                return $query->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
                            case 'expiring_8':
                                $expiryDate = now()->addDays(8)->startOfDay();
                                return $query->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
                            case 'expiring_7':
                                $expiryDate = now()->addDays(7)->startOfDay();
                                return $query->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
                            case 'expiring_5':
                                $expiryDate = now()->addDays(5)->startOfDay();
                                return $query->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
                            case 'expiring_3':
                                $expiryDate = now()->addDays(3)->startOfDay();
                                return $query->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
                            case 'expiring_1':
                                $expiryDate = now()->addDays(1)->startOfDay();
                                return $query->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
                            case 'expired':
                                return $query->whereDate('created_at', '<=', now()->subYear());
                            case 'active':
                                return $query->whereDate('created_at', '>', now()->subYear()->addDays(30));
                        }
                        return $query;
                    }),
                    
                Tables\Filters\Filter::make('has_whatsapp')
                    ->label('Has WhatsApp Number')
                    ->query(fn ($query) => $query->whereNotNull('mobile')),
            ])
            ->actions([
                // 30 Days Reminder
                Action::make('send_30_day')
                    ->label('30 Day Reminder')
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Send 30-Day Renewal Reminder')
                    ->modalDescription(fn ($record) => "Send 30-day renewal reminder template to {$record->applicant_name}?")
                    ->action(function ($record) {
                        static::sendTemplateReminder($record, '30_days');
                    })
                    ->visible(fn ($record) => $record->mobile && $record->days_left <= 30 && $record->days_left > 15),
                
                // 15 Days Reminder
                Action::make('send_15_day')
                    ->label('15 Day Reminder')
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Send 15-Day Renewal Reminder')
                    ->modalDescription(fn ($record) => "Send 15-day renewal reminder template to {$record->applicant_name}?")
                    ->action(function ($record) {
                        static::sendTemplateReminder($record, '15_days');
                    })
                    ->visible(fn ($record) => $record->mobile && $record->days_left <= 15 && $record->days_left > 8),
                    
                // 8 Days Reminder (Urgent)
                Action::make('send_8_day')
                    ->label('8 Day Reminder')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Send 8-Day Renewal Reminder')
                    ->modalDescription(fn ($record) => "Send 8-day renewal reminder template to {$record->applicant_name}?")
                    ->action(function ($record) {
                        static::sendTemplateReminder($record, '8_days');
                    })
                    ->visible(fn ($record) => $record->mobile && $record->days_left <= 8 && $record->days_left > 5),
                
                // 5 Days Reminder (Very Urgent)
                Action::make('send_5_day')
                    ->label('5 Day Reminder')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Send 5-Day Renewal Reminder')
                    ->modalDescription(fn ($record) => "Send 5-day renewal reminder template to {$record->applicant_name}?")
                    ->action(function ($record) {
                        static::sendTemplateReminder($record, '5_days');
                    })
                    ->visible(fn ($record) => $record->mobile && $record->days_left <= 5 && $record->days_left > 3),
                
                // 3 Days Reminder (Critical)
                Action::make('send_3_day')
                    ->label('3 Day Reminder')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Send 3-Day Renewal Reminder')
                    ->modalDescription(fn ($record) => "Send 3-day renewal reminder template to {$record->applicant_name}?")
                    ->action(function ($record) {
                        static::sendTemplateReminder($record, '3_days');
                    })
                    ->visible(fn ($record) => $record->mobile && $record->days_left <= 3 && $record->days_left > 1),
                
                // Tomorrow Reminder
                Action::make('send_tomorrow')
                    ->label('Tomorrow Reminder')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Send Tomorrow Renewal Reminder')
                    ->modalDescription(fn ($record) => "Send tomorrow renewal reminder template to {$record->applicant_name}?")
                    ->action(function ($record) {
                        static::sendTemplateReminder($record, 'tomorrow');
                    })
                    ->visible(fn ($record) => $record->mobile && $record->days_left <= 1 && $record->days_left > 0),
                
                // Expired Reminder
                Action::make('send_expired')
                    ->label('Expired Reminder')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Send Expired Membership Reminder')
                    ->modalDescription(fn ($record) => "Send expired membership reminder template to {$record->applicant_name}?")
                    ->action(function ($record) {
                        static::sendTemplateReminder($record, 'expired');
                    })
                    ->visible(fn ($record) => $record->mobile && $record->days_left <= 0),
                    
                
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('send_30_day_bulk')
                    ->label('Send 30-Day Reminders')
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Send 30-day renewal reminder templates to selected members?')
                    ->action(function ($records) {
                        static::sendBulkTemplates($records, '30_days');
                    }),
                    
                Tables\Actions\BulkAction::make('send_8_day_bulk')
                    ->label('Send 8-Day Reminders')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Send 8-day renewal reminder templates to selected members?')
                    ->action(function ($records) {
                        static::sendBulkTemplates($records, '8_days');
                    }),
                    
                
            ]);
    }

    protected static function getExpiryColor($record): string
    {
        $daysLeft = $record->days_left;
        if ($daysLeft <= 8) return 'danger';
        if ($daysLeft <= 30) return 'warning';
        return 'success';
    }

    protected static function sendTemplateReminder($member, $type)
    {
        try {
            $whatsappService = app(WhatsAppService::class);
            
            if (!$member->mobile) {
                Notification::make()
                    ->title('Cannot Send')
                    ->body('Member has no mobile number')
                    ->danger()
                    ->send();
                return;
            }
            
            $templateName = static::getTemplateName($type);
            $parameters = static::getTemplateParameters($member, $type);
            
            $result = $whatsappService->sendTemplateMessage(
                $member->mobile,
                $templateName,
                $parameters
            );
            
            if ($result['success']) {
                Notification::make()
                    ->title('Template Sent')
                    ->body("Renewal reminder sent to {$member->applicant_name}")
                    ->success()
                    ->send();
                static::logMessage($member, $templateName, 'sent');
            } else {
                throw new \Exception($result['error'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to Send')
                ->body($e->getMessage())
                ->danger()
                ->send();
            static::logMessage($member, $templateName ?? '', 'failed', $e->getMessage());
        }
    }

    protected static function sendBulkTemplates($members, $type)
    {
        $sent = 0;
        $failed = 0;
        $templateName = static::getTemplateName($type);
        
        foreach ($members as $member) {
            if (!$member->mobile) {
                $failed++;
                continue;
            }
            
            try {
                $whatsappService = app(WhatsAppService::class);
                $parameters = static::getTemplateParameters($member, $type);
                $result = $whatsappService->sendTemplateMessage(
                    $member->mobile,
                    $templateName,
                    $parameters
                );
                
                if ($result['success']) {
                    $sent++;
                    static::logMessage($member, $templateName, 'sent');
                } else {
                    $failed++;
                    static::logMessage($member, $templateName, 'failed', $result['error']);
                }
                usleep(500000);
            } catch (\Exception $e) {
                $failed++;
                static::logMessage($member, $templateName, 'failed', $e->getMessage());
            }
        }
        
        Notification::make()
            ->title('Bulk Send Complete')
            ->body("Sent: {$sent}, Failed: {$failed}")
            ->success()
            ->send();
    }

    /**
     * Get template name from your WABA panel
     * Update these to match your approved template names
     */
    protected static function getTemplateName($type)
    {
        $templates = [
            '30_days' => env('WHATSAPP_TEMPLATE_30_DAYS', 'membership_renewal_30_days'),
            '15_days' => env('WHATSAPP_TEMPLATE_15_DAYS', 'membership_renewal_15_days'),
            '8_days' => env('WHATSAPP_TEMPLATE_8_DAYS', 'membership_renewal_8_days'),
            '5_days' => env('WHATSAPP_TEMPLATE_5_DAYS', 'membership_renewal_5_days'),
            '3_days' => env('WHATSAPP_TEMPLATE_3_DAYS', 'membership_renewal_3_days'),
            'tomorrow' => env('WHATSAPP_TEMPLATE_TOMORROW', 'membership_renewal_tomorrow'),
            'expired' => env('WHATSAPP_TEMPLATE_EXPIRED', 'membership_expired'),
        ];
        
        return $templates[$type];
    }

    /**
     * Get template parameters based on your WABA template structure
     * Adjust this based on your template placeholders
     */
    protected static function getTemplateParameters($member, $type)
    {
        $memberName = strtoupper($member->applicant_name);
        $memberId = $member->member_id ?? $member->parent_member_id;
        $expiryDate = $member->expiry_date->format('d F Y');
        $daysLeft = $member->days_left;
        
        // Base parameters
        $parameters = [
            'body' => [
                $memberName,      // {{1}}
                $memberId,        // {{2}}
                $expiryDate,      // {{3}}
            ]
        ];
        
        // Add days left for reminders (optional, depends on your template)
        if (in_array($type, ['8_days', '5_days', '3_days', 'tomorrow'])) {
            $parameters['body'][] = $daysLeft;
        }
        
        return $parameters;
    }

    protected static function logMessage($member, $templateName, $status, $error = null)
    {
        \Illuminate\Support\Facades\Log::channel('renewal_reminders')->info('WhatsApp template', [
            'member_id' => $member->id,
            'member_name' => $member->applicant_name,
            'mobile' => $member->mobile,
            'template' => $templateName,
            'status' => $status,
            'error' => $error,
            'timestamp' => now()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRenewals::route('/'),
        ];
    }
}