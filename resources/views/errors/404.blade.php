<x-guest-layout>
  <!-- login page start-->
  <!-- Add jQuery library -->
  <!-- Add jQuery library -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Add Bootstrap CSS and JS (if not already included) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

  </head>

  <body>
    <div class="error-container">

      <!-- tap on top starts-->
      <div class="tap-top"><i data-feather="chevrons-up"></i></div>
      <!-- tap on tap ends-->
      <!-- page-wrapper Start-->
      <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- error-404 start-->
        <div class="error-wrapper">
            <div class="container"><h2 style="font-size: 22rem;color: #757575;font-weight: 800;">404</h2>
            <div class="col-md-8 offset-md-2">
              <p class="sub-content">The page you are attempting to reach is currently not available. This may be because the page does not exist or has been moved.</p>
            </div>
            <div><a class="btn btn-danger-gradien btn-lg" href="{{('/customers')}}">BACK TO HOME PAGE</a></div>
          </div>
        </div>
        <!-- error-404 end      -->
      </div>
    </div>
    <!-- latest jquery-->
  
</body>   
</x-guest-layout>