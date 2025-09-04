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
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VisitorController extends Controller
{
    public function store(Request $request)
    {
        // Rate limiting untuk mencegah spam submission
        $key = 'visitor_form_' . $request->ip();
        $attempts = cache()->get($key, 0);
        
        if ($attempts >= 3) {
            $lockoutTime = cache()->get($key . '_lockout', 0);
            if (time() < $lockoutTime) {
                $remainingTime = $lockoutTime - time();
                return back()->withErrors([
                    'general' => "Too many form submissions. Please try again in " . ceil($remainingTime / 60) . " minutes.",
                ])->withInput();
            } else {
                cache()->forget($key);
                cache()->forget($key . '_lockout');
            }
        }

        // Enhanced validation dengan sanitasi
        $validated = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'nik' => [
                'required',
                'string',
                'max:16',
                'regex:/^[0-9]+$/'
            ],
            'id_card_photo' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],
            'self_photo' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],
            'company' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\.\-\&\,]+$/'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9\-\+\(\)\s]+$/'
            ],
            'deptpurpose' => [
                'required',
                'integer',
                'exists:depts,deptID'
            ],
            'visit_purpose' => [
                'required',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9\s\.\-\,\!\?]+$/'
            ],
            'startdate' => [
                'required',
                'date',
                'after:now'
            ],
            'enddate' => [
                'required',
                'date',
                'after:startdate'
            ],
            'equipment_type' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\.\-\&\,]+$/'
            ],
            'brand' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\.\-\&\,]+$/'
            ],
            'pledge_agreement' => [
                'required',
                'accepted'
            ]
        ], [
            'full_name.required' => 'Full name is required',
            'full_name.regex' => 'Full name contains invalid characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.regex' => 'Please enter a valid email address',
            'nik.required' => 'NIK is required',
            'nik.regex' => 'NIK must contain only numbers',
            'company.regex' => 'Company name contains invalid characters',
            'phone.regex' => 'Phone number contains invalid characters',
            'visit_purpose.regex' => 'Visit purpose contains invalid characters',
            'equipment_type.regex' => 'Equipment type contains invalid characters',
            'brand.regex' => 'Brand contains invalid characters',
            'startdate.after' => 'Start date must be in the future',
            'enddate.after' => 'End date must be after start date',
        ]);

        try {
            DB::beginTransaction();

            // Sanitasi input untuk mencegah XSS dan injection
            $fullName = $this->sanitizeInput($validated['full_name']);
            $email = filter_var(trim($validated['email']), FILTER_SANITIZE_EMAIL);
            $nik = $this->sanitizeInput($validated['nik']);
            $company = $validated['company'] ? $this->sanitizeInput($validated['company']) : null;
            $phone = $validated['phone'] ? $this->sanitizeInput($validated['phone']) : null;
            $visitPurpose = $this->sanitizeInput($validated['visit_purpose']);
            $equipmentType = $validated['equipment_type'] ? $this->sanitizeInput($validated['equipment_type']) : null;
            $brand = $validated['brand'] ? $this->sanitizeInput($validated['brand']) : null;

            // Validasi tambahan untuk email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Invalid email format');
            }

            // Validasi panjang input untuk mencegah buffer overflow
            if (strlen($fullName) > 255 || strlen($email) > 255 || strlen($nik) > 16) {
                throw new \Exception('Input too long');
            }

            // Validasi file upload security
            $idCardPhoto = $request->file('id_card_photo');
            $selfPhoto = $request->file('self_photo');

            // Validasi file type dan content
            if (!$this->isValidImage($idCardPhoto)) {
                \Log::warning('Invalid ID card photo upload attempt', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'filename' => $idCardPhoto->getClientOriginalName(),
                    'size' => $idCardPhoto->getSize(),
                    'mime_type' => $idCardPhoto->getMimeType(),
                    'extension' => $idCardPhoto->getClientOriginalExtension()
                ]);
                throw new \Exception('Invalid ID card photo file');
            }

            if (!$this->isValidImage($selfPhoto)) {
                \Log::warning('Invalid self photo upload attempt', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'filename' => $selfPhoto->getClientOriginalName(),
                    'size' => $selfPhoto->getSize(),
                    'mime_type' => $selfPhoto->getMimeType(),
                    'extension' => $selfPhoto->getClientOriginalExtension()
                ]);
                throw new \Exception('Invalid self photo file');
            }

            // Generate secure filename untuk mencegah path traversal
            $idCardPhotoPath = $this->generateSecureFilename($idCardPhoto, 'id_cards');
            $selfPhotoPath = $this->generateSecureFilename($selfPhoto, 'self_photos');

            // Handle ID Card Photo Upload
            $idCardPhotoPath = $idCardPhoto->storeAs('id_cards', $idCardPhotoPath, 'public');
            
            // Handle Self Photo Upload
            $selfPhotoPath = $selfPhoto->storeAs('self_photos', $selfPhotoPath, 'public');

            // Get department info dengan validasi
            $dept = DB::table('depts')
                ->where('deptID', (int)$validated['deptpurpose'])
                ->first();

            if (!$dept) {
                throw new \Exception('Department not found');
            }

            // Insert into database dengan data yang sudah disanitasi
            $visitorId = DB::table('visitors')->insertGetId([
                'fullname' => $fullName,
                'email' => $email,
                'nik' => $nik,
                'idcardphoto' => $idCardPhotoPath,
                'selfphoto' => $selfPhotoPath,
                'company' => $company,
                'phone' => $phone,
                'deptpurpose' => (int)$validated['deptpurpose'],
                'visit_purpose' => $visitPurpose,
                'startdate' => $validated['startdate'],
                'enddate' => $validated['enddate'],
                'equipment_type' => $equipmentType,
                'brand' => $brand,
                'status' => 'For Review',
                'submit_date' => now(),
                'approved_date' => null,
                'ticket_number' => null,
                'barcode' => null
            ]);

            // Get department admin
            $deptAdmin = DB::table('accounts')
                ->where('deptID', (int)$validated['deptpurpose'])
                ->first();

            if (!$deptAdmin) {
                \Log::error('Department admin not found', [
                    'dept_id' => $validated['deptpurpose']
                ]);
                throw new \Exception('Department admin not found');
            }

            // Send email notification to department admin
            Mail::send(new VisitorNotification([
                'recipient_name' => $deptAdmin->name,
                'name' => $fullName,
                'company' => $company,
                'visit_purpose' => $visitPurpose,
                'startdate' => $validated['startdate'],
                'enddate' => $validated['enddate'],
                'department' => $dept->nameDept,
                'status' => 'For Review',
                'message' => "A new visitor has requested approval for your department. Please review this request.",
                'to' => $deptAdmin->email
            ]));

            DB::commit();

            // Reset form submission attempts on successful submission
            cache()->forget($key);
            cache()->forget($key . '_lockout');

            // Log successful registration
            \Log::info('Visitor registration successful', [
                'visitor_id' => $visitorId,
                'email' => $email,
                'dept_admin_email' => $deptAdmin->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->back()->with('success', 'Your visitor registration has been submitted successfully. Please wait for approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Increment form submission attempts on error
            $this->incrementFormAttempts($key);
            
            // Log error but don't expose details to user
            \Log::error('Error in visitor registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Return more specific error messages for common issues
            $errorMessage = 'An error occurred while processing your registration. Please try again.';
            
            if (strpos($e->getMessage(), 'Invalid image file') !== false) {
                $errorMessage = 'Please ensure you are uploading valid image files (JPG, JPEG, PNG) only.';
            } elseif (strpos($e->getMessage(), 'Input too long') !== false) {
                $errorMessage = 'One or more fields contain data that is too long. Please check your input.';
            } elseif (strpos($e->getMessage(), 'Invalid email format') !== false) {
                $errorMessage = 'Please enter a valid email address.';
            } elseif (strpos($e->getMessage(), 'Department not found') !== false) {
                $errorMessage = 'Selected department is not valid. Please try again.';
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $errorMessage]);
        }
    }

    public function index(Request $request)
    {
        try {
            $admin = auth()->guard('admin')->user();
            
            // Sanitize search input
            $searchTerm = $this->sanitizeSearchInput($request->get('search', ''));
            
            // Base query with security measures
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

            // If search term exists, add secure search conditions
            if (!empty($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(visitors.fullname) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(visitors.email) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(visitors.nik) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(visitors.company) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(visitors.phone) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(visitors.visit_purpose) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(depts.nameDept) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
                });
            }

            // Department filter for non-master admin
            if ($admin->deptID !== 1) {
                $query->where('visitors.deptpurpose', $admin->deptID);
            }

            // Get department info for header
            $deptInfo = DB::table('depts')
                ->where('deptID', $admin->deptID)
                ->first();

            // Execute query with limits
            $visitors = $query->limit(1000)->get(); // Limit results for performance

            return view('visitor-list', [
                'visitors' => $visitors,
                'isMasterAdmin' => $admin->deptID === 1,
                'deptInfo' => $deptInfo,
                'searchTerm' => $searchTerm
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in visitor list:', [
                'error' => $e->getMessage(),
                'user_id' => $admin->id ?? null,
                'search' => $searchTerm ?? null
            ]);
            
            return view('visitor-list', [
                'visitors' => collect([]),
                'isMasterAdmin' => $admin->deptID === 1,
                'deptInfo' => $deptInfo ?? null,
                'error' => 'An error occurred while loading the visitor list.'
            ]);
        }
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
                'status' => 'Accepted'
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
                'status' => 'Rejected'
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
                    THEN CONCAT('" . url('storage') . "//', visitors.idcardphoto)
                    ELSE NULL 
                END as id_card_url"),
                DB::raw("CASE 
                    WHEN visitors.selfphoto IS NOT NULL 
                    THEN CONCAT('" . url('storage') . "//', visitors.selfphoto)
                    ELSE NULL 
                END as self_photo_url"),
                DB::raw("CASE
                    WHEN visitors.ticket_number IS NOT NULL AND visitors.barcode IS NOT NULL
                    THEN CONCAT('" . url('/barcode') . "/', visitors.ticket_number)
                    ELSE NULL
                END as barcode_url")
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

    private function generateQrCode($content)
    {
        try {
            if (!class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                throw new \Exception('QR code package is not installed. Run: composer require simplesoftwareio/simple-qrcode');
            }

            $path = 'qrcodes';
            Storage::disk('public')->makeDirectory($path);

            // Try PNG first (requires GD/Imagick)
            try {
                $png = QrCode::format('png')
                    ->size(400)
                    ->margin(1)
                    ->errorCorrection('M')
                    ->generate($content);

                $filename = 'qrcode_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $content) . '_' . time() . '.png';
                $fullPath = $path . '/' . $filename;

                if (!Storage::disk('public')->put($fullPath, $png)) {
                    throw new \Exception('Failed to save QR code file to public storage');
                }

                return [
                    'path' => $fullPath,
                    'base64' => base64_encode($png)
                ];
            } catch (\Throwable $e) {
                \Log::warning('PNG QR generation failed, falling back to SVG', [
                    'error' => $e->getMessage()
                ]);
            }

            // Fallback to SVG (no GD required)
            $svg = QrCode::format('svg')
                ->size(400)
                ->margin(1)
                ->errorCorrection('M')
                ->generate($content);

            $filename = 'qrcode_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $content) . '_' . time() . '.svg';
            $fullPath = $path . '/' . $filename;

            if (!Storage::disk('public')->put($fullPath, $svg)) {
                throw new \Exception('Failed to save QR code SVG to public storage');
            }

            return [
                'path' => $fullPath,
                'base64' => base64_encode($svg)
            ];
        } catch (\Exception $e) {
            \Log::error('QR code generation failed:', [
                'error' => $e->getMessage(),
                'content' => $content
            ]);
            return null;
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $visitor = DB::table('visitors')->where('id', $id)->first();
            if (!$visitor) {
                throw new \Exception('Visitor not found');
            }

            $admin = auth()->guard('admin')->user();
            $newStatus = $request->status;
            $currentStatus = $visitor->status;

            // Block changes if already final
            if (in_array($currentStatus, ['Rejected', 'Approved (2/2)'])) {
                throw new \Exception('This visitor has already been processed.');
            }

            // Department Admin Approval (First Level)
            if ($newStatus === 'Accepted' && $admin->deptID !== 1) {
                if ($currentStatus !== 'For Review') {
                    throw new \Exception('Invalid status transition');
                }

                $updated = DB::table('visitors')
                    ->where('id', $id)
                    ->update([
                        'status' => 'Approved (1/2)'
                    ]);

                if (!$updated) {
                    throw new \Exception('Failed to update visitor status');
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated to Approved (1/2)',
                    'status' => 'Approved (1/2)'
                ]);
            }

            // Master Admin Final Approval (Second Level)
            if ($newStatus === 'Accepted' && $admin->deptID === 1) {
                if ($currentStatus !== 'Approved (1/2)') {
                    throw new \Exception('Invalid status transition');
                }

                // Generate ticket number
                $ticketNumber = $this->generateTicketNumber($id);
                
                // Generate QR code
                $qrData = $this->generateQrCode($ticketNumber);
                if (!$qrData) {
                    throw new \Exception('Failed to generate QR code');
                }

                // Update visitor status
                $updated = DB::table('visitors')
                    ->where('id', $id)
                    ->update([
                        'status' => 'Approved (2/2)',
                        'approved_date' => now(),
                        'ticket_number' => $ticketNumber,
                        'barcode' => $qrData['path']
                    ]);

                if (!$updated) {
                    throw new \Exception('Failed to update visitor data');
                }

                // Get department info
                $dept = DB::table('depts')
                    ->where('deptID', $visitor->deptpurpose)
                    ->first();

                // Get QR code content and convert to proper data URI
                try {
                    $qrContent = Storage::disk('public')->get($qrData['path']);
                    $mimeType = 'image/svg+xml';
                    
                    // Create proper data URI for SVG
                    $base64QrCode = 'data:' . $mimeType . ';base64,' . base64_encode($qrContent);

                    // Send email with QR code
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
                        'barcode' => $base64QrCode, // Send properly formatted data URI
                        'to' => $visitor->email
                    ]));
                } catch (\Exception $e) {
                    \Log::error('Failed to process or send QR code:', [
                        'error' => $e->getMessage(),
                        'path' => $qrData['path']
                    ]);
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated to Approved (2/2)',
                    'status' => 'Approved (2/2)',
                    'approved_date' => now()->format('d-m-Y H:i')
                ]);
            }

            // Rejection rules
            if ($newStatus === 'Rejected') {
                // Dept admin can reject only "For Review"
                // Master admin can reject "For Review" or "Approved (1/2)"
                $canReject = false;
                if ($admin->deptID === 1 && in_array($currentStatus, ['For Review', 'Approved (1/2)'])) {
                    $canReject = true;
                } elseif ($admin->deptID !== 1 && $currentStatus === 'For Review') {
                    $canReject = true;
                }

                if (!$canReject) {
                    throw new \Exception('Invalid status transition');
                }

                DB::table('visitors')
                    ->where('id', $id)
                    ->update([
                        'status' => 'Rejected'
                    ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated to Rejected',
                    'status' => 'Rejected'
                ]);
            }

            throw new \Exception('Invalid status update request');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Status update failed:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
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

    /**
     * Sanitize input untuk mencegah XSS dan injection
     */
    private function sanitizeInput($input)
    {
        if (is_string($input)) {
            // Remove HTML tags
            $input = strip_tags($input);
            // Convert special characters
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            // Trim whitespace
            $input = trim($input);
            // Remove null bytes
            $input = str_replace(chr(0), '', $input);
        }
        return $input;
    }

    /**
     * Sanitize search input to prevent SQL injection and XSS
     * @param string $input
     * @return string
     */
    private function sanitizeSearchInput($input)
    {
        if (empty($input)) {
            return '';
        }

        // Remove any non-alphanumeric characters except spaces and common symbols
        $input = preg_replace('/[^a-zA-Z0-9\s\-\_\@\.\,]/', '', $input);
        
        // Remove SQL injection characters
        $input = str_replace(
            ['\'', '"', ';', '*', '=', 'OR', 'AND', '--'], 
            '', 
            $input
        );
        
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // Limit length
        $input = substr($input, 0, 100);
        
        return trim($input);
    }

    /**
     * Validate image file untuk mencegah malicious uploads
     */
    private function isValidImage($file)
    {
        try {
            if (!$file || !$file->isValid()) {
                return false;
            }

            // Check file extension
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                return false;
            }

            // Check MIME type
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
            $mimeType = $file->getMimeType();
            
            if (!in_array($mimeType, $allowedMimes)) {
                return false;
            }

            // Check file size (max 2MB)
            if ($file->getSize() > 2 * 1024 * 1024) {
                return false;
            }

            // Additional check: verify it's actually an image
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo === false) {
                return false;
            }

            // Check image dimensions untuk mencegah DoS (lebih longgar)
            if ($imageInfo[0] > 8000 || $imageInfo[1] > 8000) {
                return false;
            }

            // Check magic bytes untuk mencegah polyglot files (optional untuk performance)
            if (!$this->hasValidMagicBytes($file)) {
                return false;
            }

            // Check untuk PHP code dalam file (optional untuk performance)
            if ($this->containsPHPCode($file)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error validating image file', [
                'error' => $e->getMessage(),
                'filename' => $file ? $file->getClientOriginalName() : 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Check magic bytes untuk mencegah polyglot files
     */
    private function hasValidMagicBytes($file)
    {
        try {
            $handle = fopen($file->getPathname(), 'rb');
            if (!$handle) {
                return true; // Skip magic byte check if can't open file
            }

            // Read first 12 bytes untuk check magic bytes
            $header = fread($handle, 12);
            fclose($handle);

            if (strlen($header) < 3) {
                return true; // Skip if file too small
            }

            // JPEG magic bytes: FF D8 FF
            $jpegMagic = "\xFF\xD8\xFF";
            
            // PNG magic bytes: 89 50 4E 47 0D 0A 1A 0A
            $pngMagic = "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A";

            // Check untuk valid image magic bytes
            if (strpos($header, $jpegMagic) === 0) {
                return true;
            }

            if (strpos($header, $pngMagic) === 0) {
                return true;
            }

            // If magic bytes don't match, log but don't block (for compatibility)
            \Log::warning('Magic bytes validation failed', [
                'filename' => $file->getClientOriginalName(),
                'header' => bin2hex(substr($header, 0, 8))
            ]);
            
            return true; // Allow file to pass for now
        } catch (\Exception $e) {
            \Log::error('Error checking magic bytes', ['error' => $e->getMessage()]);
            return true; // Skip magic byte check on error
        }
    }

    /**
     * Check untuk PHP code dalam file
     */
    private function containsPHPCode($file)
    {
        try {
            $handle = fopen($file->getPathname(), 'rb');
            if (!$handle) {
                return false;
            }

            // Read first 1KB of file content (for performance)
            $content = fread($handle, 1024);
            fclose($handle);

            // Check untuk PHP tags (most common)
            $phpPatterns = [
                '/<\?php/i',
                '/<\?=/i',
                '/<\?/i',
                '/\?>$/i',
            ];

            foreach ($phpPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    \Log::warning('PHP code detected in uploaded file', [
                        'filename' => $file->getClientOriginalName(),
                        'pattern' => $pattern
                    ]);
                    return true;
                }
            }

            // Check untuk dangerous functions (optional)
            $dangerousPatterns = [
                '/eval\s*\(/i',
                '/system\s*\(/i',
                '/exec\s*\(/i',
                '/shell_exec\s*\(/i',
            ];

            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    \Log::warning('Dangerous function detected in uploaded file', [
                        'filename' => $file->getClientOriginalName(),
                        'pattern' => $pattern
                    ]);
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Error checking PHP code', ['error' => $e->getMessage()]);
            return false;
        }
    }



    /**
     * Generate secure filename untuk mencegah path traversal
     */
    private function generateSecureFilename($file, $prefix)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        
        return $prefix . '_' . $timestamp . '_' . $randomString . '.' . $extension;
    }

    /**
     * Increment form submission attempts
     */
    private function incrementFormAttempts($key)
    {
        $attempts = cache()->get($key, 0) + 1;
        cache()->put($key, $attempts, 600); // 10 minutes
        
        if ($attempts >= 3) {
            cache()->put($key . '_lockout', time() + 1800, 1800); // 30 minutes lockout
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    public function changePassword(Request $request) 
    {
        try {
            $admin = Auth::guard('admin')->user();
            $currentPasswordValid = false;

            // First try MD5 check
            $md5Match = DB::table('accounts')
                ->where('id', $admin->id)
                ->where('password', md5($request->current_password))
                ->exists();

            if ($md5Match) {
                $currentPasswordValid = true;
            }
            // If MD5 fails, try bcrypt
            elseif (Hash::check($request->current_password, $admin->password)) {
                $currentPasswordValid = true;
            }

            if (!$currentPasswordValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong current password'
                ]);
            }

            // Validate new password match
            if ($request->new_password !== $request->new_password_confirmation) {
                return response()->json([
                    'success' => false,
                    'message' => 'New password and confirmation do not match'
                ]);
            }

            // Update with new MD5 hashed password
            $updated = DB::table('accounts')
                ->where('id', $admin->id)
                ->update([
                    'password' => md5($request->new_password)
                ]);

            if (!$updated) {
                throw new \Exception('Failed to update password');
            }

            Auth::guard('admin')->logout();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Password change error:', [
                'error' => $e->getMessage(),
                'user_id' => $admin->id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while changing password'
            ]);
        }
    }

    public function viewBarcode($ticket_number)
    {
        $visitor = DB::table('visitors')->where('ticket_number', $ticket_number)->first();
        if (!$visitor || !$visitor->barcode) {
            abort(404);
        }

        $storagePath = ltrim($visitor->barcode, '/');
        if (!Storage::disk('public')->exists($storagePath)) {
            abort(404);
        }

        $data = Storage::disk('public')->get($storagePath);
        $ext = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));
        $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/png';
        return response($data)->header('Content-Type', $mime);
    }
}
