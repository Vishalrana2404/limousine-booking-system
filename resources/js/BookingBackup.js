import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import $ from "jquery";
import "./JqueryCheckBox";
import "daterangepicker";
import axios from "axios";

/**
 * Represents the Bookings class.
 * @extends BaseClass
 */
export default class Bookings extends BaseClass {
    /**
     * Constructor for the Bookings class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        this.previousCell = null;
        this.handleOnLoad();

        $(document).on("click", "#addGuest", this.handleAddGuest);
        $(document).on("click", "#addStop", this.handleAddStop);
        $(document).on("click", ".multiple-add-stop", this.handleMultipleAddStop);
        $(document).on("click", ".remove-guest", this.handleRemoveGuest);
        $(document).on("click", ".remove-stop", this.handleRemoveStop);
        $(document).on("click", ".multiple-remove-stop", this.handleMultipleRemoveStop);
        $(document).on("click", ".multiple_service_type_id ", this.handleMultipleAdditionalStopAddButtonOnServiceType);
        $(document).on("click", ".multiple-add-guest", this.handleMultipleAddGuest);
        $(document).on("click", '.multiple-remove-guest', this.handleRemoveMultipleGuest);
        $(document).on("click", '#addEventFormButton', this.handleSaveEvent);
        $(document).on("change", '#clientIdForEvent', this.handleClientForEventError);
        $(document).on("keyup", '#event_name', this.handleEventNameError);
        $(document).on(
            "change",
            "#clientId",
            this.handleEventsOfCorporate
        );
        $(document).on(
            "change",
            ".multiple_client_id",
            this.handleMultipleEventsOfCorporate
        );
        $(document).on(
            "click",
            "#addBookingFormButton",
            this.handleSaveBooking
        );
        $(document).on(
            "change",
            "#serviceTypeId, #pickupLocationId,#dropoffLocationId",
            this.handlePickupLocationField,
            this.initializeAdditionalStopLimits
        );
        $(document).on("click", "#addNewRow", this.handleNewRow);
        $(document).on(
            "change",
            ".serviceTypeId, .pickupLocationId, .dropOffLocationId ",
            this.handleMultiplePickupLocationField
        );
        $(document).on(
            "click",
            "#sortComment, #sortDriverRemark,#sortPickUpDate,#sortInstructions, #sortStatus, #sortVehicleType, #sortDriver, #sortContact, #sortClient, #sortDropOf, #sortPikUp, #sortType, #sortTime, #sortBooking",
            this.handleSorting
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on("change", "#driversList", this.handleFilter);
        $(document).on(
            "click",
            ".remove-booking-row",
            this.handleRemoveBookingRow
        );
        $(document).on("click", ".dispatch", this.handleDispatch);
        $(document).on("click", "#closeDispatchIcon", this.closeDispatchModal);
        $(document).on("click", "#saveDispatchButton", this.handleDispatchForm);
        $(document).on(
            "dblclick",
            "#bookingTable tbody tr td",
            this.inlineEditCell
        );

        $(document).on("change", "#pickup_time_to_be_advised", this.handlePickupTime);
        $(document).on("change", ".multiple-pickup-time-to-be-advised", this.handleMultiplePickupTime);
        $(document).on(
            "click",
            "#closeBlcackOutInfoButton, #closeBlcackOutInfoIcon",
            this.closeBlackoutModel
        );
        $(document).on("click", "#bulkDelete", this.handleBulkDelete);
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmBooking
        );
        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeleteBookings
        );
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on("change", ".multiple_child_seat_required", function() {
            let child_seat_index = this.id.split("_")[3];

            if(this.value == 'yes')
            {
                $('#no_of_seats_required_' + child_seat_index).css('display', 'block');
                $('#child_1_age_' + child_seat_index).css('display', 'block');
            }else{
                $('#no_of_seats_required_' + child_seat_index).css('display', 'none');
                $('#child_1_age_' + child_seat_index).css('display', 'none');
                $('#child_2_age_' + child_seat_index).css('display', 'none');
            }
        });
        $(document).on("change", ".multiple_no_of_seats_required", function() {
            let child_seat_index = this.id.split("_")[4];

            if(this.value == 1)
            {
                $('#child_1_age_' + child_seat_index).css('display', 'block');
                $('#child_2_age_' + child_seat_index).css('display', 'none');
            }else{
                $('#child_1_age_' + child_seat_index).css('display', 'block');
                $('#child_2_age_' + child_seat_index).css('display', 'block');
            }
        });
    }
    
    handleClientForEventError = () => { 
        if($('#clientIdForEvent').val() == '')
        {
            $('#hotel_for_event_error').css('display', 'inline-block');
        }else{
            $('#hotel_for_event_error').css('display', 'none');
        }
    }
    
    handleEventNameError = () => { 
        if($('#event_name').val() == '')
        {
            $('#event_name_error').css('display', 'inline-block');
        }else{
            $('#event_name_error').css('display', 'none');
        }
    }
    handleEventsOfCorporate = ({ target }) => {
        try {
            const hotelId = $(target).val();
            const url = this.props.routes.getEventsByHotel;
            
            axios
                .get(url, {
                    params: {
                        hotel_id: hotelId,
                    }
                })
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    
                    if (statusCode === 200) {
                        $('#eventId').empty().append('<option value="">Select An Event</option>');

                        if (response.data.data.length > 0) {
                            $.each(response.data.data, function(index, item) {
                                $('#eventId').append(`<option value="${item.id}">${item.name}</option>`);
                            });
                        }
                    } else {
                        throw flash;
                    }
                })
                .catch((error) => {
                    this.handleException(error);
                });
        } catch (error) {
            this.handleException(error);
        }
    };
    
    getEventsAfterCreateEvent = ({ hotelId }) => {
        try {
            const url = this.props.routes.getEventsByHotel;
            
            axios
                .get(url, {
                    params: {
                        hotel_id: hotelId,
                    }
                })
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    
                    if (statusCode === 200) {

                        $('#clientId').val(hotelId).change();

                        $('#eventId').empty().append('<option value="">Select An Event</option>');

                        if (response.data.data.length > 0) {
                            $.each(response.data.data, function(index, item) {
                                $('#eventId').append(`<option value="${item.id}">${item.name}</option>`);
                            });
                        }
                    } else {
                        throw flash;
                    }
                })
                .catch((error) => {
                    this.handleException(error);
                });
        } catch (error) {
            this.handleException(error);
        }
    };

    handleMultipleEventsOfCorporate = ({ target }) => {
        try {
            const event_box_id = $(target).attr('id').split("_")[1];
            console.log(event_box_id)
            const hotelId = $(target).val();
            const url = this.props.routes.getEventsByHotel;
            
            axios
                .get(url, {
                    params: {
                        hotel_id: hotelId,
                    }
                })
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    
                    if (statusCode === 200) {
                        $('#eventId_' + event_box_id).empty().append('<option value="">Select An Event</option>');

                        if (response.data.data.length > 0) {
                            $.each(response.data.data, function(index, item) {
                                $('#eventId_' + event_box_id).append(`<option value="${item.id}">${item.name}</option>`);
                            });
                        }
                    } else {
                        throw flash;
                    }
                })
                .catch((error) => {
                    this.handleException(error);
                });
        } catch (error) {
            this.handleException(error);
        }
    };

    handlePickupTime = () => {
        if($('#pickup_time_to_be_advised').is(':checked')){
            $('#pickupTime').prop('readonly', true);
            $('#pickupTime').val("00:00");
            $('#pickupTimePicker').hide();
        }else{
            $('#pickupTime').prop('readonly', false);
            $('#pickupTime').val("");
            $('#pickupTimePicker').show();
        }
    }
    handleMultiplePickupTime = ({target}) => {
        let index = $(target).attr('id').split("_")[6];
        if($(target).is(":checked")){
            $('#pickupTime_' + index).prop('readonly', true);
            $('#pickupTime_' + index).val("00:00");
            $('#pickupTimePicker_' + index).hide();
        }else{
            $('#pickupTime_' + index).prop('readonly', false);
            $('#pickupTime_' + index).val("");
            $('#pickupTimePicker_' + index).show();
        }
    }
    handleCloseModal = () => {
        this.closeModal("deleteConfirmModal");
    };
    handleDeleteBookings = () => {
        try {
            const bookingId = $("#deleteConfirmationTitle").data("id");
            const formData = new FormData();
            formData.append("booking_ids[]", bookingId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleDeleteModal = ({ target }) => {
        try {
            const bookingId = $(target).data("id");
            this.openModal("deleteConfirmModal");
            $("#deleteConfirmationTitle").text(
                "Are you sure you want to delete this booking?"
            );
            $("#deleteConfirmationTitle").data("id", bookingId);
        } catch (error) {
            this.handleException(error);
        }
    };
    sendDeleteRequest = (formData) => {
        const url = this.props.routes.deleteBookings;
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    this.closeModal("deleteConfirmModal");
                    $(".bookingTableCheckbox").prop("checked", false);
                    const bookingIds = Array.from(
                        formData.getAll("booking_ids[]")
                    );
                    $.each(bookingIds, (index, bookingId) => {
                        $(`tr[data-id="${bookingId}"]`).remove();
                    });
                    $("#loader").hide();
                }
                throw flash;
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };
    handleBulkDeleteConfirmBooking = () => {
        try {
            let bookingIds = $("#deleteConfirmationTitle").data("id");
            bookingIds = bookingIds.split(",");
            const formData = new FormData();
            $.each(bookingIds, (index, bookingId) => {
                formData.append("booking_ids[]", bookingId);
            });
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleBulkDelete = () => {
        try {
            let bookingIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    bookingIds.push(parseInt($(this).closest("tr").data("id"))); // Push the ID of the checked checkbox into the array
                }
            });
            if (!bookingIds.length) {
                throw new ErrorHandler(422, this.languageMessage.atleast_one);
            }
            this.openModal("deleteConfirmModal");
            $("#deleteConfirmationTitle").text(
                "Are you sure you want to delete these bookings?"
            );
            $("#deleteConfirmationTitle").data("id", bookingIds.join(","));
            $("#deleteConfirmButton").attr("id", "deleteBulkConfirmButton");
        } catch (error) {
            this.handleException(error);
        }
    };

    closeBlackoutModel = () => {
        $("#blcackOutModal").modal("hide");
        return false;
    };

    inlineEditCell = ({ target }) => {
        if (this.previousCell) {
            this.revertOldValue();
            this.previousCell = null;
        }
        const bookingId = $(target).closest("tr").data("id");
        const name = $(target).data("name");
        const old = $(target).data("old");
        const oldId = $(target).data("old-id");
        const serviceId = $(target).data("service-id");
        let html = ``;
        switch (name) {
            case "pickup_date":
                html = this.createPickUpDateInput(old);
                $(target).html(html);
                this.initializeDatePicker(`pickupDatePicker`);
                break;
            case "pickup_time":
                html = this.createPickUpTimeInput(old);
                $(target).html(html);
                this.initializeTimePicker(`pickupTimePicker`);
                break;
            case "pickup_location":
                html = this.createPickUpLocationField(old, oldId, serviceId);
                $(target).html(html);
                const id = $(html).find("input").attr("id");
                if (id === "pick_up_location") {
                    this.initializeGoogleMapAutoComplete(id);
                }
                break;
            case "drop_of_location":
                html = this.createDropOffLocationField(old, oldId, serviceId);
                $(target).html(html);
                const idVal = $(html).find("input").attr("id");
                if (idVal === "drop_of_location") {
                    this.initializeGoogleMapAutoComplete(idVal);
                }
                break;
            case "guest_name":
                html += `<form id="inlineEditTableForm">`;
                html += this.createInput(old, name, "Guest Name");
                html += `</form>`;
                $(target).html(html);
                break;
            case "additional_stops":
                html = this.createAdditionalStopField(old, oldId, serviceId);
                $(target).html(html);
                console.log(html)
                const idValAS = $(html).find("input").attr("id");
                if (idValAS === "additional_stops") {
                    this.initializeGoogleMapAutoComplete(idValAS);
                }
                break;
            case "phone":
                html += `<form id="inlineEditTableForm">`;
                html += this.createInput(old, name, "Contact");
                html += `</form>`;
                $(target).html(html);
                break;
            case "driver_id":
                const drivers = this.props.drivers;
                const driverOffDays = this.props.driverOffDays;
                const pickUpDate = $(target).closest("tr").data("pickup-date");
                if (!pickUpDate) {
                    return false;
                }

                let driverIds = [];
                for (const offDay of driverOffDays) {
                    if (offDay.off_date === pickUpDate) {
                        driverIds.push(offDay.driver_id);
                    }
                }
                html += `<form id="inlineEditTableForm">`;
                html += this.createDropdown(
                    drivers,
                    "driver_id",
                    oldId,
                    driverIds
                );
                html += `</form>`;
                $(target).html(html);
                break;
            case "vehicle_id":
                const vehicles = this.props.vehicles;
                html += `<form id="inlineEditTableForm">`;
                html += this.createDropdown(vehicles, "vehicle_id", oldId);
                html += `</form>`;
                $(target).html(html);
                break;
            case "vehicle_type_id":
                const vehicleTypes = this.props.vehicleTypes;
                html += `<form id="inlineEditTableForm">`;
                html += this.createDropdown(vehicleTypes, "vehicle_type_id", oldId);
                html += `</form>`;
                $(target).html(html);
                break;
            case "status":
                const statusArray = [
                    { id: "ACCEPTED", name: "Accepted" },
                    { id: "PENDING", name: "Pending" },
                    { id: "COMPLETED", name: "Completed" },
                    { id: "CANCELLED", name: "Cancelled" },
                    { id: "SCHEDULED", name: "Scheduled" },
                ];
                html += `<form id="inlineEditTableForm">`;
                html += this.createDropdown(statusArray, name, oldId);
                html += `</form>`;
                $(target).html(html);
                break;
            case "client_instructions":
                html += `<form id="inlineEditTableForm">`;
                html += this.createInput(old, name, "Client Instructions");
                html += `</form>`;
                $(target).html(html);
                break;
            case "driver_remark":
                html += `<form id="inlineEditTableForm">`;
                html += this.createInput(old, name, "Driver Remarks");
                html += `</form>`;
                $(target).html(html);
                break;
            case "internal_remark":
                html += `<form id="inlineEditTableForm">`;
                html += this.createInput(old, name, "Internal Remarks");
                html += `</form>`;
                $(target).html(html);
                break;
            default:
                if (this.previousCell) {
                    this.revertOldValue();
                    this.previousCell = null;
                }
                break;
        }
        if (name) {
            this.previousCell = { target, name, old };
        }
        this.validateInlineForm();
        let formData = new FormData();
        formData.append("booking_id", bookingId);
        const url = this.props.routes.updateBookings;
        $(target).on("change.td keypress hide.td", (e) => {
            const $target = $(e.target);
            const isDropdown = $target.is("select");
            const isDatePicker = $target.hasClass("date-input-change");
            const isPicker = $target.hasClass("input-change");
            const isTimePicker = $target.hasClass("pickup-time-input");
            const eventType = e.type;
            const newValue = $target.val();
            const fieldName = $target.attr("name");
            if (!$("#inlineEditTableForm").valid()) {
                return; // If the field is not valid, prevent further processing
            }
            // Check if the event should be processed
            if (
                (isDropdown && eventType === "change") ||
                (!isDropdown && eventType === "keypress" && e.which === 13) ||
                (isDatePicker && eventType === "change") ||
                (isPicker && eventType === "change") ||
                (isTimePicker && eventType === "hide")
            ) {
                e.preventDefault();
                if (isTimePicker) {
                    const latestVal = $("#pickupTime").val();
                    if (latestVal && latestVal !== old) {
                        formData.append("pickup_time", latestVal);
                        this.sendPostRequest(url, formData, bookingId);
                    } else {
                        if (this.previousCell) {
                            this.revertOldValue();
                        }
                    }
                } else if (isDatePicker) {
                    // const latestValue = this.convertDate(newValue);
                    if (newValue && newValue !== old) {
                        formData.append(fieldName, newValue);
                        this.sendPostRequest(url, formData, bookingId);
                    } else {
                        return false;
                    }
                } else if (isPicker) {
                    if (newValue && newValue !== old) {
                        formData.append(fieldName, newValue);
                        this.sendPostRequest(url, formData, bookingId);
                    } else {
                        if (this.previousCell) {
                            this.revertOldValue();
                        }
                    }
                } else {
                    const oldValue = isDropdown ? oldId : old;
                    if (newValue && newValue !== oldValue) {
                        formData.append(fieldName, newValue);
                        this.sendPostRequest(url, formData, bookingId);
                    } else {
                        if (this.previousCell) {
                            this.revertOldValue();
                        }
                    }
                }
            }
        });
    };

    validateInlineForm = () => {
        $.validator.addMethod(
            "dateFormat",
            function (value, element) {
                // Check if the value matches the dd/MM/yyyy format
                return value.match(/^\d{2}\/\d{2}\/\d{4}$/);
            },
            this.languageMessage.pickup_date.regex
        );
        $.validator.addMethod(
            "timeFormat",
            function (value, element) {
                // Check if the value matches the HH:mm format
                return value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/);
            },
            this.languageMessage.time_format
        );
        $("#inlineEditTableForm").validate({
            rules: {
                pick_up_location_id: {
                    required: true,
                },
                flight_detail: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                pick_up_location: {
                    required: true,
                    minlength: 3,
                },
                drop_off_location_id: {
                    required: true,
                },
                drop_of_location: {
                    required: true,
                    minlength: 3,
                },
                pickup_date: {
                    required: true,
                    dateFormat: true,
                },
                pickup_time: {
                    required: true,
                    timeFormat: true,
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    // maxlength: 10,
                },
                guest_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                },
            },
            messages: {
                flight_detail: {
                    required: this.languageMessage.flight_detail.required,
                    minlength: this.languageMessage.flight_detail.min,
                    maxlength: this.languageMessage.flight_detail.max,
                },
                pick_up_location_id: {
                    required: this.languageMessage.pick_up_location_id.required,
                },
                pick_up_location: {
                    required: this.languageMessage.pick_up_location.required,
                    minlength: this.languageMessage.pick_up_location.min,
                },
                drop_off_location_id: {
                    required:
                        this.languageMessage.drop_off_location_id.required,
                },
                drop_of_location: {
                    required: this.languageMessage.drop_of_location.required,
                    minlength: this.languageMessage.drop_of_location.min,
                },
                vehicle_type_id: {
                    required: this.languageMessage.vehicle_type_id.required,
                },
                pickup_date: {
                    required: this.languageMessage.pickup_date.required,
                },
                pickup_time: {
                    required: this.languageMessage.pickup_time.required,
                },
                phone: {
                    required: this.languageMessage.phone.required,
                    // digits: this.languageMessage.phone.regex,
                    minlength: this.languageMessage.phone.min,
                    // maxlength: this.languageMessage.phone.max,
                },
                guest_name: {
                    required: this.languageMessage.guest_name.required,
                    minlength: this.languageMessage.guest_name.min,
                    maxlength: this.languageMessage.guest_name.max,
                },
                // Add custom error messages for other fields here
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.parent(".input-group").length) {
                    error.insertAfter(element.parent()); // Error placement for input-group
                } else {
                    error.insertAfter(element); // Default error placement
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            },
        });
    };

    createDropOffLocationField = (old, oldId, serviceId) => {
        let html = `<form id="inlineEditTableForm">`;
        const locations = this.props.locations;
        if (serviceId == 3) {
            html += this.createInput(old, "flight_detail", "Flight Detail");
        } else {
            if (oldId && oldId != 8) {
                html += this.createDropdown(
                    locations,
                    "drop_off_location_id",
                    oldId
                );
            } else if (oldId == 8) {
                html += this.createDropdown(
                    locations,
                    "pick_up_location_id",
                    oldId
                );
                html += this.createInput(
                    old,
                    "drop_of_location",
                    "Drop Off Location",
                    "input-change"
                );
            } else {
                html += this.createInput(
                    old,
                    "drop_of_location",
                    "Drop Off Location",
                    "input-change"
                );
            }
        }
        html += `</form>`;
        return html;
    };

    createAdditionalStopField = (old, oldId, serviceId) => {
        let html = `<form id="inlineEditTableForm">`;
        const locations = this.props.locations;
        if (serviceId == 3) {
        } else {
            if (oldId && oldId != 8) {
            } else if (oldId == 8) {
                html += this.createInput(
                    old,
                    "additional_stops",
                    "Additional Stops",
                    "input-change"
                );
            } else {
                html += this.createInput(
                    old,
                    "additional_stops",
                    "Additional Stops",
                    "input-change"
                );
            }
        }
        html += `</form>`;
        return html;
    };
    createPickUpLocationField = (old, oldId, serviceId) => {
        let html = `<form id="inlineEditTableForm">`;
        const locations = this.props.locations;
        if (serviceId == 1) {
            html += this.createInput(old, "flight_detail", "Flight Detail");
        } else {
            if (oldId && oldId != 8) {
                html += this.createDropdown(
                    locations,
                    "pick_up_location_id",
                    oldId
                );
            } else if (oldId == 8) {
                html += this.createDropdown(
                    locations,
                    "pick_up_location_id",
                    oldId
                );
                html += this.createInput(
                    old,
                    "pick_up_location",
                    "Pick Up Location",
                    "input-change"
                );
            } else {
                html += this.createInput(
                    old,
                    "pick_up_location",
                    "Pick Up Location",
                    "input-change"
                );
            }
        }
        html += `</form>`;
        return html;
    };
    createInput = (old, name, placeholder, classValue = "") => {
        return `<input type="text" name="${name}" id="${name}" value="${old}" class="form-control ${classValue}" placeholder="${placeholder}" autocomplete="off">`;
    };

    createDropdown = (data, name, selectedId, filterIds = []) => {
        return `<select name="${name}" class="form-control form-select custom-select" autocomplete="off">
                    <option value="">Select one</option>
                    ${data
                        .filter((row) => !filterIds.includes(row.id))
                        .map((row) => {
                            let rowName;
                            if (name === "vehicle_id") {
                                rowName = row.vehicle_number;
                            } else {
                                rowName = row.name;
                            }
                            return `<option value="${row.id}" ${
                                selectedId && row.id === selectedId
                                    ? "selected"
                                    : ""
                            }>${rowName}</option>`;
                        })
                        .join("")}
                </select>`;
    };

    createPickUpTimeInput = (old) => {
        return `<form id="inlineEditTableForm"> <div class="input-group date pickup-time-input" id="pickupTimePicker" data-target-input="nearest">
                        <input type="text" name="pickup_time" value="${old}" id="pickupTime"
                            class="form-control datetimepicker-input pickup-time-input" data-target="#pickupTimePicker" placeholder="HH:MM"
                            autocomplete="off" autofocus />
                        <div class="input-group-append" data-target="#pickupTimePicker" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                        </div>
                    </div></form>`;
    };
    createPickUpDateInput = (old) => {
        return `<form id="inlineEditTableForm"> <div class="input-group date" id="pickupDatePicker" data-target-input="nearest">
                    <input type="text" name="pickup_date" value="${old}" id="pickup_date" class="form-control datetimepicker-input date-input-change" data-target="#pickupDatePicker" placeholder="dd/mm/yyyy" autocomplete="off" autofocus />
                    <div class="input-group-append" data-target="#pickupDatePicker" data-toggle="datetimepicker">
                         <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                    </div>
                </div></form>`;
    };

    revertOldValue = () => {
        if (this.previousCell) {
            const { target, old } = this.previousCell;
            const oldValue = old ? old : "N/A";
            $(target).html(oldValue);
        }
    };

    validateDispatchForm = () => {
        $("#updateDispatchForm").validate({
            rules: {
                is_driver_notified: {
                    required: true,
                },
                is_driver_acknowledge: {
                    required: true,
                },
            },
            messages: {
                is_driver_notified: {
                    required: this.languageMessage.is_driver_notified.required,
                },
                is_driver_acknowledge: {
                    required:
                        this.languageMessage.is_driver_acknowledge.required,
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.closest(".col-sm-6").length) {
                    element.closest(".col-sm-6").append(error);
                } else {
                    element.closest(".form-group").append(error);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            },
        });
    };

    handleDispatchForm = (e) => {
        e.preventDefault();
        if (!$("#updateDispatchForm").valid()) {
            return false;
        }
        const bookingId = $("#bookingIdInput").val();
        const isDriverNotified = $("#driverNotified").is(":checked");
        const isDriverAcknowledge = $("#driverAcknowledge").is(":checked");
        if (bookingId && (isDriverNotified || isDriverAcknowledge)) {
            const formData = new FormData();
            formData.append("booking_id", bookingId);
            formData.append("is_driver_notified", isDriverNotified);
            formData.append("is_driver_acknowledge", isDriverAcknowledge);
            const url = this.props.routes.updateDispatch;
            this.sendPostRequest(url, formData, bookingId);
            this.closeDispatchModal("dispatchModal");
        }
    };

    handleDispatch = ({ target }) => {
        try {
            const bookingId = $(target).closest("tr").data("id");
            const isNotified = $(target).closest("tr").data("is-notified");
            const isAcknowledge = $(target)
                .closest("tr")
                .data("is-acknowledge");
            const DriverId = $(target).closest("tr").data("driver-id");
            if (!DriverId) {
                throw new ErrorHandler(422, this.languageMessage.assign_driver);
            }
            if (bookingId) {
                this.openModal("dispatchModal");
                this.validateDispatchForm();
                $("#dispatchTitle").text("Booking #" + bookingId);
                $("#dispatchTitle").data("is-notified", isNotified);
                $("#dispatchTitle").data("is-acknowledge", isAcknowledge);
                $("#bookingIdInput").val(bookingId);
                $("#driverAcknowledge").attr("disabled", "disabled");
                $("#driverNotified").removeAttr("disabled");
                $("#driverNotified").prop("checked", false);
                $("#driverAcknowledge").prop("checked", false);
                if (isNotified) {
                    $("#driverNotified").prop("checked", true);
                    $("#driverNotified").attr("disabled", "disabled");
                    $("#driverAcknowledge").removeAttr("disabled");
                }
            }
        } catch (error) {
            this.handleException(error);
        }
    };
    sendPostRequest = (url, formData, bookingId) => {
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    const tbody = $("#bookingTable tbody");
                    $("#" + bookingId).replaceWith(response.data.data.html);
                }
                throw flash;
            })
            .catch((error) => {
                $("#loader").hide();
                if (error.response && error.response.status === 422) {
                    // Display validation errors
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach((fieldName) => {
                        const errorMessage = errors[fieldName][0]; // Assuming you only want to display the first error message for each field
                        const errorElementName = '[name="' + fieldName + '"]';
                        $(errorElementName).val("");
                        let errorElement = $(errorElementName).next(
                            ".error.invalid-feedback"
                        );
                        if (!errorElement.length) {
                            // Create error element if not exists
                            errorElement = $("<span>").addClass(
                                "error invalid-feedback"
                            );
                            $(errorElementName).after(errorElement);
                        }
                        // Update error message
                        errorElement.text(errorMessage);
                        errorElement.show();
                    });
                } else {
                    // Handle other types of errors
                    this.handleException(error);
                }
            });
    };
    closeDispatchModal = () => {
        $("#dispatchModal").modal("hide");
    };
    handleRemoveBookingRow = ({ target }) => {
        $(target).closest("tr").remove();
    };

    handleFilter = (startDate, endDate) => {
        try {
            let pickupDateRange = $("#pickupDateBooking").val();
            const driverId = $("#driversList").val();
            if (startDate && endDate) {
                pickupDateRange = startDate + " - " + endDate;
            }

            const params = {
                pickupDateRange: pickupDateRange,
                search: $("#search").val(),
                driverId: driverId,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterBookings + "?" + queryParams;
            this.handleFilterRequest(url);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleSorting = ({ target }) => {
        try {
            const sortOrder = $("#sortOrder").val() === "asc" ? "desc" : "asc";
            let pickupDateRange = $("#pickupDateBooking").val();
            const driverId = $("#driversList").val();
            const params = {
                pickupDateRange: pickupDateRange,
                sortField: $(target).attr("id"),
                sortDirection: sortOrder,
                search: $("#search").val(),
                driverId: driverId,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterBookings + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleFilterRequest = (url, sortOrder = null) => {
        $("#loader").show();
        axios
            .get(url)
            .then((response) => {
                const statusCode = response.data.status.code;
                if (statusCode === 200) {
                    const tbody = $("#bookingTable tbody");
                    tbody.html(response.data.data.html);
                    if (sortOrder) {
                        $("#sortOrder").val(sortOrder);
                    }
                    $("#loader").hide();
                    $("#bookingTable").checkboxTable();
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };

    handleMultiplePickupLocationField = ({ target }) => {
        const targetVal = parseInt($(target).val());
        if ($(target).hasClass("serviceTypeId")) {
            switch (targetVal) {
                case 1:
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationDropdown")
                        .show();
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationTextbox")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationDropdown")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationTextBox")
                        .show();
                    $(target).closest("tr").find(".departureTimeField").hide();
                    $(target).closest("tr").find(".noOfHoursTextBox").hide();
                    $(target).closest("tr").find(".crossBorderField").hide();
                    $(target)
                        .closest("tr")
                        .find(
                            ".flightDetailDiv, .totalLuggageDiv, .totalPaxDiv"
                        )
                        .show();
                    break;
                case 3:
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationDropdown")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationTextbox")
                        .show();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationDropdown")
                        .show();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationTextBox")
                        .hide();
                    $(target).closest("tr").find(".departureTimeField").show();
                    $(target).closest("tr").find(".noOfHoursTextBox").hide();
                    $(target).closest("tr").find(".crossBorderField").hide();
                    $(target)
                        .closest("tr")
                        .find(
                            ".flightDetailDiv, .totalLuggageDiv, .totalPaxDiv"
                        )
                        .show();
                    break;
                case 4:
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationDropdown")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationTextbox")
                        .show();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationDropdown")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationTextBox")
                        .show();
                    $(target).closest("tr").find(".departureTimeField").hide();
                    $(target).closest("tr").find(".noOfHoursTextBox").show();
                    $(target).closest("tr").find(".crossBorderField").show();
                    $(target)
                        .closest("tr")
                        .find(
                            ".flightDetailDiv, .totalLuggageDiv, .totalPaxDiv"
                        )
                        .show();
                    break;
                case 5:
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationDropdown")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationTextbox")
                        .show();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationDropdown")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationTextBox")
                        .show();
                    $(target).closest("tr").find(".departureTimeField").hide();
                    $(target).closest("tr").find(".noOfHoursTextBox").hide();
                    $(target).closest("tr").find(".crossBorderField").hide();
                    $(target)
                        .closest("tr")
                        .find(
                            ".flightDetailDiv, .totalLuggageDiv, .totalPaxDiv"
                        )
                        .hide();
                    break;
                default:
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationDropdown")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".pickUpLocationTextbox")
                        .show();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationDropdown")
                        .hide();
                    $(target)
                        .closest("tr")
                        .find(".dropOffLocationTextBox")
                        .show();
                    $(target).closest("tr").find(".departureTimeField").hide();
                    $(target).closest("tr").find(".noOfHoursTextBox").hide();
                    $(target).closest("tr").find(".crossBorderField").hide();
                    $(target)
                        .closest("tr")
                        .find(
                            ".flightDetailDiv, .totalLuggageDiv, .totalPaxDiv"
                        )
                        .show();
            }
        } else if ($(target).hasClass("pickupLocationId")) {
            if (targetVal === 8) {
                $(target).closest("tr").find(".pickUpLocationTextbox").show();
            } else {
                $(target).closest("tr").find(".pickUpLocationTextbox").hide();
            }
        } else if ($(target).hasClass("dropOffLocationId")) {
            if (targetVal === 8) {
                $(target).closest("tr").find(".dropOffLocationTextBox").show();
            } else {
                $(target).closest("tr").find(".dropOffLocationTextBox").hide();
            }
        }
    };
    handleSaveMultpleBooking = () => {
        $.validator.addMethod(
            "dateFormatMultiple",
            function (value, element) {
                // Check if the value matches the dd/MM/yyyy format
                return value.match(/^\d{2}\/\d{2}\/\d{4}$/);
            },
            this.languageMessage.pickup_date.regex
        );
        $.validator.addMethod(
            "dateTimeFormatMultiple",
            function (value, element) {
                // Regular expression to match the format "dd/mm/yyyy hh:mm"
                return (
                    this.optional(element) ||
                    /^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/.test(value)
                );
            },
            this.languageMessage.customDateFormat
        );
        $.validator.addMethod(
            "timeFormatMultiple",
            function (value, element) {
                // Check if the value matches the HH:mm format
                return value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/);
            },
            this.languageMessage.time_format
        );

        $.validator.addMethod(
            "customMinMultiple",
            (value, element) => {
                const vehicleTypeElement = $(element)
                    .closest("tr")
                    .find(".multiple_vehicle_type_id");
                const selectedSeatingCapacity = parseInt(
                    vehicleTypeElement
                        .find(":selected")
                        .data("seating-capacity")
                );
                const crossBoreder = $(element)
                    .closest("tr")
                    .find(".multiple_is_cross_border");
                if (crossBoreder.is(":checked")) {
                    return parseInt(value) >= 6;
                } else if (!selectedSeatingCapacity) {
                    return parseInt(value) >= 3;
                } else if (selectedSeatingCapacity <= 13) {
                    return parseInt(value) >= 3;
                } else if (selectedSeatingCapacity > 13) {
                    return parseInt(value) >= 4;
                }
            },
            (params, element) => {
                const vehicleTypeElement = $(element)
                    .closest("tr")
                    .find(".multiple_vehicle_type_id");
                const selectedSeatingCapacity = parseInt(
                    vehicleTypeElement
                        .find(":selected")
                        .data("seating-capacity")
                );
                const crossBoreder = $(element)
                    .closest("tr")
                    .find(".multiple_is_cross_border");
                if (crossBoreder.is(":checked")) {
                    return this.languageMessage.no_of_hours.min_cross_border;
                } else if (!selectedSeatingCapacity) {
                    return this.languageMessage.no_of_hours.min;
                } else if (selectedSeatingCapacity <= 13) {
                    return this.languageMessage.no_of_hours
                        .min_less_then_13_seat;
                } else if (selectedSeatingCapacity > 13) {
                    return this.languageMessage.no_of_hours.min_greater_13_seat;
                }
            }
        );
        $.validator.addMethod(
            "validFileSizeMultiple",
            function (value, element) {
                const maxSize = 5 * 1024 * 1024; // 5 MB in bytes
                if (element.files.length) {
                    const fileSize = element.files[0].size;

                    // Check file size
                    if (fileSize > maxSize) {
                        return false;
                    }
                }
                return true;
            },
            this.languageMessage.attachment.validFileSize
        );
        $.validator.addMethod(
            "departureDateTimeChecked",
            function (value, element) {
                // Retrieve pickup date and time values
                const pickupDate = $(element)
                    .closest("tr")
                    .find(".multiple_pickup_date")
                    .val();
                const pickupTime = $(element)
                    .closest("tr")
                    .find(".multiple_pickup_time")
                    .val();
                if (!pickupDate || !pickupTime || !value) {
                    return true;
                }
                // Helper function to parse date strings in the format 'dd/mm/yyyy hh:mm'
                function parseDateTime(dateTimeStr) {
                    const [datePart, timePart] = dateTimeStr.split(" ");
                    const [day, month, year] = datePart.split("/").map(Number);
                    const [hours, minutes] = timePart.split(":").map(Number);
                    return new Date(year, month - 1, day, hours, minutes);
                }

                // Combine pickup date and time into a single string
                const pickupDateTimeStr = pickupDate + " " + pickupTime;

                // Parse the pickup date and time
                const pickupDateTime = parseDateTime(pickupDateTimeStr);

                // Parse the departure date and time
                const departureDateTime = parseDateTime(value);

                // Compare departure date and time with pickup date and time
                return departureDateTime > pickupDateTime;
            },
            this.languageMessage.laterThan
        );
        $.validator.addMethod(
            "notPastTimeMultiple",
            function (value, element) {
                // Get the pickup date and time from the form
                const pickupDate = $(element)
                    .closest("tr")
                    .find(".multiple_pickup_date")
                    .val();
                const pickupTime = value; // This is the current time being validated
                const [pickupHours, pickupMinutes] = pickupTime.split(":").map(Number);
                
                // Parse the pickup date
                const [day, month, year] = pickupDate.split("/").map(Number);
                const selectedDateTime = new Date(year, month - 1, day, pickupHours, pickupMinutes);
        
                // Get the current date and time
                const now = new Date();
        
                // Compare the selected date and time with the current date and time
                return selectedDateTime >= now;
            },
            this.languageMessage.pickup_time.notPastTime
        );
        
        
        $.validator.addMethod(
            "notPastDateMultiple",
            function (value, element) {
                // Parse the pickup date from the form
                const pickupDate = $(element)
                .closest("tr")
                .find(".multiple_pickup_date")
                .val();
                const [day, month, year] = pickupDate.split("/").map(Number);
                const newPickupDate = new Date(year, month - 1, day);
                
                // Get today's date and set the time to 00:00:00
                const today = new Date();
                today.setHours(0, 0, 0, 0);
        
                // Check if pickupDate is today or in the future
                return newPickupDate >= today;
            },
            this.languageMessage.pickup_date.notPastDate
        );
        $("#createMultipleBookingForm").validate({
            errorElement: "span",
            errorPlacement: function (error, element) {
                element
                    .closest(".form-group")
                    .find(".invalid-feedback")
                    .remove();
                error.addClass("invalid-feedback");
                element.closest(".form-group").append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            },
            submitHandler: (form) => {
                const saveButton = $("#addMultipleBookingFormButton"); // Select the button by ID or class
                saveButton.prop("disabled", true); // Disable the button
                saveButton.html('<span class="spinner-border spinner-border-sm"></span> Saving...'); // Show loader
                let isBlackOutPeriod = false;
                let isDisposalService = false;
                $(".multiple_service_type_id").each((i, e) => {
                    isDisposalService =
                        $(e).val() && $(e).val() === "4" ? true : false;
                    if (isDisposalService) {
                        return false; // Break the loop
                    }
                });
                const loggedUser = this.props.loggedUser;
                const isClientUser =
                    loggedUser.user_type &&
                    loggedUser.user_type.type === "client"
                        ? true
                        : false;
                $(".multiple_pickup_date").each((i, e) => {
                    isBlackOutPeriod = this.checkDateBetweenPeakPeriods(
                        $(e).val()
                    );
                    const noOfHours = $(e)
                        .closest("tr")
                        .find(".multiple_no_of_hours")
                        .val();
                    if (isBlackOutPeriod && noOfHours < 10) {
                        isBlackOutPeriod = true;
                        return false; // Break the loop
                    } else {
                        isBlackOutPeriod = false;
                    }
                });
                if (isDisposalService && isBlackOutPeriod && isClientUser) {
                    // Show the modal when form is valid
                    this.openModal("blcackOutModal");
                    // Handle the Yes button click
                    $("#yesSubmitForm")
                        .off("click")
                        .on("click", () => {
                            // Hide the modal
                            $("#blcackOutModal").modal("hide");
                            // Submit the form without XHR
                            form.submit();
                        });
                } else {
                    form.submit();
                }
            },
        });
        this.handleInitilizeValidation();
    };

    handleInitilizeValidation = () => {
        $(".multiple_event_id").each((i, e) => {
            $(e).rules("add", {
                required: true,
                messages: {
                    required: this.languageMessage.event_id.required,
                },
            });
        });

        $(".multiple_departure_time").each((i, e) => {
            $(e).rules("add", {
                dateTimeFormatMultiple: true,
                departureDateTimeChecked: true,
                messages: {
                    // required: this.languageMessage.departure_time.required,
                },
            });
        });

        $(".multiple_attachment").each((i, e) => {
            $(e).rules("add", {
                extension: "jpg|jpeg|png|gif|doc|docx|txt|pdf|xls|xlsx",
                validFileSizeMultiple: true,
                messages: {
                    extension: this.languageMessage.attachment.extension,
                },
            });
        });
        $(".multiple_client_id").each((i, e) => {
            $(e).rules("add", {
                required: true,
                messages: {
                    required: this.languageMessage.client_id.required,
                },
            });
        });
        $(".multiple_service_type_id").each((i, e) => {
            $(e).rules("add", {
                required: true,
                messages: {
                    required: this.languageMessage.service_type_id.required,
                },
            });
        });
        $(".multiple_pick_up_location").each((i, e) => {
            $(e).rules("add", {
                required: true,
                minlength: 3,
                messages: {
                    required: this.languageMessage.pick_up_location.required,
                    minlength: this.languageMessage.pick_up_location.min,
                },
            });
        });
        $(".multiple_drop_of_location").each((i, e) => {
            $(e).rules("add", {
                required: {
                    depends: function (element) {
                        return (
                            parseInt(
                                $(e)
                                    .closest("tr")
                                    .find(".multiple_service_type_id")
                                    .val()
                            ) !== 4
                        );
                    },
                },
                minlength: 3,
                messages: {
                    required: this.languageMessage.drop_of_location.required,
                    minlength: this.languageMessage.drop_of_location.min,
                },
            });
        });
        $(".multiple_flight_detail").each((i, e) => {
            $(e).rules("add", {
                required: {
                    depends: function (element) {
                        return (
                            $.inArray(
                                $(e)
                                    .closest("tr")
                                    .find(".multiple_service_type_id")
                                    .val(),
                                ["1", "3"]
                            ) != -1
                        );
                    },
                },
                minlength: 3,
                maxlength: 50,
                messages: {
                    required: this.languageMessage.flight_detail.required,
                    minlength: this.languageMessage.flight_detail.min,
                    maxlength: this.languageMessage.flight_detail.max,
                },
            });
        });
        $(".multiple_vehicle_type_id").each((i, e) => {
            $(e).rules("add", {
                required: true,
                messages: {
                    required: this.languageMessage.vehicle_type_id.required,
                },
            });
        });
        $(".multiple_pickup_date").each((i, e) => {
            $(e).rules("add", {
                required: true,
                dateFormatMultiple: true,
                notPastDateMultiple:true,
                messages: {
                    required: this.languageMessage.pickup_date.required,
                },
            });
        });
        $(".multiple_pickup_time").each((i, e) => {
            $(e).rules("add", {
                required: true,
                timeFormatMultiple: true,
                notPastTimeMultiple:true,
                messages: {
                    required: this.languageMessage.pickup_time.required,
                },
            });
        });
        $(".multiple_no_of_hours").each((i, e) => {
            $(e).rules("add", {
                required: true,
                digits: true,
                max: 24,
                customMinMultiple: true,
                messages: {
                    required: this.languageMessage.no_of_hours.required,
                    digits: this.languageMessage.no_of_hours.digits,
                    max: this.languageMessage.no_of_hours.max,
                },
            });
        });
        $(".multiple_country_code").each((i, e) => {
            $(e).rules("add", {
                minlength: 1,
                // maxlength: 3,
                digits: true,
                messages: {
                    minlength: this.languageMessage.country_code.min,
                    // maxlength: this.languageMessage.country_code.max,
                    digits: this.languageMessage.country_code.integer,
                },
            });
        });
        $(".multiple_phone").each((i, e) => {
            $(e).rules("add", {
                required: true,
                digits: true,
                minlength: 6,
                // maxlength: 10,
                messages: {
                    required: this.languageMessage.phone.required,
                    // digits: this.languageMessage.phone.regex,
                    minlength: this.languageMessage.phone.min,
                    // maxlength: this.languageMessage.phone.max,
                },
            });
        });
        $(".multiple_total_pax").each((i, e) => {
            $(e).rules("add", {
                required: {
                    depends: function (element) {
                        return (
                            parseInt(
                                $(e)
                                    .closest("tr")
                                    .find(".multiple_service_type_id")
                                    .val()
                            ) != 5
                        );
                    },
                },
                digits: true,
                min: 1,
                max: 45,
                messages: {
                    required: this.languageMessage.total_pax_booking.required,
                    digits: this.languageMessage.total_pax_booking.digits,
                    min: this.languageMessage.total_pax_booking.min,
                    max: this.languageMessage.total_pax_booking.max,
                },
            });
        });
        $(".multiple_total_luggage").each((i, e) => {
            $(e).rules("add", {
                required: {
                    depends: function (element) {
                        return (
                            parseInt(
                                $(e)
                                    .closest("tr")
                                    .find(".multiple_service_type_id")
                                    .val()
                            ) != 5
                        );
                    },
                },
                digits: true,
                max: 100,
                messages: {
                    required:
                        this.languageMessage.total_luggage_booking.required,
                    digits: this.languageMessage.total_luggage_booking.digits,
                    max: this.languageMessage.total_luggage_booking.max,
                },
            });
        });
        $(".multiple_guest_name").each((i, e) => {
            $(e).rules("add", {
                required: true,
                minlength: 3,
                maxlength: 100,
                messages: {
                    required: this.languageMessage.guest_name.required,
                    minlength: this.languageMessage.guest_name.min,
                    maxlength: this.languageMessage.guest_name.max,
                },
            });
        });
    };

    handleNewRow = () => {
        const table = $("#createMultipleBokkingTable tbody"); // Select the tbody element inside the table
        const lastRow = table.find("tr:last"); // Find the last row within the tbody
        const lastId = lastRow.find("td:nth-child(4) input:last").prop("id"); // Find the last input element in the first cell of the last row

        // Extract the current number from the last ID
        const currentNumber = parseInt(lastId.split("_")[1]);

        // Increment the number to generate the new IDs
        const index = currentNumber + 1;
        const serviceTypes = this.props.serviceTypes;
        const vehicleTypes = this.props.vehicleTypes;
        const locations = this.props.locations;
        const hotelClients = this.props.hotelClients;
        const multipleCorporatesHotelData = this.props.multipleCorporatesHotelData;
        const loggedUser = this.props.loggedUser;
        const loggedUserSlug = loggedUser.user_type?.slug ?? null;
        const events = this.props.events;
        const html = $(`
    
                    <tr>
                        <td>
                            <div class="form-group">
                                <select name="multiple_service_type_id[${index}]" id="serviceTypeId_${index}" class="form-control multiple_service_type_id serviceTypeId form-select custom-select" autocomplete="off">
                                    <option value="">Select one</option>
                                    ${serviceTypes
                                        .map(
                                            (type) => `<option value="${type.id}">${type.name}</option>`
                                        )
                                        .join("")}
                                </select>
                            </div>
                        </td>
                        ${
                            loggedUserSlug === null || ["admin", "admin-staff"].includes(loggedUserSlug)
                            ?
                                `<td>
                            <div class="form-group">
                                <select name="multiple_client_id[${index}]" id="clientId_${index}" class="form-control form-select multiple_client_id custom-select" autocomplete="off">
                                    <option value="">Select One</option>
                                    ${hotelClients
                                        .filter(
                                            (client) =>
                                                client.client !== null &&
                                                client.client !== undefined
                                        )
                                        .map(
                                            (client) =>
                                                `<option value="${client.client.id}">
                                                ${client.name} 
                                            </option>`
                                        )
                                        .join("")}
                                </select>
                            </div>
                            </td>`
                            : (multipleCorporatesHotelData.length > 1 ? 
                                    `<td>
                                        <div class="form-group">
                                            <select name="multiple_client_id[${index}]" id="clientId_${index}" class="form-control form-select multiple_client_id custom-select" autocomplete="off">
                                                <option value="">Select One</option>
                                                ${multipleCorporatesHotelData
                                                    .filter(
                                                        (client) =>
                                                            client.client !== null &&
                                                            client.client !== undefined
                                                    )
                                                    .map(
                                                        (client) =>
                                                            `<option value="${client.client.id}">
                                                            ${client.name} 
                                                        </option>`
                                                    )
                                                    .join("")}
                                            </select>
                                        </div>
                                    </td>` : "")}
                    <td>
                        <div class="form-group">
                            <select name="multiple_event_id[${index}]"
                                id="eventId_${index}"
                                class="form-control form-select multiple_event_id custom-select"
                                autocomplete="off">
                                <option value="">Select An Event</option>
                                ${
                                    events !== null && loggedUserSlug !== null && (loggedUserSlug === 'client-admin' || loggedUserSlug === 'client-staff')
                                        ?
                                        `${events
                                            .map(
                                                (event) =>
                                                    `<option value="${event.id}">
                                                    ${event.name} 
                                                </option>`
                                            )
                                            .join("")}`
                                        :
                                        ""
                                }
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <div class="input-group date" id="pickupDatePicker_${index}" data-target-input="nearest">
                                <input type="text" name="multiple_pickup_date[${index}]" value=""  id="pickupDate_${index}" class="form-control multiple_pickup_date datetimepicker-input" data-target="#pickupDatePicker_${index}" placeholder="dd/mm/yyyy" autocomplete="off" autofocus />
                                <div class="input-group-append" data-target="#pickupDatePicker_${index}" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <div class="input-group date" id="pickupTimePicker_${index}" data-target-input="nearest">
                                <input type="text" name="multiple_pickup_time[${index}]" value="" id="pickupTime_${index}" class="form-control multiple_pickup_time datetimepicker-input" data-target="#pickupTimePicker_${index}" placeholder="HH:MM" autocomplete="off" autofocus />
                                <div class="input-group-append" data-target="#pickupTimePicker_${index}" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="margin-bottom : 0 !important; text-align:center;">
                            <input type="checkbox" class="multiple-pickup-time-to-be-advised" name="multiple_pickup_time_to_be_advised[${index}]" id="multiple_pickup_time_to_be_advised_${index}"
                            style="width: 18px; height: 18px; border: 2px solid #a6acaf; border-radius: 4px; 
                            box-shadow: 0px 0px 5px rgba(10,20,30,50%); cursor: pointer; 
                            appearance: none; outline: none; background-color: #fff; display: inline-block;"
                            onclick="this.style.backgroundColor = this.checked ? '#0e161e' : '#fff'; 
                            this.style.borderColor = this.checked ? '#c3c3c3' : '#a6acaf'; 
                            this.style.boxShadow = this.checked ? '0px 0px 8px rgba(15,20,26,80%)' : '0px 0px 5px rgba(10,20,30,50%)';">
                        </div>
                    </td>
                    <td>
                        <div class="form-group flightDetailDiv">
                            <input type="text" id="flightDetail_${index}" value="" name="multiple_flight_detail[${index}]" class="form-control multiple_flight_detail" placeholder="Flight Details" autocomplete="off">
                        </div>
                    </td>
                    <td>
                        <div class="form-group guestNameContainer" style="display:flex; align-items: center; justify-content: center">
                            <input type="text" id="guestName_${index}_0" value="" name="multiple_guest_name[${index}][0]" class="form-control multiple_guest_name" placeholder="Name of Guest(s)" autocomplete="off" autofocus>
                            <button type="button" class="col-sm-3 multiple-add-guest" style="bottom: 8px;"><span class="fa fa-plus mt-3"></span></button>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2 guestContactContainer">
                            <div class="form-group w-50">
                                <input type="text" id="country_code_${index}_0" value="" name="multiple_country_code[${index}][0]" class="form-control multiple_country_code" placeholder="Code" autocomplete="off" autofocus>
                            </div>
                            <div class="form-group">
                                <input type="text" id="phone_${index}_0" value="" name="multiple_phone[${index}][0]" class="form-control multiple_phone" placeholder="Contact" autocomplete="off">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-group totalPaxDiv">
                            <input type="text" id="totalPax_${index}" value="" name="multiple_total_pax[${index}]" class="form-control multiple_total_pax" placeholder="Total Pax" autocomplete="off" autofocus>
                        </div>
                    </td>
                    <td>
                        <div class="form-group totalLuggageDiv">
                            <input type="text" id="totalLuggage_${index}" value="" name="multiple_total_luggage[${index}]" class="form-control multiple_total_luggage" placeholder="Total Luggage" autocomplete="off" autofocus>
                        </div>
                    </td>
                    <td>
                        <div class="form-group noOfHoursTextBox"  style="display: none;">
                            <input type="text" id="noOfHours_${index}" value="" name="multiple_no_of_hours[${index}]" class="form-control multiple_no_of_hours" placeholder="No. of Hours" autocomplete="off">
                        </div>
                    </td>
                    <td>
                        <div class="form-group departureTimeField" style="display:none;">
                            <div class="input-group date" id="departureTimePicker_${index}" data-target-input="nearest">
                                <input type="text" value="" name="multiple_departure_time[${index}]" id="departureTime_${index}" class="form-control multiple_departure_time datetimepicker-input" data-target="#departureTimePicker_${index}" placeholder="dd/mm/yyyy HH:MM" autocomplete="off" autofocus />
                                <div class="input-group-append" data-target="#departureTimePicker_${index}" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                    <div class="d-flex gap-2">
                    <div class="form-group pickUpLocationDropdown" style="display:none;">
                        <select name="multiple_pick_up_location_id[${index}]" id="pickupLocationId_${index}" class="form-control multiple_pick_up_location_id form-select custom-select pickupLocationId" autocomplete="off">
                            <option value="">Select one</option>
                            ${locations
                                .map(
                                    (location) =>
                                        `<option value="${location.id}">${location.name}</option>`
                                )
                                .join("")}
                        </select>
                    </div>
                        <div class="form-group pickUpLocationTextbox">
                            <input type="text" id="pickupLocationtext_${index}" value="" name="multiple_pick_up_location[${index}]" class="form-control multiple_pick_up_location" placeholder="Pick Up Location" autocomplete="off">
                        </div>
                        </div>
                    </td>
                    <td>
                    <div class="d-flex gap-2">
                        <div class="form-group dropOffLocationDropdown" style="display: none;">
                        <select name="multiple_drop_off_location_id[${index}]" id="dropOffLocationId_${index}" class="form-control multiple_drop_off_location_id form-select custom-select dropOffLocationId" autocomplete="off">
                            <option value="">Select one</option>
                            ${locations
                                .map(
                                    (location) =>
                                        `<option value="${location.id}">${location.name}</option>`
                                )
                                .join("")}
                        </select>
                        </div>
                        <div class="form-group dropOffLocationTextBox">
                            <input type="text" id="dropOfLocation_${index}" value="" name="multiple_drop_of_location[${index}]" class="form-control multiple_drop_of_location" placeholder="Drop Off Location" autocomplete="off">
                        </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2 additionalContainer" style="flex-direction: column;">
                            <div class="additionalStopInput col-sm-12" style="display:flex; align-items: flex-start; justify-content: center;">
                                <input type="text" id="multipleAdditionalStops_${index}_0" value="" name="multiple_additional_stops[${index}][0]" class="form-control col-sm-9 multiple_additional_stops" placeholder="Additional Stops" autocomplete="off">
                                <button type="button" class="col-sm-3 multiple-add-stop"><span class="fa fa-plus mt-3"></span></button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <select name="multiple_vehicle_type_id[${index}]" id="vehicleType_${index}" class="form-control form-select multiple_vehicle_type_id custom-select" autocomplete="off">
                                <option value="">Select one</option>
                                ${vehicleTypes
                                    .map(
                                        (type) =>
                                            `<option value="${type.id}"  data-seating-capacity="${type.seating_capacity}">${type.name} (${type.seating_capacity})s</option>`
                                    )
                                    .join("")}
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <input type="text" id="clientInstructions_${index}" value="" name="multiple_client_instructions[${index}]" value="" class="form-control multiple_client_instructions" placeholder="Client Instructions" autocomplete="off" autofocus />
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <select name="multiple_child_seat_required[]" id="child_seat_required_${index}" class="form-control form-select custom-select multiple_child_seat_required" autocomplete="off">
                                <option value="yes">Yes</option>
                                <option value="no" selected>No</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <select name="multiple_no_of_seats_required[]" id="no_of_seats_required_${index}" class="form-control form-select custom-select multiple_no_of_seats_required" autocomplete="off" style="display:none;">
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <select name="multiple_child_1_age[]"
                                id="child_1_age_${index}"
                                class="form-control form-select custom-select multiple_child_1_age"autocomplete="off" style="display:none;">
                                <option value="<1 yo" selected>&lt;1 yo</option>
                                <option value="1 yo">1 yo</option>
                                <option value="2 yo">2 yo</option>
                                <option value="3 yo">3 yo</option>
                                <option value="4 yo">4 yo</option>
                                <option value="5 yo">5 yo</option>
                                <option value="6 yo">6 yo</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <select name="multiple_child_2_age[]"
                                id="child_2_age_${index}"
                                class="form-control form-select custom-select multiple_child_2_age" autocomplete="off" style="display:none;">
                                <option value="<1 yo" selected>&lt;1 yo</option>
                                <option value="1 yo">1 yo</option>
                                <option value="2 yo">2 yo</option>
                                <option value="3 yo">3 yo</option>
                                <option value="4 yo">4 yo</option>
                                <option value="5 yo">5 yo</option>
                                <option value="6 yo">6 yo</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <input type="file" id="attachment_${index}" name="multiple_attachment[${index}]" class="form-control multiple_attachment" placeholder="File">
                        </div>
                    </td>
                    <td>
                        <div class="form-check mt-2 crossBorderField" style="display: none;">
                            <input class="form-check-input multiple_is_cross_border" value="" type="checkbox" id="crossBorder_${index}" name="multiple_is_cross_border[${index}]">
                            <label for="crossBorder_${index}" class="form-check-label"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="remove-booking-row"><span class="fas fa-times mt-3 text-danger"></span></button>
                    </td>
</tr>`);
        table.append(html);
        this.initializeDatePicker(
            `pickupDatePicker_${index}`,
            `pickupTimePicker_${index}`
        );
        this.initializeTimePicker(`pickupTimePicker_${index}`);
        this.initializeDateTimePicker(`departureTimePicker_${index}`);
        this.initializeGoogleMapAutoComplete(`pickupLocationtext_${index}`);
        this.initializeGoogleMapAutoComplete(`dropOfLocation_${index}`);
        this.initializeGoogleMapAutoComplete(`multipleAdditionalStops_${index}_0`);
        this.handleInitilizeValidation();
    };

    handlePickupLocationField = ({ target }) => {
        if ($(target).attr("id") === "serviceTypeId") {
            switch (parseInt($(target).val())) {
                case 1:
                    $("#pickupLocationDropdown").show();
                    $("#pickupLocationTextBox").hide();
                    $("#departureTimeField").hide();
                    $("#crossBorderField").hide();
                    $("#dropoffLocationDropdown").hide();
                    $("#dropOffLocationTextBox").show();
                    $("#dropOffSpan").show();
                    $("#noOfHoursTextBox").hide();
                    $("#totalLuggageSpan, #totalPaxSpan").show();
                    $("#flightDetailSpan").show();
                    $(
                        "#totalLuggageDiv, #totalPaxDiv, #flightDetailDiv"
                    ).show();
                    break;
                case 3:
                    $("#pickupLocationDropdown").hide();
                    $("#pickupLocationTextBox").show();
                    $("#departureTimeField").show();
                    $("#crossBorderField").hide();
                    $("#dropoffLocationDropdown").show();
                    $("#dropOffLocationTextBox").hide();
                    $("#dropOffSpan").show();
                    $("#noOfHoursTextBox").hide();
                    $("#totalLuggageSpan, #totalPaxSpan").show();
                    $("#flightDetailSpan").show();
                    $(
                        "#totalLuggageDiv, #totalPaxDiv, #flightDetailDiv"
                    ).show();
                    break;
                case 4:
                    $("#pickupLocationDropdown").hide();
                    $("#pickupLocationTextBox").show();
                    $("#crossBorderField").show();
                    $("#departureTimeField").hide();
                    $("#dropoffLocationDropdown").hide();
                    $("#dropOffLocationTextBox").show();
                    $("#dropOffSpan").hide();
                    $("#noOfHoursTextBox").show();
                    $("#totalLuggageSpan, #totalPaxSpan").show();
                    $("#flightDetailSpan").hide();
                    $(
                        "#totalLuggageDiv, #totalPaxDiv, #flightDetailDiv"
                    ).show();
                    break;
                case 5:
                    $("#pickupLocationDropdown").hide();
                    $("#pickupLocationTextBox").show();
                    $("#departureTimeField").hide();
                    $("#crossBorderField").hide();
                    $("#dropoffLocationDropdown").hide();
                    $("#dropOffLocationTextBox").show();
                    $("#dropOffSpan").show();
                    $("#noOfHoursTextBox").hide();
                    $("#totalLuggageSpan, #totalPaxSpan").hide();
                    $("#flightDetailSpan").hide();
                    $(
                        "#totalLuggageDiv, #totalPaxDiv, #flightDetailDiv"
                    ).hide();
                    break;
                default:
                    $("#pickupLocationDropdown").hide();
                    $("#pickupLocationTextBox").show();
                    $("#departureTimeField").hide();
                    $("#crossBorderField").hide();
                    $("#dropoffLocationDropdown").hide();
                    $("#dropOffLocationTextBox").show();
                    $("#dropOffSpan").show();
                    $("#noOfHoursTextBox").hide();
                    $("#totalLuggageSpan, #totalPaxSpan").show();
                    $("#flightDetailSpan").hide();
                    $(
                        "#totalLuggageDiv, #totalPaxDiv, #flightDetailDiv"
                    ).show();
            }
        } else if ($(target).attr("id") === "pickupLocationId") {
            if (parseInt($(target).val()) === 8) {
                $("#pickupLocationTextBox").show();
            } else {
                $("#pickupLocationTextBox").hide();
            }
        } else if ($(target).attr("id") === "dropoffLocationId") {
            if (parseInt($(target).val()) === 8) {
                $("#dropOffLocationTextBox").show();
                
            } else {
                $("#dropOffLocationTextBox").hide();
                
            }
        }
        $("#createBookingForm").validate().resetForm();
    };

    handleSaveBooking = () => {
        $.validator.addMethod(
            "notPastTime",
            function (value, element) {
                // Get the pickup date and time from the form
                const pickupDate = $("#pickupDate").val();
                const [pickupHours, pickupMinutes] = value.split(":").map(Number);
                
                // Parse the pickup date
                const [day, month, year] = pickupDate.split("/").map(Number);
                const selectedDateTime = new Date(year, month - 1, day, pickupHours, pickupMinutes);
        
                // Get the current date and time
                const now = new Date();
        
                // If the pickup date is today, check if the pickup time is in the future
                if (selectedDateTime.getFullYear() === now.getFullYear() &&
                    selectedDateTime.getMonth() === now.getMonth() &&
                    selectedDateTime.getDate() === now.getDate()) {
                    return selectedDateTime >= now;
                }
        
                // If the pickup date is not today, it's valid
                return true;
            },
            this.languageMessage.pickup_time.notPastTime
        );
        
        $.validator.addMethod(
            "notPastDate",
            function (value, element) {
                // Parse the pickup date from the form
                const [day, month, year] = value.split("/").map(Number);
                const pickupDate = new Date(year, month - 1, day);
                
                // Get today's date and set the time to 00:00:00
                const today = new Date();
                today.setHours(0, 0, 0, 0);
        
                // Check if pickupDate is today or in the future
                return pickupDate >= today;
            },
            this.languageMessage.pickup_date.notPastDate
        );
        
        $.validator.addMethod(
            "dateTimeFormat",
            function (value, element) {
                // Regular expression to match the format "dd/mm/yyyy hh:mm"
                return (
                    this.optional(element) ||
                    /^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/.test(value)
                );
            },
            this.languageMessage.customDateFormat
        );

        $.validator.addMethod(
            "dateFormat",
            function (value, element) {
                // Check if the value matches the dd/MM/yyyy format
                return value.match(/^\d{2}\/\d{2}\/\d{4}$/);
            },
            this.languageMessage.pickup_date.regex
        );
        $.validator.addMethod(
            "timeFormat",
            function (value, element) {
                // Check if the value matches the HH:mm format
                return value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/);
            },
            this.languageMessage.time_format
        );
        $.validator.addMethod(
            "customMin",
            (value, element) => {
                const selectedSeatingCapacity = parseInt(
                    $("#vehicleType option:selected").data("seating-capacity")
                );
                if ($("#crossBorder").is(":checked")) {
                    return parseInt(value) >= 6;
                } else if (!selectedSeatingCapacity) {
                    return parseInt(value) >= 3;
                } else if (selectedSeatingCapacity <= 13) {
                    return parseInt(value) >= 3;
                } else if (selectedSeatingCapacity > 13) {
                    return parseInt(value) >= 4;
                }
            },
            (params, element) => {
                const selectedSeatingCapacity = parseInt(
                    $("#vehicleType option:selected").data("seating-capacity")
                );
                if ($("#crossBorder").is(":checked")) {
                    return this.languageMessage.no_of_hours.min_cross_border;
                } else if (!selectedSeatingCapacity) {
                    return this.languageMessage.no_of_hours.min;
                } else if (selectedSeatingCapacity <= 13) {
                    return this.languageMessage.no_of_hours
                        .min_less_then_13_seat;
                } else if (selectedSeatingCapacity > 13) {
                    return this.languageMessage.no_of_hours.min_greater_13_seat;
                }
            }
        );
        // Custom method to check file size
        $.validator.addMethod(
            "validFileSize",
            function (value, element) {
                const maxSize = 5 * 1024 * 1024; // 5 MB in bytes
                if (element.files.length) {
                    const fileSize = element.files[0].size;

                    // Check file size
                    if (fileSize > maxSize) {
                        return false;
                    }
                }
                return true;
            },
            this.languageMessage.attachment.validFileSize
        );
        $.validator.addMethod(
            "departureDateTimeCheck",
            function (value, element) {
                // Retrieve pickup date and time values
                const pickupDate = $("#pickupDate").val();
                const pickupTime = $("#pickupTime").val();
                if (!pickupDate || !pickupTime || !value) {
                    return true;
                }
                // Helper function to parse date strings in the format 'dd/mm/yyyy hh:mm'
                function parseDateTime(dateTimeStr) {
                    const [datePart, timePart] = dateTimeStr.split(" ");
                    const [day, month, year] = datePart.split("/").map(Number);
                    const [hours, minutes] = timePart.split(":").map(Number);
                    return new Date(year, month - 1, day, hours, minutes);
                }
                // Combine pickup date and time into a single string
                const pickupDateTimeStr = pickupDate + " " + pickupTime;

                // Parse the pickup date and time
                const pickupDateTime = parseDateTime(pickupDateTimeStr);

                // Parse the departure date and time
                const departureDateTime = parseDateTime(value);

                // Compare departure date and time with pickup date and time
                return departureDateTime > pickupDateTime;
            },
            this.languageMessage.laterThan
        );

        $("#createBookingForm").validate({
            rules: {
                service_type_id: {
                    required: true,
                },
                attachment: {
                    extension: "jpg|jpeg|png|gif|doc|docx|txt|pdf|xls|xlsx",
                    validFileSize: true,
                },
                flight_detail: {
                    required: {
                        depends: function (element) {
                            return (
                                $.inArray($("#serviceTypeId").val(), [
                                    "1",
                                    "3",
                                ]) != -1
                            );
                        },
                    },
                    minlength: 3,
                    maxlength: 50,
                },
                pick_up_location: {
                    required: true,
                    minlength: 3,
                },
                drop_of_location: {
                    required: {
                        depends: function (element) {
                            return $("#serviceTypeId").val() != 4;
                        },
                    },
                    minlength: 3,
                },
                vehicle_type_id: {
                    required: true,
                },
                pickup_date: {
                    required: true,
                    dateFormat: true,
                    notPastDate: true,
                },
                pickup_time: {
                    required: true,
                    timeFormat: true,
                    notPastTime: true, 
                },
                no_of_hours: {
                    required: true,
                    digits: true,
                    customMin: true,
                    max: 24,
                },
                departure_time: {
                    dateTimeFormat: true,
                    departureDateTimeCheck: true,
                },
                country_code: {
                    minlength: 1,
                    // maxlength: 3,
                    digits: true,
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    // maxlength: 10,
                },
                total_pax: {
                    required: {
                        depends: function (element) {
                            return $("#serviceTypeId").val() != 5;
                        },
                    },
                    digits: true,
                    min: 1,
                    max: 45,
                },
                total_luggage: {
                    required: {
                        depends: function (element) {
                            return $("#serviceTypeId").val() != 5;
                        },
                    },
                    digits: true,
                    max: 100,
                },
                client_id: {
                    required: true,
                },
                event_id: {
                    required: true,
                },
                child_seat_required: {
                    required: true
                },
                "guest_name[]": {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                },
            },
            messages: {
                departure_time: {
                    // required: this.languageMessage.departure_time.required,
                },
                client_id: {
                    required: this.languageMessage.client_id.required,
                },
                event_id: {
                    required: this.languageMessage.event_id.required,
                },
                service_type_id: {
                    required: this.languageMessage.service_type_id.required,
                },
                attachment: {
                    extension: this.languageMessage.attachment.extension,
                },
                flight_detail: {
                    required: this.languageMessage.flight_detail.required,
                    minlength: this.languageMessage.flight_detail.min,
                    maxlength: this.languageMessage.flight_detail.max,
                },
                pick_up_location: {
                    required: this.languageMessage.pick_up_location.required,
                    minlength: this.languageMessage.pick_up_location.min,
                },
                drop_of_location: {
                    required: this.languageMessage.drop_of_location.required,
                    minlength: this.languageMessage.drop_of_location.min,
                },
                vehicle_type_id: {
                    required: this.languageMessage.vehicle_type_id.required,
                },
                pickup_date: {
                    required: this.languageMessage.pickup_date.required,
                },
                pickup_time: {
                    required: this.languageMessage.pickup_time.required,
                },
                no_of_hours: {
                    required: this.languageMessage.no_of_hours.required,
                    digits: this.languageMessage.no_of_hours.digits,
                    max: this.languageMessage.no_of_hours.max,
                },
                country_code: {
                    required: this.languageMessage.country_code.required,
                    minlength: this.languageMessage.country_code.min,
                    // maxlength: this.languageMessage.country_code.max,
                    digits: this.languageMessage.country_code.integer,
                },
                phone: {
                    required: this.languageMessage.phone.required,
                    // digits: this.languageMessage.phone.regex,
                    minlength: this.languageMessage.phone.min,
                    // maxlength: this.languageMessage.phone.max,
                },
                total_pax: {
                    required: this.languageMessage.total_pax_booking.required,
                    digits: this.languageMessage.total_pax_booking.digits,
                    min: this.languageMessage.total_pax_booking.min,
                    max: this.languageMessage.total_pax_booking.max,
                },
                total_luggage: {
                    required:
                        this.languageMessage.total_luggage_booking.required,
                    digits: this.languageMessage.total_luggage_booking.digits,
                    max: this.languageMessage.total_luggage_booking.max,
                },
                child_seat_required: {
                    required: this.languageMessage.child_seat_required.required,
                },
                "guest_name[]": {
                    required: this.languageMessage.guest_name.required,
                    minlength: this.languageMessage.guest_name.min,
                    maxlength: this.languageMessage.guest_name.max,
                },
                // Add custom error messages for other fields here
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                element
                    .closest(".form-group")
                    .find(".invalid-feedback")
                    .remove();
                error.addClass("invalid-feedback");
                element.closest(".form-group").append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            },
            submitHandler: (form) => {
                const saveButton = $("#addBookingFormButton"); // Select the button by ID or class
                saveButton.prop("disabled", true); // Disable the button
                saveButton.html('<span class="spinner-border spinner-border-sm"></span> Saving...'); // Show loader
            
                const isBlackOutPeriod = this.checkDateBetweenPeakPeriods(
                    $("#pickupDate").val()
                );
                const loggedUser = this.props.loggedUser;
                const noOfHours = $("#noOfHours").val();
                const isClientUser =
                    loggedUser.user_type && loggedUser.user_type.type === "client"
                        ? true
                        : false;
                const isDisposalService =
                    $("#serviceTypeId").val() && $("#serviceTypeId").val() === "4"
                        ? true
                        : false;
            
                if (isDisposalService && isBlackOutPeriod && isClientUser && noOfHours < 10) {
                    this.openModal("blcackOutModal");
                    
                    $("#yesSubmitForm")
                        .off("click")
                        .on("click", () => {
                            $("#blcackOutModal").modal("hide");
                            form.submit();
                        });
            
                    // Re-enable the button since form is not submitted yet
                    saveButton.prop("disabled", false);
                    saveButton.html("Save");
                } else {
                    form.submit();
                }
            },
        });
    };

    handleSaveEvent = () => {
        try {
            const hotel_id = $('#clientIdForEvent').val();
            const name = $('#event_name').val();
            const url = this.props.routes.createEventByAjax;
            
            const formData = new FormData();
            formData.append("hotel_id", hotel_id);
            formData.append("name", name);
            formData.append("status", 'ACTIVE');
            
            if(hotel_id == '' || name == '')
            {
                if(hotel_id == '')
                {
                    $('#hotel_for_event_error').css('display', 'inline-block');
                }
                if(name == '')
                {
                    $('#event_name_error').css('display', 'inline-block');
                }
            }else{
                $('#hotel_for_event_error').css('display', 'none');
                $('#event_name_error').css('display', 'none');

                const saveButton = $("#addEventFormButton"); // Select the button by ID or class
                saveButton.prop("disabled", true); // Disable the button
                saveButton.html('<span class="spinner-border spinner-border-sm"></span> Saving...');
                axios.post(url, formData)
                    .then((response) => {
                        const statusCode = response.data.status.code;
                        const message = response.data.status.message;
                        const flash = new ErrorHandler(statusCode, message);
                        if (statusCode === 200) {
                            this.getEventsAfterCreateEvent({hotelId : hotel_id})
                            this.closeModal("addEventModal");
                        }
                        throw flash;
                    })
                    .catch((error) => {
                        $("#loader").hide();
                        this.handleException(error);
                    });
            }

        } catch (error) {
            this.handleException(error);
        }
    }
    initializeChildSeatRequest = () => {
        $(function() {
            $("input[name='child_seat_required']").on("change", function() {
                if ($("#childSeatYes").is(":checked")) {
                    $("#childSeatOptions").slideDown();
                    updateChildInputs();
                } else {
                    $("#childSeatOptions").slideUp();
                    $("#childSeatCount").val("1"); // Reset to default
                    $("#childAgeInputs").empty();  // Clear child age fields
                }
            });
        
            $("#childSeatCount").on("change", function() {
                updateChildInputs();
            });
        
            function updateChildInputs() {
                let count = parseInt($("#childSeatCount").val(), 10);
                let childAgeInputs = $("#childAgeInputs");
                childAgeInputs.empty();
        
                for (let i = 1; i <= count; i++) {
                    childAgeInputs.append(`
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Child ${i}:</label>
                                <select class="form-select child-age" name="child_${i}_age">
                                    <option value="<1 yo" selected>&lt;1 yo</option>
                                    <option value="1 yo">1 yo</option>
                                    <option value="2 yo">2 yo</option>
                                    <option value="3 yo">3 yo</option>
                                    <option value="4 yo">4 yo</option>
                                    <option value="5 yo">5 yo</option>
                                    <option value="6 yo">6 yo</option>
                                </select>
                            </div>
                        </div>
                    `);
                }
            }
        });
    }

    handleMultipleChildSeatRequest = () => {

    }
    handleRemoveGuest = ({ target }) => {
        $(target).closest(".col-md-1").prev(".col-md-3").remove();
        $(target).closest(".col-md-1").remove();
    };

    handleAddGuest = () => {
        const lastGuestInputContainer = $(".guest-name")
            .last()
            .closest(".col-md-3");
        const lastId = parseInt(
            lastGuestInputContainer.find(".guest-name").attr("id").split("_")[1]
        );
        const newId = lastId + 1;
        const newGuestInput = `
            <div class="col-md-3">
                <div class="form-group">
                    <label for="guestName_${newId}">Name of Guest(s) <span class="text-danger">*</span></label>
                    <input type="text" id="guestName_${newId}" name="guest_name[]" class="form-control guest-name" placeholder="Name of Guest(s)" autocomplete="off" autofocus>
                </div>
            </div>`;

        const newGuestRemoveEl = `
            <div class="col-md-1 mt-4 iconContainer">
                <button type="button" class="remove-guest"><span class="fas fa-times mt-3 text-danger"></span></button>
            </div>`;

        $(newGuestInput).insertAfter(lastGuestInputContainer);
        $(newGuestRemoveEl).insertAfter(lastGuestInputContainer);
    };

    handleRemoveStop = ({ target }) => {
        $(target).closest(".col-md-1").prev(".col-md-3").remove();
        $(target).closest(".col-md-1").remove();
        this.initializeAdditionalStopLimits();
    };

    handleAddStop = () => {
        if($('#serviceTypeId').val() == 1 || $('#serviceTypeId').val() == 2 || $('#serviceTypeId').val() == 3)
        {
            if($('.additional-stops').length >= 2)
            {
                $('#addStop').hide();
            }else
            {                
                const lastStopInputContainer = $(".additional-stops")
                    .last()
                    .closest(".col-md-3");
                const lastId = parseInt(
                    lastStopInputContainer.find(".additional-stops").attr("id").split("_")[1]
                );
                const newId = lastId + 1;
                const newStopInput = `
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="additionalStops_${newId}">Additional Stop(s) <span class="text-danger">*</span></label>
                            <input type="text" id="additionalStops_${newId}" name="additional_stops[]" class="form-control additional-stops" placeholder="Additional Stop(s)" autocomplete="off" autofocus>
                        </div>
                    </div>`;

                const newStopRemoveEl = `
                    <div class="col-md-1 mt-4 iconContainer">
                        <button type="button" class="remove-stop"><span class="fas fa-times mt-3 text-danger"></span></button>
                    </div>`;

                    
                this.initializeGoogleMapAutoComplete(`additionalStops_${newId}`);
                $(newStopInput).insertAfter(lastStopInputContainer);
                $(newStopRemoveEl).insertAfter(lastStopInputContainer);
            }
        }else{            
            const lastStopInputContainer = $(".additional-stops")
                .last()
                .closest(".col-md-3");
            const lastId = parseInt(
                lastStopInputContainer.find(".additional-stops").attr("id").split("_")[1]
            );
            const newId = lastId + 1;
            const newStopInput = `
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="additionalStops_${newId}">Additional Stop(s) <span class="text-danger">*</span></label>
                        <input type="text" id="additionalStops_${newId}" name="additional_stops[]" class="form-control additional-stops" placeholder="Additional Stop(s)" autocomplete="off" autofocus>
                    </div>
                </div>`;

            const newStopRemoveEl = `
                <div class="col-md-1 mt-4 iconContainer">
                    <button type="button" class="remove-stop"><span class="fas fa-times mt-3 text-danger"></span></button>
                </div>`;

                
            this.initializeGoogleMapAutoComplete(`additionalStops_${newId}`);
            $(newStopInput).insertAfter(lastStopInputContainer);
            $(newStopRemoveEl).insertAfter(lastStopInputContainer);
        }
        this.initializeAdditionalStopLimits();
    };

    initializeAdditionalStopLimits = () => {
        if($('#serviceTypeId').val() == 1 || $('#serviceTypeId').val() == 2 || $('#serviceTypeId').val() == 3)
        {
            if($('.additional-stops').length >= 2)
            {
                $('#addStop').hide();
            }else
            {
                $('#addStop').show();
            }

            if($('.additional-stops').length > 2){
                $('.additional-stops').slice(2).closest('.col-md-3').remove();
                $('.remove-stop').slice(1).closest('.col-md-1').remove();
            }
        }else{
            $('#addStop').show();
        }
    };

    handleMultipleAddStop = ({ target }) => {
        const lastStopInputContainer = $(target)
            .closest('.additionalStopInput')
            .parent('.additionalContainer')
            .find('.col-sm-12')
            .last();
    
        const lastStopInput = lastStopInputContainer.find('.multiple_additional_stops').last();
        const [_, lastId, lastStopId] = lastStopInput.attr('id').split('_').map(Number);
    
        const serviceType = $(`#serviceTypeId_${lastId}`).val();
        const additionalStopsCount = $(`.multiple_additional_stops[id^="multipleAdditionalStops_${lastId}_"]`).length;
    
        const createNewStopInput = (lastId, newStopId) => {
            const newStopInput = `
                <div class="additionalStopInput col-sm-12" style="display:flex; align-items: flex-start; justify-content: center;">
                    <input type="text" id="multipleAdditionalStops_${lastId}_${newStopId}"
                        name="multiple_additional_stops[${lastId}][${newStopId}]"
                        class="form-control col-sm-9 multiple_additional_stops"
                        placeholder="Additional Stop(s)" autocomplete="off" autofocus>
                    <button type="button" class="col-sm-3 multiple-remove-stop">
                        <span class="fas fa-times mt-3 text-danger"></span>
                    </button>
                </div>`;
    
                $(newStopInput).insertAfter(lastStopInputContainer);
                this.handleMultipleAdditionalStopIds();
                this.initializeMultipleAdditionalStopLimits({lastId});
        };
            
        const newStopId = lastStopId + 1;
        this.initializeGoogleMapAutoComplete(`multipleAdditionalStops_${lastId}_${newStopId}`);
    
        if (serviceType == 1 || serviceType == 2 || serviceType == 3) {
            if (additionalStopsCount >= 2) {
                $(`#multipleAdditionalStops_${lastId}_0`).closest('.col-sm-12').find('.multiple-add-stop').hide();
            } else {
                createNewStopInput(lastId, newStopId);
            }
        } else {
            createNewStopInput(lastId, newStopId);
        }
    };
    

    handleMultipleRemoveStop = ({ target }) => {
        const lastId = $(target).closest('.col-sm-12').find('.multiple_additional_stops').attr('id').split('_')[1];
        $(target).closest(".col-sm-12").remove();
        this.handleMultipleAdditionalStopIds();
        this.initializeMultipleAdditionalStopLimits({lastId});
    };

    handleMultipleAdditionalStopIds = () => {
        let rowCounters = {};
    
        $(".multiple_additional_stops").each(function () {
            let $this = $(this);
            let oldId = $this.attr('id');
    
            let match = oldId.match(/multipleAdditionalStops_(\d+)_/);
            if (match) {
                let i = match[1];
    
                if (typeof rowCounters[i] === 'undefined') {
                    rowCounters[i] = 0;
                }
    
                $this.attr('id', `multipleAdditionalStops_${i}_${rowCounters[i]}`);
                $this.attr('name', `multiple_additional_stops[${i}][${rowCounters[i]}]`);
    
                rowCounters[i]++;
            }
        });
    };

    initializeMultipleAdditionalStopLimits = ({lastId}) => {
        const serviceType = $(`#serviceTypeId_${lastId}`).val();
        const additionalStopsCount = $(`.multiple_additional_stops[id^="multipleAdditionalStops_${lastId}_"]`).length;

        if (serviceType == 1 || serviceType == 2 || serviceType == 3) {
            if (additionalStopsCount >= 2) {
                $(`#multipleAdditionalStops_${lastId}_0`).closest('.col-sm-12').find('.multiple-add-stop').hide();
            } else {
                $(`#multipleAdditionalStops_${lastId}_0`).closest('.col-sm-12').find('.multiple-add-stop').show();
            }
        } else {
            $(`#multipleAdditionalStops_${lastId}_0`).closest('.col-sm-12').find('.multiple-add-stop').show();
        }
    };

    handleMultipleAdditionalStopAddButtonOnServiceType = ({target}) => {
        const lastId = $(target).attr('id').split('_')[1];
        const serviceType = $(`#serviceTypeId_${lastId}`).val();
        const additionalStopsCount = $(`.multiple_additional_stops[id^="multipleAdditionalStops_${lastId}_"]`).length;

        if (serviceType == 1 || serviceType == 2 || serviceType == 3) {
            if (additionalStopsCount >= 2) {
                $(`#multipleAdditionalStops_${lastId}_0`).closest('.col-sm-12').find('.multiple-add-stop').hide();
            } else {
                $(`#multipleAdditionalStops_${lastId}_0`).closest('.col-sm-12').find('.multiple-add-stop').show();
            }

            if(additionalStopsCount > 2)
            {
                $(`.multiple_additional_stops[id^="multipleAdditionalStops_${lastId}_"]`).slice(2).closest('.col-sm-12').remove();
                this.handleMultipleAdditionalStopIds();
            }
        } else {
            $(`#multipleAdditionalStops_${lastId}_0`).closest('.col-sm-12').find('.multiple-add-stop').show();
        }
    };

    handleMultipleAddGuest = ({ target }) => {
        const lastGuestInputContainer = $(target).closest('.guestNameContainer');

        const lastId = parseInt(
            lastGuestInputContainer.find(".multiple_guest_name").attr("id").split("_")[1]
        );
        const lastGuestId = parseInt(
            lastGuestInputContainer.find(".multiple_guest_name").attr("id").split("_")[2]
        );

        const newGuestId = lastGuestId + 1;

        const newGuestInput = `
                    <div class="form-group guestNameContainer" style="display:flex; align-items: center; justify-content: center">
                        <input type="text" id="guestName_${lastId}_${newGuestId}"
                            name="multiple_guest_name[${lastId}][${newGuestId}]"
                            value=""
                            class="form-control multiple_guest_name"
                            placeholder="Name of Guest(s)" autocomplete="off" autofocus>
                            <button type="button" class="col-sm-3 multiple-remove-guest" style="bottom: 8px;"><span class="fas fa-times mt-3 text-danger"></span></button>
                    </div>`;

        const newGuestContactInput = `
                    <div class="d-flex gap-2 guestContactContainer">
                        <div class="form-group w-50">
                            <input type="text" id="country_code_${lastId}_${newGuestId}"
                                name="multiple_country_code[${lastId}][${newGuestId}]"
                                value=""
                                class="form-control multiple_country_code"
                                placeholder="code" autocomplete="off" autofocus>
                        </div>
                        <div class="form-group">
                            <input type="text" id="phone_${lastId}_${newGuestId}"
                                name="multiple_phone[${lastId}][${newGuestId}]"
                                value=""
                                class="form-control multiple_phone"
                                placeholder="Contact" autocomplete="off">
                        </div>
                    </div>`;

                    
        $(newGuestInput).insertAfter(lastGuestInputContainer);

        $(newGuestContactInput).insertAfter($('#country_code_' + lastId + '_' + lastGuestId).closest('.guestContactContainer'));
        
        $("#guestName_" + lastId + "_" + newGuestId).each((i, e) => {
            $(e).rules("add", {
                required: true,
                minlength: 3,
                maxlength: 100,
                messages: {
                    required: this.languageMessage.guest_name.required,
                    minlength: this.languageMessage.guest_name.min,
                    maxlength: this.languageMessage.guest_name.max,
                },
            });
        });
        
        $("#phone_" + lastId + "_" + lastGuestId).each((i, e) => {
            $(e).rules("add", {
                required: true,
                digits: true,
                minlength: 6,
                // maxlength: 10,
                messages: {
                    required: this.languageMessage.phone.required,
                    // digits: this.languageMessage.phone.regex,
                    minlength: this.languageMessage.phone.min,
                    // maxlength: this.languageMessage.phone.max,
                },
            });
        });
        $("#country_code_" + lastId + "_" + lastGuestId).each((i, e) => {
            $(e).rules("add", {
                minlength: 1,
                // maxlength: 3,
                digits: true,
                messages: {
                    minlength: this.languageMessage.country_code.min,
                    // maxlength: this.languageMessage.country_code.max,
                    digits: this.languageMessage.country_code.integer,
                },
            });
        });
        this.initializeMultipleGuestIds();
        this.initializeMultipleGuestContactCodeIds();
        this.initializeMultipleGuestContactPhoneIds();
    };

    handleRemoveMultipleGuest = ({ target }) => {
        const lastGuestId = $(target).closest('.guestNameContainer').find('.multiple_guest_name').attr('id').split('_');
        $(target).closest('.guestNameContainer').remove();
        $('#country_code_' + lastGuestId[1] + '_' + lastGuestId[2]).closest('.guestContactContainer').remove();
        this.initializeMultipleGuestIds();
        this.initializeMultipleGuestContactCodeIds();
        this.initializeMultipleGuestContactPhoneIds();
    };
    
    initializeMultipleGuestIds = () => {
        let rowCounters = {};
    
        $(".multiple_guest_name").each(function () {
            let $this = $(this);
            let oldId = $this.attr('id');
    
            let match = oldId.match(/guestName_(\d+)_/);
            if (match) {
                let i = match[1];
    
                if (typeof rowCounters[i] === 'undefined') {
                    rowCounters[i] = 0;
                }
    
                $this.attr('id', `guestName_${i}_${rowCounters[i]}`);
                $this.attr('name', `multiple_guest_name[${i}][${rowCounters[i]}]`);
    
                rowCounters[i]++;
            }
        });
    };
    
    initializeMultipleGuestContactCodeIds = () => {
        let rowCounters = {};
    
        $(".multiple_country_code").each(function () {
            let $this = $(this);
            let oldId = $this.attr('id');
    
            let match = oldId.match(/country_code_(\d+)_/);
            if (match) {
                let i = match[1];
    
                if (typeof rowCounters[i] === 'undefined') {
                    rowCounters[i] = 0;
                }
    
                $this.attr('id', `country_code_${i}_${rowCounters[i]}`);
                $this.attr('name', `multiple_country_code[${i}][${rowCounters[i]}]`);
    
                rowCounters[i]++;
            }
        });
    };
    
    initializeMultipleGuestContactPhoneIds = () => {
        let rowCounters = {};
    
        $(".multiple_phone").each(function () {
            let $this = $(this);
            let oldId = $this.attr('id');
    
            let match = oldId.match(/phone_(\d+)_/);
            if (match) {
                let i = match[1];
    
                if (typeof rowCounters[i] === 'undefined') {
                    rowCounters[i] = 0;
                }
    
                $this.attr('id', `phone_${i}_${rowCounters[i]}`);
                $this.attr('name', `multiple_phone[${i}][${rowCounters[i]}]`);
    
                rowCounters[i]++;
            }
        });
    };
    

    handleOnLoad = () => {
        if (this.props.isCreatePage) {
            this.initializeDatePicker("pickupDatePicker", "pickupTimePicker");
            this.initializeTimePicker("pickupTimePicker");
            this.initializeDateTimePicker("departureTimePicker");
            //for multiple booking
            this.initializeDatePicker(
                "pickupDatePicker_0",
                "pickupTimePicker_0"
            );
            this.initializeTimePicker("pickupTimePicker_0");
            this.initializeDateTimePicker("departureTimePicker_0");
            //google map auto complete
            this.initializeGoogleMapAutoComplete("pickupLocationtext");
            this.initializeGoogleMapAutoComplete("dropOfLocation");
            this.initializeGoogleMapAutoComplete("pickupLocationtext_0");
            this.initializeGoogleMapAutoComplete("dropOfLocation_0");
            this.initializeGoogleMapAutoComplete("additionalStops_0");
            this.initializeGoogleMapAutoComplete("multipleAdditionalStops_0_0");
            
            this.handleSaveMultpleBooking();
            this.handleSaveBooking();
            this.initializeChildSeatRequest();
        } else {
            $("#bookingTable").checkboxTable();
            let todayEndDate = moment().endOf("day");

            if (
                this.props.userTypeId === null ||
                this.props.userTypeId === 1 ||
                this.props.userTypeId === 2
            ) {
                todayEndDate = moment()
                    .add(1, "days")
                    .startOf("day")
                    .add(4, "hours");
            }

            $("#pickupDateBooking").daterangepicker(
                {
                    startDate: moment().startOf("day"),
                    endDate: todayEndDate,
                    parentEl: ".wrapper",
                    timePicker: true,
                    timePicker24Hour: true,
                    timePickerIncrement: 1,
                    ranges: {
                        Today: [moment().startOf("day"), todayEndDate],
                        Tomorrow: [
                            moment().add(1, "day").startOf("day"),
                            moment().add(1, "day").endOf("day"),
                        ],
                        Yesterday: [
                            moment().subtract(1, "days").startOf("day"),
                            moment().subtract(1, "days").endOf("day"),
                        ],
                        "Last 7 Days": [
                            moment().subtract(6, "days").startOf("day"),
                            moment().endOf("day"),
                        ],
                        "Last 30 Days": [
                            moment().subtract(29, "days").startOf("day"),
                            moment().endOf("day"),
                        ],
                        "This Month": [
                            moment().startOf("month").startOf("day"),
                            moment().endOf("month").endOf("day"),
                        ],
                        "Last Month": [
                            moment().subtract(1, "month").startOf("month").startOf("day"),
                            moment().subtract(1, "month").endOf("month").endOf("day"),
                        ],
                        "Next 7 Days": [
                            moment().startOf("day"),
                            moment().add(6, "days").endOf("day"),
                        ],
                        "Next 30 Days": [
                            moment().startOf("day"),
                            moment().add(29, "days").endOf("day"),
                        ],
                        "Next Week": [
                            moment().add(1, "week").startOf("week").startOf("day"),
                            moment().add(1, "week").endOf("week").endOf("day"),
                        ],
                        "Next Month": [
                            moment().add(1, "month").startOf("month").startOf("day"),
                            moment().add(1, "month").endOf("month").endOf("day"),
                        ],
                        "This Week": [
                            moment().startOf("week").startOf("day"),
                            moment().endOf("week").endOf("day"),
                        ],
                        "Last Week": [
                            moment().subtract(1, "week").startOf("week").startOf("day"),
                            moment().subtract(1, "week").endOf("week").endOf("day"),
                        ],
                    },
                    locale: {
                        format: "DD/MM/YYYY HH:mm", // Format of the date displayed in the input
                    },
                },
                (start, end) => {
                    this.handleFilter(
                        start.format("DD/MM/YYYY HH:mm"),
                        end.format("DD/MM/YYYY HH:mm")
                    );
                }
            );

            let calendarShown = 0;

            $("#pickupDateBooking").on(
                "showCalendar.daterangepicker",
                function (ev, picker) {
                    calendarShown++;

                    if (calendarShown == 2) {
                        picker.setStartDate(
                            picker.startDate.clone().startOf("day")
                        );
                        picker.setEndDate(picker.endDate.clone().endOf("day"));
                        $(".drp-calendar.left .calendar-time .hourselect").val(
                            0
                        );
                        $(
                            ".drp-calendar.left .calendar-time .minuteselect"
                        ).val(0);
                        $(".drp-calendar.right .calendar-time .hourselect").val(
                            23
                        );
                        $(
                            ".drp-calendar.right .calendar-time .minuteselect"
                        ).val(59);
                    }
                }
            );

            $("#pickupDateBooking").on(
                "hide.daterangepicker",
                function (ev, picker) {
                    calendarShown = 0;
                }
            );
        }
    };
}

window.service = new Bookings(props);
