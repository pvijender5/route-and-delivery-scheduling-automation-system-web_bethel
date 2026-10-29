<x-app-layout>
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Shipments</h3>
                </div>
                @include('partials.breadcrumb')
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <ul class="nav nav-tabs border-tab nav-primary" id="info-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="info-home-tab" data-bs-toggle="tab" href="#info-home" role="tab" aria-controls="info-home" aria-selected="true">
                                            <i class="fa fa-truck"></i>All Shipments
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="progressshipment-tab" data-bs-toggle="tab" href="#progressshipment" role="tab" aria-controls="progressshipment" aria-selected="false">
                                            <i class="fa fa-spinner"></i>In Progress
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="deliveredshipment-tab" data-bs-toggle="tab" href="#deliveredshipment" role="tab" aria-controls="deliveredshipment" aria-selected="false">
                                            <i class="fa fa-check"></i>Delivered
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="cancelledshipment-tab" data-bs-toggle="tab" href="#cancelledshipment" role="tab" aria-controls="cancelledshipment" aria-selected="false">
                                            <i class="fa fa-times"></i>Cancelled
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('shipments.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Create Shipment
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="tab-content" id="info-tabContent">
                            <div class="tab-pane fade show active" id="info-home" role="tabpanel" aria-labelledby="info-home-tab">
                                <x-shipment-table 
                                    :shipments="$shipments" 
                                    :showCheckbox="true"
                                />
                            </div>

                            <div class="tab-pane fade" id="progressshipment" role="tabpanel" aria-labelledby="progressshipment-tab">
                                <x-shipment-table 
                                    :shipments="$inProgressShipments" 
                                    :showCheckbox="false"
                                />
                            </div>

                            <div class="tab-pane fade" id="deliveredshipment" role="tabpanel" aria-labelledby="deliveredshipment-tab">
                                <x-shipment-table 
                                    :shipments="$deliveredShipments" 
                                    :showCheckbox="false"
                                />
                            </div>

                            <div class="tab-pane fade" id="cancelledshipment" role="tabpanel" aria-labelledby="cancelledshipment-tab">
                                <x-shipment-table 
                                    :shipments="$cancelledShipments" 
                                    :showCheckbox="false"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('modals.viewnotes_modal')
    @include('modals.inventory_modal')
    @include('modals.notes_modal')

    @push('scripts')
    <script>
        var userId = "{{ Auth::id() }}";
        var userName = "{{ Auth::user()->name }}";
        var viewShipmentRoute = "{{ route('customers.viewshipment', '') }}";
        var maproute = "{{ route('routes.map')}}";
    </script>
    @endpush
</x-app-layout>