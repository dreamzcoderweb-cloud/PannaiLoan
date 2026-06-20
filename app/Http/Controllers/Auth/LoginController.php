<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        //  return $request->post();
        //  exit;
        $request->validate(
            [
                'email' => 'required',
                'password' => 'required',
            ]
        );


        $email = $request->input('email');
        $password = $request->input('password');


        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $request->session()->regenerate();
            session()->flash('success', 'Login successful! Welcome to Dashboard.');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'login_status' => 0,
                    'redirect_url' => route('admin.dashboard', [], false),
                ]);
            }

            return redirect()->route('admin.dashboard');
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['login_status' => 1]);
            }

            return back()->withErrors(['email' => 'Login Failed!'])->withInput();
        }
    }



    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // back to login page
    }
}
