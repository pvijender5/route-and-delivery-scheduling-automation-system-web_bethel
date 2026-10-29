  <!-- Email -->
  <div>
      <label for="primary_contact_number" class="form-label">Primary Contact Number</label>
      <span id="primary_contact_number" class="form-input f-w-600 ms-2">{{ $user->primary_contact_number }}</span>
  
  <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Name -->
      <div>
          <label for="name" class="form-label">Name</label>
          <span id="name" class="form-input f-w-600 ms-2">{{ $user->name }}</span>
      </div>

      <!-- Email -->
      <div>
          <label for="email" class="form-label">Email</label>
          <span id="email" class="form-input f-w-600 ms-2">{{ $user->email }}</span>
      </div>
  </div>
  <div class="row">
      <!-- Role -->
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
          <label for="role" class="form-label">Role</label>
          <input type="hidden" name="role_id" id="role_id" class="form-input " value="{{ $user->role_id }}">
          <select name="role" id="role" class="form-select" disabled>
              @foreach($roles as $role)
              <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                  {{ $role->name }}
              </option>
              @endforeach
          </select>
      </div>

      <!-- Status -->
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
          <label for="status" class="form-label">Status</label>
          <select name="status" id="status" class="form-select" required>
              <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
      </div>
  </div>
</div>
</div>