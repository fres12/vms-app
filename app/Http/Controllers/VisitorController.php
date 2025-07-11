<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
            'visit_date' => 'required|date',
            'visit_time' => 'required',
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

        $visit_datetime = Carbon::parse($request->visit_date . ' ' . $request->visit_time);

        DB::table('visitors')->insert([
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

        return redirect()->back()->with('success', 'Visitor saved!');
    }

    public function index()
    {
        $visitors = \DB::table('visitors')->orderByDesc('created_at')->get();
        return view('visitor-list', compact('visitors'));
    }
}
