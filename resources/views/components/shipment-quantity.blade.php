@props(['shipment'])

<td>
    @php
        $pQuantity = isset($shipment->shipmentDeliveries->P_quantity) ? "P-" . $shipment->shipmentDeliveries->P_quantity : "";
        $rQuantity = isset($shipment->shipmentDeliveries->R_quantity) ? " R-" . $shipment->shipmentDeliveries->R_quantity : "";
        $tQuantity = isset($shipment->shipmentDeliveries->T_quantity) ? " T-" . $shipment->shipmentDeliveries->T_quantity : "";
        $combinedQuantities = $pQuantity . $rQuantity . $tQuantity;
    @endphp
    {{ $combinedQuantities }}
    <span href="#" class="view-shipment-link" data-shipment-id="{{ $shipment->id }}">
        <i class="fa fa-info-circle"></i>
    </span>
</td> 