@props(['shipments', 'showCheckbox' => false, 'type' => 'delivery'])

<div class="table-responsive">
    <table class="table" id="shipmenttable-{{ $type }}">
        <thead>
            <tr>
                @if($showCheckbox)
                <th scope="col">
                    <div class="checkbox-checked">
                        <label class="d-block" for="chk-ani4-{{ $type }}"></label>
                        <input class="checkbox_animated checkAll" id="chk-ani4-{{ $type }}" type="checkbox" name="select_all" value="1">
                    </div>
                </th>
                @endif
                <th scope="col">Shipment ID</th>
                <th scope="col">Order ID</th>
                <th scope="col">Warehouse (Origin)</th>
                <th scope="col">Customer Name</th>
                <th scope="col">Location Name</th>
                <th scope="col">Delivery Location</th>
                <th scope="col">Shipment Contact No</th>
                <th scope="col">Shipment/{{ ucfirst($type) }} Date</th>
                <th scope="col">{{ ucfirst($type) }} Window</th>
                <th scope="col">Item Quantity</th>
                <th scope="col">Forklift</th>
                <th scope="col">Shipment Notes</th>
                <th scope="col">Status</th>
                <th scope="col">View</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shipments as $shipment)
                <tr>
                    @if($showCheckbox)
                    <td>
                        <div class="checkbox-checked">
                            <input class="checkbox_animated" type="checkbox" name="shipment_ids[]" value="{{ $shipment->id }}">
                        </div>
                    </td>
                    @endif
                    <td>{{ $shipment->id }}</td>
                    <td>{{ $shipment->order_id ?? 'N/A' }}</td>
                    <td>{{ $shipment->warehouse->name ?? 'N/A' }}</td>
                    <td>{{ $shipment->customer->name ?? 'N/A' }}</td>
                    <td>{{ optional($shipment->shipmentCustomerDeliveryLocations)->delivery_location_name ?? 'N/A' }}</td>
                    <td>{{ concatenateAddress($shipment->shipmentCustomerDeliveryLocations) }}</td>
                    <td>{{ optional($shipment->shipmentCustomerDeliveryLocations)->delivery_contact_phone ?? 'N/A' }}</td>
                    <td>{{ $shipment->route_Date ? date('m/d/Y', strtotime($shipment->route_Date)) : 'N/A' }}</td>
                    
                    <x-shipment-delivery-window :deliveryWindow="$shipment->props_delivery_window" />
                    
                    <x-shipment-quantity :shipment="$shipment" />
                    
                    <td>
                        <span class="txt-{{ isset($shipment->fork_lift) && $shipment->fork_lift ? 'success' : 'danger' }}">
                            {{ isset($shipment->fork_lift) && $shipment->fork_lift ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    
                    <x-shipment-notes :notes="$shipment->props_notes" :shipmentId="$shipment->id" />
                    
                    <x-shipment-status 
                        :status="optional($shipment->shipmentDeliveries)->delivery_status"
                        :deletedAt="$shipment->deleted_at"
                        :deletedByUser="$shipment->deletedByUser"
                    />
                    
                    <td>
                        <a href="{{ route('customers.viewshipment', $shipment->id) }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#shipmenttable-{{ $type }}').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search shipments..."
        }
    });

    // Handle select all checkbox
    $('#chk-ani4-{{ $type }}').on('change', function() {
        $('input[name="shipment_ids[]"]').prop('checked', $(this).prop('checked'));
    });
});
</script>
@endpush 