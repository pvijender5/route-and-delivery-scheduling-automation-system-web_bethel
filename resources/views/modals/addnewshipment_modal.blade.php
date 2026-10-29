<div class="modal fade show" id="addnewshipment" tabindex="-1" aria-labelledby="addnewshipment" aria-modal="true"
    role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-xl post-route assign-route" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4  id="myExtraLargeModal">
                    Add a New Shipment
                </h4>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <p>Total Available Capacity in Truck: <b><input type="hidden" id="truckAvailableCapacity" /><span class="availableCapacity">0</span> lb</b></p>
                <p>
                Subtracted Quantity for New Shipment: <b><span class="subtractedQuantity">0</span> lb</b></p>
                <p>
                Remaining Capacity in Truck: <b><span class="remainingCapacity">0</span> lb</b></p>
                <h6 class="mb-2">List of New Open Shipments:</h6>
                <table id="shipmentTable" class="table table-responsive text-nowrap table-scroll custom-scrollbar mb-4">
                    <thead>
                        <tr>
                            <th style="width:8%">Shipment ID</th>
                            <th style="width:10%">Customer Name</th>
                            <th style="width:10%">Location Name</th>
                            <th style="width:12%">Delivery Location</th>
                            <th style="width:12%">Shipment/Delivery Date</th>
                            <th style="width:10%">Delivery Window</th>
                            <th style="width:10%">Quantity</th>
                            <th style="width:10%">Shipment Notes</th>
                            <th style="width:10%">Shipment Status</th>
                            <th style="width:8%">Save</th>
                        </tr>
                    </thead>
                    <tbody id="newShipment">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="route_id" />
                <button class="btn btn-primary post-route-close" type="button" data-bs-dismiss="modal">
                    Close
                </button>
                <button class="btn btn-success-outline save-addNewShipment-button" type="button">
                Add & Notify Driver
                </button>
            </div>
        </div>
    </div>
</div>


<style>
  .modal-lg,
        .modal-xl {
            --bs-modal-width: 1327px;
        }
</style>           