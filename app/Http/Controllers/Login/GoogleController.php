<?php

namespace App\Http\Controllers\Login;

use Brian2694\Toastr\Toastr;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {

            $user = Socialite::driver('google')->user();
            $finduser = User::where('google_id', $user->id)->first();
            // dd($finduser);
            if($finduser){
                Auth::login($finduser);
                	Toastr()->success('Đăng nhập thành công!');
                return redirect()->intended('/');
            }else{
                $role = Role::where('name', 'User')->first();
                $newUser = User::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'name' => $user->name,
                        'google_id'=> $user->id,
                        'password' => encrypt('123456dummy'),
                        'role_id' => $role->id ?? null,
                        'status' => "Hoạt Động",
                    ]
                );
                Auth::login($newUser);
                	toastr()->success('Đăng nhập thành công!');
                return redirect()->intended('/');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
