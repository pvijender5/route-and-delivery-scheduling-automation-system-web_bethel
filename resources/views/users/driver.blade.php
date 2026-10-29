<style>
    .toggle-switch {
        display: inline-block;
        position: relative;
        width: 35px;
        height: 10px;
    }

    .toggle-switch .hidden {
        display: none;
    }

    .toggle-switch .toggle-label {
        display: block;
        width: 30px;
        height: 14px;
        background-color: #ccc;
        border-radius: 34px;
        cursor: pointer;
        position: absolute;
        top: 0;
        left: 0;
    }

    .toggle-switch .toggle-label:after {
        content: '';
        width: 10px;
        height: 10px;
        background-color: white;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: transform 0.4s;
    }

    .toggle-switch .hidden:checked+.toggle-label {
        background-color: #608339;
    }

    .toggle-switch .hidden:checked+.toggle-label:after {
        transform: translateX(16px);
    }

    .toggle-switch span {
        margin-left: 10px;
        vertical-align: middle;
        display: none;
    }

    /* Add your custom CSS styles here */
    .form-group {
        margin-bottom: 15px;
    }
</style>
<div class="row mb-3">

    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
        <label for="status" class="form-label">Truck Unit</label>
        <select id="truck_model_name" name="truck_model_name" class="form-control form-select">
            <option value="">Select truck_model_name</option>
            @php
            $distinctTrailerTypesArray = $distinctTrailerTypes->toArray();
            @endphp
            @foreach($distinctTrailerTypesArray as $trailerType => $id)
            <option value="{{ $id }}" {{ (isset($driverDetail) && isset($driverDetail->truck) && $driverDetail->truck->truck_model_name == $trailerType) ? 'selected' : '' }}>
                {{ $trailerType }}
            </option>
            @endforeach
            @if (isset($driverDetail) && isset($driverDetail->truck) && !array_key_exists($driverDetail->truck->truck_model_name, $distinctTrailerTypesArray))
            <option value="{{ $driverDetail->truck->id }}" selected>
                {{ $driverDetail->truck->truck_model_name }}
            </option>
            @endif
        </select>



        @error('trailer_type')
        <span class="text-red-600">{{ $message }}</span>
        @enderror
    </div>

    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
        <label for="vin" class="form-label">VIN</label>
        <input type="text" class="form-control" id="vin" placeholder="vin" name="vin" value="{{ $driverDetail->truck->vin ?? 'N/A' }}" readonly>
    </div>
</div>

<div class="row mb-4">
    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-2 mb-3 mb-xl-0">
        <div class="checkbox-checked">
            <input type="checkbox" name="make_default" id="status" class="checkbox_animated" @if(isset($driverDetail)) {{ ($driverDetail->make_default == 1) ? 'checked' : '' }} @endif>
            <label for="status" class="form-label label-cursor">Make it Default</label>
        </div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-8 col-xl-10">
        <div class="toggle-switch">
            <input type="checkbox" id="toggle-switch" name="resequencing_shipments_allowed" class="hidden" @if(isset($driverDetail)) {{ ($driverDetail->resequencing_shipments_allowed == 1) ? 'checked' : '' }} @endif>

            <label for="toggle-switch" class="toggle-label"></label>
            @if(isset($driverDetail))
            <span id="status-span">{{ ($driverDetail->resequencing_shipments_allowed == 1) ? 'On' : 'Off' }}</span>
            @else
            <span id="status-span">{{ 'Off' }}</span>
            @endif
        </div>
        <label for="toggle-switch" class="form-label label-cursor">Resequencing of shipments within Route allowed?</label>
        <!-- Hidden input to store the value -->
        <input type="hidden" name="resequencing_shipments_allowed_hidden" id="resequencing_shipments_allowed_hidden" @if(isset($driverDetail)) value="{{ $driverDetail->resequencing_shipments_allowed }}" @endif>
        <input type="hidden" name="driver_id" id="driver_id" @if(isset($driverDetail)) value="{{ $driverDetail->id }}" @endif>
    </div>
</div>

<script>
    const toggleSwitch = document.getElementById('toggle-switch');
    const statusSpan = document.getElementById('status-span');
    const hiddenInput = document.getElementById('resequencing_shipments_allowed_hidden');

    toggleSwitch.addEventListener('change', function() {
        if (this.checked) {
            statusSpan.textContent = 'On';
            hiddenInput.value = 1; // Set the value to 1 when checked
        } else {
            statusSpan.textContent = 'Off';
            hiddenInput.value = 0; // Set the value to 0 when unchecked
        }
    });

    $(document).ready(function() {

        $('#truck_model_name').on('change', function() {
            console.log('Dropdown changed'); // Add this line
            var selectedTruckModel = $(this).val();

            if (selectedTruckModel !== "") {
                $.ajax({
                    type: 'GET',
                    url: '/get-vin/' + selectedTruckModel,
                    success: function(data) {
                        console.log('Received VIN:', data.vin);
                        $('#vin').val(data.vin);
                    },
                    error: function() {
                        // Handle error, if necessary
                    }
                });
            } else {
                $('#vin').val('');
            }
        });
    });
</script>