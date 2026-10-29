<x-guest-layout>
  <div class="auth-pages">
    <div class="login-logo"><a class="logo text-start" href="{{ url('/') }}"><img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}"alt="looginpage"></a></div>
    <div class="row">
      <div class="col-xl-7 col-lg-4"></div>
      <div class="col-xl-5 col-lg-8 p-0">
        <div class="login-card login-dark">

          <div class="login-main">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-center text-center alert alert-success successclass" id="successMessage" :status="session('status')" />
            <div class="authenticate">
              <form class="row submit-loading" id="otpform" action="{{ route('otp.verify', ['token' => $token]) }}" method="post">
                @csrf
                <header>
                  <i class="bx bxs-check-shield"></i>
                </header>
                <div class="theme-form">
                  <h2 class="text-center mb-2  pb-3"><span>You've got</span> mail!</h2>
                  <img src="../assets/images/login/otp-phone.png" class="img-fluid m-auto d-block mb-3">
                  <div class="col otp-generate input-field email_otp" name="email_otp">
                    <input class="form-control code-input" type="text" maxlength="1" pattern="[0-9]" required="" inputmode="numeric" name="otp[]">
                    <input class="form-control code-input" type="text" maxlength="1" pattern="[0-9]" required="" inputmode="numeric" name="otp[]">
                    <input class="form-control code-input" inputmode="numeric" type="text" maxlength="1" pattern="[0-9]" required="" name="otp[]">
                    <input class="form-control code-input" type="text" maxlength="1" pattern="[0-9]" required="" inputmode="numeric" name="otp[]">
                  </div>



                  <div class="otpError text-danger">@if ($errors->any())
                      @foreach ($errors->all() as $error)
                      <p class="text-danger">{{ $error }}</p>
                      @endforeach
                  @endif</div>
                  

                  <!-- <div class="text-center text-muted mb-4 mt-3"> Didn't receive email? You can resend in <span class="text-success"> 15 s.</span></div> -->
                  <div class="form-group mb-0">
                    <button class="btn btn-primary m-auto d-block px-5 " type="submit">
                      <span class="spinner-border spinner-border-sm d-none spinner-btn" role="status" aria-hidden="true"></span>
                      Continue
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-guest-layout>
<script src="{{ asset('js/auth/authentication.js') }}"></script>