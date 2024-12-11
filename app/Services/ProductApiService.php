<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class ProductApiService
{
    /**
     * Call the external API to retrieve products.
     *
     * @return array|string
     */
    public function getProducts()
    {
        $baseUrl = 'https://app.kazisafe.com/v1/';
        $endpoint = 'produit/showall';
        $token = 'eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MzM5MTE3MTYsImlhdCI6MTczMzkwOTkxNiwiaXNzIjoiaHR0cHM6Ly9hcHAua2F6aXNhZmUuY29tL3YxL2F1dGgvYXV0aDAvd2ViL3NpZ25pbiIsInN1YiI6ImMyNTQ3ZGM4ZDEyNDRlOTQ4NGI1MzQ0YjIwYWVjNGFlIiwianRpIjoiNDg2MDhmNjMtNGRkMS00ODA0LTk3YTItZDBlNGEzMDRlZmIxIn0.aSxm4fUuntfIr5IXa2VPqk_Fnb5dTf-RTCVKGCiESr-HRzUL3PqNqa-wBXtf-85VggCnZQjHSbHuCfGpDyXsKw';

        try {
            // Make the GET request
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withToken($token)
                ->accept('application/json')
                ->get("{$baseUrl}{$endpoint}");

            \Log::info('Request URL', ['url' => "{$baseUrl}{$endpoint}"]);
            \Log::info('Response Status', ['status' => $response->status()]);
            \Log::info('Response Headers', ['headers' => $response->headers()]);
            \Log::info('Response Body', ['body' => $response->body()]);

            // Check if the response is successful
            if ($response->successful()) {
                return $response->json();
            }

            // Handle client or server errors
            if ($response->clientError() || $response->serverError()) {
                \Log::error('API Error: ', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                ]);

                return [
                    'status' => 'error',
                    'message' => 'API request failed with status: ' . $response->status(),
                    'details' => $response->json(),
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Connection Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Connection timeout or unreachable server.',
            ];
        } catch (Exception $e) {
            \Log::error('General Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ];
        }
    }


}



