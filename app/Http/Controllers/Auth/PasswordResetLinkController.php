<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Constants\Roles;
use App\Constants\Commonconstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\View;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\PasswordResetToken;
use App\Models\SettingSmsTemplate;
use Carbon\Carbon;


class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'emailOrPhone' => ['required'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we 
        // need to show to the user. Finally, we'll send out a proper response.
        $checkmail = User::checkmail($request->emailOrPhone);
        if ($checkmail) {
            $pin = rand(1000, 9999).' ';
            $newTime = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +10 minutes"));
            $token = Str:: random(64);
            $userName = $checkmail->name;

            //Send sms for forgot password
            if($checkmail->username){
                $smsTemp = SettingSmsTemplate::where('name', Commonconstant::SMSMODULENAME['FORGOT_EMAIL'])->where('status', '=', 'active')->value('description');
                if(isset($smsTemp)) {
                    $appName = ' '.getenv("APP_NAME");
                    $smsTemplate = strip_tags($smsTemp);
                    $tags = array("[userName]", "[pin]", "[appName]");
                    $values = array($userName, $pin, $appName);
                    $message = str_replace($tags, $values, $smsTemplate);
                    sendMobileSms($checkmail->username, $message);
                }
            }
            if($checkmail->email){
                Mail::to($checkmail->email)->send(new ForgotPassword($pin, $userName));
            }
            $checkmail->update([
                'email_otp' => $pin,
                'optexpiretime' => strtotime($newTime)
            ]);
            $tokenRecord = PasswordResetToken::where('user_id', $checkmail->id)->first();
            if ($tokenRecord) {
                // Update the existing token record
                $tokenRecord->update(['token' => $token]);
            } else {
                // Create a new token record if none exists
                PasswordResetToken::create([
                    'user_id' => $checkmail->id,
                    'token' => $token,
                ]);
            }
            session()->flash('status', 'An OTP to reset your password has been sent. Please check your email or SMS. Note that the OTP is time-sensitive and valid for a limited time.');
            // return redirect('/otpverification');
            return redirect() -> route('password.otpverification', ['token' => $token]);

        } else {

            return back()->withInput($request->only('email'))->withErrors(['email' => "We can't find a user with that email or phone."]);
        }
    }




    /**
     * To 
     * show
     *  otpverification 
     * page
     */
    public function otp( $token)
    {
        // return view('auth.otpVerificaion');
                $token = $token;
        return view('auth.otpVerificaion', compact('token'));

    }
    /**
     * Functionn 
     * to verify 
     * otp 
     * recived on mail
     */

    public function verifyOtp(Request $request, $token) {
        $request -> validate([
            'otp' => 'required',
        ]);

        $enteredOtp = implode("", $request -> otp);
        $tokenRecord = PasswordResetToken:: where('token', $token) -> first();

         if ($tokenRecord) {
            $user = User::find($tokenRecord->user_id);
            if ($user && $tokenRecord->user_id === $user->id) {
                if ($tokenRecord->token === $token) {
                    if($user->email_otp == $enteredOtp){
                        session()->flash('status', 'OTP verification successful. Your identity has been confirmed. Please proceed to set a new password for your account.');
                        return redirect()->route('password.resetpassword', ['token' => $token]);
                    } else {
                        return back()->withErrors(['otp' => 'The OTP is incorrect or has expired. Please try again.']);
                    } 
                } else {
                    return back()->withErrors(['otp' => 'Invalid token']);
                }
            } else {
                return back()->withErrors(['otp' => 'Invalid user or token']);
            }
        } else {
            return back()->withErrors(['token' => 'Invalid token']);
        }
    }

    /**
     * To show 
     * resetpassword
     * page
     * after otp
     * verifications 
     */
   
    public function resetpassword(Request $request, $token)
{
    $tokenRecord = PasswordResetToken::where('token', $token)->first();

    if ($tokenRecord) {
        $user = User::find($tokenRecord->user_id);

        if ($user) {
            $userEmail = $user->email;
            return view('auth.change-password', compact('token', 'userEmail','user'));
        } else {
            return back()->withErrors(['token' => 'Invalid user associated with the token']);
        }
    } else {
        return back()->withErrors(['token' => 'Invalid OTP or token']);
    }
}


    /**
     * After verify otp 
     * change password page open
     *  then change the password 
     * for particular email.
     */


    public function updatePassword(Request $request)
    {

    $checkToken = PasswordResetToken::checkResetPasswordToken($request->token);
        if ($checkToken) {
            $createdAt = Carbon::parse($checkToken->updated_at);
            $currentDateTime = Carbon::now();
            
            // Calculate the difference in hours
            $hoursDifference = $createdAt->diffInHours($currentDateTime);
            
            if ($hoursDifference >= 24) {
                // Token has expired for one day
                return redirect()->route('password.request')->withErrors(['token' => 'Your token has been expired, Please reset it again.']);
            } 
        } else {
            // Token not found
           // return back()->withErrors(['token' => 'Token or User not found.']);
           return redirect()->route('password.request')->withInput($request->only('email'))->withErrors(['email' => 'Token or User not found, Please reset it again.']);
          // return back()->withInput($request->only('email'))->withErrors(['email' => "Token or User not found, Please reset it again."]);
        }

        // Validate Password 
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed'
            ],
            'password_confirmation' => ['required', 'same:password'],
        ], [
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters in length.',
            'password.regex' => 'The password must meet the following criteria: at least one lowercase letter, at least one uppercase letter, at least one digit, and at least one special character.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required' => 'The password confirmation field is required.',
            'password_confirmation.same' => 'The password confirmation must match the password field.'
        ]);
    
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        
        // delete token after reset password
        PasswordResetToken::deleteByEmail($request->token);
        return redirect('/')->with('status', 'Password changed successfully. Please sign in.');
    } else {
        return back()->withErrors(['token' => 'Invalid or expired token']);
    }
  }

}
