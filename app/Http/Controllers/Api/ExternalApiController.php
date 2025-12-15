<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch users from external API (JSONPlaceholder).
     */
    public function getUsers(): JsonResponse
    {
        try {
            $response = Http::timeout(10)->get('https://jsonplaceholder.typicode.com/users');

            if ($response->failed()) {
                Log::error('External API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return $this->errorResponse(
                    'Failed to fetch users from external API',
                    $response->status()
                );
            }

            $users = $response->json();

            return $this->successResponse([
                'users' => $users,
                'count' => count($users),
                'source' => 'https://jsonplaceholder.typicode.com/users',
            ], 'External users retrieved successfully');

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('External API connection error', [
                'message' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'Unable to connect to external API',
                503
            );

        } catch (\Exception $e) {
            Log::error('External API error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverErrorResponse('An error occurred while fetching external users');
        }
    }
}
