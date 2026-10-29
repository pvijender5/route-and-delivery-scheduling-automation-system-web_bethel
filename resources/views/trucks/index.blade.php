<x-app-layout>
    <style>
        .alert-danger {
            background-color: transparent;
            color: red;
            border: none;
            padding: 0;
            margin: 0;
        }

        .is-invalid {
            border-color: red;
        }
    </style>
    <div class="container-fluid">
        <div class="page-title mb-3">
            <h5 class="f-w-700">Trucks</h5>

            <div class="row">
                @include('partials.breadcrumb')
                <!-- <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 text-md-end">
                    <label class="col-form-label text-muted">Last updated {{date("H:i:s, m/d/y", strtotime($latestUpdatedAt))}}</label>
                </div> -->
            </div>
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <div class="container-fluid">
                <div class="page-title mb-3">

                    <div class="alert alert-danger text-left" id="error-container" style="display: none;">
                        <span id="error-message"></span>
                    </div>

                </div>
            </div>



            <div class="row justify-content-end mt-3"> 
                <div class="col-sm-12 col-md-4 col-lg-2 col-xl-5 col-xxl-7 text-md-end">
                    <label class="col-form-label w-auto text-muted"><b>Filter by:</b></label>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-10 col-xl-7 col-xxl-5 light-card p-3 rounded">
                    <div class="row align-items-end">
                        <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-8">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-2 col-xxl-2 text-xl-end">
                                    Roles
                                </label> 
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10 col-xxl-10">
                                <select id="trailer_type" name="trailer_type" class="form-select">
                                    <option value="">Select Trailer Type</option>
                                    @foreach($distinctTrailerTypes as $trailerType)
                                    <option value="{{ $trailerType }}">{{ $trailerType }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-3 mt-sm-0">
                            <button id="add-truck-button" class="btn ">  Add Truck </button>
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
                                <select id="trailer_type" name="trailer_type" class="form-select">
                                    <option value="">Select Trailer Type</option>
                                    @foreach($distinctTrailerTypes as $trailerType)
                                    <option value="{{ $trailerType }}">{{ $trailerType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-flex align-items-center ms-3 ps-3">
                                <button id="add-truck-button" class="btn ">
                                    Add Truck
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
  
    </div>
    <div class="container-fluid">
        <div class="border-box">
            <div class="col-sm-12 col-lg-12 col-xl-12">
                <div class="table-responsive">
                <table table id="truck-table" class="table dataTable no-footer" >
                    <thead>
                        <tr>
                            <th>Truck Model Name</th>
                            <th>Trailer Type</th>
                            <th>VIN</th>
                            <th>Reg. No.</th>
                            <th>Payload Capacity</th>
                            <th>Unladen Weight</th>
                            <th>Gross Weight</th>
                            <th style="min-width: 150px;">Status</th>
                            <th style="min-width: 100px;">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            </div>
        </div>
    </div>

    <script>
        const truckDataTable = "{{ route('trucks.datatables') }}";
        const truckSave = "{{ route('trucks.saveTruck') }}";
    </script>
    <script src="{{ asset('js/truck/truck.js') }}"></script>
</x-app-layout>