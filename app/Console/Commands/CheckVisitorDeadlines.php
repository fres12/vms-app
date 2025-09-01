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
    protected $description = 'Check and auto-decline visitor requests past their deadline';

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

            if ($now->greaterThan($deadlineDate)) {
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
                        'department' => DB::table('depts')->where('deptID', $visitor->deptpurpose)->value('nameDept'),
                        'status' => 'Declined',
                        'message' => 'Your visit request has been automatically declined as it passed the deadline (H-2 12:00).'
                    ]));
                } catch (\Exception $e) {
                    \Log::error('Failed to send auto-decline notification', [
                        'error' => $e->getMessage(),
                        'visitor_id' => $visitor->id
                    ]);
                }

                $declinedCount++;
            }
        }

        $this->info("Completed. Auto-declined {$declinedCount} overdue requests.");
    }
} 