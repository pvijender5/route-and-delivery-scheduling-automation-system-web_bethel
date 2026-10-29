<div class="modal fade show" id="replaceWithNewShipmentModel" tabindex="-1" aria-labelledby="replaceWithNewShipmentModel" aria-modal="true"
    role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-xl post-route assign-route" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 >
                Replace with a New Shipment
                </h4>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <h6>Available Stock after Shipment Cancellation</h6>
                <p>Unallocated Inventory in Truck: <b><input type="hidden" id="truckUnallocatedPQuantity" /><input type="hidden" id="truckUnallocatedRQuantity" /><input type="hidden" id="truckUnallocatedTQuantity" /><span class="unallocatedInventory">P-0 R-0 T-0</span></b></p>
                <p>Subtracted Inventory for New Shipment: <b><span class="subtractedInventory">P-0 R-0 T-0</span></b></p>
                <p>Remaining Inventory in Truck: <b><span class="remainingInventory">P-0 R-0 T-0</span></b></p>
                <table id="inventoryTable" class="table table-responsive text-nowrap table-scroll custom-scrollbar mb-4">
                    <thead>
                        <tr>
                            <th style="width:8%">Sr. No.</th>
                            <th style="width:10%">Item Name</th>
                            <th style="width:10%">Item Id</th>
                            <th style="width:10%">Quantity</th>
                            <th style="width:10%">Unit Type</th>
                            <th style="width:10%">Unit Weight</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTabledata">
                    </tbody>
                </table>
                <h6 class="mb-2">Open Shipment List for Inventory Fulfillment:</h6>
                <table id="loadersTable" class="table table-responsive text-nowrap table-scroll custom-scrollbar mb-4">
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
                    <tbody id="replacenewShipmentData">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="route_id" />
                <button class="btn btn-primary-outline post-route-close" type="button" data-bs-dismiss="modal">
                    Close
                </button>
                <button class="btn btn-warning save-replacenewshipment-button" type="button">
                Add & Notify Driver
                </button>
            </div>
        </div>
    </div>
</div>


