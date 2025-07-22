<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorNotification;
use Illuminate\Support\Facades\DB;

class SendVisitorEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $visitor;
    protected $newStatus;
    protected $deadlineDate;

    public function __construct($visitor, $newStatus, $deadlineDate = null)
    {
        $this->visitor = $visitor;
        $this->newStatus = $newStatus;
        $this->deadlineDate = $deadlineDate;
    }

    public function handle()
    {
        try {
            $dept = DB::table('depts')->where('deptID', $this->visitor->deptpurpose)->first();

            if ($this->newStatus === 'Approved (1/2)') {
                // Get master admin
                $masterAdmin = DB::table('accounts')
                    ->where('deptID', 1)
                    ->first();

                if ($masterAdmin) {
                    Mail::to($masterAdmin->email)->send(new VisitorNotification([
                        'recipient_name' => $masterAdmin->name,
                        'name' => $this->visitor->fullname,
                        'company' => $this->visitor->company,
                        'visit_purpose' => $this->visitor->visit_purpose,
                        'startdate' => $this->visitor->startdate,
                        'enddate' => $this->visitor->enddate,
                        'department' => $dept->nameDept,
                        'status' => 'Needs Final Approval',
                        'deadline' => $this->deadlineDate ? $this->deadlineDate->format('d-m-Y H:i') : null,
                        'message' => "This visitor has been approved by {$dept->nameDept} department and needs your final approval."
                    ]));
                }
            } elseif ($this->newStatus === 'Approved (2/2)') {
                Mail::to($this->visitor->email)->send(new VisitorNotification([
                    'name' => $this->visitor->fullname,
                    'company' => $this->visitor->company,
                    'visit_purpose' => $this->visitor->visit_purpose,
                    'startdate' => $this->visitor->startdate,
                    'enddate' => $this->visitor->enddate,
                    'department' => $dept->nameDept,
                    'status' => 'Approved',
                    'message' => 'Your visit request has been fully approved.'
                ]));
            } elseif ($this->newStatus === 'Declined') {
                Mail::to($this->visitor->email)->send(new VisitorNotification([
                    'name' => $this->visitor->fullname,
                    'company' => $this->visitor->company,
                    'visit_purpose' => $this->visitor->visit_purpose,
                    'startdate' => $this->visitor->startdate,
                    'enddate' => $this->visitor->enddate,
                    'department' => $dept->nameDept,
                    'status' => 'Declined',
                    'message' => 'Your visit request has been declined.'
                ]));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send email in queue', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'visitor_id' => $this->visitor->id,
                'new_status' => $this->newStatus
            ]);
        }
    }
} 