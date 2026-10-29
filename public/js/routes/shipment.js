// Configuration
const CONFIG = {
    API: {
        ENDPOINTS: {
            TRUCK_CAPACITY: '/routes/getAvailableCapacityTruck',
            SHIPMENTS: '/shipments/datatables',
            ADD_SHIPMENT: '/routes/addNewShipmentToRoute'
        },
        HEADERS: {
            CSRF_TOKEN: () => $('meta[name="csrf-token"]').attr("content")
        }
    },
    DATE: {
        FORMAT: {
            TIME: {
                hour: "2-digit",
                minute: "2-digit",
                timeZone: "UTC"
            }
        }
    }
};

// Shipment Manager Class
class ShipmentManager {
    constructor() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        $(document).on('click', 'input:checkbox[name="select_new_shipment"]', this.handleShipmentSelection.bind(this));
        $(document).on('click', '.save-addNewShipment-button', this.handleAddNewShipment.bind(this));
    }

    async addNewShipment(routeId, routeDate, warehouseId) {
        try {
            const capacity = await this.fetchTruckCapacity(routeId);
            this.updateCapacityDisplay(capacity);
            
            const shipments = await this.fetchNewShipments(routeDate, warehouseId);
            this.renderShipments(shipments, capacity);
            
            openModal('addnewshipment');
        } catch (error) {
            this.showError(error);
        }
    }

    async fetchTruckCapacity(routeId) {
        const response = await $.ajax({
            method: "GET",
            url: CONFIG.API.ENDPOINTS.TRUCK_CAPACITY,
            data: { route_id: routeId },
            headers: { "X-CSRF-TOKEN": CONFIG.API.HEADERS.CSRF_TOKEN() }
        });
        return response.data;
    }

    async fetchNewShipments(routeDate, warehouseId) {
        const response = await $.ajax({
            method: "GET",
            url: `${CONFIG.API.ENDPOINTS.SHIPMENTS}?delivery_status=new&route_date=${routeDate}&warehouse_id=${warehouseId}`,
            headers: { "X-CSRF-TOKEN": CONFIG.API.HEADERS.CSRF_TOKEN() }
        });
        return response.data;
    }

    updateCapacityDisplay(capacity) {
        $(".availableCapacity").html(capacity);
        $("#truckAvailableCapacity").val(capacity);
        $(".remainingCapacity").html(capacity);
    }

    renderShipments(shipments, truckCapacity) {
        const rows = shipments.length ? 
            shipments.map(shipment => this.createShipmentRow(shipment, truckCapacity)).join('') :
            '<tr><td colspan="10" align="center">No shipment found</td></tr>';
        
        $("#newShipment").html(rows);
    }

    createShipmentRow(shipment, truckCapacity) {
        const {
            formattedDate,
            formattedTime,
            formattedNotes,
            saveButton
        } = this.formatShipmentData(shipment, truckCapacity);

        return `
            <tr>
                <td>${shipment.name}</td>
                <td>${shipment.customer_name}</td>
                <td style="width: 20%">${shipment.delivery_location_name}</td>
                <td style="width: 20%">${shipment.delivery_location}</td>
                <td>${formattedDate}</td>
                <td>${formattedTime}</td>
                <td>${shipment.prt}</td>
                <td>${formattedNotes}</td>
                <td>${shipment.delivery_status}</td>
                <td>
                    <div class="form-check">
                        <div class="checkbox-checked">${saveButton}</div>
                    </div>
                </td>
            </tr>`;
    }

    formatShipmentData(shipment, truckCapacity) {
        const formattedDate = this.formatDate(shipment.route_Date);
        const formattedTime = this.formatDeliveryWindow(shipment.props_delivery_window);
        const formattedNotes = this.formatNotes(shipment.props_notes);
        const saveButton = this.createSaveButton(shipment, truckCapacity);

        return { formattedDate, formattedTime, formattedNotes, saveButton };
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()}`;
    }

    formatDeliveryWindow(window) {
        if (!Array.isArray(window) || !window.length) return 'N/A';

        const startTime = new Date(window[0].start);
        const endTime = new Date(window[0].end);

        return `${startTime.toLocaleTimeString("en-US", CONFIG.DATE.FORMAT.TIME)} - 
                ${endTime.toLocaleTimeString("en-US", CONFIG.DATE.FORMAT.TIME)}`;
    }

    formatNotes(notes) {
        return (Array.isArray(notes) && notes.length && notes[0].text) ? notes[0].text : 'N/A';
    }

    createSaveButton(shipment, truckCapacity) {
        return truckCapacity < shipment.props_weight ?
            '<i class="fa fa-warning font-danger"></i>' :
            `<input class="form-check-input checkbox_animated select-new-shipment" 
                type="checkbox" 
                value="${shipment.id}" 
                id="select_new_shipment" 
                name="select_new_shipment" 
                props_weight="${shipment.props_weight}" 
                shipment_id="${shipment.id}">`;
    }

    handleShipmentSelection(event) {
        const $checkbox = $(event.target);
        $('input:checkbox[name="select_new_shipment"]').not($checkbox).prop('checked', false);

        if ($checkbox.is(':checked')) {
            const propsWeight = parseFloat($checkbox.attr('props_weight'));
            const truckCapacity = parseFloat($("#truckAvailableCapacity").val());
            
            $(".subtractedQuantity").html(propsWeight);
            $(".remainingCapacity").html(truckCapacity - propsWeight);
        } else {
            const truckCapacity = $("#truckAvailableCapacity").val();
            $(".subtractedQuantity").html(0);
            $(".remainingCapacity").html(truckCapacity);
        }
    }

    async handleAddNewShipment() {
        const $selectedShipment = $("input[name='select_new_shipment']:checked");
        const shipmentId = $selectedShipment.val();
        const routeId = $("#route_id").val();
        const truckCapacity = $("#truckAvailableCapacity").val();

        if (!shipmentId) {
            this.showError({ message: 'Please select shipment first.' });
            return;
        }

        try {
            const response = await this.saveNewShipment(shipmentId, routeId, truckCapacity);
            await this.handleSuccessfulAdd(response, routeId);
        } catch (error) {
            this.showError(error);
        }
    }

    async saveNewShipment(shipmentId, routeId, truckCapacity) {
        return $.ajax({
            url: CONFIG.API.ENDPOINTS.ADD_SHIPMENT,
            type: 'POST',
            data: {
                shipment_delivery_id: shipmentId,
                route_id: routeId,
                truckAvailableCapacity: truckCapacity
            },
            dataType: "json"
        });
    }

    async handleSuccessfulAdd(response, routeId) {
        await reloadrouteLi(routeId, response.routedata);
        
        await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: response.message
        });

        closeModal('addnewshipment');
        const $routeCheckbox = $(`#checkbox_route_${routeId}`);
        $routeCheckbox.trigger('click').trigger('click');
    }

    showError(error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.responseJSON?.message || error.message
        });
    }

    async deleteShipment(routeId, shipmentDeliveryId) {
        try {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                const response = await $.ajax({
                    url: '/routes/deleteShipmentFromRoute',
                    type: 'POST',
                    data: { route_id: routeId, shipment_delivery_id: shipmentDeliveryId }
                });

                await reloadrouteLi(routeId, response.routedata);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'The shipment has been removed.'
                });
            }
        } catch (error) {
            this.showError(error);
        }
    }

    async cancelShipment(routeId, shipmentDeliveryId) {
        try {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to cancel this shipment?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it!'
            });

            if (result.isConfirmed) {
                const response = await $.ajax({
                    url: '/routes/cancelShipmentFromRoute',
                    type: 'POST',
                    data: { route_id: routeId, shipment_delivery_id: shipmentDeliveryId }
                });

                await reloadrouteLi(routeId, response.routedata);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Cancelled!',
                    text: 'The shipment has been cancelled.'
                });
            }
        } catch (error) {
            this.showError(error);
        }
    }
}

// Initialize Shipment Manager
const shipmentManager = new ShipmentManager();

// Export functions for global use (backward compatibility)
window.addNewShipment = (routeId, routeDate, warehouseId) => shipmentManager.addNewShipment(routeId, routeDate, warehouseId);
window.deleteShipment = (routeId, shipmentDeliveryId) => shipmentManager.deleteShipment(routeId, shipmentDeliveryId);
window.cancelShipment = (routeId, shipmentDeliveryId) => shipmentManager.cancelShipment(routeId, shipmentDeliveryId);
