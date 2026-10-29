<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .address-content-table th,
        .address-content-table td {
            border: 0;
            padding: 4px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #9ab476;
        }



        .lading-bill table.table thead th {
            text-transform: uppercase;
        }

        .address-content {
            display: flex;
            justify-content: space-between;
        }

        .address-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .address-content ul li {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <div class="top-bar">
        <h2 class="top-heading text-center"><b>Bill of Lading</b></h2>
    </div>

    <table class="table address-content-table ">
        <tbody>
            <tr>
                <td>
                    <div class="address-content">
                        <img src="assets/images/logo/logo.png" alt="logo" width="200">
                        <div class="d-flex justify-content-between">
                            <address class="mt-3">
                                <strong>Bethel Farms LLLP.</strong><br>
                                8780 NW Bethel Farms Road<br>
                                Arcadia, FL, 34266<br>
                            </address>
                        </div>
                    <div>
                </td>
                <td>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <b>Customer ID:</b>
                                </td>
                                <td>
                                    {{ isset($shipment->customer->customer_id) ? ($shipment->customer->customer_id) : 'N/A' }}
                                </td>
                            <tr>
                                <td>
                                    <b>Ship Date:</b>
                                </td>
                                <td>
                                    {{ @$shipment->route_Date }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Order Date:</b>
                                </td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Route Number:</b>
                                </td>
                                <td>{{ @$shipmentDeliveries->routes->id }}</td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Shipment Number:</b>
                                </td>
                                <td>{{ @$shipment->order_id }}</td>
                            </tr>

                            <tr>
                                <td>
                                    <b>Driver:</b>
                                </td>
                                <td>{{ isset($shipmentDeliveries->routes->driver->name) ? $shipmentDeliveries->routes->driver->name : 'N/A' }}</td>
                            </tr>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th valign="middle" align="center">Bill To</th>
                <th valign="middle" align="center">Ship To</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td valign="middle" align="left">
                    <address class="mt-3">
                        {{concatenateAddress($shipment->shipmentCustomerDeliveryLocations)}}
                    </address>
                </td>
                <td valign="middle" align="left">
                    <address class="mt-3">
                        {{concatenateAddress($shipment->shipmentCustomerDeliveryLocations)}}
                    </address>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th valign="middle" align="center">Contact</th>
                <th valign="middle" align="center">Ship Via</th>
                <th valign="middle" align="center">Fob Point</th>
                <th valign="middle" align="center">Warehouse</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td valign="middle" align="left">{{ @$shipment->warehouse->primary_phone_no }}</td>
                <td valign="middle" align="left">Delivery</td>
                <td valign="middle" align="left"></td>
                <td valign="middle" align="left">{{ @$shipment->warehouse->name }}</td>
            </tr>
        </tbody>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th valign="middle" align="center">So Type</th>
                <th valign="middle" align="center">So Number</th>
                <th valign="middle" align="center">Customer P.O. NO.</th>
                <th valign="middle" align="center">Terms</th>
            </tr>
        </thead>
            <tbody>
                <tr>
                    <td valign="middle" align="left">SO</td>
                    <td valign="middle" align="left">1507435</td>
                    <td valign="middle" align="left"></td>
                    <td valign="middle" align="left">COD</td>
                </tr>
            </tbody>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th valign="middle" align="center">No.</th>
                <th valign="middle" align="center">Item</th>
                <th valign="middle" align="center">Qty.</th>
                <th valign="middle" align="center">Uom</th>
                <th valign="middle" align="center">Qty. Shipped</th>
                <th valign="middle" align="center">Qty. B/O</th>
            </tr>
        </thead>
        <tbody>
            @php
            $key = 0;
            $totalQuantity = 0;
            $totalUnitWait = 0;
            @endphp
            @foreach($shipment->shipmentProps as $key=>$item)
            @php
            $totalQuantity += $item->quantity;
            $totalUnitWait += $item->unit_weight;

            @endphp
            <tr>
                <td valign="middle" align="left">{{ @$key+1 }}</td>
                <td valign="middle" align="left">{{ @$item->item_id }}</td>
                <td valign="middle" align="left">{{ @$item->quantity }}</td>
                <td valign="middle" align="left">{{ @$item->unit_type }}</td>
                <td valign="middle" align="left">{{ @$item->unit_weight }}</td>
                <td valign="middle" align="left">0.00</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" valign="middle" align="left"></td>
                <td valign="middle" align="left"><b>Total Qty:</b></td>
                <td colspan="2" valign="middle" align="left"></td>
                <td valign="middle" align="left"><b>{{@$totalQuantity}}</b> </td>

            </tr>
            <tr>
                <td colspan="2" valign="middle" align="left"></td>
                <td valign="middle" align="left"><b>Total Weight (LB):</b></td>
                <td colspan="2" valign="middle" align="left"></td>
                <td valign="middle" align="left"><b>{{ @$totalUnitWait }}</b> </td>
            </tr>
            <tr>
                <td colspan="2" valign="middle" align="left"></td>
                <td valign="middle" align="left"><b>Total Volume (M3):</b></td>
                <td colspan="2" valign="middle" align="left"></td>
                <td valign="middle" align="left"><b>0</b> </td>
            </tr>
        </tbody>
    </table>
</body>
</html>