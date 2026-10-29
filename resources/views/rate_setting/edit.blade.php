<x-app-layout>
    <style>
        .error-message,
        .text-red-600,
        .text-red-400,
        .text-red-500 {
            color: red;
            font-size: 14px;
        }

        /* Style for highlighting input fields */
        .error-input {
            border: 1px solid red;
        }
    </style>
     <script src="{{ asset('js/rateSetting/rateSetting.js') }}"></script>
    @if(session('success'))
    <div class="alert alert-success" id="successMessage">
        {{ session('success') }}
    </div>
    @endif
    <div class="container-fluid">
        <div class="card height-equal m-0">
            <div id="successMessage" style="display: none;" class="alert alert-success mb-3">
            </div>
            <div id="errorMessage" style="display: none;" class="alert alert-danger mb-3">
            </div>
        </div>
        <div class="page-title mb-3">
            <h5 class="f-w-700 px-0">Rate Setting</h5>
            <div class="row">
                @include('partials.breadcrumb')
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        @endif

        <div class="border-box card">
            <form id="rate-edit-form" action="{{ route('rate-setting.update', $rateSetting->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="row mb-3">
                        <!-- Email -->

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="rate_per_mile" class="form-label">Rate Per Mile</label>
                            <input type="text" name="rate_per_mile" id="rate_per_mile" class="form-control"
                                value="{{ $rateSetting->rate_per_mile }}">
                            @error('rate_per_mile')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="base_rate" class="form-label">Base Rate</label>
                            <input type="number" name="base_rate" id="base_rate" class="form-control"
                                value="{{ $rateSetting->base_rate }}">
                            @error('base_rate')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="base_add" class="form-label">Base Add</label>
                            <input type="number" name="base_add" id="base_add" class="form-control"
                                value="{{ $rateSetting->base_add }}">
                            @error('base_add')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="fsc" class="form-label">FSC</label>
                            <input type="number" name="fsc" id="fsc" class="form-control"
                                value="{{ $rateSetting->fsc }}">
                            @error('fsc')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="fsc_add" class="form-label">FSC Add</label>
                            <input type="number" name="fsc_add" id="fsc_add" class="form-control"
                                value="{{ $rateSetting->fsc_add }}">
                            @error('fsc_add')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="rate_inc" class="form-label">Rate Inc(%)</label>
                            <input type="number" name="rate_inc" id="rate_inc" class="form-control"
                                value="{{ $rateSetting->rate_inc }}">
                            @error('rate_inc')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="min_per_mile" class="form-label">Min Per Mile</label>
                            <input type="number" name="min_per_mile" id="min_per_mile" class="form-control"
                                value="{{ $rateSetting->min_per_mile }}">
                            @error('min_per_mile')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="fork_bill" class="form-label">Fork Bill</label>
                            <input type="number" name="fork_bill" id="fork_bill" class="form-control"
                                value="{{ $rateSetting->fork_bill }}">
                            @error('fork_bill')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="fork_pay" class="form-label">Fork Pay</label>
                            <input type="number" name="fork_pay" id="fork_pay" class="form-control"
                                value="{{ $rateSetting->fork_pay }}">
                            @error('fork_pay')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="stop_pay" class="form-label">Stop Pay</label>
                            <input type="number" name="stop_pay" id="stop_pay" class="form-control"
                                value="{{ $rateSetting->stop_pay }}">
                            @error('stop_pay')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="base_var" class="form-label">Base Var</label>
                            <input type="number" name="base_var" id="base_var" class="form-control"
                                value="{{ $rateSetting->base_var }}">
                            @error('base_var')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="exp_var" class="form-label">Exp Var</label>
                            <input type="number" name="exp_var" id="base_var" class="form-control"
                                value="{{ $rateSetting->exp_var }}">
                            @error('exp_var')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="add_base" class="form-label">Add Base</label>
                            <input type="number" name="add_base" id="add_base" class="form-control"
                                value="{{ $rateSetting->add_base }}">
                            @error('add_base')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                            <label for="extra" class="form-label">Extra</label>
                            <input type="number" name="extra" id="extra" class="form-control"
                                value="{{ $rateSetting->extra }}">
                            @error('extra')
                            <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="text-end mt-4">
                            <button type="submit" class="btn add-sub-driver add_button thirdbuttons">
                                Save
                            </button>
                        </div>
                        <div class="col-md-12">
                    </div>
                 </div>                    
            </form>
        </div>
    </div>
    <div class="border-box card mt-4">
    <form id="rate-edit-form-calculate" action="{{ route('rate-setting-calculate.edit', $rateSetting->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <h6 class="mb-3">Calculation Test Fields</h6>
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <label for="miles" class="form-label">Miles</label>
                <input type="text" name="miles" id="miles" class="form-control"
                            value="{{ $rateSetting->miles }}">
                @error('miles')
                <span class="text-red-600">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <label for="stops" class="form-label">Stops</label>
                <input type="number" name="stops" id="stops" class="form-control"
                            value="{{ $rateSetting->stops }}">
                @error('stops')
                <span class="text-red-600">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <label for="fork_lift" class="form-label">Fork lift</label>
                <input type="number" name="fork_lift" id="fork_lift" class="form-control"
                    value="{{ $rateSetting->fork_lift }}">
                @error('fork_lift')
                <span class="text-red-600">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <label for="diesel_price" class="form-label">Diesel Price</label>
                <input type="number" name="diesel_price" id="diesel_price" class="form-control"
                    value="{{ $rateSetting->diesel_price }}" step="0.01">
                @error('diesel_price')
                <span class="text-red-600">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <div class="light-card p-3 rounded">
                    <label for="carrierOwnerTotalRate" class="form-label"><b>Carrier Owner Rate:-</b></label>
                    <span class="text-red-600">{{$carrierOwnerTotalRate['rateCarrier'] }}
                                </span>
                </div> 
            </div> 
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <div class="light-card p-3 rounded">
                    <label for="carrierOwnerTotalRate" class="form-label"><b>Forklift:-</b></label>
                    <span class="text-red-600">{{$carrierOwnerTotalRate['forkliftCarrier'] }}
                                </span>
                </div>
            </div>
                    
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <div class="light-card p-3 rounded">
                    <label for="carrierOwnerTotalRate" class="form-label"><b>Carrier Owner Total Rate:-</b></label>
                    <span class="text-red-600">{{$carrierOwnerTotalRate['totalRateCarrier'] }}
                            </span>
                </div>                                   
            </div>


        <div class="text-end">
            <button type="submit" class="btn add-sub-driver add_button thirdbuttons">Calculate</button>
        </div>
        </form> 
    </div>
    </div>
    </div>
    <script>
    </script>
</x-app-layout>