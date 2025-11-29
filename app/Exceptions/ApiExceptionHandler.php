<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    public function handle(Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof ValidationException => $this->handleValidation($e),
            $e instanceof ModelNotFoundException => $this->handleModelNotFound($e),
            $e instanceof NotFoundHttpException => $this->handleNotFound(),
            $e instanceof HttpException => $this->handleHttp($e),
            default => $this->handleGeneric($e),
        };
    }

    private function handleValidation(ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    }

    private function handleModelNotFound(ModelNotFoundException $e): JsonResponse
    {
        $model = class_basename($e->getModel());
        
        return response()->json([
            'success' => false,
            'message' => "{$model} not found",
        ], 404);
    }

    private function handleNotFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint not found',
        ], 404);
    }

    private function handleHttp(HttpException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage() ?: 'HTTP Error',
        ], $e->getStatusCode());
    }

    private function handleGeneric(Throwable $e): JsonResponse
    {
        $message = app()->environment('production')
            ? 'An unexpected error occurred'
            : $e->getMessage();

        return response()->json([
            'success' => false,
            'message' => $message,
            'exception' => app()->environment('local') ? get_class($e) : null,
        ], 500);
    }
}

