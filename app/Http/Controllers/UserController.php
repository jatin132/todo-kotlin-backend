<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DurationTask;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'profile_photo' => $fileName,
            ];

            $user = User::create($data);

            // dd($user);

            $token = $user->createToken($request->device)->plainTextToken;

            return response()->json(["token"=>$token,'url' => route("profile_picture", ["filename" => $fileName])], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }    
    }

    function getUserDetails(Request $request){
        try {
            $user = $request->user()
                ->withCount('projects')
                ->withCount('todos')
                ->withCount('durationTasks')
                ->first();
    
            if (!$user) {
                return response()->json(['msg' => 'User not found!'], 404);
            } 
    
            $completedTodos = Todo::where('user_id', $user->id)
                ->where('is_completed', 1)->count();
    
            $totalTodos = $user->todos_count;
    
            $completionRateTodos = 0;
            if ($totalTodos > 0) {
                $completionRateTodos = ($completedTodos / $totalTodos) * 100;
            }
    
            $completedProjects = $user->projects()
                ->where('is_completed', 1)->count();

            $totalProjectsTasks = $user->projectsTasks()
            ->where('project_tasks.user_id', $user->id) 
            ->count();            
    
            $completedProjectsTasks = $user->projectsTasks()
            ->with(['tasks' => function ($query) {
                $query->where('is_completed', 1);
            }])
            ->get()
            ->pluck('tasks_count')
            ->sum();        
            // dd($completedProjectsTasks);
    
            $totalTimeTasks = DurationTask::where("user_id", $user->id)->pluck('time')->toArray();
    
            $totalTimeTasksSum = array_sum(array_map('intval', $totalTimeTasks));
    
            return response()->json([
                'user' => $user,
                'completion_rate_todos' => $completionRateTodos,
                'completion_rate_projects' => $completedProjects,
                'total_projects_tasks' => $totalProjectsTasks,
                'completion_rate_projects_tasks' => $completedProjectsTasks,
                'total_time_tasks' => $totalTimeTasksSum,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }    
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
