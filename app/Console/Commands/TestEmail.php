<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorNotification;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email functionality for visitor notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing email functionality...');

        $testVisitorData = [
            'full_name' => 'Test Visitor',
            'nik' => '1234567890123456',
            'company' => 'Test Company',
            'phone' => '081234567890',
            'department_purpose' => 'Dept A',
            'section_purpose' => 'Test Section',
            'visit_datetime' => now()->format('Y-m-d H:i:s'),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        try {
            Mail::to('siregarfresnel@gmail.com')->send(new VisitorNotification($testVisitorData));
            $this->info('Email sent successfully!');
            $this->info('Check siregarfresnel@gmail.com for the test email.');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            $this->error('Please check your email configuration in .env file.');
        }
    }
} 