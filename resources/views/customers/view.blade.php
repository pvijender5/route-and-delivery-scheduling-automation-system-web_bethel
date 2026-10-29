<x-app-layout>
    <script>
        const customerList = "{{ route('customers.datatables') }}";
    </script>
    <script src="{{ asset('js/customer/customer.js') }}"></script>
    @php
    $deliveryShipments = $shipmentsDELIVERYData ?? collect();
    $pickupShipments = $shipmentsPICKUPData ?? collect();
    @endphp
    <div class="container-fluid">
        <div class="page-title mb-3">
    <h5 class="f-w-700">Customer Detail</h5>


            <div class="row">
                @include('partials.breadcrumb')
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="col-sm-12 col-lg-12 col-xl-12">

            <div class="user-profile">
                <div class="card hovercard border-box">
                    <div class="info p-0">
                        <h6 class="mb-3">Customer Detail</h6>

                        <div class="row  r-user-profile">
                            <div class="col-lg-6 col-md-6 col-xl-3 mb-md-3 mb-lg-3">
                                <div class="ttl-info balance-card text-start light-card p-3 rounded  height-equal">
                                    <div class="svg-box">
                                        <i data-feather="user"></i>
                                    </div>
                                    <div>
                                        <span class="f-light">Customer Name</span>  
                                        <h6 class="mt-1 mb-0"><span>{{ $customer->name }}</span></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xl-3 mb-md-3 mb-lg-3"> 
                                <div class="ttl-info balance-card text-start light-card p-3 rounded height-equal">
                                    <div class="svg-box">
                                        <i data-feather="credit-card"></i>
                                    </div>
                                    <div>
                                        <span class="f-light">Customer ID</span>  
                                        <h6 class="mt-1 mb-0"><span>{{ $customer->customer_id }}</span></h6>
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-6 col-md-6 col-xl-3 "> 
                                <div class="ttl-info balance-card text-start light-card p-3 rounded height-equal">
                                    <div class="svg-box">
                                        <i data-feather="mail"></i>
                                    </div>
                                    <div>
                                        <span class="f-light">Email</span>  
                                        <h6 class="mt-1 mb-0"><span>@if($customer->email){{ $customer->email }}@else N/A @endif</span></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-xl-3"> 
                                <div class="ttl-info balance-card text-start light-card p-3 rounded height-equal">
                                    <div class="svg-box">
                                        <i data-feather="phone"></i>
                                    </div>
                                    <div>
                                        <span class="f-light">Primary Mobile No.</span>  
                                        <h6 class="mt-1 mb-0"><span>{{ $customer->phone }}</span></h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="border-box">
                <h6 class="mb-3">Shipments <span class="text-gray">(Deliveries)</span></h6>
                <x-shipment-table 
                    :shipments="$deliveryShipments" 
                    :showCheckbox="false"
                    type="delivery"
                />
            </div>



            <div class="border-box mt-5">
                <h6 class="mb-3">Shipments <span class="text-gray">(Pickup)</span></h6>
                <x-shipment-table 
                    :shipments="$pickupShipments" 
                    :showCheckbox="false"
                    type="pickup"
                />
            </div>
            </div>

            <div class="fotter-button text-end">
                <button class="btn px-5 my-5" onclick="window.history.back()">Back</button>
            </div>

        </div>
    </div>

    @include('modals.viewnotes_modal')
    @include('modals.inventory_modal')
    @include('modals.notes_modal')
</x-app-layout>