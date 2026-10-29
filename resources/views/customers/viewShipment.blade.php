<x-app-layout>
  <script src="{{ asset('js/common.js') }}"></script>
  <script src="{{ asset('js/shipment/activitylog.js') }}"></script>
  <!-- Place the button in a row -->
  <div class="container-fluid">
    <div class="page-title mb-3">
      <div class="row">
        <!-- <div class="col-6">
          <h5 class="mb-2 f-w-700">Customers</h5>
          <ol class="breadcrumb justify-content-start">
            <li class="breadcrumb-item">Manage Customer & Shipment</li>
            <li class="breadcrumb-item ">All Shipment </li>
            <li class="breadcrumb-item active">{{$shipment->id}}</li>
          </ol>
        </div> -->
      <h5 class="mb-2 f-w-700">Shipment View</h5>

        @include('partials.breadcrumb')
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 text-md-end">
          <button class="btn activity-log-btn" data-shipment-id="{{$shipment->id}}" data-toggle="modal" data-target="#activityLogModal">
            Activity Log
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="col-sm-12 col-lg-12 col-xl-12">
      <!-- ... Display other customer details ... -->
      <div class="shipment-details">
        @include('shipments.shipment', ['shipment' => $shipment,'warehouse' => $shipment->warehouse]) <!-- Pass the $shipment data to the view -->
      </div>



      <div class="inventory-details">
        @include('inventory.inventory', ['inventoryItems' => $shipment->shipmentProps]) <!-- Pass the $inventoryItems data to the view -->
      </div>

      <div class="row">
         @if(in_array("DELIVERY", $shipmentDeliveries->shipment->labels))
        <div class="delivery-details col-md-6">
          @include('delivery.delivery', [
          'shipmentDeliveries' => isset($shipmentDeliveries)?$shipmentDeliveries:null,
          'deliveryProofs' => isset($shipmentDeliveries->deliveryProofs)?$shipmentDeliveries->deliveryProofs:null,
          'driver' => isset($shipmentDeliveries->routes->driver)?$shipmentDeliveries->routes->driver:null,
          'truck' => isset($shipmentDeliveries->routes->truck)?$shipmentDeliveries->routes->truck:null,
          ])
        </div>
        @endif
      
        <div class="col-md-6 customer-full-width">
          @if(in_array("DELIVERY", $shipmentDeliveries->shipment->labels))
            <div class="feedback-details">
              @include('delivery.feedback', ['delivery' => $shipmentDeliveries])
            </div>
          @endif

          @if($shipment->customer)
            <div class="customer-details col-md-6">
              @include('customers.customer', ['customer' => $shipment->customer]) <!-- Pass the $customer data to the view -->
            </div>
          @endif
      </div>
      </div>
      <div class="fotter-button text-end">
        @if ($shipment->trashed())
        <div class="text-end mt-3">
          <form action="{{ route('shipments.restoreshipment', $shipment->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-primary">Restore Shipment</button>
          </form>
          @else
          <button class="btn  btn-primary" onclick="goBack()">Back</button>
          <a href="{{ route('shipments.exportShipment',[$shipment->id]) }}" class="btn btn-green-outline mx-2 excel-shipment " onclick="event.preventDefault(); document.getElementById('export-form').submit();">
            Export Details
          </a>
          <form id="export-form" action="{{ route('shipments.exportShipment',[$shipment->id]) }}" method="GET" style="display: none;">
            @csrf
          </form>
          <a class="btn download-shipment" href="{{route('customers.viewshipment', [$shipment->id, 'download'=>'pdf'])}}">Download BOL</a>          
        </div>
        @endif
       
      </div>

    </div>
  </div>

  @include('modals.activitylog_modal')
</x-app-layout>