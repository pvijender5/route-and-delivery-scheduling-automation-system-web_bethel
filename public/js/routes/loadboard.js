$(document).on('click', '.btn-post-route', function () {
  // Check the button label text for the word "selected"
  const buttonLabel = $('.btn-post-route').text();
  var routeIds = $(this).data('routeids');
  if (routeIds.length > 0) {
    // If the button label contains "selected", show the modal
    showpostRouteSelectionModal(routeIds);
  } else {
    // If not, show SweetAlert for route selection
    showRouteSelectionAlert();
  }

});

// widraw selected route
$(document).on('click', '.btn-withdraw-route', function () {

  


  // Check the button label text for the word "selected"
  const buttonLabel = $('.btn-withdraw-route').text();
  var routeIds = $(this).data('routeids');
  if (routeIds.length > 0) {

    checkRoutesLoadboardData(routeIds);
  } else {
    // If not, show SweetAlert for route selection
    showRouteSelectionAlert();
  }

});


// Function to show SweetAlert message for route selection
function showRouteSelectionAlert() {
  Swal.fire(
    'Error',
    'Please select a route.',
    'error'
  );
}

// Function to show modal for route selection
function showpostRouteSelectionModal(routeIds) {
  // Make an AJAX request to fetch data
  $.ajax({
    url: '/get-routes-data', // Replace with the actual route to your controller method
    type: 'POST',
    data: {
      routeIds: routeIds
    },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (response) {
      // Clear the existing table data
      $('#loadersTable tbody').empty();
      $.each(response, function (index, route) {
        var newRow = `
        <tr>
            <td>${index + 1}</td>
            <td>${route.id}</td>
            <td>${route.delivery_date}</td>
            <td>${route.PDT_type}</td>
            <td>${route.total_stops}</td>
            <td>${(route.loaded_distance*0.00062137).toFixed(2)}</td>
            <td>${route.fork_lift === 1 ? 'Yes' : 'No'}</td>
            <td><b>$${route.recommended_price}</b></td>
            <td>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkbox_animated loadingByCheckBox${route.id}" type="checkbox" name="loadingType_${index}" data-route-id="${route.id}" id="loader_${index}" ${route.loading_by_loader === 'loader' ? 'checked' : ''} />
                    <label class="form-check-label" for="loader_${index}">Loader</label>
                </div>
                <div class="form-check form-check-inline">
                    <input data-route-id="${route.id}" class="form-check-input checkbox_animated loadingByCheckBox${route.id}" type="checkbox" name="loadingType_${index}" id="driver_${index}" ${route.loading_by_driver === 'driver' ? 'checked' : ''} />
                    <label class="form-check-label" for="driver_${index}">Driver</label>
                </div>
            </td>
            <td>
                <div class="edit-price">
                    <input class="form-control" type="text" value="${route.total_price}" />
                </div>
            </td>
        </tr>`;
         /* <td>
                <div class="form-check form-switch form-check-inline">
                    <input class="form-check-input switch-success check-size" type="checkbox" role="switch" checked />
                </div>
            </td>*/
        $('#loadersTable tbody').append(newRow);
        openModal(postRoute);
      });


    },
    error: function (xhr, status, error) {
      var errorMessage = xhr.responseJSON.message;
      Swal.fire({
          title: "Warning",
          html: errorMessage,
          icon: "warning",
      });
      console.error(error);
    }
  });

 
}


// Function to show modal for route selection
function checkRoutesLoadboardData(routeIds){
  // Make an AJAX request to fetch data
  $.ajax({
    url: '/checkRoutesLoadboardData', // Replace with the actual route to your controller method
    type: 'POST',
    data: {
      routeIds: routeIds
    },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (response) {

      Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to withdraw the routes.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, withdraw it!',
        cancelButtonText: 'No, keep it',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#7d2326',
      }).then((result) => {
        if (result.isConfirmed) {
                $.ajax({
                  url: '/withdraw-loadboard-routes', // Replace this with your actual update route endpoint
                  type: 'POST',
                  data: {
                    routeIds: routeIds
                  },
                  success: function (response) {
                    Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: response.message, // Assuming the response contains a 'message' property
                    });
                  
                    //window.location.reload();
                    $.each(routeIds, function(i, routeId) {
                      reloadrouteLi(routeId);
                      
                    })  
                  },
                  error: function (error) {
                    // Handle the error response from the server if needed
                    console.error('Error occurred while sending data to the server:', error);
                    // Show SweetAlert error message
                    Swal.fire({
                      icon: 'error',
                      title: 'Error!',
                      text: 'Error occurred while sending data to the server',
                    });
                  }
              });
        }else if(result.dismiss === Swal.DismissReason.cancel) {
          // If the user clicks "No, keep it"
          Swal.fire(
            'Cancelled',
            'Route withdraw cancelled',
            'error'
          );
        }
      });
    },  
    error: function (xhr, status, error) {
      var errorMessage = xhr.responseJSON.message;
      Swal.fire({
          title: "Warning",
          html: errorMessage,
          icon: "warning",
      });
      console.error(error);
    }
  });

 
}



$(document).on('click', '.post-route-close', function () {
  closeModal(postRoute);
});



$(document).on('click', '.checkbox_animated', function () {
  var routeId = $(this).data('route-id');
  $('.loadingByCheckBox'+routeId).not(this).prop('checked', false);
});



$(document).on('click', '.save-postloadboard-button', function () {
  // Create an array to store data for all rows
  var rowData = [];
  var uniqueRouteIds = [];
  // Loop through each row in the table
  $('#loadersTable tbody tr').each(function (index, row) {
    // Get the values from the row
    var routeId = $(row).find('td:eq(1)').text(); // Assuming the route ID is in the second column
    var loaderChecked = $(row).find('input[id^="loader"]:checked').length > 0; // Check if loader is checked
    var driverChecked = $(row).find('input[id^="driver"]:checked').length > 0; // Check if driver is checked
    var totalPrice = $(row).find('input[type="text"]').val(); // Get the total price input value
    var switchChecked = $(row).find('input[type="checkbox"]').is(':checked'); // Check if the switch is checked

    if (!uniqueRouteIds[routeId]) {
      // Create an object for the current row's data
      var rowDataItem = {
        routeId: routeId,
        loaderChecked: loaderChecked,
        driverChecked: driverChecked,
        totalPrice: totalPrice,
        switchChecked: switchChecked
      };

      // Add the row data to the array
      rowData.push(rowDataItem);
      // Mark this routeId as added
      uniqueRouteIds[routeId] = true;
    } 
  });

  // Prepare the data to send via AJAX
  var data = {
    rows: rowData
  };

  $.ajax({
    url: '/update-loadboard-routes', // Replace this with your actual update route endpoint
    type: 'POST',
    data: data,
    success: function (response) {
      // Handle the success response from the server if needed
      console.log('Data successfully sent to the server:', response);
      // Show SweetAlert success message
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: response.message, // Assuming the response contains a 'message' property
      });
      closeModal(postRoute);
      //window.location.reload();
      $.each(rowData, function(i, route) {
        reloadrouteLi(route.routeId);
        //$("#anchorChangeDriver" + route.routeId).data('driverid', '');
        var inputElement = document.getElementById("anchorChangeDriver" + route.routeId);
        inputElement.removeAttribute("data-driverid");
      })  
    },
    error: function (error) {
      // Handle the error response from the server if needed
      console.error('Error occurred while sending data to the server:', error);
      // Show SweetAlert error message
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Error occurred while sending data to the server',
      });
    }
  });

});


