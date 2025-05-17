<?php

namespace App\Http\Controllers;

use App\Models\ResetCodePassword;
use App\Models\User;
use App\Notifications\SendResetCodePassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ✅ Register
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم التسجيل بنجاح.',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    // ✅ Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    // ✅ Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح.']);
    }

//_______________________________________________________________________________________________


   public function forgetPassword(Request $request)
{
    // Validate the incoming request to ensure 'email' is provided, valid, and exists in the 'users' table
    $data = $request->validate([
        'email' => ['required', 'email', 'exists:users,email'],
    ]);

    // Delete any previous reset codes for this email to ensure only one valid code exists
    ResetCodePassword::where('email', $data['email'])->delete();

    // Generate a new 4-digit random reset code
    $data['code'] = mt_rand(1000, 9999);

    // Store the generated code with the user's email in the 'reset_code_passwords' table
    $resetCodePassword = ResetCodePassword::create($data);

    // Retrieve the user instance by email
    $user = User::where('email', $data['email'])->first();

    // Send the reset code to the user using the notification system
    $user->notify(new SendResetCodePassword($resetCodePassword['code']));

    // Return a success response to the client
    return response()->json(['message' => 'code.sent']);
}

//_______________________________________________________________________________________________
   public function checkCode(Request $request)
{
    // Validate the request to ensure email and code are provided and exist in the reset_code_passwords table
    $data = $request->validate([
        'email' => ['required', 'email', 'exists:reset_code_passwords,email'],
        'code' => ['required', 'digits:4', 'exists:reset_code_passwords,code'],
    ]);

    // Find the reset code record matching both the email and code
    $resetCodePassword = ResetCodePassword::where('email', $data['email'])
        ->where('code', $data['code'])->first();

    // If no matching code is found, return an error response
    if(!$resetCodePassword){
        return response()->json(['message' => 'code is invalid!'], 422);
    }

    // Check if the reset code is expired (older than 5 minutes)
    if( $resetCodePassword['created_at']->addMinutes(5) < now() ){
        // If expired, delete the code and return an error response
        $resetCodePassword->delete();
        return response()->json(['message' => 'code is expired'],422);
    }

    // If code is valid and not expired, return a success response
    return response()->json(['message' => 'password code is valid']);
}

//_______________________________________________________________________________________________



 public function resetPassword(Request $request){
    // Validate the request data: email and code must exist, new password must be confirmed
    $data = $request->validate([
        'email' => ['required' , 'email' , 'exists:reset_code_passwords,email'],
        'code' => ['required' , 'digits:4' ],
        'new_password' => ['required' , 'string' , 'confirmed'],
    ]);     

    // Retrieve the reset code record using the email and code
    $resetCodePassword = ResetCodePassword::where('email' , $data['email'])
        ->where('code' , $data['code'])->first();

    // If no reset code is found, return an error response
    if(!$resetCodePassword){
        return response()->json(['message' => 'code is invalid!'], 422);
    }

    // If the reset code is expired (more than 5 minutes old), delete it and return an error response
    if( $resetCodePassword['created_at']->addMinutes(5) < now() ){
        $resetCodePassword->delete();
        return response()->json(['message' => 'code is expired'],422);
    }

    // Retrieve the user by email
    $user = User::where('email' , $data['email'])->first();

    // Update the user's password after encrypting it
    $user->update([
        'password' => bcrypt($data['new_password']),
    ]);

    // Delete the reset code so it can't be used again
    $resetCodePassword->delete();

    // Return a success response
    return response()->json(['message' => 'password has been successfully reset']);
}

}
