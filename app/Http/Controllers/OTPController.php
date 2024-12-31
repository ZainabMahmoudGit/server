<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Hash;
class OTPController extends Controller
{
   

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^\+[1-9]\d{1,14}$/',
        ]);
    
        $existingUser = User::where('mobile', $request->mobile)->first();
        if ($existingUser) {
            return response()->json(['error' => 'This mobile number is already registered.'], 400);
        }
    
        $otp = rand(100000, 999999); 
    
        $user = User::updateOrCreate(
            ['mobile' => $request->mobile],
            ['otp' => $otp, 'otp_verified' => false]
        );
    
        try {
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $twilio->messages->create(
                $request->mobile,
                [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => "Your OTP code is: $otp"
                ]
            );
    
            return response()->json(['message' => 'OTP sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send OTP.', 'details' => $e->getMessage()], 500);
        }
    }
    

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^\+[1-9]\d{1,14}$/',
            'otp' => 'required|digits:6',
        ]);
    
        $user = User::where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->first();
    
        if (!$user) {
            return response()->json(['error' => 'Invalid OTP.'], 400);
        }
    
        $user->update([
            'otp_verified' => true,
            'otp' => null 
        ]);
    
        return response()->json(['message' => 'OTP verified successfully.']);
    }
    
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'city' => 'required',
            'street' => 'required',
            'payment_method' => 'required',
            'password' => 'required|min:8' 
        ]);
    
        $user = User::where('mobile', $request->mobile)->first();
    
        if (!$user || !$user->otp_verified) {
            return response()->json(['error' => 'User not verified.'], 403);
        }
    
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'city' => $request->city,
            'street' => $request->street,
            'payment_method' => $request->payment_method,
            'password' => Hash::make($request->password) 
        ]);
    
        return response()->json(['message' => 'Profile updated successfully.']);
    }
    
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'city' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'password' => 'nullable|min:8' 
        ]);
    
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
    
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'city' => $request->city,
            'street' => $request->street,
            'payment_method' => $request->payment_method,
            'password' => $request->password ? Hash::make($request->password) : $user->password, // تحديث كلمة المرور فقط إذا تم إرسالها
        ]);
    
        return response()->json(['message' => 'User updated successfully.', 'user' => $user], 200);
    }
    
    public function indexUser($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['error' => 'User not found.'], 404);
    }

    return response()->json(['user' => $user], 200);
}

    public function deleteUser($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['error' => 'User not found.'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'User deleted successfully.'], 200);
}
 


}
