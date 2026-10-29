<style type="text/css">
    .toggle-switch {
        display: inline-block;
        position: relative;
        width: 35px;
        height: 10px;
    }

    .toggle-switch .hidden {
        display: none;
    }

    .toggle-switch .toggle-label {
        display: block;
        width: 30px;
        height: 14px;
        background-color: #ccc;
        border-radius: 34px;
        cursor: pointer;
        position: absolute;
        top: 0;
        left: 0;
    }

    .toggle-switch .toggle-label:after {
        content: '';
        width: 10px;
        height: 10px;   
        background-color: white;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: transform 0.4s;
    }

    .toggle-switch .hidden:checked+.toggle-label {
        background-color: #608339;
    }

    .toggle-switch .hidden:checked+.toggle-label:after {
        transform: translateX(16px);
    }

    .toggle-switch span {
        margin-left: 10px;
        vertical-align: middle;
        display: none;
    }
    .driver-toggle-switch {
        display: inline-block;
        position: relative;
        width: 35px;
        height: 10px;
    }

    .driver-toggle-switch .hidden {
        display: none;
    }

    .driver-toggle-switch .toggle-label {
        display: block;
        width: 30px;
        height: 14px;
        background-color: #ccc;
        border-radius: 34px;
        cursor: pointer;
        position: absolute;
        top: 0;
        left: 0;
    }

    .driver-toggle-switch .toggle-label:after {
        content: '';
        width: 10px;
        height: 10px;
        background-color: white;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: transform 0.4s;
    }

    .driver-toggle-switch .hidden:checked+.toggle-label {
        background-color: #608339;
    }

    .driver-toggle-switch .hidden:checked+.toggle-label:after {
        transform: translateX(16px);
    }

    .driver-toggle-switch span {
        margin-left: 10px;
        vertical-align: middle;
        display: none;
    }
</style>
<x-app-layout>
    
    <script src="{{ asset('js/user/user.js') }}"></script>
    <div class="container-fluid">
        <div class="page-title mb-3">
            <h5 class="f-w-700">Users</h5>
            <div class="row">
                @include('partials.breadcrumb')
            </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


            <div class="row justify-content-end mt-3"> 
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-3 text-xxl-end">
                    <label class="col-form-label w-auto text-muted"><b>Filter by:</b></label>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-9 light-card p-3 rounded">
                    <div class="row">
                        <div class="col-12 col-sm-5 col-md-5 col-lg-5 col-xl-5">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-2 col-xxl-2 text-xl-end">
                                    Roles
                                </label> 
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10 col-xxl-10">
                                    <select id="role-filter" class="form-select">
                                        <option value="">All Roles</option>
                                        @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-5 col-md-5 col-lg-5 col-xl-5">
                              <div class="form-group row"> 
                                <label class="col-form-label col-sm-12 col-md-12 col-lg-12 col-xl-2 col-xxl-2 text-xl-end">Status</label> 
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10 col-xxl-10">
                                    <select id="status-filter" class="form-select  digits">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 mt-3 mt-sm-0">
                            <a class="btn" href="{{ route('users.create') }}"> Add User</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="border-box">
            <div class="col-sm-12 col-lg-12 col-xl-12">
                <div class="table-responsive">
                    <table class="table" id="user-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Primary Mobile No</th>
                                <th>Roles</th>
                                @if (Auth::user()->role->id == \App\Constants\Roles::SUPERADMIN_ID)
                                <th>Route Lock</th>
                                @endif
                             @if (Auth::user()->role->id == \App\Constants\Roles::SUPERADMIN_ID)
                                    <th>Driver Assigned</th>
                                @endif
                                <th style="min-width: 150px;">Status</th>
                                <th style="min-width: 150px;">View</th>
                                <!--Add more columns as needed -->
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
        const userDataTable = "{{ route('users.datatables') }}";
        const statusUpdate = "{{ route('users.updateStatus') }}";
        const unlockStatus = "{{ route('users.unlockStatus') }}";
        const unlockDriverAccess = "{{ route('users.unlockDriverAccess') }}";

    </script>