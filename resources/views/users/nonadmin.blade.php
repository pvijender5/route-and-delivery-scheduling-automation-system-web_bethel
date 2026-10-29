

        <div class="row mb-3">
            <!-- Name -->
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" name="firstname" id="firstname" class="form-control" value="{{ $user->firstname }}">
                @error('firstname')
                <span class="text-red-600">{{ $message }}</span>
                @enderror
            </div>

             <!-- Name -->
             <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" name="lastname" id="lastname" class="form-control" value="{{ $user->lastname }}">
                @error('lastname')
                <span class="text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}">
         
            </div>
            <!-- Primary Contact Number -->
              <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
                <label for="primary_contact_number" class="form-label">Primary Contact Number</label>
                <input type="text" name="primary_contact_number" id="primary_contact_number" class="form-control" value="{{ $user->primary_contact_number }}">
                @error('primary_contact_number')
                <span class="text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3 mb-3">
            <!-- Role -->
                <label for="role" class="form-label">Role</label>
                <input type="hidden" name="role_id" id="role_id" class="form-input " value="{{ $user->role_id }}">
                <select name="role" id="role" class="form-control" disabled>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ $role->display_name }}
                    </option>
                    @endforeach
                </select>
            </div>
    
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
            <!-- Status -->
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            @if($user->role->name === App\Constants\Roles::LOADER)
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                <label for="warehouse_id" class="form-label">Warehouse</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-select mt-1 block w-full">
                    <option value="">-select-</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ isset($user->warehouseTeamDetail) && $user->warehouseTeamDetail->warehouse_id == $warehouse->id ? 'selected' : '' }}> 
                            {{$warehouse->name}}</option>
                            @endforeach
                        </select>
                        
                    </div>
           @endif
        </div>
        


     
