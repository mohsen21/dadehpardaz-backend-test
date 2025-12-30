<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Return a successful JSON response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data = null, ?string $message = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = ['success' => true];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a created JSON response (201).
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function createdResponse($data = null, ?string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, ?array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a not found JSON response (404).
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Return a single resource response wrapped in standard format.
     *
     * @param mixed $resource
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function resourceResponse($resource, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->successResponse($resource, null, $statusCode);
    }

    /**
     * Return a collection response wrapped in standard format.
     *
     * @param mixed $collection
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function collectionResponse($collection, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->successResponse($collection, null, $statusCode);
    }
}

