<x-app-layout>
    <script>
        const userDataTable = "{{ route('reports.datatables') }}";
    </script>
    <script src="{{ asset('js/reports/systemReport.js') }}"></script>
     <script src="{{ asset('js/common.js') }}"></script>
    <div class="container-fluid mb-3">
        <div class="page-title mb-3">
             <h5 class="f-w-700">System Reports</h5>
            <div class="row">
                @include('partials.breadcrumb')
            </div>   
            
            
            <div class="row justify-content-end mt-3"> 
                    <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-xxl-4 text-lg-end">
                        <label class="col-form-label w-auto text-muted"><b>Filter by:</b></label>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10 col-xxl-8 light-card p-3 rounded">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4">Delivery
                                    Date From</label> 
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 col-xxl-8">
                                        <input class="form-control digits select-date wherehouesfilter" id="datepickerFrom"
                                        placeholder="mm/dd/yy" type="text" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                 <div class="form-group row"> 
                                    <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-4">Delivery Date To</label> 
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 col-xxl-8">
                                        <input class="form-control digits select-date wherehouesfilter" id="datepickerTo"
                                        placeholder="mm/dd/yy" type="text" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>   
        <div class="r-user-profile">
    
            <div class="card hovercard border-box bg-light" style="margin-bottom:0px !important">
                <div class="info p-0">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-4">
                            <div class="card widget-1"> 
                                <div class="card-body"> 
                                    <div class="widget-content">
                                    <div class="widget-round secondary-2">
                                        <div class="bg-round">
                                        <i class="icofont icofont-map-pins"></i>
                                        
                                        </div>
                                    </div>
                                    <div> 
                                        <h4 id="totalRoutes">{{$routes['totalRoutes']}}</h4><span class="f-light">Total Route</span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-4">
                            <div class="card widget-1"> 
                                <div class="card-body"> 
                                    <div class="widget-content">
                                    <div class="widget-round primary-2">
                                        <div class="bg-round">
                                        <i class="icofont icofont-delivery-time"></i>
                                        
                                        </div>
                                    </div>
                                    <div> 
                                        <h4 id="percentOnTimeDeliveries">{{ round($routes['perentOnTimeDeliveries'], 2)}}%</h4><span class="f-light">Average On-time deliveries</span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-4">
                            <div class="card widget-1"> 
                                <div class="card-body"> 
                                    <div class="widget-content">
                                    <div class="widget-round success-2">
                                        <div class="bg-round">
                                        <i data-feather="dollar-sign"></i>
                                        
                                        </div>
                                    </div>
                                    <div> 
                                        <h4 id="averageDeliveryPrice">$ {{ round($routes['averageDeliveryPrice'], 2)}}</h4><span class="f-light">Average Delivery price</span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-4 ">
                            <div class="card widget-1"> 
                                <div class="card-body"> 
                                    <div class="widget-content">
                                    <div class="widget-round warning">
                                        <div class="bg-round">
                                        <i class="icofont icofont-fast-delivery"></i>
                                        
                                        </div>
                                    </div>
                                    <div> 
                                        <h4 id="totalShipments">{{$routes['totalShipments']}}</h4><span class="f-light">Total Shipments</span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-4 ">
                            <div class="card widget-1"> 
                                <div class="card-body"> 
                                    <div class="widget-content">
                                    <div class="widget-round info-2">
                                        <div class="bg-round">
                                        <i class="icofont icofont-delivery-time"></i>
                                        
                                        </div>
                                    </div>
                                    <div> 
                                        <h4 id="percentDelayedDeliveries">{{ round($routes['perentDelayedDeliveries'], 2)}}%</h4><span class="f-light">Average Delayed deliveries</span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="border-box">
        <h5 class="f-w-700" style="float: left;">Route Based Reports</h5>
            <div class="col-sm-12 col-lg-12 col-xl-12">
                 <div class="table-buttons  text-end mb-3 dt-top-btns">
                        <div class="btn-group">
                            <button class="btn dropdown-toggle " type="button" data-bs-toggle="dropdown" aria-expanded="true"><i class="fa fa-download me-2"></i>Export Routes</button>
                            <ul class="dropdown-menu dropdown-block" data-popper-placement="bottom-start" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 37px);">
                                <li><a class="dropdown-item" href="#">Export All</a></li>
                                <li><a class="dropdown-item" href="#">Export Only Selected</a></li>
                            </ul>
                        </div>
                    </div>
                <div class="table-responsive">
                <table class="table" id="reports-table">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Route ID</th>
                            <th>Delivery date</th>
                            <th>Total Stops</th>
                            <th>Warehouse</th>
                            <th>Driver Name & ID</th>
                            <th>Driver Type</th>
                            <th>Loaded Miles</th>
                            <th>Empty Miles</th>
                            <th>Timeframe</th>
                            <th>On-time Shipments</th>
                            <th>Delayed Shipments</th>
                            <th>Breakdown Time </th>
                            <th>Pause Time</th>
                            <th>Delivery Price</th>
                        </tr>
                    </thead>




        <tr class="table1 secondtables sub-table">
          <td colspan="8">
            
        <div class="card card-body mb-0 dark-accordion">
          <div class="sub-table-container">
            <table class="table-light accordion-table sub-table" id="rowdata">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">Order ID</th>
                  <th scope="col">Warehouse (Origin)</th>
                  <th scope="col">Customer Name</th>
                  <th scope="col">Location Name</th>
                  <th scope="col">Delivery Location</th>
                  <th scope="col">Shipment Contact No</th>
                  <th scope="col">Shipment/Delivery Date</th>
                  <th scope="col">Delivery Window</th>
                  <th scope="col">Item Quantity</th>
                  <th scope="col">Forklift</th>
                  <th scope="col">Status</th>
                  <th scope="col">View</th>
                </tr>
              </thead>
              <tbody class="sub-table-data">
              </tbody>  

            </table>
          </div>
          </div>
            @include('modals.deliveryproof_modal')

          </td>
        </tr>

            </table>
        </div>
     </div>
    </div>
</div>
       @include('modals.inventory_modal')
 
    </x-app-layout>
 <script>
 var booked_date =new Date();  
$("#datepickerFrom, #datepickerTo").datepicker({
 format: "dd/mm/yyyy",
 autoclose: true,
 todayHighlight: true,
 beforeShowDay: function(date){
     var d = date;
     var curr_date = d.getDate();
     var curr_month = d.getMonth() + 1;
     var curr_year = d.getFullYear();
     var formattedDate = curr_date + "/" + curr_month + "/" + curr_year

     if ($.inArray(formattedDate, booked_date) != -1){
       return {
          classes: 'activeClass'
       };
     }
  return;
 },
 clearBtn: true
});
</script>