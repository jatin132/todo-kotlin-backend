<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function apiLogin(Request $request){
        $validation = Validator::make($request->all(), [
            'identifier'    => ['required', 'string'],
            'password'      => ['required', 'string'],
            'device'        => ['required', 'string'],
        ]);
        
        if ($validation->fails()) {
            return response()->json($validation->errors()->toArray(), 422);
        }

        try{
            $user = User::where(function ($query) use ($request){
                $query->where("email", $request->identifier)
                ->orWhere("username", $request->identifier);
            })->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'Account not found!'
                ], 422);
            }
    
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'Invalid password!'
                ], 422);
            }    
        
            $token = $user->createToken($request->device)->plainTextToken;
    
            return response()->json([
                'token' => $token,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }   
}
