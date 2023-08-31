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
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/register', [UserController::class, 'apiSignUp']);
Route::post('/reset-passowrd/{email}/{otp}', [PasswordController::class, 'resetPassword']);

/**
 * Send otp
 * otp verification
 * resend otp 
*/
Route::post('/send-otp',[OtpController::class,'sendOtp']);
Route::post('/otp-verification/{otpVerification}',[OtpController::class,'verifyOtp']);
Route::post('/resend',[OtpController::class,'resentOtp']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(["middleware"=> ['auth:sanctum']], function (){
    Route::group(['prefix'=>'v1'], function ($route){

        // user routes
        $route->post('/get-user', [UserController::class, 'getUserDetails']);
        $route->post('/logout', [UserController::class, 'deviceLogout']);
        $route->post('/add-tokens', [DeviceController::class, 'addTokenOnAuthentication']);

        // password routes
        $route->post('/change-password/{uuid}', [PasswordController::class, 'changePassword']);

        // add member routes
        $route->post('/add-member-profile/{user_id}/{member_id}', [AddMemberController::class, 'addMemeberToProfile']);
        $route->post('/add-member-project/{project_id}/{member_id}', [AddMemberController::class, 'addMemeberToProject']);

        // add task to project
        $route->post('/add-member-project/{project_id}/{member_id}/{task_id}', [AddProjectTaskController::class, 'addTaskToProject']);

        // duration task routes
        $route->post('/create-task', [DurationTaskController::class, 'createTask']);
        $route->post('/delete-task/{uuid}', [DurationTaskController::class, 'deleteTask']);
        $route->post('/edit-task/{uuid}', [DurationTaskController::class, 'editTask']);
        $route->post('/get-today-tasks/{uuid}', [DurationTaskController::class, 'getTodayTasks']);
        $route->post('/get-all-tasks/{uuid}', [DurationTaskController::class, 'getAllTasks']);

        // project routes
        $route->post('/create-project', [ProjectController::class, 'createProject']);
        $route->post('/delete-project/{uuid}', [ProjectController::class, 'deleteProject']);
        $route->post('/edit-project/{uuid}', [ProjectController::class, 'editProject']);
        $route->post('/get-all-projects/{uuid}', [ProjectController::class, 'getProjects']);

        // todos routes
        $route->post('/create-todo', [TodoController::class, 'createTodo']);
        $route->post('/delete-todo/{uuid}', [TodoController::class, 'deleteTodo']);
        $route->post('/edit-todo/{uuid}', [TodoController::class, 'editTodo']);
        $route->post('/mark-complete-todo/{uuid}', [TodoController::class, 'markComplete']);
        $route->post('/get-today-todos/{uuid}', [TodoController::class, 'getTodayTodos']);
        $route->post('/get-all-todos/{uuid}', [TodoController::class, 'getAllTodos']);

    });
});