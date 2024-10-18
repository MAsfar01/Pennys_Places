<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signUp(Request $request){
       $validateUser = Validator::make(
        $request->all(),
        [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validateUser->errors()->all()
            ],401);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Account Created Successfully',
            'user' => $user,
        ],200);
    }
    public function login(Request $request){
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
            );
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication Failed',
                    'errors' => $validateUser->errors()->all()
                ],401);
            }
            if(Auth::attempt(['email' => $request->email,'password' => $request->password])){
                $authUser = Auth::user();
                return response()->json([
                    'status' => true,
                    'message' => 'User Logged In Successfully',
                    'token' => $authUser->createToken("Login Token")->plainTextToken,
                    'token_type'=> 'bearer'
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Email Or Password',
                ],401);
            }
    }
    public function forgot_password(Request $request){
        $validateUser = Validator::make(
            $request->all(),
            [
              'email' => 'required|email|exists:users,email',  
            ]
            );
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Email Does not Exists',
                    'errors' => $validateUser->errors()->all()
                ],401);
            }
            // Generate a 4-digit OTP
             $otp = rand(1000, 9999);
             // Store the OTP in the password_resets table
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $otp,
                'created_at' => now()
                
            ]);
            // Send the OTP via email
            Mail::raw("Your OTP for password reset is: $otp", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Your Password Reset OTP');
            });
        
            return response()->json([
                'status' => true,
                'message' => 'Otp Send Successfully',
                'otp' => $otp,
            ],200);    
    }
    public function verifyOtp(Request $request){
        $otp = $request->otp;
        $validateUser = Validator::make(
            $request->all(),[
              'email' => 'required|email|exists:users,email',
              'otp'   => 'required|numeric'  
            ]);
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Email Does not Exists',
                    'errors' => $validateUser->errors()->all()
                ],401);
            }
            $record = DB::table('password_resets')->where('email', $request->email)->first();
            if ($record && $request->otp == $record->token) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Otp Matched Successfully',
                    'otp' => $request->otp,
                    'record' => $record,
                ],200);
            }else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid OTP. Please try again.',
                ],401);
            }
            
    }
    public function resetPassword(Request $request){
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|confirmed'
            ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validateUser->errors()->all()
            ],401);
        }
        $user = User::where('email',$request->email)->first();
        $user->update(['password' => $request->password]);
        return response()->json([
            'status' => true,
            'message' => 'Password Reset Successfully',
        ],200);
    }
    public function changePassword(Request $request){
        $validateUser = Validator::make(
            $request->all(),
            [
                'oldPassword' => 'required',
                'password' => 'required|confirmed'
            ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validateUser->errors()->all()
            ],401);
        }
        $AuthUser = Auth::user();
        if (Hash::check($request->oldPassword, $AuthUser->password)) {
            $AuthUser->password = $request->password;
            $AuthUser->save();
            return response()->json([
                'status' => true,
                'message' => 'Password Changed Successfully',
            ],200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Old Password is Incorrect'
            ],401);
        } 
            
    }
}




// public function signUp_standMan(Request $request){
//     // Check if all required fields are missing
//     if(empty($request->first_name) && empty($request->last_name) && empty($request->phone_number) && empty($request->email) && empty($request->password) && empty($request->image)) {            return response()->json([
//               'status' => 'error',
//               'message' => 'ALL FIELDS ARE REQUIRED'
//           ], 200); // Use 200 OK status code
//      }else{
//           if(DB::table('asfar_users')->where('email', $request->email)->exists()) {
//               return response()->json([
//                   'status' => 'error',
//                   'message' => 'Email already taken'
//               ], 422);
//           }else{

//               $img = $request->image;
//               $decodeImage = base64_decode($img);
//               $imageName = time().'.png';
//               $imagePath = public_path('/uploads/'.$imageName);
//               file_put_contents($imagePath,$decodeImage);
//               $imageData = '/uploads/'.$imageName;
      
//               $userId  = DB::table('asfar_users')->insertGetId([
//               'first_name' => $request->first_name,
//               'last_name' => $request->last_name,
//               'phone_number' => $request->phone_number,
//               'email' => $request->email,
//               'password' => Hash::make($request->password),
//               'image' => $imageData,
//               'created_at' => Carbon::now(),
//               'updated_at' => Carbon::now()
//               ]);
//               $user = DB::table('asfar_users')->where('users_id', $userId)->first();
//               if($user){
//                   return response()->json([
//                       'status' => 'success',
//                       'message' => 'User Created Successfully',
//                       'user' => $user 
//                   ],200);
//               }
//           }
//       }    
//  }


// Route::post('/signUp_standMan', [Api2Controller::class, 'signUp_standMan']);
// Route::post('/signIn_standMan', [Api2Controller::class, 'signIn_standMan']);

// class Api2Controller extends Controller
// {
//     // Helper method for success response
//     private function sendSuccessResponse($message, $data = [])
//     {
//         return response()->json([
//             'status' => 'success',
//             'message' => $message,
//             'data' => $data,
//         ], 200);
//     }

//     // Helper method for error response
//     private function sendErrorResponse($message, $statusCode = 422)
//     {
//         return response()->json([
//             'status' => 'error',
//             'message' => $message,
//         ], $statusCode);
//     }

//     // Helper method for validation error response
//     private function sendValidationErrorResponse($message)
//     {
//         return response()->json([
//             'status' => 'error',
//             'message' => $message,
//         ], 200); // Use 200 OK status code
//     }

//     // Your signUp method
//     public function signUp_standMan(Request $request)
//     {
//         // Check if all required fields are empty
//         if (empty($request->first_name) && empty($request->last_name) && empty($request->phone_number) && empty($request->email) && empty($request->password) && empty($request->image)) {
//             return $this->sendValidationErrorResponse('ALL FIELDS ARE REQUIRED');
//         }

//         // Check if the email is already taken
//         if (DB::table('asfar_users')->where('email', $request->email)->exists()) {
//             return $this->sendErrorResponse('Email already taken');
//         }

//         // Decode and store the image
//         $img = $request->image;
//         $decodeImage = base64_decode($img);
//         $imageName = time() . '.png';
//         $imagePath = public_path('/uploads/' . $imageName);
//         file_put_contents($imagePath, $decodeImage);
//         $imageData = '/uploads/' . $imageName;

//         // Insert the user data
//         $userId = DB::table('asfar_users')->insertGetId([
//             'first_name' => $request->first_name,
//             'last_name' => $request->last_name,
//             'phone_number' => $request->phone_number,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'image' => $imageData,
//             'created_at' => Carbon::now(),
//             'updated_at' => Carbon::now()
//         ]);

//         // Fetch the created user
//         $user = DB::table('asfar_users')->where('id', $userId)->first();

//         // Return success response with user data
//         return $this->sendSuccessResponse('User Created Successfully', $user);
//     }
// }
