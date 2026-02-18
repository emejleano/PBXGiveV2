<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * Base API Controller
 * supaya respone seragama jika berhasil atau gagal
 * Semua API controller harus extend class ini.
 * Berisi helper methods untuk standar JSON responses.
 */
class BaseApiController extends Controller
{
    /**
     * Return success response.
     */
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Return created response (201).
     */
    protected function created(mixed $data = null, string $message = 'Created successfully')
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Return error response.
     */
    protected function error(string $message = 'Error', int $code = 400, mixed $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return not found response (404).
     */
    protected function notFound(string $message = 'Resource not found')
    {
        return $this->error($message, 404);
    }

    /**
     * Return unauthorized response (403).
     */
    protected function forbidden(string $message = 'Forbidden')
    {
        return $this->error($message, 403);
    }
}
