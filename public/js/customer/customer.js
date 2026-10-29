$(document).ready(function () {
    var columnDefs = [
        { data: "customer_id", name: "customer_id" },
        { data: "name", name: "name" },
        { data: "email", name: "email", render: function (data, type, row) {
            
            if(row.email!="" && row.email!=null)
            {
                return row.email;
            }else
            {
                return "N/A";
            }
            
        }},
        { data: "phone", name: "phone" },
        {
            data: "shipments",
            name: "shipments",
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return (
                    '<button class="btn btn-light collapsed me-4 view-subtable"  data-row-id="' +
                    row.id +
                    '" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample' +
                    row.id +
                    '" aria-expanded="false" aria-controls="collapseExample' +
                    row.id +
                    '"><img class="me-1 img-fluid" src="' +
                    shipmentImg +
                    '" alt="">Shipments</button>'
                );
            },
        },
        { data: "views", name: "views", orderable: false, searchable: false },
    ];
    const table = $("#customertable").DataTable({
        processing: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        serverSide: true,
        ajax: customerList,
        lengthMenu: [5, 10, 25, 50, 100],
        columns: columnDefs,
        destroy: true,
        dom: "Blfrtip",
        buttons: [{
            className: "buttonsToHide",
            
        }],
        initComplete: function(settings, json) {
            // Hide the export buttons
            $('.dt-buttons').hide();
        },
        drawCallback: function (settings) {
            var api = this.api();
            // adding class to show entries dropdown
            $('.custom-select').addClass('form-select');
            // Handle click event for the "view-subtable" button
            api.$('.view-subtable').on('click', function () {

                var tr = $(this).closest('tr'); // Get the parent row's HTML element
                var isChildShown = tr.next('tr.sub-table').is(':visible');

                if (isChildShown) {
                    // Hide the sub-table
                    tr.next('tr.sub-table').hide();
                    $(this).closest("tr").removeClass("active");
                    //tr.removeClass('sub-table');
                } else {
                    // Show the sub-table
                    $(this).closest("tr").addClass("active");
                    var selectedOption = tr.next().find('.select-small').val() || 'DELIVERY';
                    var data = api.row(tr).data(); // Get the data for the parent row
                    var subTable = formatSubTableContent(data, selectedOption);
                    var subTableRow = $(
                        '<tr class="sub-table"><td colspan="' +
                        columnDefs.length +
                        '"></td></tr>'
                    );
                    subTableRow.find("td").html(subTable);
                    tr.after(subTableRow);
                    //  tr.addClass('sub-table');
                }

                // Toggle the "collapsed" class
                $(this).toggleClass("collapsed", !isChildShown);
            });
        },
    });

    // Save notes Ajax
    $(document).on("click", ".add-notes-link", function () {
        const notesBtn = $(this);
        const notesTextarea = $("#notesTextarea");
        const saveNotesBtn = $("#saveNotesBtn");
        const notesModal = $("#notesModal");
    
        // Get the shipment ID and notes
        const shipmentId = notesBtn.data("shipment-id");
        const notes = notesBtn.data("notes");
    
        // Populate the textarea in the modal
        notesTextarea.val(notes);
        $(".notestext").text(notesTextarea.val());
    
        // Save notes when the Save Notes button is clicked
        saveNotesBtn.off("click").on("click", function () {
            const updatedNotes = notesTextarea.val();
            // Include the CSRF token in the headers
            const csrfToken = $('meta[name="csrf-token"]').attr("content");
            // Make an AJAX request to update the notes
            $.ajax({
                method: "PUT", // Use the appropriate HTTP method
                url: `/update-notes/${shipmentId}`,
                data: {
                    text: updatedNotes,
                    id: shipmentId,
                },
                headers: {
                    "X-CSRF-TOKEN": csrfToken, // Include the CSRF token in headers
                },
                success: function (response) {
                    // Update the notes in the modal and close it
                    $(".notestext").text(updatedNotes);
                    notesModal.modal("hide");
                    table.ajax.reload();
                },
                error: function (error) {
                    console.error("Error updating notes:", error);
                },
            });
        });
    
        // Show the notes modal
        notesModal.modal("show");
    });

    // Handle change event for the select dropdown outside of DataTables initialization
    $("#customertable").on("change", ".select-small", function () {
        var selectedOption = $(this).val();
        var tr = $(this).closest("tr").prev(); // Get the parent row's HTML element
        var subTableContainer = tr.next().find(".sub-table-container");

        // Update the sub-table content based on the selected option
        var data = $("#customertable").DataTable().row(tr).data(); // Get the data for the parent row
        loadSubTableContent(selectedOption, data).then(function (subTable) {
            subTableContainer.html(subTable);
        });
    });
    // Rest of your code remains the same
    $('#customertable_bakc').DataTable({
        processing: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        serverSide: true,
        ajax: customerList,
        lengthMenu: [5, 10, 25, 50, 100],
        columns: columnDefs,
        drawCallback: function (settings) {
            var api = this.api();

            // Handle click event for the "view-subtable" button
            api.$(".view-subtable").on("click", function () {
                var row = api.row($(this).parents("tr"));
                var tr = row.node(); // Get the parent row's node
                var isChildShown = row.child.isShown();

                if (isChildShown) {
                    // Hide the sub-table
                    row.child.hide();
                    $(tr).next("tr").removeClass("sub-table");
                } else {
                    // Show the sub-table
                    var selectedOption =
                        $(this)
                            .closest("tr")
                            .next()
                            .find(".select-small")
                            .val() || "DELIVERY";
                    var data = row.data();
                    var subTable = formatSubTableContent(data, selectedOption); // Call formatSubTableContent with data and selectedOption
                    row.child(subTable).show();
                    $(tr).next("tr").addClass("sub-table");
                }

                // Toggle the "collapsed" class
                $(this).toggleClass("collapsed", !isChildShown);
            });

            // Handle change event for the select dropdown using event delegation
            $("#customertable")
                .off("change", ".select-small")
                .on("change", ".select-small", function () {
                    var selectedOption = $(this).val();
                    var row = $(this).closest("tr").prev(); // Get the parent row of the container
                    var tr = row[0]; // Get the parent row's node
                    var subTableContainer = $(tr)
                        .next()
                        .find(".sub-table-container");

                    // Update the sub-table content based on the selected option
                    var data = row.data();
                    loadSubTableContent(selectedOption, data).then(function (
                        subTable
                    ) {
                        subTableContainer.html(subTable);
                    });
                });
        },
    });

    function formatSubTableContent(data, selectedOption) {

        console.log("data",data);
        // Create a container for the sub-table content
        var container = $("<div>").addClass(
            "card card-body mb-0 dark-accordion"
        );

        // Create select element and set its initial value
        var selectElement = $("<select>").addClass(
            "form-select red-select select-small"
        );
        selectElement.append(
            '<option data-id="' +
            data.id +
            '" value="DELIVERY" ' +
            (selectedOption === "DELIVERY" ? "selected" : "") +
            ">Deliveries</option>"
        );
        selectElement.append(
            '<option  data-id="' +
            data.id +
            '"  value="PICKUP" ' +
            (selectedOption === "PICKUP" ? "selected" : "") +
            ">Pickup Order</option>"
        );

        // Append the select element to the container
        container.append(selectElement);

        // Create a container for the sub-table
        var subTableContainer = $("<div>").addClass("sub-table-container");

        // Append the select and sub-table container to the main container
        container.append(selectElement, subTableContainer);

        // Handle change event for the select input
        // selectElement.on('change', function () {
        //     var selectedOption = $(this).val();
        //     var dataID = $(this).data('id');
        //     var row = container.closest('tr').prev(); // Get the parent row of the container
        //     var tr = row.node(); // Get the parent row's node
        //     var subTableContainer = $(tr).next().find('.sub-table-container');

        //     // Update the sub-table content based on the selected option
        //     var data = row.data();
        //     console.log("dataID"+dataID);
        //     loadSubTableContent(selectedOption, data)
        //         .then(function (subTable) {
        //             subTableContainer.html(subTable);
        //         });
        // });

        // Create an initial sub-table with default option "Deliveries"
        loadSubTableContent(selectedOption, data).then(function (subTable) {
            subTableContainer.html(subTable);
        });

        return container;
    }

    function loadSubTableContent(selectedOption, data) {
        // Create an AJAX request to fetch shipments based on the selected option
        return $.ajax({
            url: "/fetch-shipments/" + selectedOption + "/" + data.customer_id,
            method: "GET",
            async: true,
        }).then(function (shipments) {
            // Return the sub-table HTML
            return updateSubTableRows(shipments);
        });
    }

    function updateSubTableRows(shipments) {
        // Create a new sub-table
        var subTable = $("<table>").addClass("table accordion-table sub-table");
        var subTableHead = $("<thead>");
        var subTableBody = $("<tbody>");

        // Create the sub-table header row
        var headerRow = $("<tr>").append(
            '<th scope="col">Shipment ID</th>',
            '<th scope="col">Order ID</th>',
            '<th scope="col">Warehouse (Origin)</th>',
            '<th scope="col">Customer Name</th>',
            '<th scope="col">Location Name</th>',
            '<th scope="col">Delivery Location</th>',
            '<th scope="col">Shipment Contact No</th>',
            '<th scope="col">Shipment/ Delivery Date</th>',
            '<th scope="col">Delivery Window</th>',
            '<th scope="col">Item Quantity</th>',
            '<th scope="col">Forklift</th>',
            '<th scope="col">Shipment Notes</th>',
            '<th scope="col">Status</th>',
            '<th scope="col">View</th>'
        );

        // Append the header row to the sub-table head
        subTableHead.append(headerRow);
        subTable.append(subTableHead, subTableBody);

        // Update the sub-table body based on the shipments data
        if (shipments.length === 0) {
            var noRecordsRow = $("<tr>").append(
                '<td colspan="14">No records found</td>'
            );
            subTableBody.append(noRecordsRow);
        } else {
            for (var i = 0; i < shipments.length; i++) {
                var shipment = shipments[i];
                var notesIcon;
                var notesArray = shipment.notes;
                if (notesArray.length > 0 && notesArray) {
                    // Display a link to view notes in a modal
                    notesIcon =
                        '<span href="#" class="view-notes-link btn btn-rounded"  data-notes="' +
                        notesArray +
                        '"><i class="fa fa-file-text"></i></span>';
                } else {
                    // Display a link to add notes
                    notesIcon =
                        '<span href="#" class="btn btn-rounded add-notes-link" data-shipment-id="' +
                        shipment.id +
                        '" data-toggle="modal" data-target="#notesModal"><i class="fa fa-plus"></i></span>';
                }
                
                let P_quantity=shipment?.item_quantity?.P_quantity?shipment.item_quantity.P_quantity:'0';
                let R_quantity=shipment?.item_quantity?.R_quantity?shipment.item_quantity.R_quantity:'0';
                let T_quantity=shipment?.item_quantity?.T_quantity?shipment.item_quantity.T_quantity:'0';

                if (shipment.shipment_status) {
                    var shipmentStatus = shipment.shipment_status;
                } else {
                    var shipmentStatus = "default-status";
                }
               
                var row = $("<tr>").append(
                    "<td>" + shipment.id + "</td>",
                    "<td>" + shipment.order_id + "</td>",
                    "<td>" + shipment.warehouse_name + "</td>",
                    "<td>" + shipment.customer_name + "</td>",
                    "<td>" + shipment.delivery_location_name + "</td>",
                    "<td>" + shipment.address + "</td>",
                    "<td>" + shipment.delivery_contact_phone + "</td>",
                    "<td>" + shipment.route_date + "</td>",
                    "<td>" + shipment.delivery_window + "</td>",
                    '<td>P-' + P_quantity + ' R-' + R_quantity + ' T-' + T_quantity + ' <a class="load-info view-shipment-link text-green cursor-pointer"  data-shipment-id="' +
                    shipment.id + '"><i class="fa fa-info-circle"></i></td>',
                    '<td><span class="txt-success">' +
                    shipment.fork_lift +
                    "</td>",
                    "<td>" + notesIcon + "</td>",
                    "<td class='text-capitalize'> <span class='shipment-status btn-" + shipmentStatus + "'>" + shipment.shipment_status + " </span></td>",
                    '<td><a class="btn btn-rounded btn-primary" href="' +
                    shipment.view_link +
                    '"><i class="fa fa-eye"></i></a></td>'
                );

                // Append the row to the sub-table body
                subTableBody.append(row);
            }
        }

        return subTable;
    }

    /*************improve code end********************* */

    $("#customertable123").DataTable({
        processing: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        serverSide: true,
        ajax: customerList,
        lengthMenu: [5, 10, 25, 50, 100], // Define the per page options
        columns: columnDefs,
        drawCallback: function (settings) {
            var api = this.api();

            // Handle click event for the "view-subtable" button
            api.$(".view-subtable").on("click", function () {
                var row = api.row($(this).parents("tr"));
                var tr = row.node(); // Get the parent row's node
                var isChildShown = row.child.isShown();

                if (isChildShown) {
                    // Hide the sub-table
                    row.child.hide();
                    $(tr).next("tr").removeClass("sub-table"); // Remove your custom class from the next tr
                } else {
                    // Show the sub-table
                    row.child(formatSubTableContent(row.data())).show();
                    $(tr).next("tr").addClass("sub-table"); // Add your custom class to the next tr
                }

                // Toggle the "collapsed" class
                $(this).toggleClass("collapsed", !isChildShown);
            });
        },
    });
    // Get the search input element
    var searchInput = $('div.dataTables_filter input');

    // Add a tooltip to the search input
    searchInput.attr('title', ' Search in Customer Id,customer Name,Email,Primary Mobile No');
});

function formatSubTableContent_old(data) {
    // Create a container for the sub-table content
    var container = $("<div>");

    // Load sub-table content via AJAX
    $.ajax({
        url: "/fetch-shipments/DELIVERY/" + data.id, // Replace with your actual AJAX endpoint
        method: "GET",
        async: false, // Ensure synchronous loading
        success: function (shipments) {
            // Build the sub-table using the AJAX response (shipments)
            var cardElement = $("<div>").addClass(
                "card card-body mb-0 dark-accordion"
            );
            var selectElement = $("<select>").addClass(
                "form-select red-select select-small"
            );
            //selectElement.append('<option selected>Shipments</option>');
            selectElement.append(
                '<option value="DELIVERY">Deliveries</option>'
            );
            selectElement.append(
                '<option value="PICKUP">Pickup Order</option>'
            );

            // Build the sub-table using the AJAX response (shipments)

            var subTable = $("<table>").addClass("table accordion-table");
            var subTableHead = $("<thead>").addClass("");
            var subTableBody = $("<tbody>");

            // Create the sub-table header row
            var headerRow = $("<tr>");
            headerRow.append('<th scope="col">Shipment ID</th>');
            headerRow.append('<th scope="col">Order ID</th>');
            headerRow.append('<th scope="col">Warehouse (Origin)</th>');
            headerRow.append('<th scope="col">Customer Name</th>');
            headerRow.append('<th scope="col">Location Name</th>');
            headerRow.append('<th scope="col">Delivery Location</th>');
            headerRow.append('<th scope="col">Shipment Contact No</th>');
            headerRow.append('<th scope="col">Shipment/ Delivery Date</th>');
            headerRow.append('<th scope="col">Delivery Window</th>');
            headerRow.append('<th scope="col">Item Quantity</th>');
            headerRow.append('<th scope="col">Forklift</th>');
            headerRow.append('<th scope="col">Shipment Notes</th>');
            headerRow.append('<th scope="col">Status</th>');
            headerRow.append('<th scope="col">View</th>');

            // Append the header row to the sub-table head
            subTableHead.append(headerRow);

            // Iterate through shipments and create rows here
            if (shipments.length === 0) {
                var noRecordsRow = $("<tr>");
                noRecordsRow.append('<td colspan="14">No records found</td>'); // Adjust the colspan as needed
                subTableBody.append(noRecordsRow);
            } else {
                for (var i = 0; i < shipments.length; i++) {
                    var shipment = shipments[i];
                    var notesIcon;
                    var notesArray = shipment.notes;
                    if (notesArray.length > 0 && notesArray) {
                        // Display a link to view notes in a modal
                        notesIcon =
                            '<a href="#" class="view-notes-link btn btn-rounded"  data-notes="' +
                            notesArray +
                            '"><i class="fa fa-file-text"></i></a>';
                    } else {
                        // Display a link to add notes
                        notesIcon =
                            '<a href="#" class="btn btn-rounded add-notes-link" data-shipment-id="' +
                            shipment.id +
                            '" data-toggle="modal" data-target="#notesModal"><i class="fa fa-plus"></i></a>';
                    }
                    var row = $("<tr>").append(
                        "<td>" + shipment.id + "</td>",
                        "<td>" + shipment.order_id + "</td>",
                        "<td>" + shipment.warehouse_name + "</td>",
                        "<td>" + shipment.customer_name + "</td>",
                        "<td>" + shipment.delivery_location_name + "</td>",
                        "<td>" + shipment.address + "</td>",
                        "<td>" + shipment.delivery_contact_phone + "</td>",
                        "<td>" + shipment.route_date + "</td>",
                        "<td>" + shipment.delivery_window + "</td>",
                        '<td>P-2 : R-3 : T-0 <a class="load-info view-shipment-link"  data-shipment-id="' +
                        shipment.id +
                        '"><i class="fa fa-info-circle"></i></td>',
                        '<td><span class="txt-success">' +
                        shipment.fork_lift +
                        "</td>",
                        "<td>" + notesIcon + "</td>",
                        "<td>" + shipment.shipment_status + "</td>",
                        '<td><a class="btn btn-rounded btn-primary" href="' +
                        shipment.view_link +
                        '"><i class="fa fa-eye"></i></a></td>'
                    );
                    // Append the row to the sub-table body
                    subTableBody.append(row);
                }
            }

            // Append the sub-table to the container
            container.append(subTable);

            // Append the header and rows to the sub-table
            subTable.append(subTableHead, subTableBody);

            //container.append(selectElement, subTable);
            cardElement.append(selectElement);

            // Append the card element and sub-table to the container
            container.append(cadElement, subTable);

            // Append the sub-table to trhe container
            //  container.append(subTable);
        },
    });

    return container.html(); // Return the HTML content of the sub-table
}

// Handle click event on the "View" button to load sub-table
// $(document).on('click', '.view-subtable', function () {
//     var rowId = $(this).data('row-id');
//     loadSubTable(rowId);
// });

// Function to load the sub-table with data
function loadSubTable(rowId) {
    var subTable = $("#subTable");
    // Toggle the display of the subTable

    subTable.toggle();
    const url = "/fetch-shipments/DELIVERY/" + rowId;
    var subTable = $("#subTable").DataTable({
        ajax: {
            url: url, // Replace with your sub-table API endpoint URL
        },
        columns: [
            {
                data: "name",
                name: "name",
            },
            {
                data: "order_id",
                name: "order_id",
            },
            {
                data: "warehouse_name",
                name: "warehouse.name",
            },
            {
                data: "customer_name",
                name: "customer_name",
            },
            {
                data: "delivery_location_name",
                name: "delivery_location_name",
            },
            {
                data: "delivery_location",
                name: "delivery_location",
            },
            {
                data: "delivery_contact_phone",
                name: "delivery_contact_phone",
            },
            {
                data: "route_Date",
                name: "route_Date",
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        var dateObject = new Date(data);
                        var formattedDate =
                            dateObject.getMonth() +
                            1 +
                            "/" +
                            dateObject.getDate() +
                            "/" +
                            dateObject.getFullYear();
                        return formattedDate;
                    }
                    return data;
                },
            },
            {
                data: "props_delivery_window",
                name: "props_delivery_window",
                render: function (data, type, row) {
                    if (type === "display") {
                        //var decodedData = decodeHtmlEntities(data);
                        //var timeArray = JSON.parse(data);
                        var timeArray = data;
                        if (Array.isArray(timeArray) && timeArray.length > 0) {
                            var startTime = new Date(timeArray[0].start);
                            var endTime = new Date(timeArray[0].end);

                            // Format the time in UTC
                            var formattedTime =
                                startTime.toLocaleTimeString("en-US", {
                                    timeZone: "UTC",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                }) +
                                " - " +
                                endTime.toLocaleTimeString("en-US", {
                                    timeZone: "UTC",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                });
                            return formattedTime;
                        }
                    }
                    return data;
                },
            },
            {
                data: "id",
                name: "id",
                render: function (data, type, row) {
                    var shipmentId = row.id;
                    var displayValue =
                        'Item Quantity static<a href="#" class="view-shipment-link"  data-shipment-id="' +
                        shipmentId +
                        '">Info</a>';
                    return displayValue;
                },
            },
            {
                data: "fork_lift",
                name: "fork_lift",
                render: function (data, type, row) {
                    var hasPermission = row.fork_lift;
                    var displayValue = hasPermission ? "Yes" : "No";
                    return displayValue;
                },
            },
            {
                data: "props_notes",
                name: "props_notes",
                render: function (data, type, row) {
                    var shipmentId = row.id;
                    if (type === "display") {
                        //var decodedData = decodeHtmlEntities(data);
                        ///var notesArray = JSON.parse(data);
                        var notesArray = data;
                        if (
                            Array.isArray(notesArray) &&
                            notesArray.length > 0 &&
                            notesArray[0].text
                        ) {
                            // Display a link to view notes in a modal
                            return (
                                '<a href="#" class="view-notes-link btn btn-rounded"  data-notes="' +
                                notesArray[0].text +
                                '"><i class="fa fa-file-text"></i></a>'
                            );
                        } else {
                            // Display a link to add notes
                            return (
                                '<a href="#" class="add-notes-link" data-shipment-id="' +
                                shipmentId +
                                '" data-toggle="modal" data-target="#notesModal">Add Notes</a>'
                            );
                        }
                    }
                    return data;
                },
            },
            {
                data: "shipment_status",
                name: "shipment_status",
                render: function (data, type, row) {
                    var activeTab = $(".nav-link.active").attr("aria-controls");
                    if (activeTab === "cancelledshipment") {
                        var deletedAt = row.deleted_at;
                        var deletionInfo = "";
                        if (deletedAt) {
                            var deletionDate = new Date(deletedAt);
                            var formattedDeletionDate =
                                deletionDate.toLocaleString();
                            deletionInfo =
                                "Deleted at: " + formattedDeletionDate;

                            if (row.deleted_by) {
                                $.ajax({
                                    url: "/users/" + row.deleted_by,
                                    method: "GET",
                                    success: function (user) {
                                        var deletedByName =
                                            user.name || "Unknown User";
                                        var deletedById =
                                            user.id || "Unknown ID";
                                        deletionInfo +=
                                            "<br>" +
                                            "Deleted by User ID: " +
                                            deletedById +
                                            ", Name: " +
                                            deletedByName;
                                    },
                                    error: function (error) {
                                        console.error(
                                            "Error fetching user details:",
                                            error
                                        );
                                    },
                                    async: false, // Ensure synchronous execution to update deletionInfo
                                });
                            }
                        }
                        return deletionInfo || "Not deleted";
                    }
                    return data;
                },
            },
            {
                data: null,
                render: function (data, type, row) {
                    var activeTab = $(".nav-link.active").attr("aria-controls");
                    var deleted = $("#deleted").val(); // Get the value of the input element with id 'deleted'

                    if (activeTab === "cancelledshipment") {
                        // Customize the URL based on the "cancelledshipment" tab requirements
                        return `<a href="${viewShipmentRoute}/${data.id
                            }/${encodeURIComponent(
                                deleted
                            )}" class="btn btn-rounded btn-primary"><i class="fa fa-eye"></i></a>`;
                    } else {
                        // Default URL for other tabs
                        return `<a href="${viewShipmentRoute}/${data.id}" class="btn btn-rounded btn-primary"><i class="fa fa-eye"></i></a>`;
                    }
                },
                targets: -1, // This targets the last column in the table
                orderable: false, // Make the column not sortable
            },
        ],
    });
}
$(document).on("click", ".show-shipments", function () {
    var customerId = $(this).data("customer-id");
    openModalWithData("#shipmentsModal", "/fetch-shipments/" + customerId);
});

$(document).on("click", ".hide-shipments .close", function () {
    closeModal("#shipmentsModal");
});
$(document).on("click", ".close", function () {
    closeModal("#shipmentsModal");
    closeModal("#notesModal");
});

$(document).on("change", "#shipmentDropdown", function () {
    const selectedOption = $(this).find("option:selected");
    const selectedShipmentId = selectedOption.val();
    const customerId = selectedOption.data("customer-id");

    console.log(`Customer ID: ${customerId}`);

    if (selectedShipmentId) {
        const url = `/fetch-shipments/${selectedShipmentId}/${customerId}`;
        openModalWithData("#shipmentsModal", url);
    }
});

// modal.js
function openModalWithData(modalId, dataUrl) {
    $.ajax({
        url: dataUrl,
        method: "GET",
        success: function (response) {
            $(modalId + " .modal-body").html(response);
            $(modalId).modal("show");
        },
        error: function () {
            // Handle error
        },
    });
}

function closeModal(modalId) {
    $(modalId).modal("hide");
}

function openModalWithDatacustomer(customerId) {
    // Fetch customer details using AJAX
    $.ajax({
        url: "/customer-details/" + customerId, // Adjust the route as needed
        method: "GET",
        success: function (response) {
            $("#modalBody").html(response);
            $("#customerModal").modal("show");
        },
        error: function () {
            // Handle error
        },
    });
}

$(document).on("click", ".notes-btn", function () {
    const notesBtn = $(this);
    const notesContainer = notesBtn.closest(".shipment-notes");
    const notesTextarea = $("#notesTextarea");
    const saveNotesBtn = $("#saveNotesBtn");
    const notesModal = $("#notesModal");

    // Get the shipment ID and notes
    const shipmentId = notesContainer.data("shipment-id");
    const notes = notesContainer.data("notes");

    // Populate the textarea in the modal
    notesTextarea.val(notes);
    $(".notestext").text(notesTextarea.val());

    // Show the notes modal
    notesModal.modal("show");

    // Save notes when the Save Notes button is clicked
    saveNotesBtn.off("click").on("click", function () {
        const updatedNotes = notesTextarea.val().trim(); // Trim whitespace

        // Check if the updatedNotes is empty
        if (updatedNotes === "") {
            // Highlight textarea in red
            notesTextarea.addClass("is-invalid");
            return; // Don't save if it's empty
        }

        // Remove red highlighting if previously applied
        notesTextarea.removeClass("is-invalid");

        // Include the CSRF token in the headers
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        // Make an AJAX request to update the notes
        $.ajax({
            method: "PUT", // Use the appropriate HTTP method
            url: `/update-notes/${shipmentId}`,
            data: {
                text: updatedNotes,
                id: shipmentId,
            },
            headers: {
                "X-CSRF-TOKEN": csrfToken, // Include the CSRF token in headers
            },
            success: function (response) {
                // Update the notes in the modal and close it
                notesContainer.data("notes", updatedNotes);
                $(".notestext").text(updatedNotes);
                notesModal.modal("hide");
            },
            error: function (error) {
                console.error("Error updating notes:", error);
            },
        });
    });
});

//

$(document).on("click", ".notes-btn122", function () {
    $("#notesModal").modal("show");
    const notesBtn = $(this);
    const notesContainer = notesBtn.closest(".shipment-notes");
    const notesTextarea = $("#notesTextarea");
    const saveNotesBtn = $("#saveNotesBtn");

    // Get the shipment ID and notes
    const shipmentId = notesContainer.data("shipment-id");
    const notes = notesContainer.data("notes");

    // Populate the textarea in the modal
    notesTextarea.val(notes);

    $(".notestext").text(notesTextarea.val());
    // Save notes when the Save Notes button is clicked
    saveNotesBtn.off("click").on("click", function () {
        const updatedNotes = notesTextarea.val();
        // Include the CSRF token in the headers
        const csrfToken = $('meta[name="csrf-token"]').attr("content");
        // Make an AJAX request to update the notes
        $.ajax({
            method: "POST", // Use the appropriate HTTP method
            url: `/update-notes/${shipmentId}`,
            data: {
                text: updatedNotes,
                id: shipmentId,
                _token: csrfToken, // Include the CSRF token in headers
            },

            success: function (response) {
                // Update the notes in the modal and close it
                notesContainer.data("notes", updatedNotes);
                $(".notestext").text(updatedNotes);
                $("#notesModal").modal("hide");
            },
            error: function (error) {
                console.error("Error updating notes:", error);
            },
        });
    });
    
});


$(document).ready(function () {
// Get the browser's time zone
const csrfToken = $('meta[name="csrf-token"]').attr("content");
// Send the time zone to your Laravel controller via AJAX
$.ajax({
    url: `/update-timezone`,
    type: 'POST',
    data: { timeZone: browserTimeZone },
    headers: {
        "X-CSRF-TOKEN": csrfToken, // Include the CSRF token in headers
    },
});
})