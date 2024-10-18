<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends BaseController
{
    
    public function Employee_SignUp(Request $request){
        $validateEmp = Validator::make(
        $request->all(),[
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'password' => 'required',
            'profile_image' => 'required',
            'id_image' => 'required',
            'document_image' => 'required',
            'certificate_image' => 'required',
        ]
        );
        if($validateEmp->fails()){
            return $this->ErrorResponse('All Fields Are Required',200);
        }else{
            $profile_img = $request->profile_image;
            $id_img = $request->id_image;
            $document_img = $request->document_image;
            $certificate_img = $request->certificate_image;

            $decode_profile_img =  base64_decode($profile_img);
            $decode_id_img =  base64_decode($id_img);
            $decode_document_img =  base64_decode($document_img);
            $decode_certificate_img =  base64_decode($certificate_img);

            $imageName_profile_img = time() . '_profile.png';
            $imageName_id_img = time() . '_id.png';
            $imageName_document_img = time() . '_document.png';
            $imageName_certificate_img =  time() . '_certificate.png';

            $imagePath_profile_img = public_path('uploads/' .$imageName_profile_img);
            $imagePath_id_img = public_path('uploads/' .$imageName_id_img);
            $imagePath_document_img = public_path('uploads/' .$imageName_document_img);
            $imagePath_certificate_img = public_path('uploads/' .$imageName_certificate_img);

            // After decoding the base64 strings, you can save the files using file_put_contents()//
            file_put_contents($imagePath_profile_img , $decode_profile_img);
            file_put_contents($imagePath_id_img , $decode_id_img);
            file_put_contents($imagePath_document_img , $decode_document_img);
            file_put_contents($imagePath_certificate_img , $decode_certificate_img);

            $imageData_profile_img = 'uploads/' . $imageName_profile_img;
            $imageData_id_img = 'uploads/' . $imageName_id_img;
            $imageData_document_img = 'uploads/' . $imageName_document_img;
            $imageData_certificate_img = 'uploads/' . $imageName_certificate_img;
            if(DB::table('employees')->where('email', $request->email)->exists()){
                return $this->ErrorResponse('Email Already Taken',401);
            }else{
                $employee = DB::table('employees')->insert([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'profile_image' => $imageData_profile_img,
                    'id_image' => $imageData_id_img,
                    'document_image' => $imageData_document_img,
                    'certificate_image' => $imageData_certificate_img,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                if($employee){
                    return $this->SuccessResponse('Account Created Successfully',200);
                }
            }
            
        }
    }
    public function Employee_login(Request $request)
    {
        $validatorEmp = Validator::make(
            $request->all(),
            [
            'email' => 'required',
            'password' => 'required',
            ]
        );
        if($validatorEmp->fails()){
            return $this->ErrorResponse("All Fields Are Required",200);
        }else{
            $Employee = DB::table('employees')->where('email', $request->email)->first();
            if (!$Employee || !Hash::check($request->password, $Employee->password)) {
                return $this->ErrorResponse('Invalid Credentials',401);
            }else{
                return $this->SuccessDataResponse('User LoggedIn Successfully',$Employee,200);    
            }
        }    
    }
    public function Employee_delete($id){
        DB::table('employees')->where('employees_id',$id)->delete();
        return $this->SuccessResponse('Account Deleted Successfully',200);
    }    
}
