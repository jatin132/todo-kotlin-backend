<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    function sendOtp(Request $request){
        $validatedData = $request->validate([
            'email' => 'required|email'
        ]);
    
        try {
            $user = User::where('email', $validatedData['email'])->first();
            if(!$user){
                return response()->json(["msg" => "User not found!"]);
            } else {
                $token = str::random(40);
                $otp = mt_rand(100000, 999999);
                $data['email'] = $validatedData['email'];
                $data['title'] = "Password Reset";
                $data['body'] = "Please check your Email and Reset the Password.";
                $data['otp'] = $otp;
    
                Mail::send('mail',['data'=>$data], function($message) use ($data){
                    $message->to($data['email'])->subject($data['title']);
                });
    
                PasswordReset::updateOrCreate(
                    ['email' => $validatedData['email']],
                    ['token' => $token, 'otp' => $otp]
                );   
                
                dd($otp);
    
                return response()->json(['msg'=>"Please check your email"]);
            }
        } catch(\Exception $ex){
            return response()->json(['msg'=>$ex->getMessage()]);    
        }
    }    

    function verifyOtp(Request $request, $otp){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['msg'=>$validator->errors()], 404);
        }

        try{
            $sendOtp = PasswordReset::where("otp", $otp)->first();
            if(!$sendOtp){
               return response()->json(['msg' => "Invalid OTP"], 422); 
            } else {
               return response()->json(['msg' => "OTP Verified"]);
            }
        } catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage()]);    
        }
    }
    
    function resentOtp(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['msg'=>$validator->errors()], 404);
        }

        try{
            $user = User::where('email',$request->email)->first();
            if($user)
            {
                $token = str::random(40);
                $otp = mt_rand(100000, 999999);
    
                $data['email'] = $request->email;
                $data['title'] = "Password Reset";
                $data['body'] = "Please check your Email.";
                $data['otp'] = $otp;
    
                Mail::send('mail',['data'=>$data],function($message) use($data){
    
                $message->to($data['email'])->subject($data['title']);
            });
    
            $datatime = Carbon::now()->format('y-m-d H:i:s');
            
            $passwordReset = PasswordReset::where('email',$request->email)->first();
            
            if($passwordReset){
                $passwordReset->update([
                    'token' => $token,
                    'created_at' => $datatime,
                    'otp' => $otp
                ]);
            }
            else{
                PasswordReset::create([
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => $datatime,
                    'otp' => $otp
                ]);
            }
    
            return response()->json(['msg'=>"Please check your email",'otp'=>$data['otp']]);
    
            }else{
               return response()->json(['msg'=>"user not found"]);
            }
        }
        catch(\Exception $ex){
            return response()->json(['msg'=>$ex->getMessage()]);    
        }
    }
}
