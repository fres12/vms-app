<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisitorNotification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VisitorExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VisitorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'nik' => 'required|string',
            'id_card_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'self_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'company' => 'nullable|string',
            'phone' => 'nullable|string',
            'deptpurpose' => 'required|exists:depts,deptID',
            'visit_purpose' => 'required|string',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'equipment_type' => 'nullable|string',
            'brand' => 'nullable|string',
            'pledge_agreement' => 'required|accepted'
        ]);

        try {
            DB::beginTransaction();

            // Handle ID Card Photo Upload
            $idCardPhotoPath = $request->file('id_card_photo')->store('id_cards', 'public');
            
            // Handle Self Photo Upload
            $selfPhotoPath = $request->file('self_photo')->store('self_photos', 'public');

            // Get department info
            $dept = DB::table('depts')
                ->where('deptID', $request->deptpurpose)
                ->first();

            if (!$dept) {
                throw new \Exception('Department not found');
            }

            // Insert into database
            $visitorId = DB::table('visitors')->insertGetId([
                'fullname' => $request->full_name,
                'email' => $request->email,
                'nik' => $request->nik,
                'idcardphoto' => $idCardPhotoPath,
                'selfphoto' => $selfPhotoPath,
                'company' => $request->company,
                'phone' => $request->phone,
                'deptpurpose' => $request->deptpurpose,
                'visit_purpose' => $request->visit_purpose,
                'startdate' => $request->startdate,
                'enddate' => $request->enddate,
                'equipment_type' => $request->equipment_type,
                'brand' => $request->brand,
                'status' => 'For Review',
                'submit_date' => now(),
                'approved_date' => null,
                'ticket_number' => null,
                'barcode' => null
            ]);

            // Get department admin
            $deptAdmin = DB::table('accounts')
                ->where('deptID', $request->deptpurpose)
                ->first();

            if (!$deptAdmin) {
                \Log::error('Department admin not found', [
                    'dept_id' => $request->deptpurpose
                ]);
                throw new \Exception('Department admin not found');
            }

            // Send email notification to department admin
            Mail::send(new VisitorNotification([
                'recipient_name' => $deptAdmin->name,
                'name' => $request->full_name,
                'company' => $request->company,
                'visit_purpose' => $request->visit_purpose,
                'startdate' => $request->startdate,
                'enddate' => $request->enddate,
                'department' => $dept->nameDept,
                'status' => 'For Review',
                'message' => "A new visitor has requested approval for your department. Please review this request.",
                'to' => $deptAdmin->email
            ]));

            DB::commit();

            \Log::info('Visitor registration successful', [
                'visitor_id' => $visitorId,
                'dept_admin_email' => $deptAdmin->email
            ]);

            return redirect()->back()->with('success', 'Your visitor registration has been submitted successfully. Please wait for approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in visitor registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while processing your registration. Please try again.']);
        }
    }

    public function index()
    {
        $admin = auth()->guard('admin')->user();
        
        // Base query
        $query = DB::table('visitors')
            ->select(
                'visitors.*',
                'depts.nameDept as department_name',
                'accounts.name as pic_name',
                'accounts.position as pic_position',
                'accounts.no_employee as pic_employee_id'
            )
            ->leftJoin('depts', 'visitors.deptpurpose', '=', 'depts.deptID')
            ->leftJoin('accounts', 'visitors.deptpurpose', '=', 'accounts.deptID')
            ->orderByDesc('visitors.submit_date');

        // If not master admin (deptID != 1), filter by target department
        if ($admin->deptID !== 1) {
            $query->where('visitors.deptpurpose', $admin->deptID);
        }

        $visitors = $query->get();

        // Get department info for header
        $deptInfo = DB::table('depts')
            ->where('deptID', $admin->deptID)
            ->first();

        return view('visitor-list', [
            'visitors' => $visitors,
            'isMasterAdmin' => $admin->deptID === 1,
            'deptInfo' => $deptInfo,
            'isDeptPurpose' => true // This will be true since we already filter by deptID above
        ]);
    }

    public function approve($id)
    {
        try {
            $visitor = DB::table('visitors')->where('id', $id)->first();
            
            if (!$visitor) {
                return view('visitor-response', [
                    'success' => false,
                    'message' => 'Visitor not found.',
                    'visitor' => null
                ]);
            }

            if ($visitor->status !== 'For review') {
                return view('visitor-response', [
                    'success' => false,
                    'message' => 'This visitor has already been processed.',
                    'visitor' => $visitor
                ]);
            }

            DB::table('visitors')->where('id', $id)->update([
                'status' => 'Accepted',
                'updated_at' => now()
            ]);

            return view('visitor-response', [
                'success' => true,
                'message' => 'Visitor has been approved successfully!',
                'visitor' => $visitor
            ]);

        } catch (\Exception $e) {
            return view('visitor-response', [
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'visitor' => null
            ]);
        }
    }

    public function reject($id)
    {
        try {
            $visitor = DB::table('visitors')->where('id', $id)->first();
            
            if (!$visitor) {
                return view('visitor-response', [
                    'success' => false,
                    'message' => 'Visitor not found.',
                    'visitor' => null
                ]);
            }

            if ($visitor->status !== 'For review') {
                return view('visitor-response', [
                    'success' => false,
                    'message' => 'This visitor has already been processed.',
                    'visitor' => $visitor
                ]);
            }

            DB::table('visitors')->where('id', $id)->update([
                'status' => 'Rejected',
                'updated_at' => now()
            ]);

            return view('visitor-response', [
                'success' => true,
                'message' => 'Visitor has been rejected.',
                'visitor' => $visitor
            ]);

        } catch (\Exception $e) {
            return view('visitor-response', [
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'visitor' => null
            ]);
        }
    }

    public function export(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        $isMasterAdmin = $admin->deptID === 1;
        $isDeptAdmin = !$isMasterAdmin;

        $query = DB::table('visitors')
            ->select(
                'visitors.*',
                'depts.nameDept as department_name',
                DB::raw("CASE 
                    WHEN visitors.idcardphoto IS NOT NULL 
                    THEN CONCAT('" . url('storage') . "/', visitors.idcardphoto)
                    ELSE NULL 
                END as id_card_url"),
                DB::raw("CASE 
                    WHEN visitors.selfphoto IS NOT NULL 
                    THEN CONCAT('" . url('storage') . "/', visitors.selfphoto)
                    ELSE NULL 
                END as self_photo_url")
            )
            ->leftJoin('depts', 'visitors.deptpurpose', '=', 'depts.deptID')
            ->orderByDesc('visitors.submit_date');

        // If not master admin, filter by department
        if (!$isMasterAdmin) {
            $query->where('visitors.deptpurpose', $admin->deptID);
        }

        // If specific visitors are selected
        if ($request->has('selected_ids')) {
            $query->whereIn('visitors.id', $request->selected_ids);
        }

        $visitors = $query->get();

        $filename = 'visitors_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(
            new VisitorExport($visitors, $isMasterAdmin, $isDeptAdmin),
            $filename
        );
    }

    private function generateTicketNumber($visitorId)
    {
        // Format: VMS-YYYYMMDD-XXXX where XXXX is the visitor ID padded with zeros
        return 'VMS-' . date('Ymd') . '-' . str_pad($visitorId, 4, '0', STR_PAD_LEFT);
    }

    private function generateBarcode($ticketNumber)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        return base64_encode($generator->getBarcode($ticketNumber, $generator::TYPE_CODE_128));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'status' => 'required|in:Accepted,Rejected',
            ]);

            // Check if visitor exists
            $visitor = DB::table('visitors')->where('id', $id)->first();
            if (!$visitor) {
                throw new \Exception('Visitor not found');
            }

            // Get current admin's deptID
            $admin = auth()->guard('admin')->user();
            if (!$admin) {
                throw new \Exception('Admin not authenticated');
            }

            // Check if admin is master admin
            $isMasterAdmin = $admin->deptID === 1;
            
            // Check if admin is the target department
            $isDeptPurpose = $admin->deptID === $visitor->deptpurpose;

            // Get department info
            $dept = DB::table('depts')
                ->where('deptID', $visitor->deptpurpose)
                ->first();

            if (!$dept) {
                throw new \Exception('Department not found');
            }

            // Check if visit date is within allowed timeframe (before H-2 12:00)
            $visitStartDate = Carbon::parse($visitor->startdate);
            $deadlineDate = $visitStartDate->copy()->subDays(2)->setTime(12, 0, 0);
            $now = Carbon::now();

            if ($now->greaterThan($deadlineDate)) {
                $newStatus = 'Declined';
                $message = 'Request automatically declined as it is past the deadline (H-2 12:00)';
            } else {
                if ($request->status === 'Accepted') {
                    if ($isMasterAdmin) {
                        if ($visitor->status !== 'Approved (1/2)') {
                            throw new \Exception('Request approval to department admin first');
                        }
                        $newStatus = 'Approved (2/2)';
                    } else if ($isDeptPurpose) {
                        if ($visitor->status !== 'For Review') {
                            throw new \Exception('Invalid state to change status');
                        }
                        $newStatus = 'Approved (1/2)';
                    } else {
                        throw new \Exception('You are not authorized to approve this visitor');
                    }
                } else {
                    if ($visitor->status !== 'For Review') {
                        throw new \Exception('Invalid state to change status');
                    }
                    if (!$isMasterAdmin && !$isDeptPurpose) {
                        throw new \Exception('You are not authorized to decline this visitor');
                    }
                    $newStatus = 'Declined';
                }
            }

            // Generate ticket number and barcode for final approval
            $ticketNumber = null;
            $barcode = null;
            if ($newStatus === 'Approved (2/2)') {
                $ticketNumber = $this->generateTicketNumber($id);
                $barcode = $this->generateBarcode($ticketNumber);
            }

            // Update visitor status
            $updated = DB::table('visitors')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'approved_date' => $newStatus === 'Approved (2/2)' ? now() : null,
                    'ticket_number' => $ticketNumber,
                    'barcode' => $barcode
                ]);

            if (!$updated) {
                throw new \Exception('Failed to update visitor status');
            }

            DB::commit();

            // Send email notifications based on new status
            if ($newStatus === 'Approved (1/2)') {
                // Get master admin
                $masterAdmin = DB::table('accounts')
                    ->where('deptID', 1)
                    ->first();

                if ($masterAdmin) {
                    \Log::info('Sending email to master admin', [
                        'master_admin_email' => $masterAdmin->email,
                        'visitor_id' => $id,
                        'status' => $newStatus
                    ]);

                    Mail::send(new VisitorNotification([
                        'recipient_name' => $masterAdmin->name,
                        'name' => $visitor->fullname,
                        'company' => $visitor->company,
                        'visit_purpose' => $visitor->visit_purpose,
                        'startdate' => $visitor->startdate,
                        'enddate' => $visitor->enddate,
                        'department' => $dept->nameDept,
                        'status' => 'Needs Final Approval',
                        'deadline' => $deadlineDate->format('d-m-Y H:i'),
                        'message' => "This visitor has been approved by {$dept->nameDept} department and needs your final approval.",
                        'to' => $masterAdmin->email
                    ]));

                    \Log::info('Email sent successfully to master admin', [
                        'master_admin_email' => $masterAdmin->email,
                        'visitor_id' => $id
                    ]);
                } else {
                    \Log::error('Master admin not found', [
                        'visitor_id' => $id
                    ]);
                }
            } elseif ($newStatus === 'Approved (2/2)') {
                Mail::send(new VisitorNotification([
                    'name' => $visitor->fullname,
                    'company' => $visitor->company,
                    'visit_purpose' => $visitor->visit_purpose,
                    'startdate' => $visitor->startdate,
                    'enddate' => $visitor->enddate,
                    'department' => $dept->nameDept,
                    'status' => 'Approved',
                    'message' => 'Your visit request has been fully approved.',
                    'ticket_number' => $ticketNumber,
                    'barcode' => $barcode,
                    'to' => $visitor->email
                ]));
            } elseif ($newStatus === 'Declined') {
                Mail::send(new VisitorNotification([
                    'name' => $visitor->fullname,
                    'company' => $visitor->company,
                    'visit_purpose' => $visitor->visit_purpose,
                    'startdate' => $visitor->startdate,
                    'enddate' => $visitor->enddate,
                    'department' => $dept->nameDept,
                    'status' => 'Declined',
                    'message' => isset($message) ? $message : 'Your visit request has been declined.',
                    'to' => $visitor->email
                ]));
            }

            // Format the approved_date for response
            $approvedDate = null;
            if ($newStatus === 'Approved (2/2)') {
                $approvedDate = Carbon::now()->format('d-m-Y H:i');
            }

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'approved_date' => $approvedDate,
                'ticket_number' => $ticketNumber,
                'barcode' => $barcode,
                'message' => isset($message) ? $message : 'Status updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating visitor status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'visitor_id' => $id ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function showLogin()
    {
        return view('admin-login');
    }

    public function loginAdmin(Request $request)
    {
        // Rate limiting untuk mencegah brute force attack
        $key = 'login_attempts_' . $request->ip();
        $attempts = cache()->get($key, 0);
        
        if ($attempts >= 5) {
            $lockoutTime = cache()->get($key . '_lockout', 0);
            if (time() < $lockoutTime) {
                $remainingTime = $lockoutTime - time();
                return back()->withErrors([
                    'email' => "Too many login attempts. Please try again in " . ceil($remainingTime / 60) . " minutes.",
                ])->onlyInput('email');
            } else {
                // Reset attempts after lockout period
                cache()->forget($key);
                cache()->forget($key . '_lockout');
            }
        }

        // Validasi input dengan sanitasi
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255'
            ],
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.regex' => 'Please enter a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        // Sanitasi input untuk mencegah XSS dan injection
        $email = filter_var(trim($validated['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($validated['password']);

        // Validasi tambahan untuk email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->incrementLoginAttempts($key);
            return back()->withErrors([
                'email' => 'Please enter a valid email address',
            ])->onlyInput('email');
        }

        // Cek panjang maksimum untuk mencegah buffer overflow
        if (strlen($email) > 255 || strlen($password) > 255) {
            $this->incrementLoginAttempts($key);
            return back()->withErrors([
                'email' => 'Input too long',
            ])->onlyInput('email');
        }

        try {
            // Check using MD5 (legacy support)
            $admin = DB::table('accounts')
                ->where('email', $email)
                ->where('password', md5($password))
                ->first();

            if ($admin) {
                // Update the password to Bcrypt
                DB::table('accounts')
                    ->where('id', $admin->id)
                    ->update(['password' => Hash::make($password)]);

                // Log the user in
                Auth::guard('admin')->loginUsingId($admin->id);
                $request->session()->regenerate();
                
                // Reset login attempts on successful login
                cache()->forget($key);
                cache()->forget($key . '_lockout');
                
                // Log successful login
                \Log::info('Admin login successful', [
                    'email' => $email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return redirect()->intended('/visitor-list');
            }

            // If MD5 fails, try with Bcrypt (for already upgraded passwords)
            if (Auth::guard('admin')->attempt([
                'email' => $email,
                'password' => $password
            ])) {
                $request->session()->regenerate();
                
                // Reset login attempts on successful login
                cache()->forget($key);
                cache()->forget($key . '_lockout');
                
                // Log successful login
                \Log::info('Admin login successful', [
                    'email' => $email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return redirect()->intended('/visitor-list');
            }

            // Increment failed login attempts
            $this->incrementLoginAttempts($key);
            
            // Log failed login attempt
            \Log::warning('Failed admin login attempt', [
                'email' => $email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->withErrors([
                'email' => 'Email or password is incorrect',
            ])->onlyInput('email');

        } catch (\Exception $e) {
            // Log error but don't expose details to user
            \Log::error('Login error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred. Please try again.',
            ])->onlyInput('email');
        }
    }

    /**
     * Increment login attempts and set lockout if needed
     */
    private function incrementLoginAttempts($key)
    {
        $attempts = cache()->get($key, 0) + 1;
        cache()->put($key, $attempts, 300); // 5 minutes
        
        if ($attempts >= 5) {
            cache()->put($key . '_lockout', time() + 900, 900); // 15 minutes lockout
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
