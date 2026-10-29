<x-guest-layout> <!-- login page start--> <div class="auth-pages">
  <div class="login-logo"><a class="logo text-start" href="{{ url('/') }}"><img class="img-fluid for-light"
     src="{{ asset('assets/images/logo/logo.png') }}" alt="looginpage"></a></div> <div class="row"> <div class="col-xl-7 col-lg-4">
  </div>
  <div class="col-xl-5 col-lg-8 p-0">
  <div class="login-card login-dark">
  <div class="login-main">
    <form method="POST" class="theme-form" id="changePassword" action=" {{ route('password.updatePassword') }}">
    @method('PUT')
    @csrf
    <!-- Password Reset Token -->
    <input type="hidden" name="token" value="{{ $token }}">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-center text-center alert alert-success successclass" id="successMessage" :status="session('status')" />
    @if ($errors->any())
    <div class="mb-4 text-center text-center alert alert-danger errorMessage errorclass" novalidate="" id=" errorMessage">
      <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
      </ul>
    </div>
    @endif
    <h2 class="text-center mb-2 mb-md-4 pb-3"><span>Create New Password</span></h2>
    <!-- Email Address -->
    <div class="form-group mb-4">
      <div class="form-input position-relative">
        <x-text-input id="email" class="form-control" type="email" name="email" value="{{ $user->email }}" readonly
          required autofocus autocomplete="username" placeholder="Enter your email" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>
    </div>

    <!-- Password -->
    <div class="form-group mb-4">
      <div class="form-input position-relative">
        <x-text-input id="password" class="form-control" type="password" name="password" required
          autocomplete="new-password" placeholder="Enter your password" />
        <div class="show-hide"><span class="show eye-icon" title="Show"></span></div>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />

      </div>
    </div>
    <!-- Confirm Password -->
    <div class="form-group">
      <div class="form-input position-relative">
        <x-text-input id="password_confirmation" class="form-control" type="password" name="password_confirmation"
          required autocomplete="new-password" placeholder="Enter confirm password" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
      </div>
    </div>
    <div class="form-group mb-0">
      <button class="btn btn-primary m-auto d-block  px-5 mt-5" data-bs-toggle="modal" data-original-title="test"
        data-bs-target="#exampleModal" type="submit">create password</button>

    </div>
    </form>
  </div>
  </div>
  </div>
  </x-guest-layout>
  <script src="{{ asset('js/auth/authentication.js') }}"></script>
  <!-- Add jQuery library -->