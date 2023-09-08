<?php

namespace App\Http\Controllers;

use App\Models\Project as ModelsProject;
use App\Models\User;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class AddMemberController extends Controller
{
    public function addMembersToProfile(Request $request, $user_id){
        try {
            $user = User::find($user_id);

            if (!$user) {
                return response()->json(['msg' => 'User not found.'], 404);
            }

            $added_user_ids = $request->input('added_user_ids'); // Assuming the input is an array of added_user_ids

            if (empty($added_user_ids)) {
                return response()->json(['msg' => 'No members provided.'], 400);
            }

            $addedMembers = [];

            foreach ($added_user_ids as $added_user_id) {
                $member = User::find($added_user_id);

                if (!$member) {
                    return response()->json(['msg' => 'Member not found for user ' . $added_user_id], 404);
                }

                if ($user->addedMembers()->wherePivot('user_id', $user_id)->wherePivot('member_user_id', $added_user_id)->exists()) {
                    // Skip members that have already been added.
                    continue;
                }

                $user->addedMembers()->attach($added_user_id);
                $addedMembers[] = $member->toArray();
            }

            return response()->json(['msg' => 'Members added successfully.', 'added_members' => $addedMembers], 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }
    public function addMembersToProject(Request $request, $project_id){
        try {
            $project = ModelsProject::find($project_id);
    
            if (!$project) {
                return response()->json(['msg' => 'Project not found.'], 404);
            }
    
            $added_user_ids = $request->input('added_user_ids'); // Assuming the input is an array of added_user_ids
    
            if (empty($added_user_ids)) {
                return response()->json(['msg' => 'No members provided.'], 400);
            }
    
            $addedMembers = [];
    
            foreach ($added_user_ids as $added_user_id) {
                $member = User::find($added_user_id);
    
                if (!$member) {
                    return response()->json(['msg' => 'Member not found for user ' . $added_user_id], 404);
                }
    
                if ($project->addedMembersToProjects()->wherePivot('project_id', $project_id)->wherePivot('member_id', $added_user_id)->exists()) {
                    // Skip members that have already been added to the project.
                    continue;
                }
    
                $project->addedMembersToProjects()->attach($added_user_id);
                $addedMembers[] = $member->toArray();
            }
    
            return response()->json(['msg' => 'Members added successfully to project.', 'added_members' => $addedMembers], 200);
    
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
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
