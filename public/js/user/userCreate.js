
// Change form according to add driver
$(document).on('change', '#role_id', function () {
  var select2Element = $('#store_locations').select2();
  var selectedValue = $(this).val();
    $('#merchandisersForm').hide();
    $('#merchandisersLocation').hide();
    $('#dispatcherForm').hide();
    $('#routerForm').hide();
    $('#driverForm').hide();
    if (selectedValue == 2) {
      $('#driverForm').show();
    } else if (selectedValue == 5 || selectedValue == 6 || selectedValue == 7) {
      $('#merchandiserForm').show();
      if (selectedValue == 5) {
        $('#merchandisersForm').show();
        $('#merchandisersLocation').show();
      } else if (selectedValue == 6) {
        $('#dispatcherForm').show();
      } else if (selectedValue == 7) {
        $('#routerForm').show();
      } 
  
    }
  showWarehouseDropdown();

});

function goBack() {
  window.history.back();
}
var loaderRoleId = 8; 
function showWarehouseDropdown() {
  var roleSelect = document.getElementById('role_id');
  var warehouseDropdown = document.getElementById('warehouseDropdown');

  if (roleSelect.value == loaderRoleId.toString()) {
    warehouseDropdown.style.display = 'block';
  } else {
    warehouseDropdown.style.display = 'none';
  }
}

