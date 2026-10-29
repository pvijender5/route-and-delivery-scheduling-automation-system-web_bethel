<!-- customer.blade.php -->
<div class="r-user-profile">
    <div class="card hovercard border-box">
        <div class="info p-0">
            <h6 class="mb-3">Customer Detail</h6>
            <div class="row">
                <div class="col-sm-12 mb-3">
                    <div class="ttl-info balance-card text-start light-card p-3 rounded">
                        <div class="svg-box">
                            <i data-feather="user"></i>
                        </div>
                        <div>
                            <span class="f-light">Customer Name</span>  
                            <h6 class="mt-1 mb-0">{{ $customer->name }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 mb-3">
                    <div class="ttl-info balance-card text-start light-card p-3 rounded">
                        <div class="svg-box">
                            <i data-feather="credit-card"></i>
                        </div>
                        <div>
                            <span class="f-light">Customer ID</span>  
                            <h6 class="mt-1 mb-0">{{ $customer->customer_id }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12  mb-3">
                    <div class="ttl-info balance-card text-start light-card p-3 rounded">
                        <div class="svg-box">
                            <i data-feather="mail"></i>
                        </div>
                        <div>
                            <span class="f-light">Email</span>  
                            <h6 class="mt-1 mb-0"><span>@if($customer->email){{ $customer->email }}@else N/A @endif</span></h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 ">
                    <div class="ttl-info balance-card text-start light-card p-3 rounded">
                        <div class="svg-box">
                            <i data-feather="phone"></i>
                        </div>
                        <div>
                            <span class="f-light">Primary Mobile No</span>  
                            <h6 class="mt-1 mb-0"><span>@if($customer->phone){{ $customer->phone }}@else N/A @endif</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

