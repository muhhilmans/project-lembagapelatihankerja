<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse($data = [], string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        if (!empty($data)) {
            if (is_array($data) && array_keys($data) !== range(0, count($data) - 1)) {
                 $response = array_merge($response, $data);
            } else {
                 $response['data'] = $data;
            }
        }

        return response()->json($response, $code);
    }

    protected function errorResponse(string $message = 'Error', $errors = [], int $code = 400): JsonResponse
    {
        $response = [
            'status' => 'failed',
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function validationErrorResponse($validator, string $message = 'Validasi gagal'): JsonResponse
    {
        return $this->errorResponse($message, $validator->errors(), 422);
    }
}
