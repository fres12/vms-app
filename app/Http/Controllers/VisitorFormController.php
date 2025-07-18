<?php

namespace App\Http\Controllers;

use App\Models\Dept;
use Illuminate\Http\Request;

class VisitorFormController extends Controller
{
    public function create()
    {
        $depts = Dept::all();
        return view('visitor-form', compact('depts'));
    }

    public function store(Request $request)
    {
        // Form submission handling will be implemented here
        // For now, we'll just redirect back
        return redirect()->back();
    }
} 