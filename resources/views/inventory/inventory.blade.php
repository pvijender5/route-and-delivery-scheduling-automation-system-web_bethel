<div class="border-box">
    <h6 class="mb-3">Item/Inventory Detail: <span class="text-gray text-end"></span>@if($shipment->shipmentDeliveries) <strong>{{ "P-".$shipment->shipmentDeliveries->P_quantity." R-".$shipment->shipmentDeliveries->R_quantity." T-".$shipment->shipmentDeliveries->T_quantity }} </strong>@endif</h6>
    @if (count($inventoryItems) > 0)
    <div class="table-responsive">
        <x-inventory-table :inventoryItems="$inventoryItems" :shipment="$shipment" />
    </div>
    @else
    <p>No records found.</p>
    @endif
</div>
<br/>