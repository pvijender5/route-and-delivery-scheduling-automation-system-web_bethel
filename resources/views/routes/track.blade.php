@php
use App\Constants\Roles;
@endphp
<link rel="stylesheet" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/route.css') }}">
<x-app-layout>
  <!-- Add this in the head section of your HTML file -->
  <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_API_KEY')}}"></script> <!-- Page Sidebar
  Ends-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier/1.0.3/oms.min.js"></script>
  <div class="container-fluid">
    <div class="page-title mb-3">
      <div class="row">
        <div class="col-6 col-sm-12 col-lg-6">
          <h5 class="mb-2 f-w-700">Track Route</h5>
          <ol class="breadcrumb justify-content-start">
            <li class="breadcrumb-item">Manage Route</li>
            <li class="breadcrumb-item active">Track Route</li>
          </ol>
        </div> 
        <div class="col-6 col-sm-12 col-lg-6 text-end pe-0">
          <a class="btn btn-primary" type="button" href="{{ route('shipments.createRoute') }}">Back</a>
          <a class="btn btn-success-outline" onclick="refreshRoutes()" type="button" href="javascript:void(0);">Refresh Routes</a>
        </div>
      </div>
        </div> <!-- <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 text-md-end">
          <label class="col-form-label text-muted">Last updated HH:MM:SS, DD/MM/YY</label>
          <button class="btn btn-primary ms-3">Update List</button>
        </div> -->

        <div class="row justify-content-end mt-3"> 
                <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-xxl-3 text-lg-end">
                    <label class="col-form-label w-auto text-muted"><b>Filter by:</b></label>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10 col-xxl-9 light-card p-3 rounded">
                    <div class="row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4 text-xl-end">Warehouse
                                </label> 
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 col-xxl-8">
                                      <select id="wherehouesfilter" class="digits selectpicker wherehouesfilter" multiple data-actions-box="true" data-select-all-text="Select All" data-deselect-all-text="Deselect All" title="Select Options">
                                      <option value="">Select Warehouse</option>
                                      @foreach ($wherehouse as $warehouse)
                                      <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                              <div class="form-group row"> 
                                <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4 text-xl-end">Route Status</label> 
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 col-xxl-8">
                                   <select
                                    class="form-select digits routeStatus text-capitalize" id="routeStatus">
                                    <option value="">Select route status</option>
                                    @foreach (Roles::TRACKROUTESTATUS as $value => $label)
                                    <option class="text-capitalize" value="{{ $label }}">{{ $label }}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                        </div>

                          <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                              <div class="form-group row"> 
                                <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4 text-xl-end">Delivery Date</label> 
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 col-xxl-8">
                                    <input class="form-control digits select-date wherehouesfilter" id="datepicker"
                    placeholder="mm/dd/yy" type="text" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>



      <!-- <div class="row justify-content-end mt-3"> <label class="col-form-label w-auto text-muted">Filter
          by:</label>
        <div class="w-auto p-0">
          <div class="light-card p-3 rounded">
            <div class="d-flex align-items-center justify-content-end">
              <div class="form-group d-flex align-items-center">
                <label class="col-form-label me-3">Warehouse</label> <select
                  class="form-select digits w-auto wherehouesfilter" id="wherehouesfilter">
                  <option value="">Select Warehouse</option>
                  @foreach ($wherehouse as $warehouse)
                  <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="d-flex align-items-center ps-3 justify-content-end">
                <div class="form-group d-flex align-items-center">
                  <label class="col-form-label me-3">Route status</label> <select
                    class="form-select digits w-auto routeStatus text-capitalize" id="routeStatus">
                    <option value="">Select route status</option>
                    @foreach (Roles::TRACKROUTESTATUS as $value => $label)
                    <option class="text-capitalize" value="{{ $label }}">{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group d-flex align-items-center ms-3 ps-3"> <label class="col-form-label me-3">Delivery
                    Date</label> <input class="form-control digits select-date w-auto wherehouesfilter" id="datepicker"
                    placeholder="mm/dd/yy" type="text" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> -->
    </div>
    <div class="container-fluid">
    @include('routes.route')
    <div class="routes-btn-container d-flex justify-content-end">
      
      <div class="text-end">
        <form action="{{ url('route-download')}}" method="POST">
          @csrf
          <input type="hidden" name="download_routes" class="download-routes-id">
          <button class="btn btn-success-outline download-multiple-routes disabled" type="submit" style="background: #618339; color: white;">
            Downlaod Routes
          </button>
        </form>
      </div>
        <!-- <button class="btn btn-primary btn-detele-routes disabled" type="button">
          Delete Routes
        </button>
        <button class="btn btn-green-outline btn-change-drivers disabled" type="button" data-routeids="">
          Change Driver
        </button>
        <button class="btn btn-success-outline btn-post-route disabled" type="button">
          Post Routes
        </button> -->
      </div>
    </div>
    <!-- Container-fluid Ends-->
   @section('script')
    <script>
      var id = 0;
      var getroutedetail = "getroutebyid";
      var getactivitylog = "getactivitylog";
      var arrayFromPHP = <?php echo json_encode($routes); ?> 
      var downloadRoutes = "{{ url('route-download') }}";
      var track = <?php echo $track; ?>;
    var updateDeliveryRoute = "updateDeliveryRoute";
    var statusArrayForProgress=<?php echo json_encode(Roles::INTRANSIRROUTESTATUS_ARRAY); ?> 
    </script>

    <script src="{{ asset('js/routes/routes.js') }}"></script>
    <script src="{{ asset('js/routes/shipment.js') }}"></script>
    <script src="{{ asset('js/routes/driver.js') }}"></script>
    <script src="{{ asset('js/routes/loadboard.js') }}"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
  <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
  <script src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
  <script src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
@endsection


</x-app-layout>