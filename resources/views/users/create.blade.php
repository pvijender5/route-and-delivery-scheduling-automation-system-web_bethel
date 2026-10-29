<x-app-layout>
    <script src="{{ asset('js/user/userCreate.js') }}"></script>
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    @include('partials.breadcrumb')

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-12 col-xl-12">
        <div class="card border-box">
            <div class="card-header px-0 pt-0">
                <h4 class="card-title mb-0">Create User</h4>
            </div>
            @if(session('success'))
            <div class="alert alert-success" id="successMessage">
                {{ session('success') }}
            </div>
            @endif
            <form action="{{ route('users.store') }}" method="POST" id="createUser">
                <div class="card-body px-0">
                    <div class="row">
                        @csrf
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3 "> <label class="form-label" for="firstname">First Name</label>
                                <input class="form-control" name="firstname" id="firstname" type="text" maxlength="50" aria-label="firstname" value="{{ old('firstname') }}">
                                @error('firstname')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3 "> <label class="form-label" for="lastname">Last Name</label>
                                <input class="form-control" name="lastname" id="lastname" type="text" maxlength="50" aria-label="lastname" value="{{ old('lastname') }}">
                                @error('lastname')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3 ">
                                <label class="form-label" for="role">Role</label>
                                <select name="role_id" id="role_id" class="form-select rounded-md shadow-sm block w-full" name="role_id">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                         <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3 ">
                                <label class="form-label" for="status">status</label>
                                <select id="status" class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3 ">
                                <label class="form-label" for="email">Email address</label>
                                <input class="form-control" id="email" type="email" value="{{ old('email') }}" name="email" maxlength="50">
                                @error('email')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3 ">
                                <label class="form-label" for="primary_contact_number">Primary Contact Number</label>
                                <input class="form-control" id="primary_contact_number" type="text" aria-label="primary_contact_number" name="primary_contact_number" maxlength="25" value="{{ old('primary_contact_number') }}">
                                @error('primary_contact_number')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3" id="warehouseDropdown" style="display: none;">
                            <div class="mb-3 ">
                                <label class="form-label" for="warehouse_id">Warehouse</label>
                                <select name="warehouse_id" class="form-select rounded-md shadow-sm block w-full" id="warehouse_id">
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>




                    </div>
                </div>
                <!-- ... Display other driver form details ... -->
                <div id="driverForm" style="display: none;">
                    @include('users.driver') 
                </div>             
                <div class="row" id="merchandisersLocation" style="display: none;">
                    <div class="form-group col-lg-4 col-md-6">
                        <label for="status" class="form-label">Assign Customer's Store Location:</label>
                        <select class="form-control" name="store_locations[]" id="store_locations" multiple>
                            @foreach($customerLocations as $location)
                            <option value="{{ $location->id }}">
                                {{ $location->delivery_location_name }} - {{ concatenateAddress($location) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer px-0 pb-0 text-end">
                    <div class="">
                        <input type="button" class="btn btn-primary me-2" value="Back" onclick="goBack()">
                        <button type="submit" class="btn ">Add User</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

</x-app-layout>
<script>
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$(document).ready(function () {  $.validator.addMethod("phoneUS", function (primary_contact_number, element) {
    // Regular expression for a USA phone number (10 digits, optional hyphens or spaces)
    return this.optional(element) || /^(\+\d{1,2}\s?)?(\(\d{3}\)|\d{3})([-.\s]?)\d{3}([-.\s]?)\d{4}$/.test(primary_contact_number);
  }, "Please enter a valid phone number.");
  $('#primary_contact_number').keyup(function () {
    if ($(this).val().length < 10) {
      $(this).val($(this).val().replace(/-/g, ''))
    } else {
      $(this).val($(this).val().replace(/(\d{3})\-?(\d{3})\-?(\d{4})/, '$1-$2-$3'))
    }
  });

  $("#createUser").validate({
    errorClass: "error",
    highlight: function (element, errorClass) {},
    rules: {
      firstname: {
        required: true,
      },
      lastname: {
        required: true,
      },
      username: {
        required: true,
      },
      primary_contact_number: {
        required: true,
        phoneUS: true,
        remote: {
          type: "POST",
          url: "/validate-unique-user", // Replace with your server-side endpoint
          data: {
            primary_contact_number: function () {
              return $('#primary_contact_number').val();
            },
            userId: function () {
              return $('#user_id').val();
            }
          },
          async: false,
        }
      },
      email: {
        required: true,
        email: true,
        remote: {
          type: "POST",
          url: "/validate-unique-user", // Replace with your server-side endpoint
          data: {
            email: function () {
              return $('#email').val();
            },
            userId: function () {
              return $('#user_id').val();
            }
          },
          async: false,
        }
      },
      password: {
        required: true,
        minlength: 8,
        strongPassword: true
      },
      confirm_password: {
        required: true,
        equalTo: "#password",
      },
 
    },
    messages: {
      firstname: {
        required: "Please enter first name.",
      },
      lastname: {
        required: "Please enter last name.",
      },
      username: {
        required: "Please enter username.",
      },
      primary_contact_number: {
        required: "Please enter primary contact number.",
        phoneUS: "Please enter a valid  phone number.",
        remote: "This contact number is already in use."
      },
      email: {
        required: "Please enter email.",
        remote: "This email adress is already in use."
      },
      role_id: {
        required: "Please select user role.",
      },
      password: {
        required: "Please enter password.",
        minlength: "Password must be at least 8 characters long",
        strongPassword: "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character",
        
      },
      confirm_password: {
        required: "Please confirm your password.",
        equalTo: "Confirm password can't match with password "
      },

    },
    errorElement: 'span',
    errorClass: 'text-danger',
  });
  $.validator.addMethod("strongPassword", function (value, element) {
    return this.optional(element) ||
      /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/.test(value);
  }, "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character");
});
 $('#role_id').change(function () {
  if ($(this).val() === '2') {
    $('#truck_model_name').rules('add', {
        required: true,
        messages: {
            required: "Please select Truck unit."
        }
    });
} else {
    $('#truck_model_name').rules('remove', 'required');
}

  });



   function showWarehouseDropdown() {
        var roleSelect = document.getElementById('role_id');
        var warehouseDropdown = document.getElementById('warehouseDropdown');

        // Show the warehouse dropdown if the selected role is "Loader"
        if (roleSelect.value == loaderRoleId) {
            warehouseDropdown.style.display = 'block';
        } else {
            // Hide the warehouse dropdown for other roles
            warehouseDropdown.style.display = 'none';
        }
    }

</script>