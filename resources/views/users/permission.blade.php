<!-- <div class="container">
    <h2></h2>
    <div class="row">
        <div class="col-md-12">
            @if ($user->role->name == App\Constants\Roles::DISPATCHER)
            <p>
                <strong>Special Permission:</strong><br>
                • Dispatcher will have full access to modules Manage Route (Track Routes, Loadboard, Loadboard Pricing, Assign Driver & Carrier Management).
            </p>
            @elseif ($user->role->name == App\Constants\Roles::ROUTER)
            <p>
                <strong>Special Permission:</strong><br>
                • Router has full access to modules Manage Route (Create & Optimize/Plan Routes) & Manage Customer & Shipment Module from Acumatica.
            </p>
            @elseif ($user->role->name == App\Constants\Roles::MERCHANDISER)
            <p>
                <strong>Special Permission:</strong><br>
                • Merchandisers will have mobile access, where they will only have access to the routes that include shipments from assigned customers. They will have view-only access to all routes.
            </p>
            @endif
        </div>
    </div>
</div> -->