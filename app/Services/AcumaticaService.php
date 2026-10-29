<?php

namespace App\Services;

use App\Facades\AcumeticaFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Response;
use Throwable;

class AcumaticaService
{
    /**
     * Get routes from Acumatica
     *
     * @param array $params
     * @return array
     */
    public function getRoutes(array $params): array
    {
        try {
            $response = AcumeticaFacade::getRoutes(
                $params['select'] ?? null,
                $params['filter'] ?? null,
                $params['expand'] ?? null,
                $params['custom'] ?? null
            );

            return $this->handleResponse($response, 'Routes');
        } catch (Throwable $e) {
            Log::error('Error in AcumaticaService@getRoutes: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get shipments from Acumatica
     *
     * @param array $params
     * @return array
     */
    public function getShipments(array $params): array
    {
        try {
            $response = AcumeticaFacade::getRoutes(
                $params['select'] ?? null,
                $params['filter'] ?? null,
                $params['expand'] ?? null,
                $params['custom'] ?? null
            );

            return $this->handleResponse($response, 'Shipments');
        } catch (Throwable $e) {
            Log::error('Error in AcumaticaService@getShipments: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete route from Acumatica
     *
     * @param string $id
     * @return array
     */
    public function deleteRoute(string $id): array
    {
        try {
            $response = AcumeticaFacade::deleteRoute($id);
            return $this->handleResponse($response, 'Route', 'deleted');
        } catch (Throwable $e) {
            Log::error('Error in AcumaticaService@deleteRoute: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirm shipment in Acumatica
     *
     * @param string $shipmentId
     * @return array
     */
    public function confirmShipment(string $shipmentId): array
    {
        try {
            $response = AcumeticaFacade::confirmShipment($shipmentId);
            return $this->handleResponse($response, 'Shipment', 'confirmed');
        } catch (Throwable $e) {
            Log::error('Error in AcumaticaService@confirmShipment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get file from Acumatica
     *
     * @param string $shipmentId
     * @param string $filename
     * @return array
     */
    public function getFile(string $shipmentId, string $filename): array
    {
        try {
            $response = AcumeticaFacade::getFile($shipmentId, $filename);

            if ($response->status() >= 200 && $response->status() < 300) {
                return [
                    'success' => true,
                    'data' => $response->body(),
                    'headers' => [
                        'Content-Type' => 'application/octet-stream',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                    ]
                ];
            }

            return $this->handleErrorResponse($response);
        } catch (Throwable $e) {
            Log::error('Error in AcumaticaService@getFile: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove API token
     *
     * @return array
     */
    public function removeToken(): array
    {
        try {
            Session::forget('api_access_token');
            return [
                'success' => true,
                'message' => 'API access token removed successfully'
            ];
        } catch (Throwable $e) {
            Log::error('Error in AcumaticaService@removeToken: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle API response
     *
     * @param mixed $response
     * @param string $type
     * @param string $action
     * @return array
     */
    private function handleResponse($response, string $type, string $action = 'retrieved'): array
    {
        if ($response->status() == 401) {
            return [
                'success' => false,
                'message' => 'Unauthorized. Check API token.',
                'status' => 401
            ];
        }

        if ($response->status() == 404) {
            return [
                'success' => false,
                'message' => "{$type} not found.",
                'status' => 404
            ];
        }

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => "{$type} {$action} successfully.",
                'data' => $response->json(),
                'status' => 200
            ];
        }

        return $this->handleErrorResponse($response);
    }

    /**
     * Handle error response
     *
     * @param mixed $response
     * @return array
     */
    private function handleErrorResponse($response): array
    {
        $errorMessage = 'Failed to process request.';

        if ($response->status() === 400) {
            $errorMessage = 'Bad Request: There was an issue with the data sent.';
        } elseif ($response->status() === 404) {
            $errorMessage = 'Not Found: The requested resource was not found.';
        } elseif ($response->status() === 500) {
            $errorMessage = 'Internal Server Error: Something went wrong on the server.';
        }

        $errorResponse = json_decode($response->body(), true);
        $details = $errorResponse['exceptionMessage'] ?? 
                  $errorResponse['message'] ?? 
                  'An unknown error occurred.';

        return [
            'success' => false,
            'message' => $errorMessage,
            'details' => $details,
            'status' => $response->status()
        ];
    }
}