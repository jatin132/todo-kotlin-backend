<?php

use App\Http\Controllers\AddMemberController;
use App\Http\Controllers\AddProjectTaskController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DurationTaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// authenticatoin routes
Route::post('/login', [AuthController::class, 'apiLogin']); // complete
Route::post('/register', [UserController::class, 'apiSignUp']); // complete
Route::post('/reset-password/{email}/{otp}', [PasswordController::class, 'resetPassword']); // complete

/**
 * Send otp
 * otp verification
 * resend otp 
*/
Route::post('/send-otp',[OtpController::class,'sendOtp']); 
Route::post('/otp-verification/{otp}',[OtpController::class,'verifyOtp']); // complete
Route::post('/resend',[OtpController::class,'resentOtp']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(["middleware"=> ['auth:sanctum']], function (){
    Route::group(['prefix'=>'v1'], function ($route){

        // user routes
        $route->post('/get-user', [UserController::class, 'getUserDetails']);
        $route->delete('/logout/{android_id}', [UserController::class, 'deviceLogout']); // complete
        $route->post('/add-tokens', [DeviceController::class, 'addTokenOnAuthentication']); // complete

        // password routes
        $route->post('/change-password/{uuid}', [PasswordController::class, 'changePassword']); // complete

        // add member routes
        $route->post('/add-member-profile/{user_id}/{member_id}', [AddMemberController::class, 'addMemeberToProfile']);
        $route->post('/add-member-project/{project_id}/{member_id}', [AddMemberController::class, 'addMemeberToProject']);

        // add task to project
        $route->post('/add-member-project/{project_id}/{member_id}/{task_id}', [AddProjectTaskController::class, 'addTaskToProject']);

        // duration task routes
        $route->post('/create-task/{user_id}', [DurationTaskController::class, 'createTask']);
        $route->delete('/delete-task/{uuid}', [DurationTaskController::class, 'deleteTask']);
        $route->put('/edit-task/{uuid}', [DurationTaskController::class, 'editTask']);
        $route->get('/get-today-tasks/{user_id}', [DurationTaskController::class, 'getTodayTasks']);
        $route->get('/get-all-tasks/{user_id}', [DurationTaskController::class, 'getAllTasks']);

        // project routes
        $route->post('/create-project/{user_id}', [ProjectController::class, 'createProject']);
        $route->delete('/delete-project/{uuid}', [ProjectController::class, 'deleteProject']);
        $route->put('/edit-project/{uuid}', [ProjectController::class, 'editProject']);
        $route->get('/get-all-projects/{user_id}', [ProjectController::class, 'getProjects']);

        // todos routes
        $route->post('/create-todo/{user_id}', [TodoController::class, 'createTodo']);
        $route->delete('/delete-todo/{uuid}', [TodoController::class, 'deleteTodo']);
        $route->put('/edit-todo/{uuid}', [TodoController::class, 'editTodo']);
        $route->post('/mark-complete-todo/{uuid}', [TodoController::class, 'markComplete']);
        $route->get('/get-today-todos/{user_id}', [TodoController::class, 'getTodayTodos']);
        $route->get('/get-all-todos/{user_id}', [TodoController::class, 'getAllTodos']);

    });
});