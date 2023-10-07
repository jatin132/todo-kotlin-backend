<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class RemoveAddedMembers extends Controller
{
    public function removeMembersToProfile(Request $request, $user_id, $member_user_id){
        try {
            $user = User::find($user_id);
    
            if (!$user) {
                return response()->json(['msg' => 'User not found.'], 404);
            }
    
            $member = $user->addedMembers()->find($member_user_id);
    
            if (!$member) {
                return response()->json(['msg' => 'You did not add this member in your profile.'], 404);
            }
    
            $user->addedMembers()->detach($member_user_id);
    
            return response()->json(['msg' => 'User Removed Successfully!.'], 200);
    
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }        

    public function removeMembersToProject(Request $request, $project_id, $user_id){
        try {
            $project = Project::find($project_id);
            $user = User::find($user_id);
    
            if (!$project) {
                return response()->json(['msg' => 'Project not found.'], 404);
            }
    
            if (!$user) {
                return response()->json(['msg' => 'User not found.'], 404);
            }
    
            // Check if the user is a member of the project
            if (!$project->addedMembersToProjects()->where('member_id', $user_id)->exists()) {
                return response()->json(['msg' => 'User is not a member of the project.'], 404);
            }
    
            $project->addedMembersToProjects()->detach($user);
    
            return response()->json(['msg' => 'Member removed successfully.'], 200);
    
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }    
}
