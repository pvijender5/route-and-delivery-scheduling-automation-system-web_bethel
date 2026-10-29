$(document).ready(function () {
    var ids = [];
    var columnDefs = [
        {
            data: "id",
            name: "id",
            targets: 0,
            checkboxes: {
                selectRow: true
             },
            searchable: false,
            orderable: false,
            className: "dt-body-center",
            render: function(data, type, row, meta) {
                // Add a custom class to the checkbox cell
                if (type === "display") {
                    return '<input type="checkbox" class="checkboxes checkbox_animated dt-checkboxes" value="' + data + '">';
                }
                return data;
            },
        },
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
            name: "warehouse_name",
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
                                //timeZone: "America/New_York",
                                timeZone: "UTC",
                                hour: "2-digit",
                                minute: "2-digit",
                            }) +
                            " - " +
                            endTime.toLocaleTimeString("en-US", {
                                //timeZone: "America/New_York",
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
            data: 'prt',
            name: 'prt',
            render: function (data, type, row) {
                var shipmentId = row.id;
                var displayValue = row.prt + ' <span href="#" class="view-shipment-link text-green cursor-pointer"  data-shipment-id="' + shipmentId + '"><i class="fa fa-info-circle"></i></span>';
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
                            '<span href="#" class="view-notes-link btn btn-rounded"  data-notes="' +
                            notesArray[0].text +
                            '"><i class="fa fa-file-text"></i></span>'
                        );
                    } else {
                        // Display a link to add notes
                        return (
                            '<span href="#" class="btn btn-rounded add-notes-link" data-shipment-id="' +
                            shipmentId +
                            '" data-toggle="modal" data-target="#notesModal"><i class="fa fa-plus"></i></span>'
                        );
                    }
                }
                return data;
            },
        },
        {
            data: "delivery_status",
            name: "delivery_status",
            render: function (data, type, row) {
                var activeTab = $(".nav-link.active").attr("aria-controls");
                if (activeTab === "cancelledshipment") {
                    var deletedAt = row.deleted_at;
                    var deletionInfo = "";
                    if (deletedAt) {
                        var deletionDate = new Date(deletedAt);
                        var formattedDeletionDate =
                            deletionDate.toLocaleString();
                        deletionInfo = "Deleted at: " + formattedDeletionDate;

                        if (row.deleted_by) {
                            $.ajax({
                                url: "/users/" + row.deleted_by,
                                method: "GET",
                                success: function (user) {
                                    var deletedByName =
                                        user.name || "Unknown User";
                                    var deletedById = user.id || "Unknown ID";
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
                                async: false,
                            });
                        }
                    }
                    return deletionInfo || "Not deleted";
                }
                else{
                    var statusClasses = {
                        "new": "btn-new",
                        "loaded": "btn-loaded",
                        "planned": "btn-planned",
                        "delivered": "btn-delivered",
                        "in-progress": "btn-in-progress",
                        "driver-assigned": "btn-driver-assigned",
                        "in-transit-delay": "btn-in-transit-delay",
                        "in-transit-on-time": "btn-in-transit-on-time",
                        "posted-to-loadboard":"btn-posted-to-loadboard"
                    };
                    var statusClass = statusClasses[data.toLowerCase()] || "default-status";
                    return "<span class='text-capitalize shipment-status " + statusClass + "'>" + data + "</span>";
                
                }
                // return "<span class='text-capitalize'>"+ data +"</span>";
            },
        },

        {
            data: null,
            render: function (data, type, row) {
                var activeTab = $(".nav-link.active").attr("aria-controls");
                var deleted = $("#deleted").val(); // Get the value of the input element with id 'deleted'
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
    ];
    // Trigger export shipments buttons
    $("#tableExport").on("change", function () {
        let exporttype = $("#tableExport").val();
        if (exporttype === "1") {
            // For export all
            $('input[name="id[]"]:checked').prop("checked", false);
            $("#shipmenttable").DataTable().draw();
            $("#shipment_id").val([]);
            setTimeout(function () {
                $(".exportAll").trigger("click");
                //$(".buttons-excel").trigger("click");
            }, 100);
        } else if (exporttype === "0") {
            // For export only selected
            //var numberOfCheckboxes = $('input[name="id[]"]:checked').length;
            var numberOfCheckboxes = $('.checkboxes:checked').length;
            if (numberOfCheckboxes > 0) {
                $(".exportSelected").trigger("click");
                // $(".buttons-excel").trigger("click");
            } else {
                // addd sweet alert here
                alert("Please select atleast one shipment.");
            }
        }
    });

    // Trigger export shipments buttons
    $(".dropdown-item").on("click", function () {
        let exporttype = $(this).text().trim();

        if (exporttype === "Export All") {
            $('input[name="id[]"]:checked').prop("checked", false);
            $("#shipmenttable").DataTable().draw();
            $("#shipment_id").val([]);
            setTimeout(function () {
                $(".exportAll").trigger("click");
                // $(".buttons-excel").trigger("click");
            }, 100);
        } else if (exporttype === "Export Only Selected") {
            //var numberOfCheckboxes = $('input[name="id[]"]:checked').length;
            var numberOfCheckboxes = $('.checkboxes:checked').length;
            if (numberOfCheckboxes > 0) {
                $(".exportSelected").trigger("click");
                // $(".buttons-excel").trigger("click");
            } else {
                // Add a sweet alert here or any other handling for no selected items
                Swal.fire({
                    title: "No Shipments Selected",
                    text: "Please select at least one shipment for export.",
                    icon: "warning",
                });
            }
        }
    });

    // shipment data table setting
    const table = $("#shipmenttable").DataTable({
        processing: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            // Replace the font-awesome spinner with your preferred loading icon or text
        },
        serverSide: true,
        // ajax: shipmentList,
        ajax: {
            url: shipmentList,
            data: function (d) {
                (d.route_date = $("#datepicker").val()),
                    (d.delivery_status = $("#statusTab").val()),
                    (d.search = $('input[type="search"]').val());
            },
        },
        //lengthMenu: [5, 10, 25, 50, 100,-1],
        lengthMenu: [ [5, 10, 25,50,100,-1 ], [5, 10, 25,50,100,"All" ] ],
        pageLength: 100,
        columns: columnDefs,
        columnDefs: [
            { targets: [0, 1,2,3,7,8,9,10,11,12], sortable: true },
            { targets: [4,5,6,13], sortable: false },
        ],
        destroy: true,
        // select: {
        //     style: 'single',
        //  },
        //order: [[1, "asc"]],
        order: [],
        dom: "Blfrtip",
        initComplete: function () {
            // Add a class to the "Select All" checkbox
            $("thead th:first-child input[type='checkbox']").addClass("checkbox_animated");
        },
        buttons: [{
            extend: 'csv',
            text: 'Export All',
            className: "buttonsToHide exportAll",
            exportOptions: {
                columns: ':visible:not(.not-exported)',
                    modifier: {
                        selected: null // Export all data, not just selected rows
                    },
                    columns: ':not(:first-child)' // Exclude the first column
            },
            title: 'Data export'
        }, {
            extend: 'csv',
            text: 'Export selected',
            className: "buttonsToHide exportSelected",
            exportOptions: {
                columns: ':visible:not(.not-exported)',
                modifier: {
                    selected: true
                },
                columns: ':not(:first-child)' // Exclude the first column
            },
            title: 'Data export'
        }
    ],
        select: {
         style: 'multi',
         selector: 'td:first-child'
        }
    });

     // Get the search input element
     var searchInput = $('div.dataTables_filter input');

     // Add a tooltip to the search input
     searchInput.attr('title', ' Search in Shipment ID,Order ID,Warehouse (Origin),Customer Name,Location Name,Delivery Location,Shipment Contact No');

   // Unselect checkboxes while tab change or table redraw
    table.on( 'draw', function () {
        //table.column(0).checkboxes.deselectAll();
        var numberOfCheckboxesChecked = $(".checkboxes:checked").length;
        
       
           // For create route page//////////
           $("#shipmentcount").text(numberOfCheckboxesChecked);
           $("#shipmentroutecount").text(numberOfCheckboxesChecked);
           if (numberOfCheckboxesChecked > 0)
           {
            $(".deleteShipmentButton").show();
            $(".createroute").show();
           }else
           {
            $(".deleteShipmentButton").hide();
            $(".createroute").hide();   
           }
          ///////////////////////////////////

       
          // For shipment page//////////
          if (numberOfCheckboxesChecked > 0 && $("#statusTab").val()=="deleted") {
                $(".checkboxes").show();
                $(".restoreShipmentButton").show();
                $(".deleteShipmentButton").hide();
                $(".createroute").hide();
          } else {
                $(".restoreShipmentButton").hide();
          }
    });

    
    

    // Hide export buttons
    table.buttons(".buttonsToHide").nodes().addClass("hidden");
    table.buttons(".buttonsToHide").nodes().css("display", "none");

    $("#datepicker").on("change", function () {
        table.draw();
    });

    // Shipment Status Tab filters functionality

    $("#allshipment-tab").on("click", function () {
        
        // hide restore button
        $(".restoreShipmentButton").hide();

        $(".checkAll").prop("checked", false);
        $(".checkboxes").prop("checked", false);

        $(".progressshipment").removeClass("active");
        $(".deliveredshipment").removeClass("active");
        $(".cancelledshipment").removeClass("active");
        $(this).addClass("active");
        $("#statusTab").val("");
        $("#shipmenttable").DataTable().columns().visible(true);
        $("#shipmenttable").DataTable().draw();

    });

    $("#progressshipment-tab").on("click", function () {

        // hide restore button
        $(".restoreShipmentButton").hide();


        $(".checkAll").prop("checked", false);
        $(".checkboxes").prop("checked", false);

        $(".allshipment").removeClass("active");
        $(".deliveredshipment").removeClass("active");
        $(".cancelledshipment").removeClass("active");
        $(this).addClass("active");
        $("#statusTab").val("in-progress");
        $("#shipmenttable").DataTable().column().visible(true); // Hide "Status" column (index 10)
        $("#shipmenttable").DataTable().draw();
    });

    $("#deliveredshipment-tab").on("click", function () {
        // alert('hii');

        // hide restore button
        $(".restoreShipmentButton").hide();


        $(".checkAll").prop("checked", false);
        $(".checkboxes").prop("checked", false);

        $(".progressshipment").removeClass("active");
        $(".allshipment").removeClass("active");
        $(".cancelledshipment").removeClass("active");
        $(this).addClass("active");
        $("#statusTab").val("delivered");
        $("#shipmenttable").DataTable().columns().visible(true); // Show all columns
        $("#shipmenttable").DataTable().draw(); // Refresh the table
    });

    $("#cancelledshipment-tab").on("click", function () {
        // alert('hii');
        //$(".restoreShipmentButton").show();
        $(".checkAll").prop("checked", false);
        $(".checkboxes").prop("checked", false);

        $(".progressshipment").removeClass("active");
        $(".deliveredshipment").removeClass("active");
        $(".allshipment").removeClass("active");
        $(this).addClass("active");
        $("#statusTab").val("deleted");
        // alert('deleted');

        $("#shipmenttable").DataTable().columns().visible(true); // Show all columns
        $("#shipmenttable").DataTable().draw();
        var deleted = $("#deleted").val();
        // alert(deleted);
    });

    // ****************************************************************************************************

    var ids = [];
    
    // Handle the warehouse filter change
    $("#warehouse-filter").on("change", function () {
        var warehouseId = $(this).val();

        // Update the DataTable URL based on the selected option
        var url = "/shipments/datatables";
        if (warehouseId) {
            url += "?warehouse_id=" + warehouseId;
        }

        // Clear and redraw the DataTable with the new filter
        table.clear().draw();
        table.ajax.url(url).load();
    });

    var selectedWarehouseIds = {};

$('#shipmenttable').on('click', 'tbody tr, thead tr', async function() {
     setTimeout( function() 
      {
            var selectedRowsData = table.rows({ selected: true }).data().toArray();
            console.log('selectedRowsData',selectedRowsData);
            var row = table.column(0).checkboxes.selected();
            // Create variable for the ids array
            var selected_items = [];
            // Loop through to get the selected id
            $.each(row, function(index, rowId){
            //this add selected ID as object into array
            selected_items.push(rowId);
            });
            // let numberOfCheckboxesChecked=selected_items.length;
            console.log("selected_items",selected_items)
            console.log("numberOfCheckboxesChecked",numberOfCheckboxesChecked)

            var numberOfCheckboxesChecked = $('.checkboxes:checked').length;
            if (numberOfCheckboxesChecked > 0) {
                
                $("#shipmentcount").text(numberOfCheckboxesChecked);
                $("#shipmentroutecount").text(numberOfCheckboxesChecked);
                $(".deleteShipmentButton").show();
                $(".createroute").show();
            } else {
                $(".deleteShipmentButton").hide();
                $(".createroute").hide();
            }
            ids=selected_items;
            $("#shipment_id").val(selected_items);
            toggleButtonVisibility();
      }, 100);
})

    //$('#datepicker').datepicker();
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
    // ... Your existing code
    $(".restoreShipmentButton").on("click", function () {
        var numberOfCheckboxesChecked = $(".checkboxes:checked").length;
        $("#shipmentrestorecount").text(numberOfCheckboxesChecked);
        // alert(numberOfCheckboxesChecked);

        // var ids = [];
        // table.$('input[type="checkbox"]').each(function () {
        //     if (this.checked) {
        //         ids.push(this.value);
        //     }
        // });
        table.$('input[type="checkbox"]').each(function () {
            if (this.checked) {
                ids.push(this.value);
            } else {
                var index = ids.indexOf(this.value);
                if (index !== -1) {
                    ids.splice(index, 1);
                }
            }
        });
        $("#shipment_id").val(ids);

        if (ids.length > 0) {
            Swal.fire({
                title: "Are you sure you want to restore selected shipments?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#7d2326",
                confirmButtonText: "Yes, restore them!",
                cancelButtonText: "No, cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    var restoreRoute = $(this).data("route");
                    $.ajax({
                        type: "POST",
                        url: restoreRoute,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                            ids: ids,
                        },
                        success: function (response) {
                            console.log("Restored shipments with IDs: ", ids);
                            table.ajax.reload();
                            numberOfCheckboxesChecked -= $(
                                ".checkboxes:checked"
                            ).length;
                            $("#shipmentcount").text(numberOfCheckboxesChecked);
                            // if (numberOfCheckboxesChecked = 0){
                            //     $('.deleteShipmentButton').hide();
                            // }

                            if (numberOfCheckboxesChecked === 0) {
                                $(".restoreShipmentButton").hide();
                            } else {
                                $(".restoreShipmentButton").show();
                            }
                        },
                        error: function (error) {
                            console.error("Error restoring shipments:", error);
                        },
                    });
                }
            });
        }
        toggleButtonVisibility();
    });

    $(".deleteShipmentButton").on("click", function () {
        var ids = []; 
        // var numberOfCheckboxesChecked = $('.checkboxes:checked').length;
        var numberOfCheckboxesChecked = $(".checkboxes:checked").length;

        // Initialize an array to store selected shipment IDs

        table.$('input[type="checkbox"]').each(function () {
            if (this.checked) {
                ids.push(this.value);
            }
        });

        if (ids.length > 0) {
            Swal.fire({
                title: "Delete Shipment",
                text: "Are you sure you want to delete selected shipments?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#7d2326",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    var deleteRoute = $(this).data("route");
                    var deleted_by = userId; // Assuming userId is defined and holds the user's ID

                    // Create a data object to send with the AJAX request
                    var data = {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        ids: ids,
                        deleted_by: deleted_by,
                    };

                    $.ajax({
                        type: "DELETE",
                        url: deleteRoute,
                        data: data, // Send the data object

                        success: async function (response) {
                            console.log("Deleted shipments with IDs: ", ids);
                            ids = [];
                            table.ajax.reload();
                            // location.reload(true);

                            // Display the updated count of checkboxes after successful deletion
                            numberOfCheckboxesChecked -= $(
                                ".checkboxes:checked"
                            ).length;
                            $("#shipmentcount").text(numberOfCheckboxesChecked);
                            // if (numberOfCheckboxesChecked = 0){
                            //     $('.deleteShipmentButton').hide();
                            // }

                            if (numberOfCheckboxesChecked === 0) {
                                $(".deleteShipmentButton").hide();
                                $(".createroute").hide();
                            } else {
                                $(".deleteShipmentButton").show();
                                $(".createroute").show();
                            }
                            
                            // Show loading state during deletion
                            await Swal.fire({
                                title: "Processing",
                                text: "Deleting selected shipments...",
                                icon: "info",
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            const { deleted_count = 0, deleted_names = [], failed_count = 0, failed_names = [], failed_messages = [] } = response;
                            
                            const resultIcon = deleted_count === (deleted_count + failed_count) ? "success" : 
                                             (deleted_count > 0 ? "warning" : "error");
                            const resultTitle = `Deletion Results (${deleted_count}/${deleted_count + failed_count} Successful)`;
                            
                            // Build detailed HTML message with enhanced styling
                            const messageHtml = `
                                <div class="deletion-results">
                                    ${deleted_count > 0 ? `
                                        <div class="mb-3 text-success">
                                            <strong>✓ Successfully Deleted (${deleted_count}):</strong><br>
                                            <div class="mt-2 text-left">
                                                ${deleted_names.map(name => `<div>• ${name}</div>`).join('')}
                                            </div>
                                        </div>
                                    ` : ''}
                                    ${failed_count > 0 ? `
                                        <div class="mb-3 text-warning">
                                            <strong>⚠ Failed to Delete (${failed_count}):</strong><br>
                                            <div class="mt-2 text-left">
                                                ${failed_names.map(name => `<div>• ${name}</div>`).join('')}
                                            </div>
                                        </div>
                                        <div class="text-danger">
                                            <strong>Error Details:</strong><br>
                                            <div class="mt-2 text-left">
                                                ${failed_messages.map(msg => `<div>• ${msg}</div>`).join('')}
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            `;

                            await Swal.fire({
                                title: resultTitle,
                                html: messageHtml || 'No shipments were processed.',
                                icon: resultIcon,
                                confirmButtonText: 'OK',
                                customClass: {
                                    container: 'deletion-result-modal',
                                    content: 'deletion-result-content',
                                    htmlContainer: 'deletion-result-html'
                                }
                            });

                            // Refresh the table data and update UI state
                            try {
                                await table.ajax.reload(null, false);
                                updateSelectedWarehouseIds(); // Update selected warehouse IDs after reload
                            } catch (error) {
                                console.error("Error refreshing table:", error);
                                Swal.fire({
                                    title: "Warning",
                                    text: "Shipments were processed but the table refresh failed. Please refresh the page.",
                                    icon: "warning",
                                    confirmButtonText: 'Refresh Now',
                                    showCancelButton: true,
                                    cancelButtonText: 'Later'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            }
                        },

                        error: function(xhr, status, error) {
                            console.error("Shipment deletion error:", { 
                                status, 
                                error, 
                                response: xhr.responseText,
                                statusCode: xhr.status
                            });
                            
                            let errorMessage;
                            try {
                                const response = xhr.responseJSON;
                                errorMessage = response?.message || response?.error || 
                                             "Failed to delete shipments. Please try again or contact support if the issue persists.";
                            } catch (e) {
                                errorMessage = "An unexpected error occurred while processing the response.";
                            }
                            
                            Swal.fire({
                                title: "Deletion Error",
                                text: errorMessage,
                                icon: "error",
                                confirmButtonText: 'OK',
                                showCancelButton: false,
                                customClass: {
                                    popup: 'error-modal'
                                }
                            });
                        },
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire("Cancelled", "No changes were made.", "info");
                    //table.rows().deselect();
                    //$(".checkboxes").prop("checked", false);
                }
            });
        }
    });

    var selectedWarehouseIds = {};
    $("#shipmenttable").on("change", ".checkboxes", function () {
        var checkedCheckboxes = $(".checkboxes:checked");
        console.log(checkedCheckboxes);

        if (this.checked) {
            var row = $(this).closest("tr");
            var warehouseId = row.find("td:eq(3)").text();
            console.log(warehouseId);

            selectedWarehouseIds[row.index()] = warehouseId;
            console.log(selectedWarehouseIds);
        } else {
            // Remove the warehouse ID when unchecking a checkbox
            var rowIndex = $(this).closest("tr").index();
            delete selectedWarehouseIds[rowIndex];
        }
    });

    function updateSelectedWarehouseIds() {
        selectedWarehouseIds = {};
        $(".checkboxes:checked").each(function () {
            var row = $(this).closest("tr");
            var warehouseId = row.find("td:eq(3)").text();
            selectedWarehouseIds[row.index()] = warehouseId;
        });
    }


    // Function to chunk array into smaller arrays
    function chunkArray(array, chunkSize) {
        const chunks = [];
        for (let i = 0; i < array.length; i += chunkSize) {
            chunks.push(array.slice(i, i + chunkSize));
        }
        return chunks;
    }




    /** Shipment Create Route from Google OR tool   */
    $(".createroute").on("click", function () {
        var numberOfCheckboxesChecked = $(".checkboxes:checked").length;
        var ids = []; // Initialize an array to store selected shipment IDs
        var warehouseIds = []; // Store selected warehouse IDs
        var deliveryDates = []; // Store selected delivery dates

        // Iterate through selected checkboxes
        table.$('input[type="checkbox"]').each(function () {
            if (this.checked) {
                var row = $(this).closest("tr");
                var rowIndex = row.index();
                ids.push(this.value);
                deliveryDates.push(row.find("td:eq(8)").text()); // Assuming delivery date is in the 5th column
            }
        });

        // Iterate through selected checkboxes
        table.$('input[type="checkbox"]').each(function () {
            if (this.checked) {
                var row = $(this).closest("tr");
                var rowIndex = row.index();
                ids.push(this.value);
                // if(selectedWarehouseIds[rowIndex]>0)
                // {
                //warehouseIds.push(selectedWarehouseIds[rowIndex]);
                warehouseIds.push(row.find("td:eq(3)").text());    
                //}
            }
        });

        // Check if there are multiple selected rows with different warehouse IDs
        var uniqueWarehouseIds = [...new Set(warehouseIds)];
        console.log("uniqueWarehouseIds",uniqueWarehouseIds)
        if (uniqueWarehouseIds.length > 1) {
            Swal.fire({
                title: "Warning",
                text: "Please select shipments from the same warehouse.",
                icon: "warning",
            });
            return;
        }
        var uniqueDeliveryDates = [...new Set(deliveryDates)];
        console.log("uniqueDeliveryDates",uniqueDeliveryDates)
        if (uniqueDeliveryDates.length > 1) {
            Swal.fire({
                title: "Warning",
                text: "Please select shipments with the same delivery date.",
                icon: "warning",
            });
            return;
        }

        // table.$('input[type="checkbox"]').each(function () {
        //     if (this.checked) {
        //         ids.push(this.value);
        //     }
        // });
        //console.log(ids);
        var getShipmentIdsUrl = $(this).data("route");
        // sort shipment  ids by asc and unique
        ids = [...new Set(ids)];
        ids.sort(function(a, b) {
            return a - b;
        });
        var data = {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id: ids,
        };
        var lbs = 0;
        var address = "";
        var time_windows = "";
        var demands = "";
        let forkLiftArray;
        $.ajax({
            type: "Get",
            url: getShipmentIdsUrl,
            dataType: "json",
            data: data, // Send the data object
            success: function (response) {
                //console.log(response);
                lbs = response.routes;
                address = response.locations;
                time_windows = response.delivery_time;
                demands = response.demands;
                totaldemandlbs = response.totaldemandlbs;
                vehicle_capacity = response.vehicle_capacity;
                forkLiftArray = response.forkLiftArray;
                data.id=response.ids;
                Swal.fire({
                    title: "Confirm Route Creation",
                    html:
                        "<b>*Note:</b> A truck can handle a maximum capacity <b>48K lbs</b> without a forklift and <b>44K lbs</b> with a forklift. <ul class='shipment-create-route'><li><b>" +
                        numberOfCheckboxesChecked +
                        "</b> shipment selected</li> <li><b>" +
                        Number(totaldemandlbs / 1000).toFixed(2) +
                        "</b>K lbs weight </li><li><b>" +
                        lbs +
                        "</b> route will be created.</li></ul> <p class='text-start'>To reduce weight deselect some shipment IDs. To start route creation from the beginning click on the Cancel button. Please click on Create Route button to proceed with selected shipments.<p>",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#7d2326",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Back",
                }).then((result) => {
                    if (result.isConfirmed) {
                        const postData = {
                            _token: $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        };
                        //Get warehouse elements from arrays
                        const warehouseAddress = address[0];
                        const warehouseTimeWindow = time_windows[0];
                        const warehouseDemands = demands[0];

                        // Remove warehouse element from arrays
                        address.shift();
                        time_windows.shift();
                        demands.shift();
                        forkLiftArray.shift();

                        //console.log("address2", address);

                        const chunkSize = 22; // Maximum number of addresses in each chunk that google Matrix APIs support
                        
                        // Get array in chunks
                        const addressChunks = chunkArray(address, chunkSize);
                        const timeWindowChunks = chunkArray(time_windows, chunkSize);
                        const demandsChunks = chunkArray(demands, chunkSize);
                        const ids = chunkArray(data.id, chunkSize);
                        const forkLiftArrayChunks = chunkArray(forkLiftArray
                            , chunkSize);

                        //console.log("ids", ids);


                        const requests = addressChunks.map((chunk, index) => {

                            // dissable create route button and enable loader
                            $(".createroute").attr("disabled", "disabled");
                            $(".spinner-btn").removeClass("d-none");
                            
                            // Add warehouse address to request array
                            let addressChunksUpdated = addressChunks[index]
                            addressChunksUpdated.unshift(warehouseAddress);

                            // Add warehouse Time Window to request array
                            let timeWindowChunksUpdated = timeWindowChunks[index]
                            timeWindowChunksUpdated.unshift(warehouseTimeWindow);

                            // Add warehouse demands to request array
                            let demandsChunksUpdated = demandsChunks[index]
                            demandsChunksUpdated.unshift(warehouseDemands);

                            let forkLiftArrayChunksUpdated=forkLiftArrayChunks[index]

                           // overright vehicle capacity for all type of routes
                            vehicle_capacity=48000;
                            // comment for now
                            // if (forkLiftArrayChunksUpdated.includes(1)) {
                            //     vehicle_capacity=44000;
                            // }else
                            // {
                            //     vehicle_capacity=48000;
                            // }   

                           // update shipment ids based on chunks
                            const shipmentData = {
                                ...data,
                                id: ids[index]
                            };

                            // Create updated request data based on chunks
                            const requestData = {
                                ...postData,
                                vehicle_capacity:vehicle_capacity,
                                address: addressChunksUpdated,
                                time_windows: timeWindowChunksUpdated,
                                demands: demandsChunksUpdated,
                            };
                            console.log("requestData", requestData);
                            return $.ajax({
                                type: "post",
                                url: CREATE_ROUTE_URL+'/solve_cvrptw',
                                contentType: "application/json",
                                dataType: "json",
                                data: JSON.stringify(requestData),
                                success: function (response) {
                                    if (response.success == true) {
                                        saveRoutes(response.data, shipmentData, table, addressChunks, index);
                                    } else {
                                        $(".createroute").removeAttr("disabled");
                                        $(".spinner-btn").addClass("d-none");
                                        Swal.fire({
                                            title: "Route Error",
                                            text: response.message,
                                            icon: "warning",
                                        });
                                    }
                                },
                                error: function (xhr, status, errorThrown) {
                                    Swal.fire({
                                        title: "Server Error",
                                        text: "Something went wrong, please try again later.",
                                        icon: "warning",
                                    });
                                    $(".createroute").removeAttr("disabled");
                                    $(".spinner-btn").addClass("d-none");
                                },
                            });
                        });

                        $.when(...requests).fail(function () {
                            Swal.fire({
                                title: "Error",
                                text: "Unable to generate routes for the provided addresses, time windows, and product weights.",
                                icon: "warning",
                            });
                            $(".createroute").removeAttr("disabled");
                            $(".spinner-btn").addClass("d-none");
                        });
                        console.log("addressChunks", addressChunks);
                        console.log("timeWindowChunks", timeWindowChunks);
                        console.log("demandsChunks", demandsChunks);
                        return;
                    } else {
                        //table.rows().deselect();
                        //$(".checkboxes").prop("checked", false);
                        //$(".checkAll").prop("checked", false);

                        $(".createroute").removeAttr("disabled");
                        $(".spinner-btn").addClass("d-none");
                    }
                });
            },
            error: function (xhr, status, error) {
                $(".createroute").removeAttr("disabled");
                $(".spinner-btn").addClass("d-none");
                var errorMessage = xhr.responseJSON.message;
                Swal.fire({
                    title: "Warning",
                    html: errorMessage,
                    icon: "warning",
                });
            },
        });

    })
    /** Shipment Create Route function End */

    table.on('draw', function () {

        if ($('.checkAll').is(":checked")) {
            //$(".checkboxes").prop("checked", true);
            $(".checkboxes").prop("checked", true).trigger("change");;
        }
        // Update state of "Select all" control
        //updateDataTableSelectAllCtrl(table);
    });

});

function saveRoutes(routeData, shipmentIds, table, addressChunks, index) {
    var data = {
        _token: $('meta[name="csrf-token"]').attr("content"),
        routeData: routeData,
        shipmentData: shipmentIds,
    };

    $.ajax({
        type: "post",
        url: "/routes/create",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(data), // Send the data object
        success: function (response) {
            table.ajax.reload();
            if(addressChunks.length==(index+1))
            {
                $(".createroute").removeAttr("disabled");
                $(".spinner-btn").addClass("d-none");
                window.location.href = '/routes/map';
            }
        },
        error: function (error) {
            $(".createroute").removeAttr("disabled");
            $(".spinner-btn").addClass("d-none");
        },
    });
}

function showdata() {
    // alert("You clicked the element with and ID of 'test-element'");
    $("#shipmenttable").DataTable().draw();
}
$(document).on("click", ".close", function () {
    $("#notesModal").modal("hide");
    $("#notesviewModal").modal("hide");
});

/**
 * Fuinction to show and hide delete
 */



function toggleButtonVisibility() {
    var activeTab = $(".nav-link.active").attr("aria-controls");
    var numberOfCheckboxesChecked = $(".checkboxes:checked").length;
    if (activeTab === "cancelledshipment") {
        
        if (numberOfCheckboxesChecked > 0) {
            $(".checkboxes").show();
            $(".restoreShipmentButton").show();
            $(".deleteShipmentButton").hide();
            $(".createroute").hide();
        } else {
            $(".restoreShipmentButton").hide();
        }

        $("#shipmentrestorecount").text(numberOfCheckboxesChecked);
    } else {
        $(".restoreShipmentButton").hide();
        $(".deleteShipmentButton").show();
        $(".createroute").show();
        // $("#shipmentcount").text(numberOfCheckboxesChecked);
        if (numberOfCheckboxesChecked > 0) {
            $(".deleteShipmentButton").show();
            $(".createroute").show();
            $(".restoreShipmentButton").hide();
        } else {
            $(".deleteShipmentButton").hide();
            $(".createroute").hide();
            $(".restoreShipmentButton").hide();
        }
        $("#shipmentcount").text(numberOfCheckboxesChecked);
    }
}

toggleButtonVisibility();
