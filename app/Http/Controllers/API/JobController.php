<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class JobController extends BaseController
{
    public function  createJobs(Request $request){
        
        $validateEmp = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'date' => 'required',
                'starting_time' => 'required',
                'ending_time' => 'required',
                'image' => 'required'
            ]
            );
            if($validateEmp->fails()){
                return $this->ErrorResponse('All Fields Are Required',200);
            }else{

                $img = $request->image;
                $decodeImg = base64_decode($img);
                $imageName = time() . '.png';
                $imagePath = public_path('uploads/' . $imageName);
                file_put_contents($imagePath , $decodeImg);
                $imgData = 'uploads/' . $imageName;

                $jobId = DB::table('employee_jobs')->insertGetId([
                    'name' => $request->name,
                    'date' => $request->date,
                    'starting_time' => $request->starting_time,
                    'ending_time' => $request->ending_time,
                    'image' => $imgData,  
                ]);

                                
                $job = DB::table('employee_jobs')->where('jobs_id',$jobId)->first();
                if($job){
                    return $this->SuccessDataResponse('Job Created Successfully',$job,200);
                }
               
                
            }
    }
    public function showJobs(){
        $jobs = DB::table('employee_jobs')->get();
        return $this->SuccessDataResponse('All Jobs Are Here!',$jobs,200);
    }
    public function Cal_Amount(Request $request){
        // Ensure starting_time and ending_time are in a valid time format
        $startTimestamp = strtotime($request->starting_time);
        $endTimestamp = strtotime($request->ending_time);
    
        // Check if timestamps are valid
        if ($startTimestamp === false || $endTimestamp === false) {
            return $this->ErrorResponse('Invalid time format', 400);
        }
    
        // Calculate the difference in seconds
        $differenceInSeconds = $endTimestamp - $startTimestamp;
        
        // Convert the difference to hours
        $hours = $differenceInSeconds / 3600;
    
        // Calculate the total amount
        $base_rate_per_hour = 21;
        $base_rate_per_min = 0.35;
        $service_charges = 2;
        $tax = 2.86;
        $total_amount = ($hours * $base_rate_per_hour) + $service_charges + $tax;
    
        // Return the calculated amount
        if($total_amount){
            return $this->SuccessDataResponse('Amount calculated successfully', $total_amount, 200);
        }
    }
    
    
}
