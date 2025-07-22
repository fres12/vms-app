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
                'approved_date' => null
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
            Mail::to($deptAdmin->email)->send(new VisitorNotification([
                'recipient_name' => $deptAdmin->name,
                'name' => $request->full_name,
                'company' => $request->company,
                'visit_purpose' => $request->visit_purpose,
                'startdate' => $request->startdate,
                'enddate' => $request->enddate,
                'department' => $dept->nameDept,
                'status' => 'For Review',
                'message' => "A new visitor has requested approval for your department. Please review this request."
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
        $query = DB::table('visitors');

        // If specific visitors are selected
        if ($request->has('selected_ids')) {
            $query->whereIn('id', $request->selected_ids);
        }

        $visitors = $query->orderByDesc('submit_date')->get();
        return Excel::download(new VisitorExport($visitors), 'visitors.xlsx');
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

            // Update visitor status
            $updated = DB::table('visitors')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'approved_date' => $newStatus === 'Approved (2/2)' ? Carbon::now()->format('d-m-Y H:i') : null
                ]);

            if (!$updated) {
                throw new \Exception('Failed to update visitor status');
            }

            // Send email notifications based on new status
            try {
                if ($newStatus === 'Approved (1/2)') {
                    // Get master admin
                    $masterAdmin = DB::table('accounts')
                        ->where('deptID', 1)
                        ->first();

                    if ($masterAdmin) {
                        Mail::to($masterAdmin->email)->send(new VisitorNotification([
                            'recipient_name' => $masterAdmin->name,
                            'name' => $visitor->fullname,
                            'company' => $visitor->company,
                            'visit_purpose' => $visitor->visit_purpose,
                            'startdate' => $visitor->startdate,
                            'enddate' => $visitor->enddate,
                            'department' => $dept->nameDept,
                            'status' => 'Needs Final Approval',
                            'deadline' => $deadlineDate->format('d-m-Y H:i'),
                            'message' => "This visitor has been approved by {$dept->nameDept} department and needs your final approval."
                        ]));
                    }
                } elseif ($newStatus === 'Approved (2/2)') {
                    Mail::to($visitor->email)->send(new VisitorNotification([
                        'name' => $visitor->fullname,
                        'company' => $visitor->company,
                        'visit_purpose' => $visitor->visit_purpose,
                        'startdate' => $visitor->startdate,
                        'enddate' => $visitor->enddate,
                        'department' => $dept->nameDept,
                        'status' => 'Approved',
                        'message' => 'Your visit request has been fully approved.'
                    ]));
                } elseif ($newStatus === 'Declined') {
                    Mail::to($visitor->email)->send(new VisitorNotification([
                        'name' => $visitor->fullname,
                        'company' => $visitor->company,
                        'visit_purpose' => $visitor->visit_purpose,
                        'startdate' => $visitor->startdate,
                        'enddate' => $visitor->enddate,
                        'department' => $dept->nameDept,
                        'status' => 'Declined',
                        'message' => isset($message) ? $message : 'Your visit request has been declined.'
                    ]));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send email notification', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'visitor_id' => $id
                ]);
                // Continue even if email fails
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'approved_date' => $newStatus === 'Approved (2/2)' ? Carbon::now()->format('d-m-Y H:i') : null,
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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check using MD5
        $admin = DB::table('accounts')
            ->where('email', $request->email)
            ->where('password', md5($request->password))
            ->first();

        if ($admin) {
            // Update the password to Bcrypt
            DB::table('accounts')
                ->where('id', $admin->id)
                ->update(['password' => Hash::make($request->password)]);

            // Log the user in
            Auth::guard('admin')->loginUsingId($admin->id);
            $request->session()->regenerate();
            
            return redirect()->intended('/visitor-list');
        }

        // If MD5 fails, try with Bcrypt (for already upgraded passwords)
        if (Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('/visitor-list');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
