$(document).ready(function () {
    var columnDefs = [
        {
            data: "name",
            name: "name",
        },
        {
            data: "email",
            name: "email",
            render: function (data, type, row) {
                
                if(data!=null && data!="")
                return data;
                else return "N/A";
            }
        },
        {
            data: "primary_contact_number",
            name: "primary_contact_number",
            render: function (data, type, row) {
                
                if(data!=null && data!="")
                return data;
                else return "N/A";
            }
        },
        {
            data: "role_name",
            name: "role.name",
            orderable: false,
            searchable: false,
        },
        {
            data: "status",
            name: "status",
            render: function (data, type, row) {
                return (
                    '<select class="form-select w-50 status-dropdown">' +
                    '<option value="active" ' +
                    (data === "active" ? "selected" : "") +
                    ">Active</option>" +
                    '<option value="inactive" ' +
                    (data === "inactive" ? "selected" : "") +
                    ">Inactive</option>" +
                    "</select>"
                );
            },
        },
        {
            data: "views",
            name: "views",
            orderable: false,
            searchable: false,
        },
    ];
    if (authUser.role.id == super_admin_id) {
        columnDefs.splice(4, 0, {
            data: "driver_assign_route_lock",
            name: "driver_assign_unlock_route",
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                if (row.role_name === 'Admin' || row.role_name === 'Router' || row.role_name === 'Dispatcher') {
                console.log('row', row);
                const isChecked = row.can_driver_assign_lock_route == 1 ? 'checked' : '';
                const inputValue = row.can_driver_assign_lock_route == 1 ? 0 : 1;
                    return `
                        <div class="driver-toggle-switch">
                            <input type="checkbox" id="driver-toggle-switch-${row.id}" name="can_driver_assign_lock_route"
                                data-id="${row.id}" value="${inputValue}" class="hidden" ${isChecked}>
                            <label for="driver-toggle-switch-${row.id}" class="toggle-label"></label>
                        </div>`;
                } else {
                    return '--';
                }
            }

        });
    }
    if (authUser.role.id == super_admin_id) {
        columnDefs.splice(4, 0, {
            data: "route_lock",
            name: "unlock_route",
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                if (row.role_name === 'Admin' || row.role_name === 'Router' || row.role_name === 'Dispatcher') {
                    return `
                    <div class="toggle-switch">
                        <input type="checkbox" id="toggle-switch-${row.id}" name="unlock" data-id="${row.id}" value="${(row.unlock_route == 1 ? 0 : 1)}" class="hidden" ` + (row.unlock_route == 1 ? 'checked' : '') + `>
                        <label for="toggle-switch-${row.id}" class="toggle-label"></label>
                    </div>`;
                } else {
                    return '--';
                }
            }
        });
    }
 

    var table = $("#user-table").DataTable({
        processing: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            // Replace the font-awesome spinner with your preferred loading icon or text
        },
        ajax: {
            url: userDataTable,
        },
        lengthMenu: [5, 10, 25, 50, 100], // Define the per page options
        columns: columnDefs,
        order: [],
    });
     // Get the search input element
     var searchInput = $('div.dataTables_filter input');

     // Add a tooltip to the search input
      searchInput.attr('title', ' Search in Name,Email,Primary Mobile No,Roles,Status');
 
    // Handle the warehouse filter change
    $("#role-filter").change(function () {
        var roleId = $(this).val();

        // Update the DataTable URL based on the selected option
        var url = "/users/datatables";
        if (roleId) {
            url += "?role_id=" + roleId;
        }

        // Clear and redraw the DataTable with the new filter
        table.clear().draw();
        table.ajax.url(url).load();
    });
    // Handle the warehouse filter change
    $("#status-filter").change(function () {
        var status = $(this).val();

        // Update the DataTable URL based on the selected option
        var url = "/users/datatables";
        if (status) {
            url += "?status=" + status;
        }

        // Clear and redraw the DataTable with the new filter
        table.clear().draw();
        table.ajax.url(url).load();
    });
    $(document).on("change", ".toggle-switch", function () {
        var row = table.row($(this).closest("tr"));
        var userId = row.data().id;
        var status = $('#toggle-switch-' + userId).val();
        let actionText = "grant";
        if (status == 0) {
            actionText = "revoke";
        }
    
        Swal.fire({
            title: "Change User Status",
            text: "Are you sure you want to " + actionText + " unlock access for route?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, change it!",
            cancelButtonText: "No, cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                var csrfToken = $('meta[name="csrf-token"]').attr("content");
    
                $.ajax({
                    type: "POST",
                    url: unlockStatus,
                    data: {
                        _token: csrfToken,
                        user_id: userId,
                        status: status,
                    },
                    success: function (response) {
                        if (response.message) {
                            Swal.fire(
                                "Status Changed",
                                response.message,
                                "success"
                            );
                            // Reload the DataTable
                            table.ajax.reload();
                        } else {
                            Swal.fire(
                                "Error",
                                "Failed to update the user status.",
                                "error"
                            );
                        }
                    },
                    error: function (error) {
                        Swal.fire(
                            "Error",
                            "An error occurred while updating the user status.",
                            "error"
                        );
                    },
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire("Cancelled", "No changes were made.", "info");
                table.ajax.reload();
            }
        });
    });    
    $(document).on("change", ".driver-toggle-switch", function () {
        var row = table.row($(this).closest("tr"));
        var userId = row.data().id;
        var status = $('#driver-toggle-switch-' + userId).val();
        let actionText = "grant";
        if (status == 0) {
            actionText = "revoke";
        }
    
        Swal.fire({
            title: "Change User Status",
            text: "Are you sure you want to " + actionText + " driver assign access for route?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, change it!",
            cancelButtonText: "No, cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                var csrfToken = $('meta[name="csrf-token"]').attr("content");
    
                $.ajax({
                    type: "POST",
                    url: unlockDriverAccess,
                    data: {
                        _token: csrfToken,
                        user_id: userId,
                        status: status,
                    },
                    success: function (response) {
                        if (response.message) {
                            Swal.fire(
                                "Status Changed",
                                response.message,
                                "success"
                            );
                            // Reload the DataTable
                            table.ajax.reload();
                        } else {
                            Swal.fire(
                                "Error",
                                "Failed to update the user status.",
                                "error"
                            );
                        }
                    },
                    error: function (error) {
                        Swal.fire(
                            "Error",
                            "An error occurred while updating the user status.",
                            "error"
                        );
                    },
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire("Cancelled", "No changes were made.", "info");
                table.ajax.reload();
            }
        });
    });    
    $(document).on("change", ".status-dropdown", function () {
        var row = table.row($(this).closest("tr"));
        var status = $(this).val();
        var userId = row.data().id;

        Swal.fire({
            title: "Change User Status",
            text: "Are you sure you want to change the user status?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, change it!",
            cancelButtonText: "No, cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                var csrfToken = $('meta[name="csrf-token"]').attr("content");

                $.ajax({
                    type: "POST",
                    url: statusUpdate,
                    data: {
                        _token: csrfToken,
                        user_id: userId,
                        status: status,
                    },
                    success: function (response) {
                        if (response.message) {
                            Swal.fire(
                                "Status Changed",
                                response.message,
                                "success"
                            );
                            // You can update the UI as needed here
                        } else {
                            Swal.fire(
                                "Error",
                                "Failed to update the user status.",
                                "error"
                            );
                        }
                    },
                    error: function (error) {
                        Swal.fire(
                            "Error",
                            "An error occurred while updating the user status.",
                            "error"
                        );
                    },
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire("Cancelled", "No changes were made.", "info");
                table.ajax.reload();
            }
        });
    });
    $("#user-table").on("change", ".status-dropdown1222", function () {
        var row = table.row($(this).closest("tr"));
        var status = $(this).val(); // Make sure this value is 'active' or 'inactive'
        var userId = row.data().id;

        // Get the CSRF token value from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        // Perform your AJAX request to update the user status
        $.ajax({
            type: "POST",
            url: statusUpdate,
            data: {
                _token: csrfToken,
                user_id: userId,
                status: status, // Make sure this is 'active' or 'inactive'
            },
            success: function (response) {
                // Handle success, if needed
                console.log("User status updated successfully");
            },
            error: function (error) {
                // Handle error, if needed
                console.error("Error updating user status:", error);
            },
        });
    });
});
