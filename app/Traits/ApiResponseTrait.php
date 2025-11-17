<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function successResponse($data = [], $message = 'نجاح', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse($message = 'خطأ', $status = 400, $data = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}
