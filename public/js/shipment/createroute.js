$(document).ready(function () {
  // alert('hii');

  /**
   * Column definitions for the shipment datatable
   */
  const columnDefs = [
    {
      data: 'id',
      name: 'name',
      targets: 0,
      searchable: false,
      orderable: false,
      className: 'dt-body-center',
      render: (data) => `
        <div class="checkbox-checked">
          <label class="d-block" for="chk-${data}"></label>
          <input type="checkbox" name="id[]" class="checkboxes" value="${data}">
        </div>`
    },
    { data: 'name', name: 'name' },
    { data: 'order_id', name: 'order_id' },
    { data: 'warehouse_name', name: 'warehouse.name' },
    { data: 'customer_name', name: 'customer_name' },
    { data: 'delivery_location_name', name: 'delivery_location_name' },
    { data: 'delivery_location', name: 'delivery_location' },
    { data: 'delivery_contact_phone', name: 'delivery_contact_phone' },
    {
      data: 'route_Date',
      name: 'route_Date',
      render: (data, type) => {
        if (type === 'display' || type === 'filter') {
          const date = new Date(data);
          return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()}`;
        }
        return data;
      }
    },
    {
      data: 'props_delivery_window',
      name: 'props_delivery_window',
      render: (data, type) => {
        if (type === 'display' && Array.isArray(data) && data.length > 0) {
          const startTime = new Date(data[0].start);
          const endTime = new Date(data[0].end);
          const timeOptions = { timeZone: 'UTC', hour: '2-digit', minute: '2-digit' };
          
          return `${startTime.toLocaleTimeString('en-US', timeOptions)} - ${endTime.toLocaleTimeString('en-US', timeOptions)}`;
        }
        return data;
      }
    },
    {
      data: 'id',
      name: 'id',
      render: (data, type, row) => 
        `Item Quantity static<a href="#" class="view-shipment-link" data-shipment-id="${row.id}">Info</a>`
    },
    {
      data: 'fork_lift',
      name: 'fork_lift',
      render: data => data ? 'Yes' : 'No'
    },
    {
      data: 'props_notes',
      name: 'props_notes',
      render: (data, type, row) => {
        if (type === 'display') {
          if (Array.isArray(data) && data.length > 0 && data[0].text) {
            return `<a href="#" class="view-notes-link btn btn-rounded" data-notes="${data[0].text}">
              <i class="fa fa-file-text"></i>
            </a>`;
          }
          return `<a href="#" class="add-notes-link" data-shipment-id="${row.id}" 
            data-toggle="modal" data-target="#notesModal">Add Notes</a>`;
        }
        return data;
      }
    },
    { data: 'shipment_status', name: 'shipment_status' },
    {
      data: null,
      orderable: false,
      render: (data) => {
        const activeTab = $('.nav-link.active').attr('aria-controls');
        const deleted = $('#deleted').val();
        const viewUrl = activeTab === 'cancelledshipment' 
          ? `${viewShipmentRoute}/${data.id}/${encodeURIComponent(deleted)}`
          : `${viewShipmentRoute}/${data.id}`;
          
        return `<a href="${viewUrl}" class="btn btn-rounded btn-primary">
          <i class="fa fa-eye"></i>
        </a>`;
      }
    }
  ];

  /**
   * Handle export functionality
   * @param {string} exportType - Type of export ('all' or 'selected')
   */
  function handleExport(exportType) {
    if (exportType === 'all') {
      $('input[name="id[]"]:checked').prop('checked', false);
      $('#shipmenttable').DataTable().draw();
      $('#shipment_id').val([]);
      setTimeout(() => $('.buttons-csv').trigger('click'), 100);
    } else if (exportType === 'selected') {
      const selectedCount = $('input[name="id[]"]:checked').length;
      if (selectedCount > 0) {
        $('.buttons-csv').trigger('click');
      } else {
        Swal.fire({
          title: 'Warning',
          text: 'Please select at least one shipment.',
          icon: 'warning'
        });
      }
    }
  }

  // Export dropdown change handler
  $('#tableExport').on('change', function() {
    handleExport($(this).val() === '1' ? 'all' : 'selected');
  });

  // Export button click handler
  $('.dropdown-item').on('click', function() {
    const exportType = $(this).text().trim() === 'Export All' ? 'all' : 'selected';
    handleExport(exportType);
  });

  /**
   * Initialize the shipment datatable
   */
  const table = $('#shipmenttable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: shipmentRoute,
      data: function(d) {
        d.deleted = $('#deleted').val();
        d.shipment_id = $('#shipment_id').val();
        d.route_date = $('#datepicker').val();
        d.status = $('#status').val();
      }
    },
    columns: columnDefs,
    order: [[1, 'asc']],
    pageLength: 10,
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'csv',
        text: 'CSV',
        exportOptions: {
          columns: ':visible:not(:first-child)',
          modifier: {
            selected: true
          }
        }
      }
    ],
    select: {
      style: 'multi',
      selector: 'td:first-child'
    },
    initComplete: function() {
      $('.dt-buttons').hide();
    }
  });

  /**
   * Refresh the datatable
   */
  function refreshTable() {
    $('#shipmenttable').DataTable().draw();
  }

  /**
   * Handle checkbox selection and update UI elements
   */
  function updateCheckboxStatus() {
    const checkedCount = $('.checkboxes:checked').length;
    const totalCheckboxes = $('.checkboxes').length;
    
    $("#shipmentcount, #shipmentroutecount").text(checkedCount);
    $(".checkAll").prop("checked", checkedCount > 0 && checkedCount === totalCheckboxes);
    
    toggleButtonVisibility();
  }

  /**
   * Toggle visibility of action buttons based on selection and active tab
   */
  function toggleButtonVisibility() {
    const checkedCount = $('.checkboxes:checked').length;
    const activeTab = $('.nav-link.active').attr('aria-controls');
    const isCancelledTab = activeTab === 'cancelledshipment';

    // Update counts
    if (isCancelledTab) {
      $("#shipmentrestorecount").text(checkedCount);
    } else {
      $("#shipmentcount").text(checkedCount);
    }

    // Toggle button visibility
    $('.restoreShipmentButton').toggle(isCancelledTab && checkedCount > 0);
    $('.deleteShipmentButton, .createroute').toggle(!isCancelledTab && checkedCount > 0);
  }

  // Event Handlers
  $(document).on("click", ".checkboxes", updateCheckboxStatus);
  $(document).on("click", "#cancelledshipment-tab", toggleButtonVisibility);
  $(document).on('click', '.close', function() {
    $('#notesModal, #notesviewModal').modal('hide');
  });

  // Initialize datepicker
  $("#datepicker-icon").click(() => $('#datepicker').datepicker());

  // Handle notes modal
  $(document).on('click', '.view-notes-link', function(e) {
    e.preventDefault();
    const notes = $(this).data('notes');
    $('#notesContent').html(notes);
    $('#notesviewModal').modal('show');
  });

  $(document).on('click', '.add-notes-link', function(e) {
    e.preventDefault();
    const shipmentId = $(this).data('shipment-id');
    $('#shipment_id_for_notes').val(shipmentId);
    $('#notesModal').modal('show');
  });

  // Handle "Check All" functionality
  $('.checkAll').click(function() {
    const isChecked = $(this).prop('checked');
    $('.checkboxes').prop('checked', isChecked);
    updateCheckboxStatus();
  });

  // Initialize button visibility
  toggleButtonVisibility();

  // Handle date filter
  $('#datepicker').change(refreshTable);
  $('#status').change(refreshTable);

  // Shipment Status Tab filters functionality

  $('#allshipment-tab').on('click', function () {
    $(".checkAll").prop("checked", false);
    $(".checkboxes").prop("checked", false);

    $('.progressshipment').removeClass('active');
    $('.deliveredshipment').removeClass('active');
    $('.cancelledshipment').removeClass('active');
    $(this).addClass('active');
    $('#statusTab').val('');
    $('#shipmenttable').DataTable().columns().visible(true); // Show all columns
    $('#shipmenttable').DataTable().draw(); // Refresh the table

  });

  $('#progressshipment-tab').on('click', function () {

    $(".checkAll").prop("checked", false);
    $(".checkboxes").prop("checked", false);

    $('.allshipment').removeClass('active');
    $('.deliveredshipment').removeClass('active');
    $('.cancelledshipment').removeClass('active');
    $(this).addClass('active');
    $('#statusTab').val('in-progress');
    $('#shipmenttable').DataTable().column().visible(true); // Hide "Status" column (index 10)
    $('#shipmenttable').DataTable().draw();


  });

  $('#deliveredshipment-tab').on('click', function () {
    // alert('hii');
    $(".checkAll").prop("checked", false);
    $(".checkboxes").prop("checked", false);


    $('.progressshipment').removeClass('active');
    $('.allshipment').removeClass('active');
    $('.cancelledshipment').removeClass('active');
    $(this).addClass('active');
    $('#statusTab').val('delivered');
    $('#shipmenttable').DataTable().columns().visible(true); // Show all columns
    $('#shipmenttable').DataTable().draw(); // Refresh the table

  });



  // ****************************************************************************************************

  var ids = [];

  // Handle the warehouse filter change
  $('#warehouse-filter').on('change',function () {
    var warehouseId = $(this).val();

    // Update the DataTable URL based on the selected option
    var url = '/shipments/datatables';
    if (warehouseId) {
      url += '?warehouse_id=' + warehouseId;
    }

    // Clear and redraw the DataTable with the new filter
    table.clear().draw();
    table.ajax.url(url).load();
  });

  $('.checkAll').on('click',function () {
    if (this.checked) {
      $(".checkboxes").prop("checked", true);
      table.rows().select();
      $("th.dt-body-center").addClass("selected");
    } else {
      $(".checkboxes").prop("checked", false);
      table.rows().deselect();
      $("th.dt-body-center").removeClass("selected");
    }
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
    table.$('input[type="checkbox"]').each(function () {
      if (this.checked) {
        ids.push(this.value);
      } else {
        ids.pop(this.value);
      }
      //  }
    });
    console.log(ids);
    $("#shipment_id").val(ids);
  });
  //$('#datepicker').datepicker();
  $(document).on('click', '.add-notes-link', function () {
    const notesBtn = $(this);
    const notesTextarea = $('#notesTextarea');
    const saveNotesBtn = $('#saveNotesBtn');
    const notesModal = $('#notesModal');

    // Get the shipment ID and notes
    const shipmentId = notesBtn.data('shipment-id');
    const notes = notesBtn.data('notes');

    // Populate the textarea in the modal
    notesTextarea.val(notes);
    $('.notestext').text(notesTextarea.val());

    // Save notes when the Save Notes button is clicked
    saveNotesBtn.off('click').on('click', function () {
      const updatedNotes = notesTextarea.val();
      // Include the CSRF token in the headers
      const csrfToken = $('meta[name="csrf-token"]').attr('content');
      // Make an AJAX request to update the notes
      $.ajax({
        method: 'PUT', // Use the appropriate HTTP method
        url: `/update-notes/${shipmentId}`,
        data: {
          text: updatedNotes,
          id: shipmentId
        },
        headers: {
          'X-CSRF-TOKEN': csrfToken // Include the CSRF token in headers
        },
        success: function (response) {
          // Update the notes in the modal and close it
          $('.notestext').text(updatedNotes);
          notesModal.modal('hide');
        },
        error: function (error) {
          console.error('Error updating notes:', error);
        }
      });
    });

    // Show the notes modal
    notesModal.modal('show');
  });
  // ... Your existing code
  $('.restoreShipmentButton').on('click',function () {
    var numberOfCheckboxesChecked = $('.checkboxes:checked').length;
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
      if (confirm('Are you sure you want to restore selected shipments?')) {
        var restoreRoute = $(this).data('route');
        $.ajax({
          type: 'POST',
          url: restoreRoute,
          data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            ids: ids
          },
          success: function (response) {
            console.log('Restored shipments with IDs: ', ids);
            table.ajax.reload();
            numberOfCheckboxesChecked -= $('.checkboxes:checked').length;
            $("#shipmentcount").text(numberOfCheckboxesChecked);
            // if (numberOfCheckboxesChecked = 0){
            //     $('.deleteShipmentButton').hide();
            // }

            if (numberOfCheckboxesChecked === 0) {
              $('.restoreShipmentButton').hide();

            } else {
              $('.restoreShipmentButton').show();
            }
          },
          error: function (error) {
            console.error('Error restoring shipments:', error);
          }
        });
      }
    }
    toggleButtonVisibility();

  });


  $('.deleteShipmentButton').on('click',function () {
    // var numberOfCheckboxesChecked = $('.checkboxes:checked').length;
    var numberOfCheckboxesChecked = $('.checkboxes:checked').length;

    var ids = []; // Initialize an array to store selected shipment IDs

    table.$('input[type="checkbox"]').each(function () {
      if (this.checked) {
        ids.push(this.value);
      }
    });

    if (ids.length > 0) {
      Swal.fire({
        title: 'Delete Shipment',
        text: 'Are you sure you want to delete selected shipments?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#7d2326',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          var deleteRoute = $(this).data('route');
          var deleted_by = userId; // Assuming userId is defined and holds the user's ID

          // Create a data object to send with the AJAX request
          var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            ids: ids,
            deleted_by: deleted_by
          };

          $.ajax({
            type: 'DELETE',
            url: deleteRoute,
            data: data, // Send the data object

            success: function (response) {
              console.log('Deleted shipments with IDs: ', ids);
              ids = [];
              table.ajax.reload();
              // location.reload(true);

              // Display the updated count of checkboxes after successful deletion
              numberOfCheckboxesChecked -= $('.checkboxes:checked').length;
              $("#shipmentcount").text(numberOfCheckboxesChecked);
              // if (numberOfCheckboxesChecked = 0){
              //     $('.deleteShipmentButton').hide();
              // }

              if (numberOfCheckboxesChecked === 0) {
                $('.deleteShipmentButton').hide();
                $('.createroute').hide();

              } else {
                $('.deleteShipmentButton').show();
                $('.createroute').show();
              }

            },
            error: function (error) {
              console.error('Error deleting shipments:', error);
            }
          });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire(
            'Cancelled',
            'No changes were made.',
            'info'
          );
          table.rows().deselect();
          $(".checkboxes").prop("checked", false);
        }
      });

    }
  });

// 
  /** Shipment Create Route function */
  $('.createroute').on('click',function () {
    var numberOfCheckboxesChecked = $('.checkboxes:checked').length;
    var ids = []; 
    alert(numberOfCheckboxesChecked);
    var selectedWarehouseId = null; 

    table.$('input[type="checkbox"]').each(function () {
      if (this.checked) {
        ids.push(this.value);

        var warehouseId = $(this).data('warehouse-id');

        if (selectedWarehouseId === null) {
          selectedWarehouseId = warehouseId;
        } else if (selectedWarehouseId !== warehouseId) {
          Swal.fire({
            title: 'Error',
            text: 'Please select shipments from the same warehouse.',
            icon: 'error',
          });
          return;
        }
      }
    });

    // table.$('input[type="checkbox"]').each(function () {
    //   if (this.checked) {
    //     ids.push(this.value);
    //   }
    // });
    console.log(ids);
    var createRoute = $(this).data('route');
    var data = {
      _token: $('meta[name="csrf-token"]').attr('content'),
      id: ids,

    };
    var lbs = 0;
    var address = '';
    var time_windows = '';
    var demands = '';
    $.ajax({
      type: 'Get',
      url: createRoute,
      dataType: "json",
      data: data, // Send the data object
      success: function (response) {
        //console.log(response);
        lbs = response.routes;
        address = response.locations;
        time_windows = response.delivery_time;
        demands = response.demands;
        Swal.fire({
          title: 'Confirm Route Creation',
          text: 'The selected ' + numberOfCheckboxesChecked + ' shipments quantity is 600K lb so ' + lbs + ' routes will be created to handle the total capacity.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Back',
        }).then((result) => {
          if (result.isConfirmed) {
            var postdata = {
              address: address,
              time_windows: time_windows,
              demands: demands,
              _token: $('meta[name="csrf-token"]').attr('content'),
            }
            $.ajax({
              type: 'post',
              //url: 'http://localhost:5000/solve_cvrptw',
              url: 'http://18.224.130.138:5000/solve_cvrptw',
              contentType: 'application/json',
              dataType: "json",
              data: JSON.stringify(postdata), // Send the data object
              success: function (response) {
                console.log(response);
              }
            });

            //window.location = maproute;
          } else {
            table.rows().deselect();
            $(".checkboxes").prop("checked", false);
          }
        });

      },
      error: function (error) {
        console.error('Error deleting shipments:', error);
      }
    });

  })

});






function showdata() {
  $('#shipmenttable').DataTable().draw();
}

$(document).on('click', '.close', function () {
  $('#notesModal').modal('hide');
  $('#notesviewModal').modal('hide');
});

/**
 * Handle checkbox selection and update UI elements
 */
function updateCheckboxStatus() {
  const checkedCount = $('.checkboxes:checked').length;
  const totalCheckboxes = $('.checkboxes').length;
  
  // Update counts in UI
  $("#shipmentcount, #shipmentroutecount").text(checkedCount);
  
  // Update "Check All" checkbox state
  $(".checkAll").prop("checked", checkedCount > 0 && checkedCount === totalCheckboxes);
  
  // Update button visibility based on selection
  toggleButtonVisibility();
}

$(document).on("click", ".checkboxes", updateCheckboxStatus);

$(document).ready(function () {
  $("#datepicker-icon").click(function () {
    $('#datepicker').datepicker();
  });
});

/**
 * Toggle visibility of action buttons based on selection and active tab
 */
function toggleButtonVisibility() {
  const checkedCount = $('.checkboxes:checked').length;
  const activeTab = $('.nav-link.active').attr('aria-controls');
  const isCancelledTab = activeTab === 'cancelledshipment';

  // Update restore count for cancelled shipments
  if (isCancelledTab) {
    $("#shipmentrestorecount").text(checkedCount);
  } else {
    $("#shipmentcount").text(checkedCount);
  }

  // Show/hide buttons based on context
  $('.restoreShipmentButton').toggle(isCancelledTab && checkedCount > 0);
  $('.deleteShipmentButton').toggle(!isCancelledTab && checkedCount > 0);
  $('.createroute').toggle(!isCancelledTab && checkedCount > 0);
}

$(document).on("#cancelledshipment-tab").click(toggleButtonVisibility);

// Initialize button visibility
toggleButtonVisibility();