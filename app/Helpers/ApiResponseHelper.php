<?php

if (!function_exists('success_response')) {
    /**
     * Success response with or without pagination.
     *
     * @param mixed $data
     * @param bool $pagination
     * @param string $message
     * @param int $responseCode
     * @return \Illuminate\Http\JsonResponse
     */
    function success_response(mixed $data, bool $pagination = false, string $message = 'success', int $responseCode = 200)
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data,
        ];

        if ($pagination) {
            $response['meta'] = [
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
            ];
        }

        return response()->json($response, $responseCode);
    }
}

if (!function_exists('error_response')) {
    /**
     * Error response for client-side errors.
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    function error_response(string $message, int $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => []
        ], $code);
    }
}

if (!function_exists('server_error')) {
    /**
     * Server error logging and response.
     *
     * @param array $e
     * @return \Illuminate\Http\JsonResponse
     */
    function server_error(array $e = [])
    {
        // if (env('APP_ENV') != 'local') {
        //     Log::channel('slack')->error('Server Error', $e ?: ['message' => 'No details available']);
        // }

        return response()->json([
            'status' => false,
            'message' => 'Something Went Wrong',
            'data' => [],
        ], 500);
    }
}

if (!function_exists('validation_error')) {
    /**
     * Validation error response.
     *
     * @param string $message
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function validation_error(string $message, $errors = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $errors ?? 'Validation failed',
            'data' => []
        ], 422);
    }
}
