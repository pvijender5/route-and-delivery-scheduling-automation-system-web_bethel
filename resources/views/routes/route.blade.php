@php
use App\Constants\Roles;
use App\Constants\Commonconstant;
@endphp
<div class="routes">
    <div class="routes-left">
        <div class="route-search"> <input autocomplete="off" class="w-100" type="text" id="searchInput"
                placeholder="Search route, shipment, status, customer...." /> <span
                class="header-search topbar-menu-icon"> <img class="img-fluid"
                    src="../assets/images/search-light.svg" />
            </span>
        </div>
        <div class="route-boxes-container {{count($routes)>3?'blur-content':''}}">
            <div class="d-flex justify-content-between mb-3 pe-xl-3">
                <div class="form-check">
                    <div id="select-all-check">
                        <input class="checkbox_animated form-check-input check_all_route" id="chk-ani"
                            type="checkbox" />
                        <label class="form-check-label m-0" for="chk-ani">Select All</label>
                    </div>
                    <div id="route-not-found" style="display:none">
                        <h6>Route data not found.</h6>
                    </div>

                </div> <!-- <span class="select-route">02 out of 50</span> -->
            </div>
            <div class="route-boxes custom-scrollbar">
                @foreach($routes as $key=>$val) <div class="route-box">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="form-check" data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="multiCollapseExample{{ $val->id }}" id="route-status{{ $val->id }}">
                            <input class="checkbox_animated form-check-input checkbox_route"
                                id="checkbox_route_<?php echo $val->id; ?>" type="checkbox"
                                data-items='<?php echo json_encode($val->item_address); ?>' 
                                data-name='<?php echo json_encode($val->name); ?>' 
                                data-props_weight='<?php echo json_encode($val->props_weight); ?>' 
                                data-duration='<?php echo json_encode($val->duration); ?>' 
                                data-color='<?php echo $val->color; ?>' 
                                data-prt='<?php echo json_encode($val->prt); ?>' 
                                data-fork_lift='<?php echo json_encode($val->fork_lift); ?>' 
                                data-ids='<?php echo json_encode($val->shipment_ids); ?>'
                                data-start='{{ $val->start }}' data-route_id='{{ $val->id }}' />
                            <label onClick="openShipmentCard({{ $val->id }})" class="form-check-label text-brown m-0" for="chk-ani2">R{{ $val->id }}</label>
                        </div>
                        @if($val->status != 'completed' && $val->status != 'delivered')
                        <div class="toggle-switch" title="Lock Status">
                            <input type="checkbox" id="toggle-switch-{{ $val->id }}" name="lock_route" data-id="{{ $val->id }}" data-unlock="{{Auth::user()->unlock_route}}" value="{{ ($val->lock_status == 1 ? 0 : 1) }}" class="hidden" {{ $val->lock_status == 1 ? 'checked' : '' }}>
                            <label for="toggle-switch-{{ $val->id }}" class="toggle-label">
                                <div class="icons">
                                    <div class="lock-icon">
                                        <i class="fa fa-unlock"></i>
                                    </div>
                                    <div class="unlock-icon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endif
                            <div >
                            @if($val->fork_lift == 1)
                            <img width="15" class="img-fluid" src="../assets/images/routes/loader-machine.svg" /> 
                            @endif
                            <button class="btn-status btn-sm btn-{{ $val->status }} btn-routes text-capitalize" id="btn-route-status{{ $val->id }}" type="button">
                                {{ $val->status }}
                            </button>
                            
                        </div>
                    </div>
                    <ul class="truck-route-detail" id="route-detail{{ $val->id }}">
                        <li class="text-success">
                            <img width="15" class="img-fluid" src="../assets/images/routes/thumb-color.svg" />
                            $ {{ $val->total_price }}
                        </li>
                        <li>
                            <img class="img-fluid" src="../assets/images/routes/meter.svg" width="15" />
                            {{ round($val->loaded_distance*0.00062137,2) }} miles
                        </li>
                        <li>
                            <img width="15" class="img-fluid" src="../assets/images/routes/location.svg"
                                id="routestop{{ $val->id }}" />
                            {{ $val->total_stops }} stops
                        </li>
                        <li>
                            <img class="img-fluid" src="../assets/images/routes/weight-scale.svg" width="15" />
                            P:{{ $val->P_quantity }} R:{{ $val->R_quantity }} T:{{ $val-> T_quantity }} ({{$val->props_weight_total}} lbs)
                        </li>
                        <li>
                            <img class="img-fluid" src="../assets/images/routes/calendar.svg" width="15"/>
                            @php
                            // Convert the date string to a timestamp
                            $timestamp = strtotime($val->delivery_date);
                            // Format the timestamp to the desired format
                            $formattedDate = date("M j, Y", $timestamp);
                            @endphp
                            {{ $formattedDate }}
                        </li>
                        <li>
                            <img class="img-fluid" src="../assets/images/routes/clock.svg" width="15" />
                            @php
                            $formattedTime = convertMinutesToHoursMinutes($val->loaded_duration);
                            @endphp
                            {{$formattedTime}}
                        </li>
                        <li>
                            <img class="img-fluid" src="../assets/images/routes/alarm.svg" width="15" />
                            @php
                            // Convert the date and time string to a DateTime object
                            $dateTime = new DateTime($val->planned_departure_time);
                            // Format the DateTime object to the desired format
                            $formattedDateTime = $dateTime->format("h:i A");
                            @endphp
                            PDT {{$formattedDateTime}}
                        </li>
                        <li>
                            @if($val->status != 'completed' && $val->status != 'delivered')
                            <form action="{{ url('route-download') }}" method="POST" style="{{ $val->lock_status == 0 ? 'display:none;' : 'display:inline;' }}" id="download-route-form{{$val->id}}">
                                @csrf
                                <input type="hidden" name="download_routes" class="" value="{{$val->id}}">
                                <button type="submit" class="" style="width:10px;height: 10px; border:none; background:none; color:#80252c; padding:5px;">
                                    <i class="fa fa-download"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ url('route-download') }}" method="POST" style="display:inline;" id="download-route-form{{$val->id}}">
                                @csrf
                                <input type="hidden" name="download_routes" class="" value="{{$val->id}}">
                                <button type="submit" class="" style="width:10px;height: 10px; border:none; background:none; color:#80252c; padding:5px;">
                                    <i class="fa fa-download"></i>
                                </button>
                            </form>
                            @endif
                        </li>
                    </ul>
                    <div class="truck-location" id="location{{$val->id}}">
                        <div class="text-start">
                            <span class="location-label">
                                <img  class="img-fluid" src="../assets/images/routes/loaction-color.svg" width="15" />
                                Origin</span>
                            <span class="location-detail">{{ $val->origin_points }}</span>
                        </div>
                        <div class="text-end">
                            <span class="location-label">
                                <img class="img-fluid" src="../assets/images/routes/loaction-color.svg" width="15" />Last Drop-off
                                Location</span>
                            <span class="location-detail">{{
                                $val->last_dropoff_location!=null?$val->last_dropoff_location:"N/A"
                                }}
                            </span>
                        </div>
                    </div>
                    <div class="route-range" id="range{{$val->id}}">
                        <div class="progress-showcase progress-b-space">
                            <div class="row">
                                <div class="col">
                                    <div class="progress sm-progress-bar overflow-visible my-1">
                                        <div class="progress-bar-custom small-progressbar rounded-pill"
                                            role="progressbar" style="width: <?php echo  $val->stopsavg."%"?>" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100">
                                            <span class="animate-circle"></span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <span class="progress-label" id="stops{{ $val->id }}">{{isset($val->deliver_stops) ? $val->deliver_stops : 0}} of
                                            {{$val->total_stops}}</span>
                                        @php
                                        // Convert the date and time string to a DateTime object
                                        $dateTime = new DateTime($val->updated_at);
                                        // Format the DateTime object to the desired format
                                        $formattedDateTime = $dateTime->format("M j, Y h:i A");
                                        @endphp
                                        <span class="progress-label" id="lastupdate{{ $val->id }}">Last Updated :- {{
                                            $formattedDateTime }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="routes-right" >
    @include('modals.assignroute_modal')
    @include('modals.postroute_modal')
    @include('modals.addnewshipment_modal')
    @include('modals.replacenewshipment_modal')
    @include('modals.inventory_modal')
    @include('modals.viewnotes_modal')
    @include('modals.deliveryproof_modal')
    @include('modals.instructions_modal')
    @include('modals.fhf_notes_modal')
    <button  class="map-expand-btn">
    <img id="fullScreenMap" class="expand" src="data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20viewBox%3D%220%200%2018%2018%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M0%200v6h2V2h4V0H0zm16%200h-4v2h4v4h2V0h-2zm0%2016h-4v2h6v-6h-2v4zM2%2012H0v6h6v-2H2v-4z%22/%3E%3C/svg%3E" alt=""> 
    <img  id="fullScreenMapExit" class="compress" src="data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20viewBox%3D%220%200%2018%2018%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M4%204H0v2h6V0H4v4zm10%200V0h-2v6h6V4h-4zm-2%2014h2v-4h4v-2h-6v6zM0%2014h4v4h2v-6H0v2z%22/%3E%3C/svg%3E" alt="">
    </button>
        <div id="map" style="width:100%;height:100vh;"></div>
        <div class="route-modal custom-scrollbar w-auto">
            @foreach($routes as $key=>$val)
            <div class="day multi-collapse dark-accordion" id="multiCollapseExamp{{ $val->id }}" style="display:none">
                <div class="card card-body mt-3 mb-0 collapse-wrapper notification driver-detail">
                    <h5 class="driver-code text-brown fw-bold d-flex justify-content-between">
                        <span>R{{ $val->id }}<img class="img-fluid" src="../assets/images/routes/employee.png" /></span>
                        <span><i id="statusLock{{ $val->id }}" class="{{ $val->lock_status == 1 ? 'fa fa-lock' : 'fa fa-unlock'}}"></i></span>
                        <small id="smallRecommendedPrice{{$val->id}}" class="text-success"><img width="15" class="img-fluid"
                                src="../assets/images/routes/thumb-color.svg" />
                            ${{ @$val->recommended_price }}</small>
                    </h5>
                    @php
                        $statusArrayForNotProgress = Roles::PLANROUTESTATUS_ARRAY;
                        $statusArrayForProgress = Roles::INTRANSIRROUTESTATUS_ARRAY;
                    @endphp
                    <div class="form-group pt-3 d-flex flex-wrap">                       
                        <div style="display: none;" id="recommendedPrice{{$val->id}}" class="row">   
                            <label class="col-form-label text-muted col-4"><small>Total Price</small> </label>
                            <div class="edit-price col-8 mb-3">
                                <input class="form-control" id="totalPriceValue" type="number" value="{{ $val->total_price }}" readonly />
                                <img class="img-fluid pencil edit-trigger_update_price" src="../assets/images/routes/pencil.png" />
                                <img class="img-fluid pencil save-trigger" data-oldrecommendedprice="{{ $val->total_price}}" data-priceupdateid="{{$val->id}}" src="../assets/images/routes/save-icon.gif" style="display: none;" />
                            </div>
                        </div>
                        <label class="col-form-label text-muted col-4"><small>Assigned Driver</small> </label>
                <div class="edit-price add-driver col-8 mb-3">
                            @if(isset($val->driver) && $val->driver->name)
                                <input style="border:none" id="driver-name{{ $val->id }}" class="form-control" type="text" value="{{ $val->driver->name }}" readonly />
                                @if (in_array($val->status, $statusArrayForNotProgress))
                                    {{-- Check lock status and permission to change driver --}}
                                    @if ($val->lock_status == 0 || ($val->lock_status == 1 && Auth::user()->can_driver_assign_lock_route == 1))
                                        <a href="#" id="anchorChangeDriver{{ $val->id }}" class="add-edit-driver change-drivers" data-routeids="{{ $val->id }}" data-driverid="{{ $val->assigned_driver }}" title="Change driver">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    @endif
                                @endif
                            @else
                                <input style="border:none" id="driver-name{{ $val->id }}" class="form-control" type="text" value="Not Assigned" readonly />

                                @if (in_array($val->status, $statusArrayForNotProgress))
                                    {{-- Check lock status and permission to assign driver --}}
                                    @if ($val->lock_status == 0 || ($val->lock_status == 1 && Auth::user()->can_driver_assign_lock_route == 1))
                                        <a href="#" id="anchorChangeDriver{{ $val->id }}" class="add-edit-driver change-drivers" data-routeids="{{ $val->id }}" title="Assign driver">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    @endif
                                @endif

                            @endif
                        </div>
                    </div>
                    <!-- Check if the value exists in the array -->
                    <div class="row">
                        <div class="col-2 mb-3" title="Delivery Instruction" id="viewDeliveryLinstruction" data-route-id="{{ $val->id }}">
                            <i style="color:#415b2a"  class="fa fa-file img-fluid pencil view-delivery-instruction-link cursor-pointer" data-notes="' + notes + '"></i>
                        </div>
                        <div class="col-10 text-end">
                            @if (in_array($val->status, $statusArrayForNotProgress))
                            <span id="addNewShipmentBtn{{ $val->id }}" style="{{ $val->lock_status == 1 ? 'display:none;' : 'display:inline;' }}">
                                <a href="#" role="button" class="statusArrayForNotProgress add-new-shipment"
                                data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="addNewShipment"
                                onClick="addNewShipment({{ $val->id }}, '{{ $val->delivery_date }}', {{ $val->wherehouse_id}})">
                                + Add New Shipment
                                </a>
                            </span>
                            @endif
                            @if (in_array($val->status, $statusArrayForProgress))
                            <span id="replaceWithNewShipmentBtn{{ $val->id }}" style="{{ $val->lock_status == 1 ? 'display:none;' : 'display:inline;' }}">
                            <a href="#" role="button" class="add-new-shipment " data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="replaceWithNewShipment"
                                onClick="replaceWithNewShipment({{ $val->id }}, '{{ $val->delivery_date }}', {{ $val->wherehouse_id}})">
                                + Replace With New Shipment
                            </a>
                           </span>
                            @endif
                        </div>
                    </div>
                    
                    @if($val->status == Roles::COMPLETED)
                    <div style="font-size:x-small">
                            <span>On-Time deliveries : <b id="onTimeDeliveries{{$val->id;}}">0</span></b>
                            <br/>
                            <span>Success rate : <b id="successRate{{$val->id;}}">0</b>%</span>
                            <br/>
                            <span>Total loaded miles completed  : <b>{{round($val->loaded_distance*Commonconstant::MILES,2)}}</b></span>
                            <br/>
                            <span>Total empty miles completed : <b>{{round(($val->total_distance-$val->loaded_distance)*(Commonconstant::MILES),2)}}</b></span>
                    </div>        
                    @endif

                    <div class="driver-tabs">
                        <ul class="nav nav-pills nav-primary" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="pills-Stops-tab-{{ $val->id }}" data-bs-toggle="pill"
                                    href="#pills-Stops-{{ $val->id }}" role="tab"
                                    aria-controls="pills-Stops-{{ $val->id }}" aria-selected="true">Stops</a>
                            </li>
                            <li class="nav-item" role="presentation">

                                <a class="nav-link logtab" data-router_id="{{ $val->id }}"
                                    id="pills-activity-tab-{{ $val->id }}" data-bs-toggle="pill"
                                    href="#pills-activity-{{ $val->id }}" role="tab"
                                    aria-controls="pills-activity-{{ $val->id }}" aria-selected="false"
                                    tabindex="-1">Activity
                                    Logs</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade active show" id="pills-Stops-{{ $val->id }}" role="tabpanel"
                                aria-labelledby="pills-Stops-tab-{{ $val->id }}">
                                <div class="dark-timeline custom-scrollbar drag-overflow">
                                    @if($val->status != 'completed' && $val->status != 'delivered')
                                    <ul id="sortable{{ $val->id}}" data-wherehouseid = "{{ $val->wherehouse_id}}" data-routeid="{{ $val->id}}" class="<?php echo  (isset($track) && $track== 1) ? 'day22' : 'day'?> {{ $val->lock_status == 1 ? 'no-drag' : '' }}">
                                    </ul>
                                    @else
                                    <ul id="sortable{{ $val->id}}" data-wherehouseid = "{{ $val->wherehouse_id}}" data-routeid="{{ $val->id}}" class="no-drag <?php echo  (isset($track) && $track== 1) ? 'day22' : 'day'?> ">
                                    </ul>
                                    @endif
                                </div>
                            </div>

                            <div class="dark-timeline tab-pane fade table-responsive custom-scrollbar"
                                id="pills-activity-{{ $val->id }}" role="tabpanel"
                                aria-labelledby="pills-activity-tab-{{ $val->id }}">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-close py-0 cross-img" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        data-route_id="{{ $val->id }}"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <input type="hidden" id="currentroute_id" />
            </div>

            @endforeach
        </div>

    </div>
</div></div>