<?php

namespace App\Http\Controllers\ExternalAuth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            // dd($user);
            $finduser = User::where('email', $user->email)->first();
            if ($finduser) {
                Auth::login($finduser, true);
                return redirect(RouteServiceProvider::HOME);
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => Hash::make($user->id)
                ]);

                Auth::login($newUser);
                return redirect(RouteServiceProvider::HOME);
            }
        } catch (\Throwable $th) {
            throw $th;
            return back();
        }
    }
}
