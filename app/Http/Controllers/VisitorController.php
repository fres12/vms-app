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
            // Handle ID Card Photo Upload
            $idCardPhotoPath = $request->file('id_card_photo')->store('id_cards', 'public');
            
            // Handle Self Photo Upload
            $selfPhotoPath = $request->file('self_photo')->store('self_photos', 'public');

            // Insert into database
            DB::table('visitors')->insert([
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

            // Send email notification to PIC of selected department
            try {
                // Get the PIC email from the accounts table where deptID = tujuan dept
                $picEmail = DB::table('accounts')
                    ->where('deptID', $request->deptpurpose)
                    ->value('email');

                \Log::info('Attempting to send email notification to department PIC', [
                    'deptID' => $request->deptpurpose,
                    'picEmail' => $picEmail
                ]);

                if ($picEmail) {
                    // Get department name for email
                    $deptName = DB::table('depts')
                        ->where('deptID', $request->deptpurpose)
                        ->value('nameDept');

                    \Log::info('Sending visitor notification email', [
                        'to' => $picEmail,
                        'department' => $deptName,
                        'visitor_name' => $request->full_name
                    ]);

                    Mail::to($picEmail)->send(new VisitorNotification([
                        'name' => $request->full_name,
                        'company' => $request->company,
                        'visit_purpose' => $request->visit_purpose,
                        'startdate' => $request->startdate,
                        'enddate' => $request->enddate,
                        'department' => $deptName,
                        'submit_date' => now()
                    ]));

                    \Log::info('Email notification sent successfully');
                } else {
                    \Log::warning('No PIC email found for department', [
                        'deptID' => $request->deptpurpose
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send email notification', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue execution even if email fails
            }

            return redirect()->back()->with('success', 'Your visitor registration has been submitted successfully. Please wait for approval.');

        } catch (\Exception $e) {
            \Log::error('Error in visitor registration: ' . $e->getMessage());
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
            $request->validate([
                'status' => 'required|in:Accepted,Rejected',
            ]);

            // Check if visitor exists
            $visitor = DB::table('visitors')->where('id', $id)->first();
            if (!$visitor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visitor not found'
                ]);
            }

            // Get current admin's deptID
            $admin = auth()->guard('admin')->user();
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin not authenticated'
                ]);
            }

            // Check if admin is master admin
            $isMasterAdmin = $admin->deptID === 1;
            
            // Check if admin is the target department
            $isDeptPurpose = $admin->deptID === $visitor->deptpurpose;

            // Check if the status can be changed based on current status
            if ($visitor->status !== 'For Review' && $visitor->status !== 'Approved (1/2)') {
                return response()->json([
                    'success' => false,
                    'message' => 'This visitor\'s status cannot be changed anymore'
                ]);
            }

            if ($request->status === 'Accepted') {
                // Master admin can only change from Approved (1/2) to Approved (2/2)
                if ($isMasterAdmin) {
                    if ($visitor->status !== 'Approved (1/2)') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Master admin can only approve visitors with status "Approved (1/2)"'
                        ]);
                    }
                    $newStatus = 'Approved (2/2)';
                }
                // Department admin can only change from For Review to Approved (1/2)
                else if ($isDeptPurpose) {
                    if ($visitor->status !== 'For Review') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Department can only approve visitors with status "For Review"'
                        ]);
                    }
                    $newStatus = 'Approved (1/2)';
                }
                // If not master admin or target department
                else {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to approve this visitor'
                    ]);
                }
            } else { // Rejected status
                // Can only reject if status is For Review
                if ($visitor->status !== 'For Review') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot decline a visitor that has already been approved'
                    ]);
                }

                // Check if user has authority to decline
                if (!$isMasterAdmin && !$isDeptPurpose) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to decline this visitor'
                    ]);
                }
                $newStatus = 'Declined';
            }

            // Update visitor status
            $updated = DB::table('visitors')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'approved_date' => $newStatus === 'Approved (2/2)' ? now() : null
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update visitor status'
                ]);
            }

            // Send email notification if status is Approved (2/2)
            if ($newStatus === 'Approved (2/2)') {
                try {
                    Mail::to($visitor->email)->send(new VisitorNotification([
                        'name' => $visitor->fullname,
                        'company' => $visitor->company,
                        'visit_purpose' => $visitor->visit_purpose,
                        'startdate' => $visitor->startdate,
                        'enddate' => $visitor->enddate,
                        'department' => DB::table('depts')->where('deptID', $visitor->deptpurpose)->value('nameDept'),
                        'status' => 'Approved'
                    ]));
                } catch (\Exception $e) {
                    \Log::error('Failed to send approval notification email', [
                        'error' => $e->getMessage(),
                        'visitor_id' => $id
                    ]);
                    // Continue execution even if email fails
                }
            }

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'approved_date' => $newStatus === 'Approved (2/2)' ? now() : null,
                'message' => 'Status updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating visitor status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating status'
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
