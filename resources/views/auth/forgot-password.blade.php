<x-guest-layout>
  <div class="auth-pages">
    <div class="login-logo"><a class="logo text-start" href="{{ url('/') }}"><img class="img-fluid for-light"  src="{{ asset('assets/images/logo/logo.png') }}"alt="looginpage"></a></div>
    <div class="row">
      <div class="col-xl-7 col-lg-4"></div>
      <div class="col-xl-5 col-lg-8 p-0">
        <div class="login-card login-dark">

          <div class="login-main">
          <x-auth-session-status class="mb-4 text-center text-center alert alert-success successclass" id="successMessage" :status="session('status')" />
            <!-- @if ($errors->any())
            <div class="mb-4 text-center text-center alert alert-danger errorMessage errorclass" novalidate="" id=" errorMessage">
              <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif -->
            <form class="theme-form submit-loading" id="forgotpasswordform" method="POST" action="{{ route('password.email') }}">
              @csrf
              <h2 class="text-center mb-2  pb-3"> Welcome to <br><span>FARM SYNC</span></h2>
              <img  src="{{ asset('assets/images/login/otp-phone.png') }}" class="img-fluid m-auto d-block mb-4">
              <div class="form-group mb-4">
                <!-- <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter your email to reset password" /> -->
                <x-text-input type="text" name="emailOrPhone" id="emailOrPhone" required class="form-control" :value="old('emailOrPhone')" required autofocus placeholder="Enter your email or phone to reset password"/>
                <x-input-error :messages="$errors->get('email')" class="mt-2 errorclass errorclass-text" />


                <div class="form-group mb-0">
                  <button class="btn btn-primary m-auto d-block px-5 mt-5 send-otp" type="submit">
                  <span class="spinner-border spinner-border-sm d-none spinner-btn" role="status" aria-hidden="true"></span>
                    Send otp
                  </button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-guest-layout>
<script src="{{ asset('js/auth/authentication.js') }}"></script>