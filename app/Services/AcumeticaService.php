<?php

namespace App\Services;

use App\Facades\CurlFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AcumeticaService



{
    protected $baseUrl;
    protected $entityUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL');
        $this->entityUrl = env('API_ENTITY_URL');
    }
    public function deleteShipment($shipmentId)
    {
        // $url = "https://bethelfarms-sb.crestwoodcloud.com/entity/BethelTMS/23.200.001/Shipment/{$shipmentId}";
        // $baseUrl = env('API_BASE_URL');
        // $url = "{$baseUrl}/entity/BethelTMS/23.200.001/Shipment/{$shipmentId}";
        $url = "{$this->entityUrl}/Shipment/{$shipmentId}";

        $response = CurlFacade::sendRequest('DELETE', $url);
        return $response;
    }

    // public function createOrUpdateRoute()
    // {
    //     $url = "{$this->entityUrl}/Route";

    //     $body = [
    //         "RouteCD" => ["value" => "132"],
    //         "RouteID" => ["value" => "cc1082dc-278c-41e3-b697-c594f8561aju"],
    //         "RouteInfo" => [
    //             "Status" => ["value" => ""],
    //             "RouteDate" => ["value" => "2023-12-23T00:00:00-04:00"],
    //             "DriverID" => ["value" => "87e7b9e6-b67b-44d7-9afa-4bd28e8a83aa"]
    //         ]
    //     ];

    //     $response = CurlFacade::sendRequest('PUT', $url, [], $body);
    //     return $response;
    // }
    // public function createOrUpdateRoute(array $body)
    // {
    //     $url = "{$this->entityUrl}/Route";
    //     $response = CurlFacade::sendRequest('PUT', $url, [], $body);
    //     dd(   $response);
        
    //     return $response;
    // }
        public function createOrUpdateRoute(array $body)
    {
        $baseUrl = $this->entityUrl;
        $expand = '$expand=RouteInfo,Tasks';
        $url = "{$baseUrl}/Route?{$expand}";
        $response = CurlFacade::sendRequest('PUT', $url, [], $body);
        return $response;
    }
        public function getRouteById($routeId)
        {
            $url = "{$this->entityUrl}/Route/{$routeId}?\$expand=RouteInfo,Tasks";
            
            $response = CurlFacade::sendRequest('GET', $url);

            if ($response->failed()) {
                Log::error('Failed to fetch route from Acumatica', ['routeId' => $routeId, 'response' => $response->body()]);
                return null;
            }

            return $response->json();
        }



    public function createOrUpdateShipment(array $body)
    {
        $url = "{$this->entityUrl}/Shipment";
        $response = CurlFacade::sendRequest('PUT', $url, [], $body);        
        return $response;
    }
    // public function createOrUpdateShipment($select = null, $filter = null, $expand = null, $custom = null)
    // {
    //     $url = "{$this->entityUrl}/Shipment";
    //     $queryParams = [];
    //     if ($select) $queryParams['$select'] = $select;
    //     if ($filter) $queryParams['$filter'] = $filter;
    //     if ($expand) $queryParams['$expand'] = $expand;
    //     if ($custom) $queryParams['$custom'] = $custom;
        
    //     if (!empty($queryParams)) {
    //         $url .= '?' . http_build_query($queryParams);
    //     }
    //         $body = [
    //         "RouteCD" => [
    //             "value" => "86949726"
    //         ],
    //         "RouteID" => [
    //             "value" => "1f13737e-c632-4e4d-8f88-694d97a26ffa"
    //         ],
    //         "RouteInfo" => [
    //             "Status" => [
    //                 "value" => "0"
    //             ],
    //             "RouteDate" => [
    //                 "value" => "2023-12-23T00:00:00-04:00"
    //             ],
    //             "DriverID" => [
    //                 "value" => "87e7b9e6-b67b-44d7-9afa-4bd28e8a83aa"
    //                 ]
    //                 ]
    //             ];
    //     $response = CurlFacade::sendRequest('PUT', $url, [], $body);        
    //     return $response;
    // }

    public function deleteRoute($routeId)
    {
        $url = "{$this->entityUrl}/Route/{$routeId}";
        $response = CurlFacade::sendRequest('DELETE', $url);
        return $response;
    }

    // public function confirmShipment($shipmentId)
    // {
    // $url = "{$this->entityUrl}/Shipment/ConfirmShipment";
    //     // Define static data as per your example
    //     $body = [
    //         "entity" => [
    //             "ControlQty" => [
    //                 "value" => 0,
    //                 "error" => "test"
    //             ],
    //             "CreatedDateTime" => [
    //                 "value" => "2025-02-04T07:21:48.351Z",
    //                 "error" => "test"
    //             ],
    //             "CustomerID" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "Hold" => [
    //                 "value" => true,
    //                 "error" => "test"
    //             ],
    //             "LastModifiedDateTime" => [
    //                 "value" => "2025-02-04T07:21:48.351Z",
    //                 "error" => "test"
    //             ],
    //             "LocationID" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "Operation" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "Owner" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "ShipmentDate" => [
    //                 "value" => "2025-02-04T07:21:48.351Z",
    //                 "error" => "test"
    //             ],
    //             "ShipmentNbr" => [
    //                 "value" => $shipmentId,  
    //                 "error" => "test"
    //             ],
    //             "Status" => [
    //                 "value" => "Confirmed",
    //                 "error" => "test"
    //             ],
    //             "ToWarehouseID" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "Type" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "WarehouseID" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "WorkgroupID" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "Loaded" => [
    //                 "value" => true,
    //                 "error" => "test"
    //             ],
    //             "DayOfWeek" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "OverideDelivery" => [
    //                 "value" => true,
    //                 "error" => "test"
    //             ],
    //             "ProjectedStartTime" => [
    //                 "value" => "2025-02-04T07:21:48.351Z",
    //                 "error" => "test"
    //             ],
    //             "ProjectedEndTime" => [
    //                 "value" => "2025-02-04T07:21:48.351Z",
    //                 "error" => "test"
    //             ],
    //             "APDocType" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "APRefNbr" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "ARDocType" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "ARRefNbr" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "CarrierDocType" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "CarrierRefNbr" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ],
    //             "Details" => [
    //                 [
    //                     "Description" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "ExpirationDate" => [
    //                         "value" => "2025-02-04T07:21:48.351Z",
    //                         "error" => "test"
    //                     ],
    //                     "FreeItem" => [
    //                         "value" => true,
    //                         "error" => "test"
    //                     ],
    //                     "InventoryID" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "LineNbr" => [
    //                         "value" => 0,
    //                         "error" => "test"
    //                     ],
    //                     "LocationID" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "OpenQty" => [
    //                         "value" => 0,
    //                         "error" => "test"
    //                     ],
    //                     "OrderedQty" => [
    //                         "value" => 0,
    //                         "error" => "test"
    //                     ],
    //                     "OrderNbr" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "OrderType" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "OriginalQty" => [
    //                         "value" => 0,
    //                         "error" => "test"
    //                     ],
    //                     "ShippedQty" => [
    //                         "value" => 0,
    //                         "error" => "test"
    //                     ],
    //                     "PackUOM" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "PackQty" => [
    //                         "value" => 0,
    //                         "error" => "test"
    //                     ],
    //                     "UOM" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "WarehouseID" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ]
    //                 ]
    //             ],
    //             "ShippingSettings" => [
    //                 "ShipToAddressOverride" => [
    //                     "value" => true,
    //                     "error" => "test"
    //                 ],
    //                 "ShipToContactOverride" => [
    //                     "value" => true,
    //                     "error" => "test"
    //                 ],
    //                 "Validated" => [
    //                     "value" => true,
    //                     "error" => "test"
    //                 ],
    //                 "ShipToAddress" => [
    //                     "AddressLine1" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "AddressLine2" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "City" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "Country" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "PostalCode" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "State" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ]
    //                 ],
    //                 "ShipToContact" => [
    //                     "Attention" => [
    //                         "value" => "test",              
    //                         "error" => "test"
    //                     ],
    //                     "BusinessName" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "Email" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ],
    //                     "Phone1" => [
    //                         "value" => "test",
    //                         "error" => "test"
    //                     ]
    //                 ]
    //             ],  
    //             "Description" => [
    //                 "value" => "test",
    //                 "error" => "test"
    //             ]
    //         ]
    //     ];

    //     $response = CurlFacade::sendRequest('POST', $url, [], $body);

    //         return $response;

    // }
    // public function getFile($shipmentId, $filename)
    // {
    //     $url = "{$this->baseUrl}/entity/BethelTMS/23.200.001/Shipment/{$shipmentId}/files/{$filename}";
    //     $response = CurlFacade::sendRequest('PUT', $url, []);
    //     return $response;


    // }

    //  public function uploadShipmentFile($shipmentId, $filePath)
    // {
    //     if (!file_exists($filePath)) {
    //         \Log::error('File does not exist', ['filePath' => $filePath]);
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'File not found'
    //         ], 404);
    //     }

    //     $filename = basename($filePath);
    //     $url = "{$this->entityUrl}/Shipment/{$shipmentId}/files/{$filename}";
    //     $file = new \CURLFile($filePath, mime_content_type($filePath), $filename);
    //     $body = [
    //         'file' => $file,
    //     ];
        
    //     \Log::info('Uploading shipment file via CurlFacade', [
    //         'url'        => $url,
    //         'filePath'   => $filePath,
    //         'shipmentId' => $shipmentId,
    //         'filename'   => $filename
    //     ]);
    //     $response = CurlFacade::sendRequest('PUT', $url,[], $body);        
    //         \Log::info('File upload response', [
    //             'status' => $response->status(),
    //             'body'   => $response->body()
    //         ]);

    //         return $response;
    // }

    public function uploadShipmentFile($shipmentId, $filePath)
    {
        if (!file_exists(filename: $filePath)) {
            \Log::error('File does not exist', ['filePath' => $filePath]);
            return response()->json([
                'status'  => false,
                'message' => 'File not found'
            ], 404);
        }
        $fullFilename = basename(path: $filePath); 
        $cleanFilename = preg_replace('/^\d+_/', '', $fullFilename);
        
        $url = "{$this->entityUrl}/Shipment/{$shipmentId}/files/{$fullFilename}";        
        $file = new \CURLFile($filePath, mime_content_type($filePath), $cleanFilename);

        $body = [
            'file' => $file,
        ];

        \Log::info('Uploading shipment file via CurlFacade', [
            'url'        => $url,
            'filePath'   => $filePath,
            'shipmentId' => $shipmentId,
            'filename'   => $cleanFilename
        ]);

        $response = CurlFacade::sendRequest('PUT', $url, [], $body);
        // \Log::info('File upload response', [
        //     'status' => $response->status(),
        //     'body'   => $response->body()
        // ]);

        return $response;
    }


    public function correctShipment($shipmentId, $correctionData)
        {
            $url = "{$this->entityUrl}/Shipment/CorrectShipment";

        $body = [
            "entity" => [
                "ControlQty" => ["value" => 0, "error" => "test"],
                "CreatedDateTime" => ["value" => now()->toIso8601test(), "error" => "test"],
                "CustomerID" => ["value" => "test", "error" => "test"],
                "Hold" => ["value" => true, "error" => "test"],
                "LastModifiedDateTime" => ["value" => now()->toIso8601test(), "error" => "test"],
                "LocationID" => ["value" => "test", "error" => "test"],
                "Operation" => ["value" => "test", "error" => "test"],
                "Owner" => ["value" => "test", "error" => "test"],
                "ShipmentDate" => ["value" => now()->toIso8601test(), "error" => "test"],
                "ShipmentNbr" => ["value" => "test", "error" => "test"],
                "Status" => ["value" => "test", "error" => "test"],
                "ToWarehouseID" => ["value" => "test", "error" => "test"],
                "Type" => ["value" => "test", "error" => "test"],
                "WarehouseID" => ["value" => "test", "error" => "test"],
                "WorkgroupID" => ["value" => "test", "error" => "test"],
                "Loaded" => ["value" => true, "error" => "test"],
                "DayOfWeek" => ["value" => "test", "error" => "test"],
                "OverideDelivery" => ["value" => true, "error" => "test"],
                "ProjectedStartTime" => ["value" => now()->toIso8601test(), "error" => "test"],
                "ProjectedEndTime" => ["value" => now()->toIso8601test(), "error" => "test"],
                "APDocType" => ["value" => "test", "error" => "test"],
                "APRefNbr" => ["value" => "test", "error" => "test"],
                "ARDocType" => ["value" => "test", "error" => "test"],
                "ARRefNbr" => ["value" => "test", "error" => "test"],
                "CarrierDocType" => ["value" => "test", "error" => "test"],
                "CarrierRefNbr" => ["value" => "test", "error" => "test"],
                "Details" => [
                    [
                        "Description" => ["value" => "test", "error" => "test"],
                        "ExpirationDate" => ["value" => now()->toIso8601test(), "error" => "test"],
                        "FreeItem" => ["value" => true, "error" => "test"],
                        "InventoryID" => ["value" => "test", "error" => "test"],
                        "LineNbr" => ["value" => 0, "error" => "test"],
                        "LocationID" => ["value" => "test", "error" => "test"],
                        "OpenQty" => ["value" => 0, "error" => "test"],
                        "OrderedQty" => ["value" => 0, "error" => "test"],
                        "OrderNbr" => ["value" => "test", "error" => "test"],
                        "OrderType" => ["value" => "test", "error" => "test"],
                        "OriginalQty" => ["value" => 0, "error" => "test"],
                        "ShippedQty" => ["value" => 0, "error" => "test"],
                        "PackUOM" => ["value" => "test", "error" => "test"],
                        "PackQty" => ["value" => 0, "error" => "test"],
                        "UOM" => ["value" => "test", "error" => "test"],
                        "WarehouseID" => ["value" => "test", "error" => "test"],
                    ]
                ],
                "ShippingSettings" => [
                    "ShipToAddressOverride" => ["value" => true, "error" => "test"],
                    "ShipToContactOverride" => ["value" => true, "error" => "test"],
                    "Validated" => ["value" => true, "error" => "test"],
                    "ShipToAddress" => [
                        "AddressLine1" => ["value" => "test", "error" => "test"],
                        "AddressLine2" => ["value" => "test", "error" => "test"],
                        "City" => ["value" => "test", "error" => "test"],
                        "Country" => ["value" => "test", "error" => "test"],
                        "PostalCode" => ["value" => "test", "error" => "test"],
                        "State" => ["value" => "test", "error" => "test"],
                    ],
                    "ShipToContact" => [
                        "Attention" => ["value" => "test", "error" => "test"],
                        "BusinessName" => ["value" => "test", "error" => "test"],
                        "Email" => ["value" => "test", "error" => "test"],
                        "Phone1" => ["value" => "test", "error" => "test"],
                    ]
                ],
                "Description" => ["value" => "test", "error" => "test"],
            ]
        ];

        // Send request to Accumatica API
     $response = CurlFacade::sendRequest('POST', $url, [], $body);
    return $response;
    }

    //  public  function markShipmentLoaded($shipmentId)
    // {
    //     $url = "{$this->entityUrl}/Shipment/MarkLoaded";
    //     $body = [
    //         "entity" => [
    //             "ControlQty" => ["value" => 0, "error" => "test"],
    //             "CreatedDateTime" => ["value" => "2025-02-04T09:56:26.573Z", "error" => "test"],
    //             "CustomerID" => ["value" => "test", "error" => "test"],
    //             "Hold" => ["value" => true, "error" => "test"],
    //             "LastModifiedDateTime" => ["value" => "2025-02-04T09:56:26.573Z", "error" => "test"],
    //             "LocationID" => ["value" => "test", "error" => "test"],
    //             "Operation" => ["value" => "test", "error" => "test"],
    //             "Owner" => ["value" => "test", "error" => "test"],
    //             "ShipmentDate" => ["value" => "2025-02-04T09:56:26.573Z", "error" => "test"],
    //             "ShipmentNbr" => ["value" => $shipmentId, "error" => "test"],
    //             "Status" => ["value" => "test", "error" => "test"],
    //             "ToWarehouseID" => ["value" => "test", "error" => "test"],
    //             "Type" => ["value" => "test", "error" => "test"],
    //             "WarehouseID" => ["value" => "test", "error" => "test"],
    //             "WorkgroupID" => ["value" => "test", "error" => "test"],
    //             "Loaded" => ["value" => true, "error" => "test"],
    //             "DayOfWeek" => ["value" => "test", "error" => "test"],
    //             "OverideDelivery" => ["value" => true, "error" => "test"],
    //             "ProjectedStartTime" => ["value" => "2025-02-04T09:56:26.573Z", "error" => "test"],
    //             "ProjectedEndTime" => ["value" => "2025-02-04T09:56:26.573Z", "error" => "test"],
    //             "Details" => [
    //                 [
    //                     "Description" => ["value" => "test", "error" => "test"],
    //                     "ExpirationDate" => ["value" => "2025-02-04T09:56:26.573Z", "error" => "test"],
    //                     "FreeItem" => ["value" => true, "error" => "test"],
    //                     "InventoryID" => ["value" => "test", "error" => "test"],
    //                     "LineNbr" => ["value" => 0, "error" => "test"],
    //                     "LocationID" => ["value" => "test", "error" => "test"],
    //                     "OpenQty" => ["value" => 0, "error" => "test"],
    //                     "OrderedQty" => ["value" => 0, "error" => "test"],
    //                     "OrderNbr" => ["value" => "test", "error" => "test"],
    //                     "OrderType" => ["value" => "test", "error" => "test"],
    //                     "OriginalQty" => ["value" => 0, "error" => "test"],
    //                     "ShippedQty" => ["value" => 0, "error" => "test"],
    //                     "PackUOM" => ["value" => "test", "error" => "test"],
    //                     "PackQty" => ["value" => 0, "error" => "test"],
    //                     "UOM" => ["value" => "test", "error" => "test"],
    //                     "WarehouseID" => ["value" => "test", "error" => "test"]
    //                 ]
    //             ],
    //             "ShippingSettings" => [
    //                 "ShipToAddressOverride" => ["value" => true, "error" => "test"],
    //                 "ShipToContactOverride" => ["value" => true, "error" => "test"],
    //                 "Validated" => ["value" => true, "error" => "test"],
    //                 "ShipToAddress" => [
    //                     "AddressLine1" => ["value" => "test", "error" => "test"],
    //                     "AddressLine2" => ["value" => "test", "error" => "test"],
    //                     "City" => ["value" => "test", "error" => "test"],
    //                     "Country" => ["value" => "test", "error" => "test"],
    //                     "PostalCode" => ["value" => "test", "error" => "test"],
    //                     "State" => ["value" => "test", "error" => "test"]
    //                 ],
    //                 "ShipToContact" => [
    //                     "Attention" => ["value" => "test", "error" => "test"],
    //                     "BusinessName" => ["value" => "test", "error" => "test"],
    //                     "Email" => ["value" => "test", "error" => "test"],
    //                     "Phone1" => ["value" => "test", "error" => "test"]
    //                 ]
    //             ],
    //             "Description" => ["value" => "test", "error" => "test"]
    //         ]
    //     ];

    //     $response = CurlFacade::sendRequest('POST', $url, [], $body);
    //     return $response;
    // }
      public  function markShipmentNotLoaded($shipmentId)
    {
        $url = "{$this->entityUrl}/Shipment/MarkNotLoaded";
          $body = [
            "entity" => [
                "ControlQty" => ["value" => 0, "error" => "test"],
                "CreatedDateTime" => ["value" => now()->toISOtest(), "error" => "test"],
                "CustomerID" => ["value" => "test", "error" => "test"],
                "Hold" => ["value" => true, "error" => "test"],
                "LastModifiedDateTime" => ["value" => now()->toISOtest(), "error" => "test"],
                "LocationID" => ["value" => "test", "error" => "test"],
                "Operation" => ["value" => "test", "error" => "test"],
                "Owner" => ["value" => "test", "error" => "test"],
                "ShipmentDate" => ["value" => now()->toISOtest(), "error" => "test"],
                "ShipmentNbr" => ["value" => $shipmentId, "error" => "test"],
                "Status" => ["value" => "test", "error" => "test"],
                "ToWarehouseID" => ["value" => "test", "error" => "test"],
                "Type" => ["value" => "test", "error" => "test"],
                "WarehouseID" => ["value" => "test", "error" => "test"],
                "WorkgroupID" => ["value" => "test", "error" => "test"],
                "Loaded" => ["value" => false, "error" => "test"],
                "DayOfWeek" => ["value" => "test", "error" => "test"],
                "OverideDelivery" => ["value" => true, "error" => "test"],
                "ProjectedStartTime" => ["value" => now()->toISOtest(), "error" => "test"],
                "ProjectedEndTime" => ["value" => now()->toISOtest(), "error" => "test"],
                "APDocType" => ["value" => "test", "error" => "test"],
                "APRefNbr" => ["value" => "test", "error" => "test"],
                "ARDocType" => ["value" => "test", "error" => "test"],
                "ARRefNbr" => ["value" => "test", "error" => "test"],
                "CarrierDocType" => ["value" => "test", "error" => "test"],
                "CarrierRefNbr" => ["value" => "test", "error" => "test"],
                "Details" => [
                    [
                        "Description" => ["value" => "test", "error" => "test"],
                        "ExpirationDate" => ["value" => now()->toISOtest(), "error" => "test"],
                        "FreeItem" => ["value" => true, "error" => "test"],
                        "InventoryID" => ["value" => "test", "error" => "test"],
                        "LineNbr" => ["value" => 0, "error" => "test"],
                        "LocationID" => ["value" => "test", "error" => "test"],
                        "OpenQty" => ["value" => 0, "error" => "test"],
                        "OrderedQty" => ["value" => 0, "error" => "test"],
                        "OrderNbr" => ["value" => "test", "error" => "test"],
                        "OrderType" => ["value" => "test", "error" => "test"],
                        "OriginalQty" => ["value" => 0, "error" => "test"],
                        "ShippedQty" => ["value" => 0, "error" => "test"],
                        "PackUOM" => ["value" => "test", "error" => "test"],
                        "PackQty" => ["value" => 0, "error" => "test"],
                        "UOM" => ["value" => "test", "error" => "test"],
                        "WarehouseID" => ["value" => "test", "error" => "test"]
                    ]
                ],
                "ShippingSettings" => [
                    "ShipToAddressOverride" => ["value" => true, "error" => "test"],
                    "ShipToContactOverride" => ["value" => true, "error" => "test"],
                    "Validated" => ["value" => true, "error" => "test"],
                    "ShipToAddress" => [
                        "AddressLine1" => ["value" => "test", "error" => "test"],
                        "AddressLine2" => ["value" => "test", "error" => "test"],
                        "City" => ["value" => "test", "error" => "test"],
                        "Country" => ["value" => "test", "error" => "test"],
                        "PostalCode" => ["value" => "test", "error" => "test"],
                        "State" => ["value" => "test", "error" => "test"]
                    ],
                    "ShipToContact" => [
                        "Attention" => ["value" => "test", "error" => "test"],
                        "BusinessName" => ["value" => "test", "error" => "test"],
                        "Email" => ["value" => "test", "error" => "test"],
                        "Phone1" => ["value" => "test", "error" => "test"]
                    ]
                ],
                "Description" => ["value" => "test", "error" => "test"]
            ]
        ];

        $response = CurlFacade::sendRequest('POST', $url, [], $body);
        return $response;
    }
public function markShipmentLoaded($shipmentId, array $body)
{
    $url = "{$this->entityUrl}/Shipment/MarkLoaded";
    $response = CurlFacade::sendRequest('POST', $url, [], $body);
    return $response;
}
 public function confirmShipment($shipmentId, array $body)
    {
    $url = "{$this->entityUrl}/Shipment/ConfirmShipment";
        $response = CurlFacade::sendRequest('POST', $url, [], $body);
            return $response;

    }

    //     public function logoutUser(array $body = [])
    // {
    //     $url = "{$this->baseUrl}/entity/auth/logout";
    //     $response = CurlFacade::sendRequest('POST', $url, [], $body);
    //     return $response;
    // }

public function logoutUser(array $body = [])
{
    $url = "{$this->baseUrl}/entity/auth/logout";
    $token = CurlFacade::getToken();
    $body['access_token'] = $token;
    $response = CurlFacade::sendRequest('POST', $url, [], $body);

    return $response;
}

}
