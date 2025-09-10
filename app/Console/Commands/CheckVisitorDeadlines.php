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
    protected $description = 'Check and auto-reject visitor requests past their deadline (H-2 day)';

    public function handle()
    {
        $this->info('Checking visitor request deadlines...');

        $declinedCount = 0;
        $today = Carbon::today();
        
        $this->info("Today's date: " . $today->format('d-m-Y'));

        // Calculate the date range to check: today, tomorrow, day after tomorrow
        // If today is 09, check start dates: 09, 10, 11
        $checkDates = [
            $today->copy()->startOfDay(),           // H (same day)
            $today->copy()->addDay()->startOfDay(), // H+1 
            $today->copy()->addDays(2)->startOfDay() // H+2
        ];

        $this->info("Checking start dates: " . implode(', ', array_map(function($date) {
            return $date->format('d-m-Y');
        }, $checkDates)));

        // Get pending requests with start dates in our range
        $visitors = DB::table('visitors')
            ->whereIn('status', ['For Review', 'Approved (1/2)'])
            ->where(function($query) use ($checkDates) {
                foreach ($checkDates as $date) {
                    $query->orWhereDate('startdate', $date->format('Y-m-d'));
                }
            })
            ->get();

        $this->info("Found " . $visitors->count() . " visitors to check");

        foreach ($visitors as $visitor) {
            try {
                $visitStartDate = Carbon::parse($visitor->startdate);
            } catch (\Throwable $e) {
                \Log::warning('Invalid startdate for visitor', ['id' => $visitor->id, 'startdate' => $visitor->startdate]);
                continue;
            }

            $this->info("Processing: {$visitor->fullname} - Start: " . $visitStartDate->format('d-m-Y') . " - Status: {$visitor->status}");

            // Get department info
            $dept = DB::table('depts')
                ->where('deptID', $visitor->deptpurpose)
                ->first();

            // Update status to Rejected
            DB::table('visitors')
                ->where('id', $visitor->id)
                ->update([
                    'status' => 'Rejected',
                    'approved_date' => null
                ]);

            // Send rejection notification
            try {
                Mail::to($visitor->email)->send(new VisitorNotification([
                    'name' => $visitor->fullname,
                    'company' => $visitor->company,
                    'visit_purpose' => $visitor->visit_purpose,
                    'startdate' => $visitor->startdate,
                    'enddate' => $visitor->enddate,
                    'department' => $dept ? $dept->nameDept : 'Unknown Department',
                    'status' => 'Rejected',
                    'message' => 'Unfortunately, your visit request has been rejected because it was not processed before the deadline.',
                    'rejection_reason' => 'Not processed before deadline (auto-rejected on ' . $today->format('d-m-Y') . ')',
                    'to' => $visitor->email
                ]));

                $this->line("✓ Auto-rejected: {$visitor->fullname} (ID: {$visitor->id})");
            } catch (\Exception $e) {
                \Log::error('Failed to send auto-reject notification', [
                    'error' => $e->getMessage(),
                    'visitor_id' => $visitor->id,
                    'visitor_email' => $visitor->email
                ]);
                $this->error("✗ Failed to send email to {$visitor->email}: {$e->getMessage()}");
            }

            $declinedCount++;
        }

        if ($declinedCount > 0) {
            $this->info("Completed. Auto-rejected {$declinedCount} requests.");
        } else {
            $this->info("No requests found for auto-rejection today.");
        }

        return 0;
    }
}