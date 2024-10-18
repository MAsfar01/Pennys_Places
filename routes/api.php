<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EmployeeController;
//Authentication  
Route::post('signup',[AuthController::class,'signUp']);
Route::post('login',[AuthController::class,'login']);
Route::post('forgotPassword',[AuthController::class,'forgot_password']);
Route::post('verifyOtp',[AuthController::class,'verifyOtp']);
Route::post('resetPassword',[AuthController::class,'resetPassword']);
Route::middleware('auth:sanctum')->post('changePassword', [AuthController::class, 'changePassword']);

//EMPLOYEE
Route::post('Employee_SignUp',[EmployeeController::class,'Employee_SignUp']);
Route::post('Employee_login',[EmployeeController::class,'Employee_login']);
Route::post('Employee_delete/{id}',[EmployeeController::class,'Employee_delete']);

Route::post('createJobs',[JobController::class,'createJobs']);
Route::get('showJobs',[JobController::class,'showJobs']);

Route::post('Cal_Amount',[JobController::class,'Cal_Amount']);


