<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We can\'t find a user with that e-mail address.']);
        }

        $otp = rand(100000, 999999);

        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        // Send Email
       Mail::html(
            "Your OTP for password reset is: <b>$otp</b><br><br>
             <small>This OTP is valid for 15 minutes.</small>",
            function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Reset Password Notification');
            }
        );

        return redirect()->route('password.otp')->with(['email' => $request->email, 'status' => 'We have e-mailed your password reset OTP!']);
    }

    public function showOtpForm()
    {
        return view('auth.passwords.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $record = DB::table('password_reset_otps')->where([
            'email' => $request->email,
            'otp' => $request->otp
        ])->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        // Check expiry (e.g., 15 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors(['otp' => 'OTP has expired.']);
        }

        return redirect()->route('password.reset')->with(['email' => $request->email, 'verified' => true]);
    }

    public function showResetForm()
    {
        if (!session('verified')) {
             return redirect()->route('password.request')->withErrors(['email' => 'Please verify OTP first.']);
        }
        return view('auth.passwords.reset');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8|max:12',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
             return back()->withErrors(['email' => 'We can\'t find a user with that e-mail address.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }
}
