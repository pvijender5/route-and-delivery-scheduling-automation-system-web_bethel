<div class="modal fade" id="assignRoute" tabindex="-1" aria-labelledby="assignRoute" aria-modal="false" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg assign-route modal-custom-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="routeDriverStatus"></h5>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row b-truck-details">
                    <div class="card small-widget col-sm-12 col-md-6 col-xl-5 mb-3">
                        <input class="form-check-input radio-buttons" id="radioCheckDriver" checked type="radio" name="driver_type" value="2" />
                        <label for="radioCheckDriver" class="light-card rounded p-2 m-0">
                            <div class="card-body warning px-0 py-2">
                                <div class="bg-gradient">
                                    <img class="img-fluid" src="{{ asset('assets/images/routes/in-house.png') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <h4>Assign to In-House Driver</h4>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="card small-widget col-sm-12 col-md-6 col-xl-5 mb-3">
                        <input class="form-check-input radio-buttons" id="radioCheckSubdriver" type="radio" name="driver_type" value="3" />
                        <label for="radioCheckSubdriver" class="light-card rounded p-2 m-0">
                            <div class="card-body warning px-0 py-2">
                                <div class="bg-gradient">
                                    <img class="img-fluid" src="{{ asset('assets/images/routes/employee.png') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <h4>Assign to 3rd Party Owner/Sub-Driver</h4>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-sm-12 col-md-8 col-xl-6 mb-3">
                        <div class="form-group row g-0">
                        </div>
                    </div>
                </div>

                <table id="usersTable" class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Driver Name</th>
                            <th>Driver Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <div class="route-description mb-4">
                    <h6 class="mb-2 mt-2">Delivery Instruction(Route)</h6>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div>

                <div class="truck-price-details">
                    <h6>Truck Details</h6>
                    <div class="light-card rounded p-3 mb-4">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3">
                                <div class="form-group">
                                    <span>Select Truck</span>
                                    <input class="form-check-input " type="hidden" name="driver" id="assign-driver-id" />
                                    <input class="form-check-input " type="hidden" name="route" id="route-id" />
                                    <select id="truckDropdown" name="truck_id" class="form-select digits custom-select">
                                        <option value="">--N/A--</option>
                                        @foreach($trucks as $truck)
                                        <option value="{{ $truck->id }}" data-trailer-type="{{ $truck->trailer_type }}" data-vin="{{ $truck->vin }}" data-reg-no="{{ $truck->reg_no }}" data-payload-capacity="{{ $truck->payload_capacity }}">{{ $truck->truck_model_name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3">
                                <span class="d-block mb-1">Truck VIN</span>
                                <span class="bg-white f-w-600 w-100 p-2 rounded span-field" id="vinInput"></span>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3 mb-md-0">
                                <span class="d-block mb-1">Registration No</span>
                                <span class="bg-white f-w-600 w-100 p-2 rounded span-field" id="regNoInput"></span>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <span class="d-block mb-1">Trailer Type</span>
                                <span class="bg-white f-w-600 w-100 p-2 rounded span-field" id="trailerTypeInput"></span>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <span class="d-block mb-1">Payload Capacity(LB)</span>
                                <span class="bg-white f-w-600 w-100 p-2 rounded span-field" id="payloadCapacityInput"></span>
                            </div>
                        </div>
                    </div>

                    <div id="notInhousedriver">
                        <h6>Price Details</h6>
                        <div class="price-details">
                            <div class="light-card rounded p-3 mb-4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3 mb-md-0">
                                        <span class="d-block mb-1">Recommended Price</span>
                                        <span class="bg-white bg-white f-w-600 w-100 p-2 rounded" id="recommended-price">$200</span>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <span class="d-block mb-1">Total Price
                                            <small>(This price will be visible to third party
                                                Owner)</small></span>
                                        <div class="edit-price">
                                            <input id="total-price" name="totalPrice" class="form-control" type="text" placeholder="$200.00" />
                                            <img class="img-fluid pencil" src="{{ asset('assets/images/routes/pencil.png') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    

                    <h6 class="mb-3">Loading By</h6>
                    <div class="d-flex">
                        <div class="form-check me-3" id="loder_checkbox">
                            <input class="form-check-input checkbox_animated" type="radio" name="loadingBy" id="loader">
                            <label class="form-check-label label-cursor text-brown m-0" for="loader">Loader</label>
                        </div>
                        <div class="form-check" id="driver_checkbox">
                            <input class="form-check-input checkbox_animated" type="radio" name="loadingBy" id="driver">
                            <label class="form-check-label label-cursor text-brown m-0" for="driver">Driver</label>
                        </div>
                        <input class="form-check-input " type="hidden" name="loading_by" id="loadingByUser" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary-outline assign-driver-close" type="button" data-bs-dismiss="modal">
                    Close
                </button>
                <button class="btn btn-success-outline confirm-assign-driver" type="button">
                    Confirm & Assign
                </button>
            </div>
        </div>
    </div>
</div>