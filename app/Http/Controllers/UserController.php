<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ✅ रिपोर्टर/यूजर की प्रोफाइल एडिट फॉर्म दिखाना
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // ✅ रिपोर्टर/यूजर की डिटेल और फोटो अपडेट करना
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // ✔️ डेटा वैलिडेशन (सारी चीजें चेक करें)
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
            'bio'   => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB
        ]);

        // ✔️ बेसिक डिटेल अपडेट करें
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        // ✔️ (अगर आपके पास 'bio' कॉलम है तो यह लाइन हटाना न भूलें, नहीं तो error आएगा)
        // $user->bio = $request->bio; 

        // ✅ फोटो अपलोड और अपडेट (न्यूज़ इमेज से बिल्कुल अलग)
        if ($request->hasFile('photo')) {
            // 1. पुरानी फोटो डिलीट करें (अगर मौजूद है)
            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            // 2. नई फोटो को public/uploads/reporters/ में सेव करें
            $fileName = time() . '_' . $request->file('photo')->getClientOriginalName();
            $request->file('photo')->move(public_path('uploads/reporters'), $fileName);

            // 3. डेटाबेस में पाथ सेव करें
            $user->photo = 'uploads/reporters/' . $fileName;
        }

        // ✔️ सब कुछ डेटाबेस में सेव करें
        $user->save();

        // ✔️ सफलता का मैसेज दिखाएँ और वापस भेजें
        return redirect()->back()->with('success', 'रिपोर्टर प्रोफ़ाइल सफलतापूर्वक अपडेट हुई!');
    }
}