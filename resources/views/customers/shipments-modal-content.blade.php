<p>{!! $shipmentsContent !!}</p>
<table class="table">
    <thead>
        <tr>
            <th>Shipment ID</th>
            <th>Order ID</th>
            <th>Warehouse (Origin)</th>
            <th>Customer Name</th>
            <th>Location Name</th>
            <th>Delivery Location</th>
            <th>Shipment Contact No</th>
            <th>Shipment/Delivery Date</th>
            <th>Delivery Window</th>
            <th>Item Quantity</th>
            <th>Forklift</th>
            <th>Shipment Notes</th>
            <th>Status</th>
            <th>View</th>
            <!-- ... Add more table headers if needed ... -->
        </tr>
    </thead>
    <tbody>
        <!-- Loop through shipments data and populate table rows -->
        @forelse ($shipmentsData as $shipment)
        <tr>
            <td>{{ $shipment->id }}</td>
            <td>{{ $shipment->order_id }}</td>
            <td>{{ $shipment->warehouse_name }}</td>
            <td>{{ $shipment->customer->name }}</td>
            <td>{{ $shipment->ShipmentCustomerDeliveryLocations->delivery_location_name }}</td>
            <td>{{ concatenateAddress($shipment->shipmentCustomerDeliveryLocations) }}</td>
            <td>{{ $shipment->ShipmentCustomerDeliveryLocations->delivery_contact_phone }}</td>
            <td>
                {{ date('m/d/Y', strtotime($shipment->route_Date)) }}
            </td>
            <td>
                @php
                $timeArray = $shipment->props_delivery_window;
                if (isset($timeArray[0]['start']) && isset($timeArray[0]['end'])) {
                $startTime = new DateTime($timeArray[0]['start']);
                $endTime = new DateTime($timeArray[0]['end']);
                echo $startTime->format('h:i A') . ' - ' . $endTime->format('h:i A');
                }
                @endphp
            </td>
            <td>Item Quantity static</td>
            <td>
                {{ isset($shipment->fork_lift) ? 'Yes' : 'No' }}
            </td>
            <td>
                @php
                $notesArray = $shipment->props_notes;
                @endphp
                @isset($notesArray[0]['text'])
                {{ $notesArray[0]['text'] }}
                @endisset
            </td>
            <td>{{ $shipment->shipment_status }}</td>
            <td>
                <a href="{{ route('customers.viewshipment', $shipment->id) }}" class="btn btn-sm btn-primary">View</a>
            </td>
            <!-- ... Populate additional table cells ... -->
        </tr>
        @empty
        <!-- This block will be executed if no records are found -->
        <tr>
            <td colspan="15">No records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
