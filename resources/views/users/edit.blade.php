<x-app-layout>
  <script src="{{ asset('js/common.js') }}"></script>
  @if(session('success'))
  <div class="alert alert-success" id="successMessage">
    {{ session('success') }}
  </div>
  @endif



  <div class="container-fluid">
    <div class="card height-equal">
      <div id="successMessage" style="display: none;" class="alert alert-success">
      </div>
      <div id="errorMessage" style="display: none;" class="alert alert-danger">
      </div>
    </div>
    <div class="page-title mb-3">
      <div class="row">
        <h5 class="f-w-700 ">User Details</h5>
        @include('partials.breadcrumb')
        <div class="col-6">
          <div class="invite-user submit-loading">
            <div class="float-end">
              <input type="hidden" id="carrierOwnerId" name="carrierOwnerId" value="{{$user->id}}" />
              <button type="button" id="userinvitation" class="btn userinvitation"> <span class="spinner-border spinner-border-sm d-none spinner-btn" role="status" aria-hidden="true"></span>
                Invite User</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    @if(Session::has('message'))
    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif

    <div class="border-box">
      <form id="user-edit-form" action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')


        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <input id="user_id" class="user_id" value="{{ old('email', isset($user->id) ? $user->id : null) }}" type="hidden">
          <!-- Name -->
          <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700">
              @if ($user->role->name === App\Constants\Roles::DRIVER)
              Driver ID:
              @elseif ($user->role->name == App\Constants\Roles::ADMIN)
              Admin ID:
              @elseif ($user->role->name == App\Constants\Roles::ROUTER)
              Router ID:
              @elseif ($user->role->name == App\Constants\Roles::MERCHANDISER)
              Merchandiser ID:
              @elseif ($user->role->name == App\Constants\Roles::DISPATCHER)
              Dispatcher ID:
              @elseif ($user->role->name == App\Constants\Roles::LOADER)
              Loader ID:
              @else
              User ID:
              @endif
            </label>
            <!-- <input type="text" name="user_id" id="user_id" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ $user->user_id }}" readonly> -->
            <b><span id="user_id" class="">{{ $user->id }}</span></b>
                <input type="hidden" name="user_id" value="{{ $user->id }}">
            
          </div>

          @include('users.nonadmin')

          @if ($user->role->name == App\Constants\Roles::DISPATCHER || $user->role->name == App\Constants\Roles::ROUTER || $user->role->name == App\Constants\Roles::MERCHANDISER)
            @include('users.permission')
          @endif

          @if(isset($customerLocations))
            @include('users.merchandise',['customerLocations' => $customerLocations, 'assignedLocations' => $assignedLocations,'merchandiser' => $user->id ])
          @endif

          @if($user->role->name === App\Constants\Roles::DRIVER)
          <!-- ... Display other customer details ... -->

            @include('users.driver', ['driverDetail' => $driverDetail]) <!-- Pass the $shipment data to the view -->

            @if(isset($shipments))
              @include('users.shipment', ['shipments' => $shipments]) <!-- Pass the $inventoryItems data to the view -->
            @endif
          @endif



          <!-- Buttons -->
          <div class="text-end mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-primary me-2">
              Back
            </a>
            <button type="submit" class="btn add-sub-driver add_button thirdbuttons">
              Save
            </button>

          </div>

      </form>
    </div>
  </div>
    @include('modals.inventory_modal')
    @php
    $userRole =$user->role->name;
    @endphp
  </div>

  <script>
    $(document).ready(function() {
      $.validator.addMethod("phoneUS", function(primary_contact_number, element) {
        // Regular expression for a USA phone number (10 digits, optional hyphens or spaces)
        return this.optional(element) || /^(\+\d{1,2}\s?)?(\(\d{3}\)|\d{3})([-.\s]?)\d{3}([-.\s]?)\d{4}$/.test(primary_contact_number);
        // return this.optional(element) || /^[\(\)\.\- ]{0,}[0-9]{3}[\(\)\.\- ]{0,}[0-9]{3}[\(\)\.\- ]{0,}[0-9]{4}[\(\)\.\- ]{0,}$/.test(primary_contact_number);
      }, "Please enter a valid phone number.");
      $('#primary_contact_number').keyup(function () {
    if ($(this).val().length < 10) {
      $(this).val($(this).val().replace(/-/g, ''))
    } else {
      $(this).val($(this).val().replace(/(\d{3})\-?(\d{3})\-?(\d{4})/, '$1-$2-$3'))
    }

      $('#user-edit-form').validate({
   
        errorPlacement: function(error, element) {
          error.insertAfter(element);
          error.addClass("error-message");
          error.removeClass("error-input");
        },
        highlight: function(element, errorClass) {
          $(element).addClass(errorClass);
        },
        unhighlight: function(element, errorClass) {
          $(element).removeClass(errorClass);
        },
        rules: {
          // Define validation rules based on user roles
          email: {
            required: true,
            email: true,
            remote: {
                type: "POST",
                url: "/validate-unique-user",
                data: {
                  email: function () {
                    return $('#email').val();
                  },
                  userId: function () {
                    return $('#user_id').val();
                  }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }
          },
          firstname: {
            required: true,
          },
          lastname: {
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
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              async: false,
            }
          },
          @if($userRole === 'driver')
          trailer_type: {
            required: true,
          },
          truck_model_name: {
            required: true,
          },
          @endif
        },
        messages: {
          // Define custom error messages for each field
          email: {
            required: "Please enter an email address.",
            email: "Please enter a valid email address.",
            remote: "This email address is already in use."
          },
          firstname: {
            required: "Please enter an First name.",
          },
          lastname: {
            required: "Please enter an Last name.",
          },
          primary_contact_number: {
            required: "Primary contact number is required.",
            phoneUS: "Please enter a valid USA phone number.",
            remote: "This contact number is already in use."
          },
          @if($userRole === 'driver')
          truck_model_name: {
            required: "Please select Truck unit .",
          },
          @endif
        },
        submitHandler: function(form) {
          // Handle the form submission here (e.g., AJAX or regular form submit)
          form.submit();
        }
      });
      });
    });
    // Hide the success message after 10 seconds (10000 milliseconds)
    $('#successMessage').delay(10000).fadeOut(500);
  </script>

</x-app-layout>