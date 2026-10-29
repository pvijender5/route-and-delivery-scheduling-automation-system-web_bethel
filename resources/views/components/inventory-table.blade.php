@props(['inventoryItems', 'shipment'])

<div class="border-box">
    <h6 class="mb-3">
        Item/Inventory Detail: 
        <span class="text-gray text-end"></span>
        @if($shipment->shipmentDeliveries)
            <strong>
                {{ "P-".$shipment->shipmentDeliveries->P_quantity." R-".$shipment->shipmentDeliveries->R_quantity." T-".$shipment->shipmentDeliveries->T_quantity }}
            </strong>
        @endif
    </h6>

    @if (count($inventoryItems) > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;" scope="col">Order ID</th>
                        <th scope="col">Item ID</th>
                        <th scope="col">Item Name</th>
                        <th style="width: 10%;" scope="col">Unit Type</th>
                        <th style="width: 10%;" scope="col">Quantity</th>
                        <th style="width: 10%;" scope="col">Unit Weight (lbs)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventoryItems as $item)
                        <tr>
                            <td>{{ $item->order_id }}</td>
                            <td>{{ $item->item_id }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->unit_type }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_weight }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-center">TOTAL</td>
                        <td>
                            @if($shipment->shipmentDeliveries)
                                {{ "P-".$shipment->shipmentDeliveries->P_quantity." R-".$shipment->shipmentDeliveries->R_quantity." T-".$shipment->shipmentDeliveries->T_quantity }}
                            @endif
                        </td>
                        <td>
                            @if($shipment->props_weight)
                                {{ $shipment->props_weight }} lbs
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <p>No records found.</p>
    @endif
</div> 