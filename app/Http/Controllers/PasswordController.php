<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    function resetPassword(Request $request, $email, $otp){
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => $validator->errors()], 422);
        }

        try {
            $user = User::where("email", $email)->first();
            if (!$user) {
                return response()->json(['msg' => 'User not found.']);
            }

            $passwordReset = PasswordReset::where('email', $email)
                                        ->where('otp', $otp)
                                        ->first();

            $user->password = Hash::make($request->get("password"));
            if ($user->save()) {
                $passwordReset->delete();

                return response()->json(['msg' => 'Password reset successfully.']);
            } else {
                return response()->json(['msg' => 'Password reset failed. Please try again.']);
            }
        } catch(\Exception $e){
            return response()->json(['msg'=>$e->getMessage()]);    
        }
    }

    function changePassword(Request $request, $uuid){
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => $validator->errors()], 422);
        } 

        try {
            $user = User::where("uuid", $uuid)->first();
            if (!$user) {
                return response()->json(['msg' => 'User not found.']);
            }

            if (Hash::check($request->get('current_password'), $user->password)) {
                $user->password = Hash::make($request->get('password'));
                if ($user->save()) {
                    return response()->json(['msg' => 'Password changed successfully.']);
                } else {
                    return response()->json(['msg' => 'Password changed failed. Please try again.']);
                }
            } else {
                return response()->json(['msg' => 'Current password is incorrect.']);
            }

        } catch(\Exception $e){
            return response()->json(['msg'=>$e->getMessage()]);    
        }
    }
}
