    <x-app-layout>
      <style>
        .images {
          width: 500px;
          height: 400px;
        }
      </style>

      <div class="container-fluid">
        <div class="page-title mb-3">
          <div class="row">
            <div class="col-6">
              <h5 class="mb-2 f-w-700">My Profile</h5>
            </div>
        </div>
        </div>

        <div class="user-profile">
      
          <div class="card hovercard border-box text-center">
            <div class="cardheader"></div>
              <div class="user-image">
                <div class="avatar"> 
                @if($user->profile_picture) 
                  <img src="{{asset('uploads/'.$user->profile_picture)}}" id="previewImage" class="profile_file" alt="images">
                @else
                  <img src="{!!asset('assets/images/User-Profile-PNG.png')!!}" id="previewImage" class="images" alt="images">
                @endif            
              </div>
              <label for="profile_picture" id="fileLabel"><div class="icon-wrapper"><i class="icofont icofont-pencil-alt-5"></i></div></label>
              <input type="file" id="profile_picture" accept="image/*" style="display: none;">
            </div>
            <div class="info">
              <div class="row">
                <div class="col-sm-6 col-lg-4 order-sm-1 order-xl-0">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="ttl-info text-center">
                        <h6 class="f-w-700"><i class="fa fa-envelope"></i>&nbsp;&nbsp;&nbsp;Email</h6><span id="email" class="text-muted" readonly>{{$user->email}}</span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="ttl-info text-center"> 
                        <h6 class="f-w-700"><i class="fa fa-id-card-o" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;User ID</h6><span id="user_id" class="text-muted" readonly>{{$user->user_id}}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 col-lg-4 order-sm-0 order-xl-1">
                  <div class="user-designation">
                    <div class="title">{{$user->name}}</div>
                    <div class="desc" id="role_id" readonly>{{ $user->role->display_name }}</div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4 order-sm-2 order-xl-2">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="ttl-info text-center">
                        <h6 class="f-w-700"><i class="fa fa-phone"></i>&nbsp;&nbsp;&nbsp;Contact Number</h6><span id="primary_contact_number" class="text-muted f-light" readonly>{{$user->primary_contact_number}}</span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="ttl-info text-start">
                        <h6><i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;Status</h6><span class="text-capitalize" id="status" readonly>{{$user->status}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      
      </div>

      <div class="fotter-button text-end">
        <!-- <button class="btn btn btn-primary me-2" onclick="goBack()">Cancel</button> -->
        <button class="btn" onclick="goBack()">Back</button>
      </div>
      </div>
    </x-app-layout>

    <script>
    $(document).ready(function () {
        $('#profile_picture').change(function () {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#previewImage').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);

                // Upload the file using AJAX
                var formData = new FormData();
                formData.append('profile_picture', file);

                $.ajax({
                    type: 'POST',
                    url: '{{ url("/upload-profile-picture") }}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        console.log(data);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        });
    });
</script>