<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    function apiSignUp(Request $request){
        $validation = FacadesValidator::make($request->all(), [
            'email'     => ['required', 'string', 'email'],
            'password'  => ['required', 'string'],
            'username'    => ['required', 'string'],
            'profile_photo' => 'image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors()->toArray(), 422);
        }

        try {
            $user = User::where("email", $request->email)->orWhere('username', $request->get('username'))->first();
            if ($user) {
                return response()->json(['error'=>"User already exists"], 422);
            }
            
            $file = $request->file('profile_photo');

            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $img = Image::make($file->getRealPath());
            $img->resize(200, 200);
            $img->stream(); 

            $path = Storage::disk('local')->put('profile_pictures' . '/' . $fileName, $img, 'public');

            if (!$path) {
                return response()->json(['msg' => 'Failed to upload photo.'], 500);
            }

            $data = [
                'uuid' => random_strings(7, 'users'),
                'username' => $request->get('username'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'profile_photo' => $file,
            ];

            $user = User::create($data);

            $token = $user->createToken($request->device)->plainTextToken;

            return response()->json(["token"=>$token,'url' => route("profile_picture", ["filename" => $fileName])], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }    
    }

    function getUserDetails(Request $request){
        
    }

    function deviceLogout(Request $request, $android_id){
        try {
            $user = $request->user();

            $device = Device::where("user_id", $user->id)
            ->where("device_id", $android_id)
            ->first();

            if(!$device) {
                return response()->json(['msg' => "Device not found"]);
            } else {
                $device->delete();
            }

            $user->currentAccessToken()->delete();

            return response()->json(["msg" => "Logout successfully"]);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }   
}
