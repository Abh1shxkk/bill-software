<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->user_id.',user_id',
            'email' => 'required|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'notification_email' => 'nullable|email|max:255',
            'profile_picture' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:500',
            'telephone' => 'nullable|string|max:50',
            'tin_no' => 'nullable|string|max:50',
            'gst_no' => 'nullable|string|max:50',
            'dl_no' => 'nullable|string|max:50',
            'dl_no_1' => 'nullable|string|max:50',
            'licensed_to' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles','public');
            $data['profile_picture'] = 'storage/'.$path;
        }

        $user->update($data);

        return back()->with('success','Profile updated');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Wrong current password']);
        }
        $user->update(['password' => Hash::make($request->input('password'))]);
        return redirect()->back()->with('success','Password updated');
    }
}


