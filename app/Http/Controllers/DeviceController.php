<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    function addTokenOnAuthentication(Request $request){
        try{    
            $user = Auth::user();

            if(!$user){
                return response()->json(['error' => "User not found"], 422);
            }

            $data = [
                "user_id" => $user->id,
                "token" => $request->get("token"),
                "device_id" => $request->get("device_id"),
            ];

            $device = Device::create($data);
    
            return response()->json(["data" => $device], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }
}
