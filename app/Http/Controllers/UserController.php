<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    // public function registerPage()
    // {
    //     return inertia('Register');
    // }
    // public function loginPage()
    // {
    //     return inertia('Login');
    // }
    public function store(RegisterRequest $request)
    {
        User::create($request->safe()->except('password') + [
            'password' => Hash::make($request->safe()['password'])
        ]);

        return redirect('/login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->safe()->only(['username', 'password']);

        // dd($credentials);
        if (Auth::attempt($credentials, $request->safe()['remember'])) {
            $request->session()->regenerate();

            return redirect(RouteServiceProvider::HOME);
        }


        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
