<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorNotification;

class CheckVisitorDeadlines extends Command
{
    protected $signature = 'visitors:check-deadlines';
    protected $description = 'Check and auto-decline visitor requests past their deadline (H-2 12:00)';

    public function handle()
    {
        $this->info('Checking visitor request deadlines...');

        // Get all pending requests (For Review or Approved 1/2)
        $visitors = DB::table('visitors')
            ->whereIn('status', ['For Review', 'Approved (1/2)'])
            ->get();

        $declinedCount = 0;

        foreach ($visitors as $visitor) {
            $visitStartDate = Carbon::parse($visitor->startdate);
            $deadlineDate = $visitStartDate->copy()->subDays(2)->setTime(12, 0, 0);
            $now = Carbon::now();

            // Check if current time is past the deadline (H-2 12:00)
            if ($now->greaterThan($deadlineDate)) {
                // Get department info
                $dept = DB::table('depts')
                    ->where('deptID', $visitor->deptpurpose)
                    ->first();

                // Update status to Declined
                DB::table('visitors')
                    ->where('id', $visitor->id)
                    ->update([
                        'status' => 'Declined',
                        'approved_date' => null
                    ]);

                // Send decline notification
                try {
                    Mail::to($visitor->email)->send(new VisitorNotification([
                        'name' => $visitor->fullname,
                        'company' => $visitor->company,
                        'visit_purpose' => $visitor->visit_purpose,
                        'startdate' => $visitor->startdate,
                        'enddate' => $visitor->enddate,
                        'department' => $dept ? $dept->nameDept : 'Unknown Department',
                        'status' => 'Declined',
                        'message' => 'Unfortunately, your visit request has been automatically declined as it was not processed within the required timeframe (H-2 12:00). Please submit a new request if you still wish to visit.',
                        'auto_declined' => true,
                        'deadline_date' => $deadlineDate->format('d-m-Y H:i'),
                        'to' => $visitor->email
                    ]));

                    $this->line("Auto-declined visitor: {$visitor->fullname} (ID: {$visitor->id})");
                    
                } catch (\Exception $e) {
                    \Log::error('Failed to send auto-decline notification', [
                        'error' => $e->getMessage(),
                        'visitor_id' => $visitor->id,
                        'visitor_email' => $visitor->email
                    ]);
                    
                    $this->error("Failed to send email to {$visitor->email}: {$e->getMessage()}");
                }

                $declinedCount++;
            }
        }

        if ($declinedCount > 0) {
            $this->info("Completed. Auto-declined {$declinedCount} overdue requests.");
        } else {
            $this->info("No overdue requests found.");
        }

        return 0;
    }
} 