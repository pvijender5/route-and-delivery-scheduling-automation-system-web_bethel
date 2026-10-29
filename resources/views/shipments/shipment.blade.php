<!-- shipment.blade.php -->
@php
    $delivery = in_array("DELIVERY", $shipmentDeliveries->shipment->labels);
    $forkliftIcon = '<svg width="28" height="28" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill="#2f2f3b" stroke="none" d="M6 4v7H4c-1.11 0-2 .89-2 2v4a3 3 0 0 0 3 3a3 3 0 0 0 3-3h2a3 3 0 0 0 3 3a3 3 0 0 0 3-3v-4l-4-9H6m11 1v14h5v-1.5h-3.5V5H17m-9.5.5h3.7l3.3 7.5h-7V5.5M5 15.5A1.5 1.5 0 0 1 6.5 17A1.5 1.5 0 0 1 5 18.5A1.5 1.5 0 0 1 3.5 17A1.5 1.5 0 0 1 5 15.5m8 0a1.5 1.5 0 0 1 1.5 1.5a1.5 1.5 0 0 1-1.5 1.5a1.5 1.5 0 0 1-1.5-1.5a1.5 1.5 0 0 1 1.5-1.5Z"/>
    </svg>';
@endphp

<div class="r-user-profile">
    <div class="card hovercard border-box">
        <div class="info p-0">
            <h6 class="mb-3">Shipment Details</h6>
            <div class="row">
                <x-shipment-detail-card 
                    title="Shipment ID"
                    :value="$shipment->name"
                    icon="credit-card"
                />

                <x-shipment-detail-card 
                    title="Order ID"
                    :value="$shipment->order_id"
                    icon="credit-card"
                >
                    <small>({{ $delivery ? 'Delivery' : 'Pickup' }})</small>
                </x-shipment-detail-card>

                <x-shipment-detail-card 
                    title="Status"
                    icon="activity"
                >
                    <x-shipment-status 
                        :status="optional($shipment->shipmentDeliveries)->delivery_status"
                        :deletedAt="$shipment->deleted_at"
                        :deletedByUser="$shipment->deletedByUser"
                    />
                </x-shipment-detail-card>

                <x-shipment-detail-card 
                    title="Shipment Contact No"
                    :value="optional($shipment->ShipmentCustomerDeliveryLocations)->delivery_contact_phone ?? 'N/A'"
                    icon="phone"
                />

                <x-shipment-detail-card 
                    title="Warehouse (Origin)"
                    :value="$warehouse->name"
                    icon="map-pin"
                />

                <x-shipment-detail-card 
                    title="Forklift"
                    :value="isset($shipment->fork_lift) && $shipment->fork_lift == 1 ? 'Yes' : 'No'"
                    :icon="$forkliftIcon"
                />

                <x-shipment-detail-card 
                    :title="$delivery ? 'Shipment/Delivery Date' : 'Shipment/Pickup Date'"
                    :value="date('m/d/Y', strtotime($shipment->route_Date))"
                    icon="calendar"
                />

                <x-shipment-detail-card 
                    title="Location Name"
                    :value="optional($shipment->ShipmentCustomerDeliveryLocations)->delivery_location_name ?? 'N/A'"
                    icon="map-pin"
                />

                <x-shipment-detail-card 
                    title="Shipment Note"
                    :value="isset($shipment->props_notes[0]['text']) && $shipment->props_notes[0]['text'] != '' ? $shipment->props_notes[0]['text'] : 'N/A'"
                    icon="file-text"
                />
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // alert('hii');


        if (activeTab === 'cancelledshipment') {
            //alert('hii');
            // ...
            if (deletedAt) {
                // ...
                if (row.deleted_by) {
                    try {
                        const user = getUserDetails(row.deleted_by);
                        var deletedByName = user.name || 'Unknown User';
                        var deletedById = user.id || 'Unknown ID';
                        userInfo = 'Deleted by User ID: ' + deletedById + ', Name: ' + deletedByName;
                    } catch (error) {
                        console.error('Error fetching user details:', error);
                    }
                }
            }
            return (deletionInfo || 'Not deleted') + '<br>' + userInfo;
        }

        async function getUserDetails(userId) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '/users/' + userId,
                    method: 'GET',
                    success: function(user) {
                        resolve(user);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }
    });
</script>