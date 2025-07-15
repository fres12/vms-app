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
            'nik' => 'required|string',
            'id_card_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'self_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'full_name' => 'required|string',
            'company' => 'nullable|string',
            'phone' => 'nullable|string',
            'department_purpose' => 'nullable|string',
            'section_purpose' => 'nullable|string',
            'visit_date' => 'required|date_format:Y-m-d',
            'visit_time' => 'required|date_format:H:i',
        ]);

        // Upload ID Card Photo
        if ($request->hasFile('id_card_photo')) {
            $idCardPhotoName = uniqid('idcard_') . '.' . $request->file('id_card_photo')->getClientOriginalExtension();
            $request->file('id_card_photo')->move(public_path('storage/idcard_photo'), $idCardPhotoName);
            $idCardPhotoPath = 'idcard_photo/' . $idCardPhotoName;
        }

        // Upload Self Photo
        if ($request->hasFile('self_photo')) {
            $selfPhotoName = uniqid('self_') . '.' . $request->file('self_photo')->getClientOriginalExtension();
            $request->file('self_photo')->move(public_path('storage/self_photo'), $selfPhotoName);
            $selfPhotoPath = 'self_photo/' . $selfPhotoName;
        }

        // Validate and parse visit datetime with better error handling
        try {
            $visit_datetime = Carbon::createFromFormat('Y-m-d H:i', $request->visit_date . ' ' . $request->visit_time);
            
            // Additional validation to ensure date is not in the past
            if ($visit_datetime->isPast()) {
                return redirect()->back()
                    ->withErrors(['visit_date' => 'Visit date and time cannot be in the past'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['visit_date' => 'Invalid visit date or time format'])
                ->withInput();
        }

        $visitorId = DB::table('visitors')->insertGetId([
            'nik' => $request->nik,
            'id_card_photo' => $idCardPhotoPath ?? null,
            'full_name' => $request->full_name,
            'company' => $request->company,
            'phone' => $request->phone,
            'department_purpose' => $request->department_purpose,
            'section_purpose' => $request->section_purpose,
            'self_photo' => $selfPhotoPath ?? null,
            'visit_datetime' => $visit_datetime,
            'status' => 'For review',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email notification if Department A is selected
        if ($request->department_purpose === 'Dept A') {
            $visitorData = [
                'id' => $visitorId,
                'full_name' => $request->full_name,
                'nik' => $request->nik,
                'company' => $request->company,
                'phone' => $request->phone,
                'department_purpose' => $request->department_purpose,
                'section_purpose' => $request->section_purpose,
                'visit_datetime' => $visit_datetime->format('Y-m-d H:i:s'),
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];

            try {
                Mail::to('fresneld@hmmi.co.id')->send(new VisitorNotification($visitorData));
            } catch (\Exception $e) {
                // Log error but don't stop the process
                \Log::error('Failed to send email notification: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Visitor saved!');
    }

    public function index()
    {
        $visitors = \DB::table('visitors')->orderByDesc('created_at')->get();
        return view('visitor-list', compact('visitors'));
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

    public function export()
    {
        $visitors = DB::table('visitors')->orderByDesc('created_at')->get();
        return Excel::download(new VisitorExport($visitors), 'visitors.xlsx');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Accepted,Rejected',
        ]);

        $updated = \DB::table('visitors')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => $updated]);
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

        // First try with MD5 (legacy)
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
