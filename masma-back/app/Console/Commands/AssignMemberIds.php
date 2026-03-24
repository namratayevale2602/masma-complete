<?php

namespace App\Console\Commands;

use App\Models\Registration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignMemberIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-member-ids
                            {--dry-run : Simulate the assignment without actually saving}
                            {--force : Force assignment even if member_id already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign unique member IDs to existing registrations and link renewals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting member ID assignment process...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('DRY RUN MODE: No changes will be saved to the database');
            $this->newLine();
        }

        // Get all registrations ordered by creation date (oldest first)
        $registrations = Registration::orderBy('created_at', 'asc')->get();
        
        $total = $registrations->count();
        $this->info("Found {$total} registrations to process");
        $this->newLine();

        $stats = [
            'new_members' => 0,
            'renewals_linked' => 0,
            'skipped' => 0,
            'errors' => 0
        ];

        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        foreach ($registrations as $registration) {
            try {
                // Skip if already has member_id and not forcing
                if ($registration->member_id && !$force) {
                    $stats['skipped']++;
                    $progressBar->advance();
                    continue;
                }

                $result = $this->processRegistration($registration, $dryRun);
                
                if ($result['type'] === 'new') {
                    $stats['new_members']++;
                } elseif ($result['type'] === 'renewal') {
                    $stats['renewals_linked']++;
                }

            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("\nError processing registration ID {$registration->id}: " . $e->getMessage());
                Log::error('Member ID assignment error', [
                    'registration_id' => $registration->id,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display statistics
        $this->table(
            ['Metric', 'Count'],
            [
                ['New Members Assigned', $stats['new_members']],
                ['Renewals Linked', $stats['renewals_linked']],
                ['Skipped (Already Assigned)', $stats['skipped']],
                ['Errors', $stats['errors']],
                ['Total Processed', $total],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('DRY RUN COMPLETED: No changes were saved.');
        } else {
            $this->newLine();
            $this->info('Member ID assignment completed successfully!');
        }

        return $stats['errors'] === 0 ? 0 : 1;
    }

    /**
     * Process a single registration
     */
    private function processRegistration($registration, $dryRun)
    {
        // Check if this is a renewal type
        $isRenewalType = $this->isRenewalType($registration->registration_type);
        
        if ($isRenewalType) {
            // Try to find existing member by email or mobile
            $existingMember = $this->findExistingMember($registration);
            
            if ($existingMember && $existingMember->member_id) {
                // Link to existing member
                $memberId = $existingMember->member_id;
                $parentMemberId = $existingMember->member_id;
                $type = 'renewal';
                
                $this->line("\n[Renewal] Registration #{$registration->id}: Linked to member {$memberId}");
                
                if (!$dryRun) {
                    DB::transaction(function() use ($registration, $memberId, $parentMemberId) {
                        $registration->member_id = $memberId;
                        $registration->parent_member_id = $parentMemberId;
                        $registration->save();
                    });
                }
            } else {
                // No existing member found for renewal - treat as new
                $memberId = $this->generateMemberId();
                $type = 'new';
                
                $this->line("\n[Warning] Registration #{$registration->id}: Marked as renewal but no existing member found. Created as new member with ID: {$memberId}");
                
                if (!$dryRun) {
                    DB::transaction(function() use ($registration, $memberId) {
                        $registration->member_id = $memberId;
                        $registration->save();
                    });
                }
            }
        } else {
            // New member - generate new ID
            $memberId = $this->generateMemberId();
            $type = 'new';
            
            $this->line("\n[New] Registration #{$registration->id}: Assigned member ID: {$memberId}");
            
            if (!$dryRun) {
                DB::transaction(function() use ($registration, $memberId) {
                    $registration->member_id = $memberId;
                    $registration->save();
                });
            }
        }

        return [
            'type' => $type,
            'member_id' => $memberId ?? null
        ];
    }

    /**
     * Generate a unique member ID
     * Format: MASMA-YYYY-XXXXX
     */
    private function generateMemberId(): string
    {
        $year = date('Y');
        $prefix = "MASMA-{$year}-";
        
        // Get the last member ID for this year from database
        $lastMember = Registration::where('member_id', 'like', $prefix . '%')
            ->orderBy('member_id', 'desc')
            ->first();
        
        if ($lastMember && $lastMember->member_id) {
            // Extract the numeric part
            $lastNumber = (int) substr($lastMember->member_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Pad with zeros to 5 digits
        $paddedNumber = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        
        return $prefix . $paddedNumber;
    }

    /**
     * Check if registration type is a renewal
     */
    private function isRenewalType($registrationType): bool
    {
        $renewalTypes = [
            'renew_epc_classic',
            'renew_student',
            'renew_dealer_distributor',
            'renew_silver_corporate',
            'renew_gold_corporate'
        ];
        
        return in_array($registrationType, $renewalTypes);
    }

    /**
     * Find existing member by email or mobile
     */
    private function findExistingMember($registration)
    {
        // First try to find by email
        $member = Registration::where('office_email', $registration->office_email)
            ->whereNotNull('member_id')
            ->orderBy('created_at', 'asc')
            ->first();
        
        if ($member) {
            return $member;
        }
        
        // Then try by mobile
        $member = Registration::where('mobile', $registration->mobile)
            ->whereNotNull('member_id')
            ->orderBy('created_at', 'asc')
            ->first();
        
        return $member;
    }
}

