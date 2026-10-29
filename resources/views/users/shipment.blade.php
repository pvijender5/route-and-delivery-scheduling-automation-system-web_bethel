<!-- shipment.blade.php -->

<div class="card-body pt-0 px-0 r-user-profile">
    <h6 class="mb-3">Shipments</h6>

      <div class="row mb-3">
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3 mb-xl-0">
        <div class="ttl-info balance-card text-start light-card p-3 rounded">
          <div class="svg-box">
              <i data-feather="truck"></i>
          </div>
          <div>
              <span class="f-light">Total deliveries</span>  
              <h6 class="mt-1 mb-0">{{$totalShipments}}</h6>
          </div>
      </div>
       
      </div>
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3 mb-xl-0">
        <div class="ttl-info balance-card text-start light-card p-3 rounded">
          <div class="svg-box">
              <i data-feather="truck"></i>
          </div>
          <div>
              <span class="f-light">On-Time Deliveries</span>  
              <h6 class="mt-1 mb-0">{{$ontimeShipments}}</h6>
          </div>
        </div>
      </div>


      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3 mb-xl-0">
        <div class="ttl-info balance-card text-start light-card p-3 rounded">
          <div class="svg-box">
              <i data-feather="trending-up"></i>
          </div>
          <div>
              <span class="f-light">Success Rate</span>  
              <h6 class="mt-1 mb-0">0%</h6>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
        <div class="ttl-info balance-card text-start light-card p-3 rounded">
          <div class="svg-box">
              <i data-feather="trending-down"></i>
          </div>
          <div>
              <span class="f-light">Breakdown(this month)</span>  
              <h6 class="mt-1 mb-0">0 Times (0 Hours)</h6>
          </div>
        </div>
      </div>
    </div>
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
            @forelse ($shipments as $shipmentDetail)
            @php
             $shipment = $shipmentDetail->shipment;
            @endphp
            @if($shipment)
            <tr>
                <td>{{ $shipment->id }}</td>
                <td>{{ $shipment->order_id ? $shipment->order_id : 'N/A' }}</td>
                <td>{{ $shipment->warehouse ? $shipment->warehouse->name : 'N/A' }}</td>
                <td>{{ $shipment->customer ? $shipment->customer->name : 'N/A' }}</td>
                <td>{{ $shipment->shipmentCustomerDeliveryLocations->delivery_location_name }}</td>
                <td>{{ concatenateAddress($shipment->shipmentCustomerDeliveryLocations) }}</td>
                <td>{{ $shipment->shipmentCustomerDeliveryLocations->delivery_contact_phone }}</td>
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
                <!-- <td>Item Quantity static<a href="#" class="view-shipment-link" data-shipment-id="{{$shipment->id}}">Info</a></td> -->
                @php
                $pQuantity = isset($shipment->shipmentDeliveries->P_quantity) ? "P-" . $shipment->shipmentDeliveries->P_quantity : "";
                $rQuantity = isset($shipment->shipmentDeliveries->R_quantity) ? " R-" . $shipment->shipmentDeliveries->R_quantity : "";
                $tQuantity = isset($shipment->shipmentDeliveries->T_quantity) ? " T-" . $shipment->shipmentDeliveries->T_quantity : "";
                $combinedQuantities = $pQuantity . $rQuantity . $tQuantity;
                @endphp
                <td>{{ $combinedQuantities }} <span href="#" class="view-shipment-link"  data-shipment-id="{{ $shipment->id }}"><i class="fa fa-info-circle"></i></span></td>

                <td>
                    {{ isset($shipment->fork_lift) ? 'Yes' : 'No' }}
                </td>
                 @php
                    $notesArray = $shipment->props_notes;
                    @endphp
                    @if($notesArray)
                        <td class="shipment-notes" data-shipment-id="{{$shipment->id}}" data-notes="@isset($notesArray[0]['text'])
                {{ $notesArray[0]['text'] }}
                @endisset"><a class="btn btn-rounded  btn-link notes-btn" data-toggle="modal" data-target="#notesviewModal"><i class="fa fa-file-text"></i></a>
                        </td>
                    @else
                            <td class="shipment-notes" data-shipment-id="{{$shipment->id}}" data-notes="@isset($notesArray[0]['text'])
                {{ $notesArray[0]['text'] }}
                @endisset"><a class="btn btn-rounded  btn-link notes-btn" data-toggle="modal" data-target="#notesviewModal"><i class="fa fa-plus"></i></a>
                        </td>   
                    @endif
                <!-- <td class="text-capitalize">
                    @if ($shipment->trashed())
                        <span>Deleted at {{ date('m/d h:i:s A', strtotime($shipment->deleted_at)) }}</span>
                    @else
                        @php
                        $lastDelivery = $shipment->shipmentDeliveries;
                        @endphp
                        <span>{{$lastDelivery->delivery_status ?? 'N/A'}}</span>
                    @endif
                </td> -->
                <td class="text-capitalize">
                @if ($shipment->trashed())
                    <span>Deleted at {{ date('m/d h:i:s A', strtotime($shipment->deleted_at)) }}</span>
                @else
                    @php
                        $lastDelivery = $shipment->shipmentDeliveries;
                        $statusClasses = [
                            "new" => "btn-new",
                            "loaded" => "btn-loaded",
                            "planned" => "btn-planned",
                            "delivered" => "btn-delivered",
                            "in-progress" => "btn-in-progress",
                            "driver-assigned" => "btn-driver-assigned",
                            "in-transit-delay" => "btn-in-transit-delay",
                            "in-transit-on-time" => "btn-in-transit-on-time",
                            "posted-to-loadboard" => "btn-posted-to-loadboard"
                        ];
                        $status = strtolower($lastDelivery->delivery_status ?? '');
                        $statusClass = isset($statusClasses[$status]) ? $statusClasses[$status] : "default-status";
                    @endphp
                    <span class="text-capitalize shipment-status {{ $statusClass }}">{{ $lastDelivery->delivery_status ?? 'N/A' }}</span>
                @endif
            </td>

                <td>
                    <a href="{{ route('customers.viewshipment', $shipment->id) }}" class="btn btn-sm btn-primary">View</a>
                </td>
                <!-- ... Populate additional table cells ... -->
            </tr>
            @endif
            @empty
            <!-- This block will be executed if no records are found -->
            <tr>
                <td colspan="15" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
       @include('modals.viewnotes_modal')

<script>
 $(document).on("click", ".notes-btn", function () {
    var shipmentId = $(this).closest('tr').find('td:first').text();
    $.ajax({
        url: '/shipmentdetail/' + shipmentId,
        type: 'GET',
        success: function (response) {
            var notesHtml = response !== 'Notes not found' ? response : 'Notes not found';
            $('#notesviewModal').find('.modal-body').html('<p>' + notesHtml + '</p>');
            $('#notesviewModal').modal('show');
        },
        error: function () {
            alert('Error fetching notes');
        }
    });
});


</script>

 </script>

