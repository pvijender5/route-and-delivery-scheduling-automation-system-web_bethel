   <x-app-layout>
       <script>
           const customerList = "{{ route('customers.datatables') }}";
           const shipmentImg = "{{ asset('assets/images/btn-icons/truck-fast-outline.svg') }}";
       </script>
       <style>
           #subTable {
               display: none;
           }
       </style>
       <script src="{{ asset('js/common.js') }}"></script>
       <script src="{{ asset('js/customer/customer.js') }}"></script>
       <div class="container-fluid">
          @if(session('success'))
            <div class="alert alert-success" id="successMessage">
            </div>
            @endif
           <div class="page-title mb-3">
               <h5 class="f-w-700">Customers</h5>
               <div class="row">
                   @include('partials.breadcrumb')
                   <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 text-md-end">

                       <label class="col-form-label">Last updated {{date("H:i:s, m/d/y", strtotime($latestUpdatedAt))}}</label>
                       <!-- <button class="btn btn-primary ms-3">Update List</button> -->

                   </div>
               </div>
           </div>
       </div>
       <div class="container-fluid">
           <div class="col-sm-12 col-lg-12 col-xl-12">
               <div class="border-box">
                   <div class="table-responsive">
                       <table class="table" id="customertable">
                           <thead>
                               <tr>
                                   <th scope="col">Customer ID</th>
                                   <th scope="col">Customer Name </th>
                                   <th scope="col">Email </th>
                                   <th scope="col">Primary Mobile No</th>
                                   <th scope="col">Shipments</th>
                                   <th scope="col">View</th>
                               </tr>
                           </thead>
                       </table>
                   </div>
               </div>
           </div>
       </div>
       @include('modals.customer_modal')
       @include('modals.viewnotes_modal')
       @include('modals.inventory_modal')
       @include('modals.notes_modal')

   </x-app-layout>