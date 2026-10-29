<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

            <!-- <p><label for="status" class="block text-sm font-medium text-gray-700">Assign Customer's Store Location:</label>
                <select name="store_locations[]" id="store_locations" multiple>
                    <option value="location1">Location 1</option>
                    <option value="location2">Location 2</option>
                    <option value="location3">Location 3</option>
                    
                </select>
            </p> -->

             <div class="row">
            <div class="form-group col-lg-4 col-md-6">
                <label for="status" class="form-label">Assign Customer's Store Location:</label>
                <select class="form-control" name="store_locations[]" id="store_locations" multiple>
                    @foreach($customerLocations as $location)
                    <option value="{{ $location->id }}" {{ $assignedLocations->contains('customer_location_id', $location->id) ? 'disabled' : '' }}>
                        {{ $location->delivery_location_name }} - {{ concatenateAddress($location) }}
                    </option>
                    @endforeach
                </select>
            </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Location Name</th>
                        <th>Delivery Location</th>
                        <th>Shipment Phone No</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locationsAssigned as $location)
                    <tr>
                        <td>{{ $location->name }}</td>
                        <td>{{ $location->delivery_location_name }}</td>
                        <td>{{ concatenateAddress($location) }}</td>
                        <td>{{ $location->delivery_contact_phone }}</td>
                        <td><button type="button" class="btn btn-primary merchandiser-assign" data-merchandiser-id="{{ $merchandiser}}" data-location-id="{{ $location->id }}">Unassign</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> 


<script>
    $(document).ready(function() {
        var select2Element = $('#store_locations').select2();
        // Define the CSRF token globally
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const unassignLocationRoute = "{{ route('users.unassign-location') }}";
        $('#user-edit-form').submit(function(e) {
            // Check if an Ajax request is in progress
            if ($(this).data('ajax-in-progress')) {
                e.preventDefault(); // Prevent the default form submission
               // alert('An Ajax request is in progress. Please wait.');
                // Swal.fire({
                //     icon: 'warning',
                //     title: 'Ajax Request in Progress',
                //     text: 'An Ajax request is already in progress. Please wait.',
                //     showConfirmButton: false, // Show the "OK" button
                // });
                return;
            }

            // Handle the form submission via the form's action attribute
        });
        $('.merchandiser-assign').on('click', function() {
            const merchandiserId = $(this).data('merchandiser-id');
            const locationId = $(this).data('location-id');

            var selectedValues = $('#store_locations').val();
            var valuesToRemove = [locationId];

            // Remove valuesToRemove from selectedValues
            // Use the filter method to remove the specified value
            selectedValues = selectedValues.filter(function(value) {
                return value != locationId;
            });
            $('#user-edit-form').data('ajax-in-progress', true);
            // Update the select2 dropdown with the modified selected values
            select2Element.val(selectedValues).trigger('change');
            // Make an Ajax request to unassign the location using the POST method
            $.ajax({
                type: 'POST', // Use the POST method
                url: unassignLocationRoute,
                data: {
                    _token: csrfToken,
                    merchandiser_id: merchandiserId,
                    location_id: locationId,
                },
                success: function(response) {
                    if (response.success) {
                        // Show a success SweetAlert notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Location Unassigned',
                            text: 'The location has been successfully unassigned.',
                        }).then(function() {
                            // Reload the page after the user clicks "OK"
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        });
                    } else {

                        // Show an error SweetAlert notification
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Unassign Location',
                            text: 'An error occurred while unassigning the location.',
                        });
                    }
                },
                error: function() {
                    // Show an error SweetAlert notification
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.',
                    });
                },
                complete: function() {
                    // Reset the flag when the Ajax request is complete
                    $('#user-edit-form').data('ajax-in-progress', false);
                }
            });
        });


    });
</script>