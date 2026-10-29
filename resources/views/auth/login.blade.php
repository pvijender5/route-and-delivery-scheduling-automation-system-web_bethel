<x-guest-layout>
  <!-- login page start-->
  <!-- Add jQuery library -->
  <!-- Add jQuery library -->
  <div class="auth-pages">
   <div class="login-logo"><a class="logo text-start" href="{{ url('/') }}"><img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="looginpage"></a></div>
    <div class="row">
      <div class="col-xl-7 col-lg-4"></div>
      <div class="col-xl-5 col-lg-8 p-0">
        <div class="login-card login-dark">
          <div class="login-main">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-center text-center alert alert-success successclass" id="successMessage" :status="session('status')" />
            <form class="theme-form row g-3" method="POST" id="loginform" novalidate action="{{ url('/') }}">
              @csrf
              <h2 class="text-center mb-2 mb-md-4 pb-3"> Welcome to <br><span>FARM SYNC</span></h2>
              <!-- Email Address -->
              <div class="form-group">
                <input id="email" class="form-control" type="text" name="email" maxlength="50" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email or phone number" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 errorclass errorclass-text" /> 
              </div>
              <!-- <!Password  -->
              <div class="form-group">
                <div class="form-input">
                  <input id="password" class="form-control" type="password" name="password" maxlength="50" required autocomplete="current-password" placeholder="Enter your password" />
                  <x-input-error :messages="$errors->get('password')" class="mt-2 errorclass errorclass-text" />
                  <div class="show-hide">
                    <span class="show eye-icon" title="Show"></span>
                  </div>
                </div>
              </div>
              <!-- Remember Me -->
              <div class="form-group mb-0">
                <div class="checkbox-checked mt-3 mb-4 ">
                  <label class="d-block" for="chk-ani4"></label>
                  <input id="remember_me" type="checkbox" class="checkbox_animated checkbox-custom" name="remember">
                  <label class="text-muted" for="chk-ani4">Stay Signed in</label>
                </div>
                <button class="btn btn-primary m-auto d-block sign-in" type="submit">{{ __('Sign in') }}</button>
              </div>
              @if (Route::has('password.request'))
              <p class="mt-4 mb-0 text-center"><a class="ms-2 text-muted" href="{{ route('password.request') }}">Forgot Password?</a></p>
              @endif
            </form>
          </div>
        </div>
      </div>
</x-guest-layout>
<script src="{{ asset('js/auth/authentication.js') }}"></script>