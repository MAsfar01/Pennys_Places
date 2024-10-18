<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
//help in creating a response
    protected function SuccessResponse($message, $statusCode)
        {
            return response()->json([
                'status' => 'success',
                'message' => $message,
            ],$statusCode);
        }
    protected function ErrorResponse($message, $statusCode)
        {
            return response()->json([
                'status' => 'error',
                'message' => $message,
            ],$statusCode);
        }
        protected function SuccessDataResponse($message, $data, $statusCode)
        {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $data,
            ],$statusCode);
        }
}
