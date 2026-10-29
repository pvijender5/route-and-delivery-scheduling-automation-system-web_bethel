<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Constants\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    // public function create(): View
    // {
    //     return view('auth.login');
    // }
    public function create(): View
    {
        return view('auth.login');
    }


    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request['email'];
        $user = User::where('email', $email)->orWhere('username', $email)->first();
        if(!$user) {
            throw ValidationException::withMessages([
                'email' => "Please enter valid email or phone number",
            ]);
        }
        $allowedroles = [Roles::ADMIN_ID,Roles::DISPATCHER_ID,Roles::ROUTER_ID,Roles::SUPERADMIN_ID];
        if (isset($user) && !empty($user) && !in_array($user->role_id, $allowedroles)) {
            throw ValidationException::withMessages([
                'email' => "This user not allowed to login",
            ]);
        }elseif(isset($user) && !empty($user) && $user->status == "inactive") {
            throw ValidationException::withMessages([
                'email' => "This user account is not active,Please contact with admin",
            ]);
        }
        else{
            $request->authenticate();
            //$request->user()->createToken('myapptoken');
            
            $request->session()->regenerate();
            session(['token' => $request->user()->createToken('myapptoken')]);
            // Authentication successful
            $user = Auth::user();

            $userRole = $user->role->name;  // Assuming role relationship is defined

            // Redirect or respond based on the user's role
            if ($userRole === 'admin' || $userRole === 'super-admin') {
                return redirect('/customers');
                // Redirect to admin dashboard
            } elseif ($userRole === 'router') {
                return redirect('/createRoute');
                 // Redirect to router dashboard
            } elseif ($userRole === 'dispatcher') {
                return redirect('/createRoute');
                 // Redirect to router dashboard
            }
        }
    }

    /**
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
      
        //$request->user()->tokens()->delete();
        auth('sanctum')->user()->tokens()->delete();
        $request->session()->invalidate();

        $request->session()->regenerateToken();
       
        return redirect('/');
    }


}
