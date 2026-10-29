<x-app-layout>
<script>
const shipmentList = "{{ route('shipments.datatables') }}";
const CREATE_ROUTE_URL= "{{ getenv("CREATE_ROUTE_URL")}}";
</script>
<script src="{{ asset('js/common.js') }}"></script>
<script src="{{ asset('js/shipment/shipment.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer">
</script>
    <style>
        .row_selected {
            td {
                background-color: #ccc !important;
            }
        }
    </style>
    <div class="container-fluid">
        <div class="page-title mb-3">
            <h5 class="f-w-700">Create Route</h5>
            <div class="row">
                @include('partials.breadcrumb')
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 text-md-end">
                    <label class="col-form-label">Last updated {{date("H:i:s, m/d/y", strtotime($latestUpdatedAt))}}</label>
                </div>
            </div>


            <div class="row justify-content-end mt-3"> 
                <div class="col-sm-12 col-md-12 col-lg-2 col-xl-5 col-xxl-5 text-lg-end">
                    <label class="col-form-label w-auto text-muted"><b>Filter by:</b></label>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-10 col-xl-7 col-xxl-7 light-card p-3 rounded">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4 text-xl-end">Warehouse
                                </label> 
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 col-xxl-8">
                                    <select id="warehouse-filter" class="form-select digits">
                                        <option value="">All Warehouses</option>
                                        @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                          <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                              <div class="form-group row"> 
                                <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4 text-xl-end">Delivery Date</label> 
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 col-xxl-8">
                                 <input type="text" class="form-control digits select-date" id="datepicker" placeholder="MM/DD/YYYY">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>


            <!-- <div class="row justify-content-end mt-3 ">
                <label class="col-form-label w-auto text-muted">Filter by:</label>
                <div class="w-auto p-0">
                    <div class="light-card p-3 rounded">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="form-group d-flex align-items-center">
                                <label class="col-form-label me-3">Warehouse</label>
                                <select id="warehouse-filter" class="form-select digits w-auto">
                                    <option value="">All Warehouses</option>
                                    @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-flex align-items-center ms-3 ps-3">
                                <label class="col-form-label me-3">Delivery Date</label>
                                <input type="text" class="form-control digits select-date w-auto" id="datepicker" placeholder="MM/DD/YYYY">
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

    
    </div>
    <div class="container-fluid">
        <div class="border-box">
        <div class="col-sm-12 col-lg-12 col-xl-12">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="row">
                <div class="tab-content" id="allshipment">
                    <div class="tab-pane fade show active" id="allshipment" role="tabpanel" aria-labelledby="allshipment-tab">
                        <div class="table-buttons  text-end mb-3 dt-top-btns">
                            <button type="button" class="btn btn-outline-danger submit restoreShipmentButton" style="display: none;" data-route="{{ route('shipments.restore') }}">
                                <i class="fas fa-trash-restore-alt"></i> Restore Deleted Shipments
                                <span id="shipmentrestorecount" style="margin-left: 5px;"></span>
                            </button>
                            <button type="button" class="btn btn-primary submit deleteShipmentButton me-2" data-route="{{ route('shipments.delete') }}" style="display: none;">
                                Delete shipment <span id="shipmentcount"></span>
                            </button>

                            <button type="button" class="btn btn-green submit createroute" data-route="{{ route('shipments.shipmentloads') }}" style="display: none;">
                            <span class="spinner-border spinner-border-sm d-none spinner-btn" role="status" aria-hidden="true"></span>
                                Create Route For selected (<span id="shipmentroutecount"></span>) shipment
                            </button>
                        </div>
                        <input type='hidden' value="new" class="form-control" id='statusTab'>
                        <input type="hidden" id="lbscount">
                        <div class="table-responsive">
                            <table class="table" id="shipmenttable">
                                <thead>
                                    <tr>

                                        <th scope="col">
                                        </th>
                                        <th scope="col">Shipment ID</th>
                                        <th scope="col">Order ID</th>
                                        <th scope="col">Warehouse(Origin)</th>
                                        <th scope="col">Customer Name</th>
                                        <th scope="col">Location Name</th>
                                        <th scope="col">Delivery Location</th>
                                        <th scope="col">Shipment Contact No</th>
                                        <th scope="col">Shipment/Delivery Date</th>
                                        <th scope="col">Delivery Window</th>
                                        <th scope="col">Item Quantity</th>
                                        <th scope="col">Forklift</th>
                                        <th scope="col">Shipment Notes</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">View</th>
                                    </tr>
                           

                                </thead>
                            </table>
                        </div>
                        <input type="hidden" id="shipment_id" />
                        <div class="tab-pane fade" id="progressshipment" role="tabpanel" aria-labelledby="progressshipment-tab">

                        </div>
                        <div class="tab-pane fade" id="deliveredshipment" role="tabpanel" aria-labelledby="deliveredshipment-tab">
                            <!-- Content for Delivered Shipments tab -->
                        </div>
                        <div class="tab-pane fade" id="cancelledshipment" role="tabpanel" aria-labelledby="cancelledshipment-tab">
                            <!-- Content for Cancelled Shipments tab  -->

                            <input type="hidden" id="deleted" value="1" name="deleted" />

                        </div>

                        @include('modals.viewnotes_modal')
                        @include('modals.inventory_modal')
                        @include('modals.notes_modal')
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    var userId = "{{ Auth::id() }}";
    var userName = "{{ Auth::user()->name }}";
    var userId = "{{ auth()->id() }}";
    var viewShipmentRoute = "{{ route('customers.viewshipment', '') }}";
    var maproute = "{{ route('routes.map')}}";
</script>
