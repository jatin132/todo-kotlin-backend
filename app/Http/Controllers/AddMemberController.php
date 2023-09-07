<?php

namespace App\Http\Controllers;

use App\Models\Project as ModelsProject;
use App\Models\User;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class AddMemberController extends Controller
{
    function addMemeberToProfile(Request $request, $user_id, $added_user_id){
        try {
            $user = User::find($user_id);
            $member = User::find($added_user_id);

            if (!$user) {
                return response()->json(['msg' => 'User not found.'], 404);
            }
    
            if (!$member) {
                return response()->json(['msg' => 'Member not found.'], 404);
            }

            if ($user->addedMembers()->wherePivot('user_id', $user_id)->wherePivot('member_user_id', $added_user_id)->exists()){
                return response()->json(['msg' => 'Member has already been added.'], 400);
            }

            $user->addedMembers()->attach($added_user_id);

            return response()->json(['msg' => 'Member added successfully.'], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function addMemeberToProject(Request $request, $project_id, $added_user_id){
        try {
            $project = ModelsProject::find($project_id);
            $member = User::find($added_user_id);

            if (!$project) {
                return response()->json(['msg' => 'Project not found.'], 404);
            }
    
            if (!$member) {
                return response()->json(['msg' => 'Member not found.'], 404);
            }

            if ($project->addedMembersToProjects()->wherePivot('project_id', $project_id)->wherePivot('member_id', $added_user_id)->exists()){
                return response()->json(['msg' => 'Member has already been added.'], 400);
            }

            $project->addedMembersToProjects()->attach($added_user_id);

            return response()->json(['msg' => 'Member added successfully to project.'], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function getAddedMembers(Request $request, $user_id){
        try {
            $user = User::where('id', $user_id)->first();
            if (!$user) {
                return response()->json(['msg' => 'User not found.'], 404);
            }

            $addedUsers = $user->addedMembers()
                ->select('users.id as user_id', 'profile_photo', 'username', 'email')
                ->orderBy('users.created_at', 'desc')
                ->get();

            return response()->json($addedUsers, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }

    function searchUsers(Request $request, $user_id){
        try {
            $limit = 10;
            $user = User::where('id', $user_id)->first();
            $query = $request->input('query');
            if (!$user) {
                return response()->json(['msg' => 'User not found.'], 404);
            }

            if (!$query) {
                return response()->json(['msg' => 'Query not found.'], 404);
            }

            $results = User::where('username', 'like', '%'. $query . '%')
                                ->where('id', '<>', $user->id) 
                                ->orderBy('created_at', 'desc')
                                ->paginate($limit);
            
            return response()->json($results, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=>$th->getMessage()], 500);
        }
    }
}
