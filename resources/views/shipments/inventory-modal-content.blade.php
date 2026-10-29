<p>Shipment Inventory Details:</p>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Item Name</th>
                <th>Item Description</th>
                <th>Quantity</th>
                <th>Weight</th>
                <!-- ... Add more table headers if needed ... -->
            </tr>
        </thead>
        <tbody>
            <!-- Loop through shipments data and populate table rows -->
            @forelse ($shipmentsInventoryData as $index => $inventory)
            <tr>
                <td>{{ $index + 1 }}</td> <!-- Serial number (Sl No) -->
                <td>{{ $inventory->item_name }}</td>
                <td>{{ $inventory->item_id }}</td>
                <td>{{ $inventory->quantity }}</td>
                <td>{{ $inventory->unit_weight }}</td>

                <!-- ... Populate additional table cells ... -->
            </tr>
            @empty
            <!-- This block will be executed if no records are found -->
            <tr>
                <td class="text-center" colspan="5">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>