// Constants and Configuration
const CONFIG = {
    DATE_FORMAT: 'mm/dd/yyyy',
    NOTIFICATION: {
        MAX_DISPLAY: 5,
        REFRESH_INTERVAL: 200,
        TIME_UNITS: {
            YEAR: 31536000,
            MONTH: 2592000,
            DAY: 86400,
            HOUR: 3600,
            MINUTE: 60
        }
    },
    MODAL: {
        ANIMATION_DURATION: 300
    }
};

// Utility Class
class Utils {
    static decodeHtmlEntities(html) {
        const textArea = document.createElement('textarea');
        textArea.innerHTML = html;
        return textArea.value;
    }

    static limit(string = '', limit = 0) {
        return string.substring(0, limit);
    }

    static timeSince(date) {
        const seconds = Math.floor((new Date() - new Date(date)) / 1000);
        const units = [
            { unit: 'years', seconds: CONFIG.NOTIFICATION.TIME_UNITS.YEAR },
            { unit: 'months', seconds: CONFIG.NOTIFICATION.TIME_UNITS.MONTH },
            { unit: 'days', seconds: CONFIG.NOTIFICATION.TIME_UNITS.DAY },
            { unit: 'h', seconds: CONFIG.NOTIFICATION.TIME_UNITS.HOUR },
            { unit: 'm', seconds: CONFIG.NOTIFICATION.TIME_UNITS.MINUTE }
        ];

        for (const { unit, seconds: unitSeconds } of units) {
            const interval = seconds / unitSeconds;
            if (interval > 1) {
                return `${Math.floor(interval)}${unit}`;
            }
        }

        return `${Math.floor(seconds)}s`;
    }

    static goBack() {
        window.history.back();
    }
}

// Date Manager Class
class DateManager {
    constructor(selector, options = {}) {
        this.selector = selector;
        this.options = {
            format: CONFIG.DATE_FORMAT,
            autoclose: true,
            todayHighlight: true,
            clearBtn: true,
            ...options
        };
        this.bookedDates = new Set();
    }

    initialize() {
        $(this.selector).datepicker({
            ...this.options,
            beforeShowDay: (date) => this.beforeShowDay(date)
        });
    }

    beforeShowDay(date) {
        const formattedDate = this.formatDate(date);
        if (this.bookedDates.has(formattedDate)) {
            return { classes: 'activeClass' };
        }
    }

    formatDate(date) {
        const d = new Date(date);
        return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
    }

    setBookedDates(dates) {
        this.bookedDates = new Set(dates);
    }
}

// Search Manager Class
class SearchManager {
    constructor(selector) {
        this.selector = selector;
        this.searchTimeout = null;
        this.setupEventListeners();
    }

    setupEventListeners() {
        $(this.selector).on('keyup', this.handleSearch.bind(this));
    }

    handleSearch(event) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performSearch($(event.target).val());
        }, 300);
    }

    async performSearch(query) {
        try {
            const response = await $.ajax({
                url: search,
                type: 'get',
                dataType: 'json',
                data: { search: query }
            });

            this.updateSearchResults(response);
        } catch (error) {
            console.error('Search failed:', error);
            this.showError();
        }
    }

    updateSearchResults(data) {
        const $searchList = $('#search_list');
        const $searchListUl = $('#search_list ul');
        
        $searchList.show();
        $searchListUl.empty();

        let dataFound = false;

        Object.entries(data.common).forEach(([key, value]) => {
            if (value.values.length > 0) {
                dataFound = true;
                this.appendSearchCategory(value.label, value.values, key);
            }
        });

        if (!dataFound) {
            $searchListUl.append('<li>No Data Found</li>');
        }
    }

    appendSearchCategory(label, values, type) {
        const $searchListUl = $('#search_list ul');
        $searchListUl.append(`<h6><b>${label}</b></h6>`);

        values.forEach(item => {
            const listItem = this.createSearchResultItem(type, item);
            $searchListUl.append(listItem);
        });
    }

    createSearchResultItem(type, item) {
        const urlMap = {
            shipment: `${shipment_view}/${item.id}`,
            customer: `${customer_view}/${item.id}`,
            routes: `${app_url}/routes/${item.route_url}`
        };

        if (!urlMap[type]) return '<li>No Data Found</li>';

        if (type === 'routes') {
            return `
                <li class='d-flex justify-content-between'>
                    <a href="${urlMap[type]}">${item.name}</a>
                    <span class="float-right btn-status btn-sm btn-${item.status} btn-routes collapsed text-capitalize">
                        ${item.status}
                    </span>
                </li>`;
        }

        return `<li><a href="${urlMap[type]}">${item.name}</a></li>`;
    }

    showError() {
        $('#search_list ul').empty().append('<li>Error performing search</li>');
    }
}

// Notification Manager Class
class NotificationManager {
    constructor() {
        this.count = 0;
        this.notifications = [];
        this.setupWebSocket();
    }

    setupWebSocket() {
        setTimeout(() => {
            window.Echo.channel('shipment')
                .listen('ShipmentEvent', this.handleShipmentEvent.bind(this));
        }, CONFIG.NOTIFICATION.REFRESH_INTERVAL);
    }

    async handleShipmentEvent(event) {
        console.log(event.message);
        const routeId = event.routeId;
        
        await this.reloadRouteLi(routeId);
        this.handleRouteCheckbox(routeId);
        this.showNotification(event.message);
    }

    handleRouteCheckbox(routeId) {
        const checkbox = $(`#checkbox_route_${routeId}`);
        if (checkbox.prop('checked')) {
            setTimeout(() => {
                checkbox.trigger('click').trigger('click');
            }, 1000);
        }
    }

    showNotification(message) {
        Swal.fire({
            title: "Shipment Notification",
            html: message,
            icon: "warning"
        });
    }

    async fetchNotifications() {
        try {
            const response = await $.ajax({
                url: notificationurl,
                method: 'GET',
                dataType: 'json'
            });

            this.updateNotificationCount(response.notificationcount);
            this.updateNotificationList(response.notifications);
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
        }
    }

    updateNotificationCount(count) {
        this.count = count;
        $("#badge").html(count);
    }

    updateNotificationList(notifications) {
        this.notifications = notifications;
        const $notificationList = $("#notificationlist");
        
        if (notifications.length > 0) {
            const notificationHtml = notifications
                .slice(0, CONFIG.NOTIFICATION.MAX_DISPLAY)
                .map(notification => this.createNotificationItem(notification))
                .join('');

            $notificationList.html(`
                ${notificationHtml}
                <li><p class="notification-msg"><a href="/notification">view all</a></p></li>
            `);
        } else {
            $notificationList.html('<li>New Notification not found</li>');
        }
    }

    createNotificationItem(notification) {
        return `
            <li class="b-l-primary border-4">
                <p class="notification-msg">
                    <a href="${notification.route_url}">${notification.notification}</a>
                    <span class="font-danger">${Utils.timeSince(notification.created_at)} ago</span>
                </p>
            </li>`;
    }

    async reloadRouteLi(routeId, routeData = null) {
        // Implementation for reloading route list item
    }
}

// Modal Manager Class
class ModalManager {
    static async openWithData(modalId, dataUrl) {
        try {
            const response = await $.ajax({
                url: dataUrl,
                method: 'GET'
            });

            $(`${modalId} .modal-body`).html(response);
            $(modalId).modal('show');
        } catch (error) {
            console.error('Failed to load modal data:', error);
            // Implement error handling UI
        }
    }

    static async openProofModal(modalId, dataUrl) {
        // Implementation for proof modal
    }

    static async openDeliveryInstructionsModal(modalId, dataUrl) {
        // Implementation for delivery instructions modal
    }

    static async openFhfNotesModal(modalId, dataUrl) {
        // Implementation for FHF notes modal
    }

    static close(modalId) {
        $(modalId).modal('hide');
    }

    static open(modalId) {
        $(modalId).modal('show');
    }
}

// Initialize Application
$(document).ready(() => {
    // Initialize date picker
    const dateManager = new DateManager('#datepicker');
    dateManager.initialize();

    // Initialize search
    const searchManager = new SearchManager('#auto');

    // Initialize notifications
    const notificationManager = new NotificationManager();
    notificationManager.fetchNotifications();

    // Export global functions (for backward compatibility)
    window.openModalWithData = ModalManager.openWithData;
    window.openProofModalWithData = ModalManager.openProofModal;
    window.openDeliveryInstructionsModalWithData = ModalManager.openDeliveryInstructionsModal;
    window.openFhfNotesModalWithData = ModalManager.openFhfNotesModal;
    window.closeModal = ModalManager.close;
    window.openModal = ModalManager.open;
    window.goBack = Utils.goBack;
    window.decodeHtmlEntities = Utils.decodeHtmlEntities;
    window.limit = Utils.limit;
});

$(document).ready(function () {
    
        // List of Items   
      
    var booked_date = new Date();
    $("#datepicker").datepicker({
        format: "mm/dd/yyyy",
        autoclose: true,
        todayHighlight: true,
        beforeShowDay: function (date) {
            var d = date;
            var curr_date = d.getDate();
            var curr_month = d.getMonth() + 1;
            var curr_year = d.getFullYear();
            var formattedDate = curr_date + "/" + curr_month + "/" + curr_year

            if ($.inArray(formattedDate, booked_date) != -1) {
                return {
                    classes: 'activeClass'
                };
            }
            return;
        },
        clearBtn: true
    });
        // jQuery inbuild function. 
        $("#auto").keyup(function() {
            $('#search_list ul').empty();
            // This function takes words list as a source. 
           // source: items
          
                // Fetch data
                $.ajax({
                     url:search,
                     type: 'get',
                     dataType: "json",
                     data: {
                          search: $(this).val()
                     },
                     success: function( data ) {
                        $('#search_list').show();
                        $('#search_list ul').empty();
                        //$('#recent_search').hide();
                        var i = 0;
                        var dataFound=false;
                        $.each(data.common, function(key, value) {
                            if (value.values.length > 0) {
                                //console.log("user result="+ value.label);
                                $('#search_list ul').append('<h6><b>' + value.label + '</b></h6>');
                                $.each(value.values, function(keyy, value_data) {
                                    // console.log("{{url('/')}}/faqs");
                                    switch (key) {
                                        case "shipment":
                                            $('#search_list ul').append(`<li><a href="`+shipment_view+'/'+ value_data.id + '">' + value_data.name + '</a></li>');
                                            break;
                                        case "customer":
                                            $('#search_list ul').append(`<li><a href="`+customer_view+'/'+ value_data.id + '">' + value_data.name + '</a></li>');
                                            break;
                                        case "routes" :
                                            $('#search_list ul').append(`<li class='d-flex justify-content-between'><a href="` + app_url + '/routes/' + value_data.route_url + '">' + value_data.name + '</a><span class="float-right btn-status btn-sm btn-' + value_data.status +' btn-routes collapsed text-capitalize">'+ value_data.status+'</span></li>');
                                            break;
                                       default:
                                            $('#search_list ul').append('<li>No Data Found</li>');
                                    }
                                })
                                dataFound=true;
                            }else{
                                
                               // $('#search_list ul').append('<li>No Data Found</li>');
                            }
                        })
                        if(dataFound==false){
                            $('#search_list ul').append('<li>No Data Found</li>');
                        }
                     }
                });
           
        }); 

    NotificationCount();
    setTimeout(() => {
        window.Echo.channel('shipment')
        .listen('ShipmentEvent', (e) => {
                console.log(e.message);
                let routeId=e.routeId;
                reloadrouteLi(routeId);
                if ($('#checkbox_route_'+routeId).prop('checked')) {    
                    setTimeout(function(){
                    $('#checkbox_route_'+routeId).trigger('click');
                    $('#checkbox_route_'+routeId).trigger('click');
                    }, 1000);
                }
                
                //alert(e.message);
                Swal.fire({
                    title: "Shipment Notification.",
                    html: e.message,
                    icon: "warning",
                });
                //document.getElementById('latest_trade_user').innerText = e.trade;
            })
    }, 200);
    
  
    $('#datepicker').datepicker({
        format: 'mm/dd/yyyy',
        clearBtn: true
    });
});

// Notification count
function NotificationCount(){
    $.ajax({
        url:notificationurl,
        method: 'GET',
        dataType: 'json',
        success: function(response){
           
            $("#badge").html(response.notificationcount);
            notificationList(response.notifications);
        }
    })
}
/** Notification List */
function notificationList(data){
    var html = '';
    if(data.length >0){
       
    $.each(data, function(index, list){
        if(index <= 4){
            
            html = html+'<li class="b-l-primary border-4">\
            <p class="notification-msg" ><a href="'+list.route_url+'">'+list.notification +'. </a> <span class="font-danger">'+timeSince(new Date(list.created_at))+' ago</span></p></li>';
        }
     
        });
        html = html+'<li><p class="notification-msg" ><a  href="/notification">view all</a></p></li>'
        $("#notificationlist").html(html)
    }else{
        html = '<li> New Notification not found </li>'
        $("#notificationlist").html(html); 
    }
    
}
/** Time slice function */
function timeSince(date) {

    var seconds = Math.floor((new Date() - date) / 1000);
  
    var interval = seconds / 31536000;
  
    if (interval > 1) {
      return Math.floor(interval) + " years";
    }
    interval = seconds / 2592000;
    if (interval > 1) {
      return Math.floor(interval) + " months";
    }
    interval = seconds / 86400;
    if (interval > 1) {
      return Math.floor(interval) + " days";
    }
    interval = seconds / 3600;
    if (interval > 1) {
      return Math.floor(interval) + "h";
    }
    interval = seconds / 60;
    if (interval > 1) {
      return Math.floor(interval) + "m";
    }
    return Math.floor(seconds) + "s";
  }
  /**
 * Returns the first `limit` characters from the given `string`.
 *
 * @param {String} string
 * @param {Number} limit
 *
 * @returns {String}
 */
function limit (string = '', limit = 0) {  
    return string.substring(0, limit)
  }
// modal.js
function openModalWithData(modalId, dataUrl) {
    $.ajax({
        url: dataUrl,
        method: 'GET',
        success: function (response) {
            $(modalId + ' .modal-body').html(response);
            $(modalId).modal('show');
        },
        error: function () {
            // Handle error
        }
    });
}
// function to fetch proof of delivery
function openProofModalWithData(modalId, dataUrl) {
    $.ajax({
        url: dataUrl,
        method: 'GET',
        success: function (response) {
            let imagesHtml = '<h6>Images:</h6><br><div class="row">';
            let signaturesHtml = '<br><h6>Signatures:</h6><br><div class="row">';

            if (response.proofs) {
                var no = 1; 
                response.proofs.forEach(function (proof) {
                    if (proof.image && proof.image.length > 0) {
                        proof.image.forEach(function (image) {
                            var imageId = 'image_' + no;

                            imagesHtml += '<div class="col-md-4">';
                            imagesHtml += '<div class="attach-files">';
                            imagesHtml += '<a href="' + image + '" data-lightbox="images" target="_blank" data-title="Image ' + no + '">';
                            imagesHtml += '<img src="' + image + '" alt="Image ' + no + '" id="' + imageId + '" class="img-thumbnail">';
                            imagesHtml += '</a>';
                            imagesHtml += '<i class="fas fa-paperclip me-2"></i> <a href="' + image + '" target="_blank">';
                            imagesHtml += 'Image ' + no++;
                            imagesHtml += '</a>';
                            imagesHtml += '</div>';
                            imagesHtml += '</div>';
                        });
                    } else {
                        imagesHtml += '<div class="col-md-4">';
                        imagesHtml += '<div class="attach-files">';
                        imagesHtml += '<p>No images found</p>';
                        imagesHtml += '</div></div>';
                    }

                    if (proof.signature && proof.signature.length > 0) {
                        proof.signature.forEach(function (signature) {
                            signaturesHtml += '<div class="col-md-4"><div class="attach-files">';
                            signaturesHtml += '<a href="' + signature + '" data-lightbox="signature-images" target="_blank">';
                            signaturesHtml += '<img src="' + signature + '" alt="Signature Image" class="signature-image" width="100" height="50">';
                            signaturesHtml += '</a>';

                            signaturesHtml += '</div></div>';
                        });
                    } else {
                        signaturesHtml += '<div class="col-md-4"><div class="attach-files">';
                        signaturesHtml += '<p>No signatures found</p>';
                        signaturesHtml += '</div></div>';
                    }
                });
            }
            imagesHtml += '</div>';
            signaturesHtml += '</div>';

            let uniqueNotesSet = new Set(response.notes);
            let uniqueNotesArray = [...uniqueNotesSet];

            let notesHtml = '<br><h6>Notes By Driver:</h6><br><div class="col-md-12">';
            uniqueNotesArray.forEach(function (note) {
                notesHtml += '<textarea class="form-control">' + note + '</textarea>';
            });
            notesHtml += '</div>';

            let modalContent = '<div class="container">' + imagesHtml + signaturesHtml + notesHtml + '</div>';
            $(modalId + ' .modal-body').html(modalContent);
            $(modalId).modal('show');
        },
        error: function () {
            // Handle error
        }
    });
}

// High light current date in datepicker calender

function openDeliveryInstructionsModalWithData(modalId, dataUrl) {
    $.ajax({
        url: dataUrl,
        method: 'GET',
        success: function (response) {
            console.log('Response:', response);
            

            let modalContent = '<div class="container">';
            if (response && response.Data) {
                modalContent += '<p>Delivery Instruction: ' + response.Data + '</p>';
            } else {
                modalContent += '<p>No data found</p>';
            }
            modalContent += '</div>';

            $(modalId + ' .modal-body').html(modalContent);
            $('#instructionContent').html(modalContent);
            $('#instructionModal').modal('show');
            // $(modalId).modal('show');
        },
        error: function () {
            console.log(error);
        }
    });
}

function openFhfNotesModalWithData(modalId, dataUrl) {
    $.ajax({
        url: dataUrl,
        method: 'GET',
        success: function (response) {
            let modalContent = '<table id="fhfNotesTable" class="display" style="width:100%"><thead><tr><th>FHF Notes</th><th>Location</th><th>Latitude</th><th>Longitude</th><th>Added By</th><th>Created On</th></tr></thead><tbody>';

            if (response && response.data && response.data.fhf_notes && response.data.fhf_notes.length > 0) {
                response.data.fhf_notes.forEach(function (note) {
                    console.log(note.added_by_user.name);
                modalContent += '<tr>';
                modalContent += '<td>' + note.fhf_notes + '</td>';
                modalContent += '<td>' + note.address + '</td>';
                modalContent += '<td>' + (note.latitude || '') + '</td>';
                modalContent += '<td>' + (note.longitude || '') + '</td>';
                modalContent += '<td>' + (note.added_by_user.name || '') + '</td>';
                    let createdAt = new Date(note.created_at);
                    let formattedCreatedAt = createdAt.toLocaleString();
                    modalContent += '<td>' + formattedCreatedAt + '</td>'; 

                    modalContent += '</tr>';
                });

                modalContent += '</tbody></table>';
            } else {
                modalContent = '<p>No FHF notes found</p>';
            }

            $(modalId + ' .modal-body').html(modalContent);

            // Initialize DataTable after the table is rendered
            setTimeout(function () {
                $('#fhfNotesTable').DataTable({
                    // processing: true,                 
                    // lengthMenu: [5, 10, 25, 50, 100],
                    // sorting: true,
                    processing: true,
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                    },
                    // serverSide: true,
                    lengthMenu: [5, 10, 25, 50, 100],
                    destroy: true,
            
                });

            }, 0);

            $(modalId).modal('show');
        },
        error: function () {
            console.log('Error occurred while fetching data');
        }
    });
}



$(document).on('click', '.view-fhf-notes-link', function () {
    var shipmentId = $(this).parent().data('shipment-id');
    openFhfNotesModalWithData('#fhfNotesModal', '/fhf-notes/' + shipmentId);
});



function closeModal(modalId) {
    $(modalId).modal('hide');
}

function openModal(modalId) {
    $(modalId).modal('show');
}
$(document).on('click', '.close, .hide-deliveryproof', function () {
    $('#deliveryproofModal').hide();
});
$(document).on('click', '.close, .hide-instruction', function () {
    $('#instructionModal').modal('hide');
});

$(document).ready(function () {
    $("#successMessage").delay(4000).slideUp(300);
    $("#errorMessage").delay(4000).slideUp(300);
});

// $(document).on('submit','form.submit-loading',function(){
//     $('button').attr('disabled', 'disabled');
//     $(".spinner-btn").removeClass("d-none");
//     $(".btn-txt").text("Processing ...");
//  });


// function to send email invitation link to user


//  $(document).ready(function() {
//     $.ajaxSetup({
//       headers: {
//         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//       }
//     });
$(".userinvitation").click(function () {
    $('.userinvitation').attr('disabled', 'disabled');
    $(".spinner-btn").removeClass("d-none");
    let carrierOwnerId = $("#carrierOwnerId").val();
    var url = "/invite-user" + '/' + carrierOwnerId;

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data.status == "success") {
                $("#successMessage").html(data.message).show();
                setTimeout(function () {
                    $("#successMessage").fadeOut();
                }, 10000);
            } else {
                $("#errorMessage").html(data.message).show();
                setTimeout(function () {
                    $("#errorMessage").fadeOut();
                }, 10000);
            }

            $('.userinvitation').removeAttr('disabled');
            $(".spinner-btn").addClass("d-none");
            //console.log(response);
        },
        error: function (error) {
            $("#errorMessage").html(error.message).show();
            setTimeout(function () {
                $("#errorMessage").fadeOut();
            }, 10000);
            $('.userinvitation').removeAttr('disabled');
            $(".spinner-btn").addClass("d-none");
        }
    });
});

//});


function decodeHtmlEntities(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
$(document).on('click', '.view-shipment-link', function () {
    var shipmentId = $(this).data('shipment-id');
    openModalWithData('#inventoryDetailModal', '/fetch-inventory-detail/' + shipmentId);
});
$(document).on('click', '.view-delivery-proof-link', function () {
    var shipmentId = $(this).data('shipment-id');
    openProofModalWithData('#deliveryproofModal', '/fetch-delivery-proof-detail/' + shipmentId);
});

$(document).on('click', '.view-delivery-instruction-link', function () {
    var routeId = $(this).parent().data('routeId');
    openDeliveryInstructionsModalWithData('#instructionModal', '/fetch-delivery-instruction/' + routeId);
});
$(document).on('click', '.view-fhf-notes-link', function () {
    var shipmentId = $(this).parent().data('shipment-id');
    openFhfNotesModalWithData('#fhfNotesModal', '/fhf-notes/' + shipmentId);
});



$(document).on('click', '.hide-inventory-log .close', function () {
    closeModal('#inventoryDetailModal');
});

$(document).on('click', '.close', function () {
    closeModal('#inventoryDetailModal');
});
function goBack() {
    window.history.back();
}
$(document).on('click', '.view-notes-link', function () {
    var notes = $(this).data('notes');
    $('#notesContent').html(notes);
    $('#notesviewModal').modal('show');
});
$(document).on('click', '.close', function () {
    $('#notesModal').modal('hide');
    $('#notesviewModal').modal('hide');
});
$(".send-otp").on('click', function () {
    $('.userinvitation').attr('disabled', 'disabled');
    $(".spinner-btn").removeClass("d-none");
})



// function reload li for route shipmet stops update
function reloadrouteLi(route_id, routedata=null) {
    //var url = "/getRouteData" + '/' + route_id;
    // alert('here');
    var url = "/getRouteData" + '/' + route_id;
    $.ajax({
        url: url,
        method: 'GET',
        contentType: 'application/json',
        dataType: "json",
        success: function (response) {
            let routedata=response.data;
            let last_dropoff_location = routedata.last_dropoff_location != null ? routedata.last_dropoff_location : "N/A";
            var delivery_date = new Date(routedata.delivery_date);
            // Format the date to the desired format
            let options = { month: 'short', day: 'numeric', year: 'numeric' };
            delivery_date = delivery_date.toLocaleDateString('en-US', options);
            var duration = convertMinutesToHoursMinutes(routedata.loaded_duration);
            var planned_departure_time = new Date(routedata.planned_departure_time);
            options = { hour: 'numeric', minute: 'numeric', hour12: true };
            planned_departure_time = planned_departure_time.toLocaleTimeString('en-US', options);

            var updated_at = new Date(routedata.updated_at); // Replace this with your actual Date object
            options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };
            updated_at = updated_at.toLocaleString('en-US', options);

            var loaded_miles = (routedata.loaded_distance * 0.00062137).toFixed(2);
            var item_address = routedata.item_address;
            var deliver_stops = routedata.deliver_stops;
            var stopavg = routedata.stopsavg;
            item_address = JSON.stringify(item_address);
            let shipmentName = JSON.stringify(routedata.name);
            let props_weight = JSON.stringify(routedata.props_weight);
            let shipmentDuration = JSON.stringify(routedata.duration);
            let prt = JSON.stringify(routedata.prt);
            let fork_lift_use = JSON.stringify(routedata.fork_lift);
            let shipment_ids = JSON.stringify(routedata.shipment_ids);


            //console.log("item_address",item_address)
            var forklift ='';
        // $.each(routedata.fork_lift,function (key, val){
                if(routedata.fork_lift == 1){
                    forklift = '<img width="15" class="img-fluid" src="../assets/images/routes/loader-machine.svg">';
                }
            //});
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var newListItems = [
                '<li class="text-success"><img width="15" class="img-fluid" src="../assets/images/routes/thumb-color.svg" /> $ ' + routedata.total_price + '</li>',
                '<li><img width="15" class="img-fluid" src="../assets/images/routes/meter.svg" /> ' + loaded_miles + ' miles</li>',
                '<li><img width="15" class="img-fluid" src="../assets/images/routes/location.svg" /> ' + routedata.total_stops + ' stops</li>',
                '<li><img width="15" class="img-fluid" src="../assets/images/routes/weight-scale.svg" /> P:' + routedata.P_quantity + ' R:' + routedata.R_quantity + 'T:' + routedata.T_quantity + ' ('+routedata.props_weight_total+' lbs)</li>',
                ' <li><img width="15" class="img-fluid" src="../assets/images/routes/calendar.svg" /> ' + delivery_date + '</li>',
                '<li><img width="15" class="img-fluid" src="../assets/images/routes/clock.svg" /> ' + duration + '</li>',
                '<li><img width="15" class="img-fluid" src="../assets/images/routes/alarm.svg" /> PDT ' + planned_departure_time + '</li>',
                '<li><form action="' + downloadRoutes + '" method="POST" style="'+ (routedata.lock_status == 0 ? 'display:none;' : 'display:inline;')+'" id="download-route-form' + routedata.id + '"><input type="hidden" name="_token" value="' + csrfToken + '"><input type="hidden" name="download_routes"  value="'+routedata.id+'"><button type="submit" class="" style="width:10px;height: 10px; border:none; background:none; color:#80252c; padding:5px;"><i class="fa fa-download"></i></button></form></li>',
            ];
            var truckhtml = '<div class="text-start"><span class="location-label"><img width="15" class="img-fluid" src="../assets/images/routes/loaction-color.svg">Origin</span>\
            <span class="location-detail"> '+ routedata.origin_points + '</span></div>\
            <div class="text-end"><span class="location-label"><img width="15" class="img-fluid" src="../assets/images/routes/loaction-color.svg">Last Drop-off Location</span>\
            <span class="location-detail"> '+ last_dropoff_location + '</span></div>';

            var routerange = '<div class="progress-showcase progress-b-space"><div class="row">\
            <div class="col">\
                <div class="progress sm-progress-bar overflow-visible my-1">\
                <div class="progress-bar-custom small-progressbar rounded-pill" role="progressbar" style="width:'+stopavg+'%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">\
                    <span class="animate-circle"></span>\
                </div>\
                </div>\
            <div class="d-flex justify-content-between">\
            <span class="progress-label">'+deliver_stops+' of '+routedata.total_stops+'</span>\
            <span class="progress-label">Last Updated :- '+updated_at+'</span></div></div></div></div>';

            var routeLock = '<div class="toggle-switch" title="Lock Status">\
            <input type="checkbox" id="toggle-switch-' + routedata.id + '" name="lock_route" data-id="' + routedata.id + '" data-unlock="'+authUser.unlock_route+'" value="' + (routedata.lock_status == 1 ? 0 : 1) + '" class="hidden" ' + (routedata.lock_status == 1 ? 'checked' : '') + '>\
            <label for="toggle-switch-' + routedata.id + '" class="toggle-label">\
            <div class="icons">\
            <div class="lock-icon">\
            <i class="fa ' + (routedata.lock_status == 1 ? 'fa-unlock' : 'fa-unlock') + '"></i>\
            </div>\
            <div class="unlock-icon">\
            <i class="fa ' + (routedata.lock_status == 1 ? 'fa-lock' : 'fa-lock') + '"></i>\
            </div>\
            </div>\
            </label>\
            </div>';
            // Clear the existing list
            $('#route-detail'+route_id).empty();
            $('#location'+route_id).empty();
            $('#range'+route_id).empty();
            $('#forklift-'+route_id).empty();
            //  Append the new list items
            $('#forklift-'+route_id).html(forklift);
            $('#route-detail'+route_id).html(newListItems);
            $('#range'+route_id).html(routerange);
            $('#toggle-switch-'+route_id).closest('.toggle-switch').html(routeLock);
            $("#checkbox_route_"+route_id).attr("data-items", item_address);

            $("#checkbox_route_"+route_id).attr("data-name", shipmentName);
            $("#checkbox_route_"+route_id).attr("data-props_weight", props_weight);
            $("#checkbox_route_"+route_id).attr("data-duration", shipmentDuration);
            $("#checkbox_route_"+route_id).attr("data-prt", prt);
            $("#checkbox_route_"+route_id).attr("data-fork_lift", fork_lift_use);
            $("#checkbox_route_"+route_id).attr("data-ids", shipment_ids);

            $('#location'+route_id).html(truckhtml);
            $('#range'+route_id).html(routerange);
            $('#btn-route-status'+route_id).text(routedata.status);
            $('#smallRecommendedPrice'+route_id).html('<img width="15" class="img-fluid" src="../assets/images/routes/thumb-color.svg"> ' + routedata.recommended_price);
            //$('#btn-route-status'+route_id).addClass("btn-"+routedata.status);
            
            $('#btn-route-status'+route_id).removeClass().addClass('btn-status btn-sm btn-routes text-capitalize btn-'+routedata.status);

            if(routedata.driver && routedata.driver.name)
            {
                $('#driver-name'+route_id).val(routedata.driver.name);
                $('#anchorChangeDriver' + route_id).attr('title', 'Change driver');
                $('#anchorChangeDriver' + route_id).html("<i class='fa fa-pencil'></i>")
            } else {
                $('#driver-name'+route_id).val("Not Assigned");
                $('#anchorChangeDriver'+ route_id).attr('title', 'Assign driver');
                $('#anchorChangeDriver' + route_id).html("<i class='fa fa-plus'></i>")
            }
            if (routedata.driver && (routedata.driver.role_id == 3 || routedata.driver.role_id == 4)) {
                $('#recommendedPrice' + route_id).show();
            } else {
                $('#recommendedPrice' + route_id).hide();
            }
        },
        error: function () {
            console.log('Error occurred while fetching data');
        }
    });

      //driver-name
   }
   