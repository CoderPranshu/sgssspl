<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Models\{User};
use Illuminate\Support\Facades\Hash;

class SiteSettingController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        // return view('admin.profile', compact('admin'));
        $setting = SiteSetting::first();
        return view('backend.settings.index', compact('setting', 'admin'));
    }

    public function update(Request $request)
    {
        $setting = SiteSetting::first();

		// return $request->all();

        if (!$setting) {
            $setting = new SiteSetting();
        }

        $setting->header_path = $request->header_path;
        $setting->footer_path = $request->footer_path;
        $setting->css = $request->css;
        $setting->js = $request->js;
		$setting->head = $request->head;


        $setting->save();

        return redirect()->back()->with('success', 'Settings Updated Successfully');
    }

    public function profileUpdate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $user = User::where('id', auth()->user()->id)->firstOrFail();

        $user->update($data);

        return back()->with('success', 'Data updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = auth()->user();

        // ✅ Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect'
            ]);
        }

        // ✅ Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully');
    }
}
