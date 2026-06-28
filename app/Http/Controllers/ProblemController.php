<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Problem;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    public function create()
    {
        $districts = District::where('is_active', true)->get();

        return view('problems.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10',
            'district_id' => 'required|exists:districts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $request->file('image') ? $request->file('image')->store('problems', 'public') : null;

        Problem::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'district_id' => $request->district_id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'आपकी समस्या दर्ज कर ली गई है। हम इसे अधिकारियों तक पहुँचाने का प्रयास करेंगे।');
    }
}