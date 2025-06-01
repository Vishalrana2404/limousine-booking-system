import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";

/**
 * Represents the Edit Bookings class.
 * @extends BaseClass
 */
export default class EditBookings extends BaseClass {
    /**
     * Constructor for the Bookings class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        this.handleOnLoad();
        $(document).on("change", "#vehicle-type", this.handleMeetAndGreet);
        $(document).on(
            "change",
            "#service-types, #pick-up-location-id, #drop-off-location-id",
            this.initializeAdditionalStopLimits,
        );
        $(document).on(
            "change",
            "#service-types, #pick-up-location-id, #drop-off-location-id",
            this.handleServiceType
        );

        $(document).on(
            "change",
            '#extra_child_seat_charges, #is-additional-stop-charge',
            this.initializeExtraChildSeatCharges
        );

        $(document).on(
            'change', 
            '#childSeatYes, #childSeatNo, #childSeatOptions',
            this.calculatedBilling
        );
        $(document).on("change", "#pickup_time_to_be_advised", this.handlePickupTime);
        $(document).on("blur", "#pick-up-time", this.handleToBeAdvised);
        $(document).on("change", "#driver-id", this.handleDriver);
        $(document).on("click", "#addStop", this.handleAddStop);
        $(document).on("click", "#addClient", this.handleAddClient);
        $(document).on("click", ".remove-stop", this.handleRemoveStop);
        $(document).on("click", ".remove-client", this.handleRemoveClient);
        $(document).on("click", "#addGuest", this.handleAddGuest);
        $(document).on("click", ".remove-guest", this.handleRemoveGuest);
        $(document).on(
            "change",
            "#service-types, #is-peak-period-surcharge, #trip-ended, #is-mid-night-surcharge, #is-arr-waiting-time-surcharge, #is-out-of-city-surcharge, #is-last-minute-booking-surcharge, #is-additional-stop-charge, #is-misc-surcharge",
            this.calculatedBilling
        );
        $(document).on(
            "keyup",
            "#peak-period-surcharge,#no-of-hours, #mid-night-surcharge, #arrivel-waiting-time, #out-of-city-surcharge, #last-minute-surcharge, #additional-stop-surcharge, #misc-surcharge, #departure-charge, #arrival-charge, #transfer-charge, #disposal-charge,#delivery-charge",
            this.calculatedBilling
        );
        $(document).on("change", "#status", this.handleStatus);
        $(document).on("change.td", "#trip-ended", this.handleDateTime);
        $(document).on("click", ".delete-booking-btn", this.handleDeleteModal);
        $(document).on("click", ".cancel-booking-btn", this.handleCancelModal);
        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeleteBookings
        );
        $(document).on(
            "click",
            "#cancelConfirmButton",
            this.handleCancelBookings
        );
        $(document).on(
            "change",
            "#service-types",
            this.handleCorporateFairCharges
        );
        $(document).on(
            "change",
            "#vehicle-type",
            this.handleCorporateFairCharges
        );
        $(document).on(
            "change",
            "#clientId",
            this.handleCorporateIdChange
        );
    };

    handleCorporateIdChange = () => {
        const newCorporateId = $('#clientId').val();

        $('#client_hotel_id').val(newCorporateId);

        this.handleCorporateFairCharges();
    }

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

    handleMeetAndGreet = () => {
        const selectedOption = $('#vehicle-type option:selected');

        if(selectedOption)
        {
            const seatingCapacity = parseInt(selectedOption.data('seating-capacity'), 10);
            
            if (seatingCapacity > 13) {
                $('#meetAndGreetYes').prop('disabled', false);
            } else {
                $('#meetAndGreetYes').prop('disabled', true);
    
                if ($('#meetAndGreetYes').is(':checked')) {
                    $('#meetAndGreetYes').prop('checked', false);
                    $('#meetAndGreetNo').prop('checked', true);
                }
            }
        } else {
            $('#meetAndGreetYes').prop('disabled', true);

            if ($('#meetAndGreetYes').is(':checked')) {
                $('#meetAndGreetYes').prop('checked', false);
                $('#meetAndGreetNo').prop('checked', true);
            }
        }
    }

    handleAddGuest = () => {
        const lastGuestInputContainer = $(".phone")
            .last().parent().parent().parent();
        const lastId = parseInt(
            lastGuestInputContainer.find(".phone").attr("id").split("-")[2]
        );
        const newId = lastId + 1;
        const newGuestInput = `
        <li class="list-group-item border-top-0">
            <div class="form-group row row-gap-2 mb-0">
                <button type="button" class="remove-guest" style="color:white; background-color:red; padding: 10px;">Remove Guest</button>
            </div>
        </li>
        <li class="list-group-item border-top-0">
            <div class="form-group row row-gap-2 mb-0">
                <label for="guest-name-${newId}" class="col-sm-6 col-form-label">Guest Name</label>
                <div class="col-sm-6">
                    <input type="text" id="guest-name-${newId}" name="guest_name[]"
                        value=""
                        class="form-control @error('is_cross_border') is-invalid @enderror guest-name"
                        placeholder="Guest Name">
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="form-group row row-gap-2 mb-0">
                <label for="contact-number-${newId}" class="col-sm-6 col-form-label">Contact
                    Number</label>
                <div class="col-sm-2">
                    <input type="text" value=""
                        id="country-code-${newId}" name="country_code[]"
                        class="form-control @error('country_code') is-invalid @enderror country-code"
                        placeholder="Country Code" autocomplete="off">
                </div>
                <div class="col-sm-4">
                    <input type="text" name="phone[]" value=""
                        id="contact-number-${newId}"
                        class="form-control @error('phone') is-invalid @enderror phone"
                        placeholder="Contact Number" autocomplete="off">
                </div>
            </div>
        </li>
        `;
        $(newGuestInput).insertBefore($('#addGuest').parent().parent());
    }

    handleRemoveGuest = ({ target }) => {
        $(target).parent().parent().prev().prev().remove();
        $(target).parent().parent().prev().remove();
        $(target).parent().parent().remove();
    }

    handleCancelModal = ({ target }) => {
        try {
            const bookingId = $(target).data("id");
            this.openModal("cancelConfirmModal");
            $("#cancelConfirmationTitle").text(
                "Are you sure you want to cancel this booking?"
            );
            $("#cancelConfirmationTitle").data("id", bookingId);
        } catch (error) {
            this.handleException(error);
        }
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

    handleCancelBookings = () => {
        try {
            const bookingId = $("#cancelConfirmationTitle").data("id");
            const formData = new FormData();
            formData.append("booking_id", bookingId);
            this.sendCancelRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleCorporateFairCharges = () => {
        let vehicleTypeId = $('#vehicle-type').val();
        let serviceTypeId = $('#service-types').val();
        let hotelId = $('#client_hotel_id').val();

        if(vehicleTypeId !== '' && serviceTypeId !== '' && hotelId !== '')
        {
            $('#perTripArrival').hide();
            $('#perTripTransfer').hide();
            $('#perTripDeparture').hide();
            $('#perTripDesposal').hide();
            
            if(serviceTypeId == 1 || serviceTypeId == 2 || serviceTypeId == 3 || serviceTypeId == 4)
            {
                let service = 'Arrival';
                if(serviceTypeId == 1)
                {
                    service = 'Arrival';
                    $('#perTripArrival').show();
                }
                if(serviceTypeId == 2)
                {
                    service = 'Transfer';
                    $('#perTripTransfer').show();
                }
                if(serviceTypeId == 3)
                {
                    service = 'Departure';
                    $('#perTripDeparture').show();
                }
                if(serviceTypeId == 4)
                {
                    service = 'Hour';
                    $('#perTripDesposal').show();
                }

                try {
                    const url = this.props.routes.corporateFareCharges;
                    
                    axios
                        .get(url, {
                            params: {
                                vehicleTypeId: vehicleTypeId,
                                serviceTypeId: service,
                                hotelId: hotelId,
                            }
                        })
                        .then((response) => {
                            const statusCode = response.data.status;
                            const message = response.data.message;
                            const flash = new ErrorHandler(statusCode, message);
                            
                            if (statusCode === 200) {
                                if(serviceTypeId == 1)
                                {
                                    $('#transfer-charge').val(0.00);
                                    $('#departure-charge').val(0.00);
                                    $('#disposal-charge').val(0.00);
                                    if(response.data.data.amount)
                                    {
                                        $('#arrival-charge').val(response.data.data.amount.toString());
                                        $('#arrival-charge').attr('value', response.data.data.amount.toString());
                                    }else{
                                        $('#arrival-charge').val('0.00');
                                        $('#arrival-charge').attr('0.00');
                                    }
                                }
                                if(serviceTypeId == 2)
                                {
                                    $('#arrival-charge').val(0.00);
                                    $('#departure-charge').val(0.00);
                                    $('#disposal-charge').val(0.00);
                                    if(response.data.data.amount)
                                    {
                                        $('#transfer-charge').val(response.data.data.amount.toString());
                                        $('#transfer-charge').attr('value', response.data.data.amount.toString());
                                    }else{
                                        $('#transfer-charge').val('0.00');
                                        $('#transfer-charge').attr('0.00');
                                    }
                                }
                                if(serviceTypeId == 3)
                                {
                                    $('#arrival-charge').val(0.00);
                                    $('#transfer-charge').val(0.00);
                                    $('#disposal-charge').val(0.00);
                                    if(response.data.data.amount)
                                    {
                                        $('#departure-charge').val(response.data.data.amount.toString());
                                        $('#departure-charge').attr('value', response.data.data.amount.toString());                                         
                                    }else{
                                        $('#departure-charge').val('0.00');
                                        $('#departure-charge').attr('0.00');                                         
                                    }
                                }
                                if(serviceTypeId == 4)
                                {
                                    $('#arrival-charge').val(0.00);
                                    $('#transfer-charge').val(0.00);
                                    $('#departure-charge').val(0.00);
                                    if(response.data.data.amount)
                                    {
                                        $('#disposal-charge').val(($('#no-of-hours').val() * response.data.data.amount).toString());
                                        $('#disposal-charge').attr('value', ($('#no-of-hours').val() * response.data.data.amount).toString());                                         
                                    }else{
                                        $('#disposal-charge').val('0.00');
                                        $('#disposal-charge').attr('0.00');                                        
                                    }
                                }
                                this.calculatedBilling();
                            } else {
                                throw flash;
                            }
                        })
                        .catch((error) => {
                                console.log(error);
                            this.handleException(error);
                        });
                } catch (error) {
                    this.handleException(error);
                }
            }else{
                $('#arrival-charge').val(0.00);
                $('#transfer-charge').val(0.00);
                $('#departure-charge').val(0.00);    
                $('#disposal-charge').val(0.00);     
                this.calculatedBilling();
            }
        }   
    }

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
                    $("#loader").hide();
                    window.location.href = this.props.routes.baseUrl + '/bookings';
                }
                throw flash;
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };

    sendCancelRequest = (formData) => {
        const url = this.props.routes.cancelBooking;
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    this.closeModal("cancelConfirmModal");
                    $("#loader").hide();
                    window.location.href = this.props.routes.baseUrl + '/bookings';
                }
                throw flash;
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };

    initializeAdditionalStopCharges = () => {
        if($('#service-types').val() == 1 || $('#service-types').val() == 2 || $('#service-types').val() == 3){
            $('#additional-stop-surcharge').val((5 * $('.additional-stops').length).toFixed(2));
        }else{
            $('#additional-stop-surcharge').val((0).toFixed(2));
        }
        this.calculatedBilling();
    }

    initializeExtraChildSeatCharges = ({target}) => {
        $(target).prop("checked", true);
    }

    handlePickupTime = () => {
        if($('#pickup_time_to_be_advised').is(':checked')){
            $('#pick-up-time').prop('readonly', true);
            $('#pick-up-time').val("00:00");
        }else{
            $('#pick-up-time').prop('readonly', false);
            $('#pick-up-time').val("");
        }
    }

    handleToBeAdvised = () => {
        $('#pickup_time_to_be_advised').prop('checked', false);
        $('#pickup_time_to_be_advised').css('border', "2px solid #a6acaf");
        $('#pickup_time_to_be_advised').css('box-shadow', "0px 0px 5px rgba(10,20,30,50%)");
        $('#pickup_time_to_be_advised').css('background-color', "#fff");
        $('#pick-up-time').prop('readonly', false);
    }

    handleAddStop = () => {
        if($('#service-types').val() == 1 || $('#service-types').val() == 2 || $('#service-types').val() == 3)
        {
            if($('.additional-stops').length >= 2)
            {
                $('#addStop').hide();
            }else
            {
                const lastStopInputContainer = $(".additional-stops").last();
                const lastId = parseInt(lastStopInputContainer.attr("id").split("_")[1]);
        
                const newId = lastId + 1;
                const newStopInput = `
                    <li class="list-group-item border-top-0 all-additional-stops">
                        <div class="form-group row row-gap-2 mb-0">
                            <label for="additionalStops_${newId}" class="col-sm-6 col-form-label" id="additional_stop_label_${newId}">Additional Stop ${newId + 1}</label>
                            <div class="col-sm-6" style="display:flex; align-items:center; justify-content: space-between;">
                                <input type="text" id="additionalStops_${newId}" name="additional_stops[]"
                                value=""
                                class="form-control col-sm-9 additional-stops"
                                placeholder="Additional Stop">
                                <button type="button" class="remove-stop col-sm-3"><span class="fas fa-times mt-3 text-danger"></span></button>
                            </div>
                        </div>
                    </li>`;
        
                    
                this.initializeGoogleMapAutoComplete(`additionalStops_${newId}`);
                $(newStopInput).insertAfter(lastStopInputContainer.closest('.all-additional-stops'));
            }
        }else{            
            const lastStopInputContainer = $(".additional-stops").last();
            const lastId = parseInt(lastStopInputContainer.attr("id").split("_")[1]);
    
            const newId = lastId + 1;
            const newStopInput = `
                <li class="list-group-item border-top-0 all-additional-stops">
                    <div class="form-group row row-gap-2 mb-0">
                        <label for="additionalStops_${newId}" class="col-sm-6 col-form-label" id="additional_stop_label_${newId}">Additional Stop ${newId + 1}</label>
                        <div class="col-sm-6" style="display:flex; align-items:center; justify-content: space-between;">
                            <input type="text" id="additionalStops_${newId}" name="additional_stops[]"
                            value=""
                            class="form-control col-sm-9 additional-stops"
                            placeholder="Additional Stop">
                            <button type="button" class="remove-stop col-sm-3"><span class="fas fa-times mt-3 text-danger"></span></button>
                        </div>
                    </div>
                </li>`;
    
                
            this.initializeGoogleMapAutoComplete(`additionalStops_${newId}`);
            $(newStopInput).insertAfter(lastStopInputContainer.closest('.all-additional-stops'));
        }
        this.initializeAdditionalStopLimits();
        this.handleAdditionalStopIds();
    };

    handleAddClient = () => {
        const lastClientInputContainer = $(".access_given_clients").last();

        const lastId = parseInt(lastClientInputContainer.attr("id").split("_")[3]);

        const newId = lastId + 1;

        const clients = this.props.clients;
        const bookingCreatedBy = this.props.bookingCreatedBy;

        const newClientInput = `
            <select name="access_given_clients[]" id="access_given_clients_${newId}"
                class="form-control form-select custom-select col-sm-9 access_given_clients"
                autocomplete="off">
                <option value="">Select Client</option>
                    ${clients
                        .filter(row => bookingCreatedBy !== row.user.id)
                        .map(
                            (row) =>
                                `<option value="${row.user.id}">
                                    ${(row.user.first_name ? row.user.first_name : '')} 
                                    ${(row.user.last_name ? row.user.last_name : '')}
                                </option>`
                        )
                        .join("")}
            </select>
            <button type="button" class="remove-client col-sm-2" id="remove_client_${newId}">
                <span class="fas fa-times mt-3 text-danger" id="client_span_${newId}"></span>
            </button>`;
            
        $('.access_given_clients_div').append(newClientInput);
    };
    

    handleRemoveClient = ({ target }) => {
        const id = $(target).attr('id').split('_')[2];

        $('#access_given_clients_' + id).remove();
        $('#remove_client_' + id).remove();
    };

    handleRemoveStop = ({ target }) => {
        $(target).closest(".all-additional-stops").remove();
        this.initializeAdditionalStopLimits();
        this.handleAdditionalStopIds();
    };

    initializeAdditionalStopLimits = () => {
        if($('#service-types').val() == 1 || $('#service-types').val() == 2 || $('#service-types').val() == 3)
        {
            if($('.additional-stops').length >= 2)
            {
                $('#addStop').hide();
            }else
            {
                $('#addStop').show();
            }

            if($('.additional-stops').length > 2){
                $('.additional-stops').slice(2).closest('.all-additional-stops').remove();
            }
        }else{
            $('#addStop').show();
        }
        this.initializeAdditionalStopCharges()
    }
    handleAdditionalStopIds = () => {
        var i = 0;
        $(".additional-stops").each(function () {
            let $this = $(this);
            let oldId = $(this).attr('id');
            $this.attr("id", "additionalStops_" + i);

            let label = $('#additional_stop_label_' + oldId.split('_')[1]);


            label.text("Additional Stop " + (i + 1));
            label.attr("id", "additional_stop_label_" + i);
            label.attr("for", "additionalStops_" + i);

    
            i++; // Increment the index
        });
    };
    
    handleDateTime = () => {
        const hours = this.calculateHoursByDateTime();
        const noOfHours = $("#no-of-hours").val();
        const finalHours = hours > 0 ? hours : noOfHours;
        $("#no-of-hours").val(finalHours);
        this.calculatedBilling();
    };

    calculateHoursByDateTime = () => {
        const tripEndedDateTime = $("#trip-ended").val();
        const pickUpTime = $("#pick-up-time").val();
        const pickUpDate = $("#pick-up-date").val();
        if (!pickUpDate || !pickUpTime || !tripEndedDateTime) {
            return 0;
        }
        // Parse the pickup date and time
        const [pickUpDay, pickUpMonth, pickUpYear] = pickUpDate.split("/");
        const [pickUpHour, pickUpMinute] = pickUpTime.split(":");
        const pickUpDateTime = new Date(
            `${pickUpYear}-${pickUpMonth}-${pickUpDay}T${pickUpHour}:${pickUpMinute}:00`
        );
        // Parse the trip ended date and time
        const [endDate, endTime] = tripEndedDateTime.split(" ");
        const [endDay, endMonth, endYear] = endDate.split("/");
        const [endHour, endMinute] = endTime.split(":");
        const tripEndDateTime = new Date(
            `${endYear}-${endMonth}-${endDay}T${endHour}:${endMinute}:00`
        );
        // Calculate the difference in milliseconds
        const diffInMillis = tripEndDateTime - pickUpDateTime;
        // Convert milliseconds to hours
        const diffInHours = diffInMillis / (1000 * 60 * 60);
        const roundedNumber =
            diffInHours % 1 > 0
                ? diffInHours - (diffInHours % 1) + 1
                : diffInHours;
        const finalHours = roundedNumber > 24 ? 24 : roundedNumber;
        return finalHours;
    };

    handleDriverOffDay = () => {
        const driverOffDays = this.props.driverOffDays;
        const drivers = this.props.drivers;
        const pickUpDate = this.convertDate($("#pick-up-date").val());
        if (!pickUpDate) {
            return false;
        }

        let driverIds = [];
        for (const offDay of driverOffDays) {
            if (offDay.off_date === pickUpDate) {
                driverIds.push(offDay.driver_id);
            }
        }
        const html = ` <option value="">Select one</option>
                    ${drivers
                        .filter((row) => !driverIds.includes(row.id))
                        .map(
                            (row) =>
                                `<option value="${row.id}"  data-driver-contact="${row.phone}" data-vehicle-id="${row.vehicle_id}">${row.name}</option>`
                        )
                        .join("")}`;
        $("#driver-id").html(html);
    };

    handleStatus = ({ target }) => {
        const status = $(target).val();
        if (status === "COMPLETED") {
            $("#tripEnded").show();
        } else {
            $("#tripEnded").hide();
        }
    };
    calculatedBilling = () => {
        let totalCharges = 0;
        const serviceType = parseInt($("#service-types").val());
        const tripCharge = this.getTripCharge(serviceType);

        const isFixedMidnightCharge = this.getIsFixedCharge(
            $("#is-fixed-midnight-surcharge").val()
        );
        const isFixedArrivalWatingCharge = this.getIsFixedCharge(
            $("#is-fixed-arrival-waiting-surcharge").val()
        );
        const isFixedOutCityCharge = this.getIsFixedCharge(
            $("#is-fixed-out-of-city-surcharge").val()
        );
        const isFixedLastMinuteCharge = this.getIsFixedCharge(
            $("#is-fixed-last-minute-surcharge").val()
        );
        const isFixedAdditionalStopCharge = this.getIsFixedCharge(
            $("#is-fixed-additional-stop-surcharge").val()
        );
        const isFixedMiscCharge = this.getIsFixedCharge(
            $("#is-fixed-misc-surcharge").val()
        );

        const peakPeriodCharge = $("#peak-period-surcharge").val()
            ? isNaN(parseFloat($("#peak-period-surcharge").val()))
                ? 0
                : parseFloat($("#peak-period-surcharge").val())
            : 0;
        const midnightCharge = $("#mid-night-surcharge").val()
            ? isNaN(parseFloat($("#mid-night-surcharge").val()))
                ? 0
                : parseFloat($("#mid-night-surcharge").val())
            : 0;
        const arrivalWaitingCharge = $("#arrivel-waiting-time").val()
            ? isNaN(parseFloat($("#arrivel-waiting-time").val()))
                ? 0
                : parseFloat($("#arrivel-waiting-time").val())
            : 0;
        const outCityCharge = $("#out-of-city-surcharge").val()
            ? isNaN(parseFloat($("#out-of-city-surcharge").val()))
                ? 0
                : parseFloat($("#out-of-city-surcharge").val())
            : 0;
        const lastMinuteCharge = $("#last-minute-surcharge").val()
            ? isNaN(parseFloat($("#last-minute-surcharge").val()))
                ? 0
                : parseFloat($("#last-minute-surcharge").val())
            : 0;
        const additionalStopCharge = $("#additional-stop-surcharge").val()
            ? isNaN(parseFloat($("#additional-stop-surcharge").val()))
                ? 0
                : parseFloat($("#additional-stop-surcharge").val())
            : 0;
        const miscCharge = $("#misc-surcharge").val()
            ? isNaN(parseFloat($("#misc-surcharge").val()))
                ? 0
                : parseFloat($("#misc-surcharge").val())
            : 0;
        const childSeatCharge = $("#extra-child-seat-charges").val()
                ? isNaN(parseFloat($("#extra-child-seat-charges").val()))
                    ? 0
                    : parseFloat($("#extra-child-seat-charges").val())
                : 0;

        const peakCharges = this.addChargeInTripCharge(
            $("#is-peak-period-surcharge"),
            tripCharge,
            peakPeriodCharge,
            true
        );
        const nightCharges = this.addChargeInTripCharge(
            $("#is-mid-night-surcharge"),
            tripCharge,
            midnightCharge,
            isFixedMidnightCharge
        );
        const waitingCharges = this.addChargeInTripCharge(
            $("#is-arr-waiting-time-surcharge"),
            tripCharge,
            arrivalWaitingCharge,
            isFixedArrivalWatingCharge
        );
        const outCityCharges = this.addChargeInTripCharge(
            $("#is-out-of-city-surcharge"),
            tripCharge,
            outCityCharge,
            isFixedOutCityCharge
        );
        const lastMinuteCharges = this.addChargeInTripCharge(
            $("#is-last-minute-booking-surcharge"),
            tripCharge,
            lastMinuteCharge,
            isFixedLastMinuteCharge
        );
        const additionalCharges = this.addChargeInTripCharge(
            $("#is-additional-stop-charge"),
            tripCharge,
            additionalStopCharge,
            isFixedAdditionalStopCharge
        );
        const miscCharges = this.addChargeInTripCharge(
            $("#is-misc-surcharge"),
            tripCharge,
            miscCharge,
            isFixedMiscCharge
        );
        const extraChildSeatCharges = ($('#childSeatYes').prop('checked') && parseInt($('#childSeatCount').val()) == 2) ? 10 : 0;
        
        totalCharges =
            tripCharge +
            peakCharges +
            nightCharges +
            waitingCharges +
            outCityCharges +
            lastMinuteCharges +
            additionalCharges +
            miscCharges+
            extraChildSeatCharges;
        totalCharges = $('#booking-status-for-total-charges').val() == 'CANCELLED' ? 0 : totalCharges;
        $("#total-charges").text(totalCharges);
        $("#total-charge").val(totalCharges);
    };
    addChargeInTripCharge = (selector, tripCharge, newValue, isFixed) => {
        let totalCharge = 0;
        if (selector.is(":checked")) {
            if (isFixed) {
                totalCharge = newValue;
            } else {
                totalCharge = tripCharge * newValue;
            }
        }
        return totalCharge;
    };

    getIsFixedCharge = (data) => {
        let isFixed = true;
        if (data && data === "x") {
            isFixed = false;
        }
        return isFixed;
    };

    getTripCharge = (serviceType) => {
        let tripCharge = 0;
        switch (serviceType) {
            case 1:
                tripCharge = $("#arrival-charge").val()
                    ? isNaN(parseFloat($("#arrival-charge").val()))
                        ? 0
                        : parseFloat($("#arrival-charge").val())
                    : 0;
                break;
            case 2:
                tripCharge = $("#transfer-charge").val()
                    ? isNaN(parseFloat($("#transfer-charge").val()))
                        ? 0
                        : parseFloat($("#transfer-charge").val())
                    : 0;
                break;
            case 3:
                tripCharge = $("#departure-charge").val()
                    ? isNaN(parseFloat($("#departure-charge").val()))
                        ? 0
                        : parseFloat($("#departure-charge").val())
                    : 0;
                break;
            case 4:
                tripCharge = $("#original-disposal-charge").val()
                    ? isNaN(parseFloat($("#original-disposal-charge").val()))
                        ? 0
                        : parseFloat($("#original-disposal-charge").val())
                    : 0;
                const hours = this.calculateHoursByDateTime();
                const noOfHours = $("#no-of-hours").val();
                
                const totalHours = hours > noOfHours ? hours : noOfHours;
                tripCharge = totalHours > 0 ? tripCharge * totalHours : tripCharge;

                $("#disposal-charge").val(tripCharge)

                break;
            case 5:
                tripCharge = $("#delivery-charge").val()
                    ? isNaN(parseFloat($("#delivery-charge").val()))
                        ? 0
                        : parseFloat($("#delivery-charge").val())
                    : 0;
                break;
            default:
                tripCharge = $("#arrival-charge").val()
                    ? isNaN(parseFloat($("#arrival-charge").val()))
                        ? 0
                        : parseFloat($("#arrival-charge").val())
                    : 0;
                break;
        }
        return tripCharge;
    };

    handleDriver = ({ target }) => {
        const drvierContact = $(target)
            .find("option:selected")
            .data("driver-contact");
        const vehicleId = $(target).find("option:selected").data("vehicle-id");
        $("#driver-contact").val(drvierContact);
        $("#vehicle-id").val(vehicleId);
    };

    handleServiceType = ({ target }) => {
        if ($(target).attr("id") === "service-types") {
            const serviceTypeId = parseInt($(target).val());
            switch (serviceTypeId) {
                case 1:
                    $("#pickUpLocationDropdown").show();
                    $("#pickUpLocationTextBox").hide();
                    $("#dropOffLocationDropdown").hide();
                    $("#dropOffLocationtextBox").show();
                    $(".flightDetailRows").show();
                    $(".departureDateTime").hide();
                    $("#perTripArrival").show();
                    $("#perTripTransfer").hide();
                    $("#perTripDeparture").hide();
                    $("#perTripDesposal").hide();
                    $("#perTripDelivery").hide();
                    $("#noOfHours").hide();
                    $("crossBorderDropDown, #tripEnded").hide();
                    $("#noOfLuggage, #noOfPax").show();
                    break;
                case 2:
                    $("#pickUpLocationDropdown").hide();
                    $("#pickUpLocationTextBox").show();
                    $("#dropOffLocationDropdown").hide();
                    $("#dropOffLocationtextBox").show();
                    $(".flightDetailRows").hide();
                    $(".departureDateTime").hide();
                    $("#perTripArrival").hide();
                    $("#perTripTransfer").show();
                    $("#perTripDeparture").hide();
                    $("#perTripDesposal").hide();
                    $("#perTripDelivery").hide();
                    $("#noOfHours").hide();
                    $("crossBorderDropDown, #tripEnded").hide();
                    $("#noOfLuggage, #noOfPax").show();
                    break;
                case 3:
                    $("#pickUpLocationDropdown").hide();
                    $("#pickUpLocationTextBox").show();
                    $("#dropOffLocationDropdown").show();
                    $("#dropOffLocationtextBox").hide();
                    $(".flightDetailRows").show();
                    $(".departureDateTime").show();
                    $("#perTripArrival").hide();
                    $("#perTripTransfer").hide();
                    $("#perTripDeparture").show();
                    $("#perTripDesposal").hide();
                    $("#perTripDelivery").hide();
                    $("#noOfHours").hide();
                    $("crossBorderDropDown, #tripEnded").hide();
                    $("#noOfLuggage, #noOfPax").show();
                    break;
                case 4:
                    $("#pickUpLocationDropdown").hide();
                    $("#pickUpLocationTextBox").show();
                    $("#dropOffLocationDropdown").hide();
                    $("#dropOffLocationtextBox").show();
                    $(".flightDetailRows").hide();
                    $(".departureDateTime").hide();
                    $("#perTripArrival").hide();
                    $("#perTripTransfer").hide();
                    $("#perTripDeparture").hide();
                    $("#perTripDesposal").show();
                    $("#perTripDelivery").hide();
                    $("#noOfHours").show();
                    $("crossBorderDropDown, #tripEnded").show();
                    $("#noOfLuggage, #noOfPax").show();
                    break;
                case 5:
                    $("#pickUpLocationDropdown").hide();
                    $("#pickUpLocationTextBox").show();
                    $("#dropOffLocationDropdown").hide();
                    $("#dropOffLocationtextBox").show();
                    $(".flightDetailRows").hide();
                    $(".departureDateTime").hide();
                    $("#perTripArrival").hide();
                    $("#perTripTransfer").hide();
                    $("#perTripDeparture").hide();
                    $("#perTripDesposal").hide();
                    $("#perTripDelivery").show();
                    $("#noOfHours").hide();
                    $("crossBorderDropDown, #tripEnded").hide();
                    $("#noOfLuggage, #noOfPax").hide();
                    break;
                case 6:
                    $("#pickUpLocationDropdown").show();
                    $("#pickUpLocationTextBox").hide();
                    $("#dropOffLocationDropdown").hide();
                    $("#dropOffLocationtextBox").show();
                    $(".flightDetailRows").show();
                    $(".departureDateTime").hide();
                    $("#perTripArrival").hide();
                    $("#perTripTransfer").hide();
                    $("#perTripDeparture").hide();
                    $("#perTripDesposal").show();
                    $("#perTripDelivery").hide();
                    $("#noOfHours").hide();
                    $("crossBorderDropDown, #tripEnded").hide();
                    $("#noOfLuggage, #noOfPax").show();
                    break;
                case 7:
                    $("#pickUpLocationDropdown").hide();
                    $("#pickUpLocationTextBox").show();
                    $("#dropOffLocationDropdown").show();
                    $("#dropOffLocationtextBox").hide();
                    $(".flightDetailRows").show();
                    $(".departureDateTime").show();
                    $("#perTripArrival").hide();
                    $("#perTripTransfer").hide();
                    $("#perTripDeparture").hide();
                    $("#perTripDesposal").show();
                    $("#perTripDelivery").hide();
                    $("#noOfHours").hide();
                    $("crossBorderDropDown, #tripEnded").hide();
                    $("#noOfLuggage, #noOfPax").show();
                    break;
                default:
                    $("#pickUpLocationDropdown").hide();
                    $("#pickUpLocationTextBox").show();
                    $("#dropOffLocationDropdown").hide();
                    $("#dropOffLocationtextBox").show();
                    $(".flightDetailRows").hide();
                    $(".departureDateTime").hide();
                    $("#perTripArrival").show();
                    $("#perTripTransfer").hide();
                    $("#perTripDeparture").hide();
                    $("#perTripDesposal").hide();
                    $("#perTripDelivery").hide();
                    $("#noOfHours").hide();
                    $("crossBorderDropDown, #tripEnded").hide();
                    $("#noOfLuggage, #noOfPax").show();
                    break;
            }
        } else if ($(target).attr("id") === "pick-up-location-id") {
            if (parseInt($(target).val()) === 8) {
                $("#pickUpLocationTextBox").show();
            } else {
                $("#pickUpLocationTextBox").hide();
            }
        } else if ($(target).attr("id") === "drop-off-location-id") {
            if (parseInt($(target).val()) === 8) {
                $("#dropOffLocationtextBox").show();
            } else {
                $("#dropOffLocationtextBox").hide();
            }
        }
    };

    validateEditBooking = () => {
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
                    $("#vehicle-type option:selected").data("seating-capacity")
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
                    $("#vehicle-type option:selected").data("seating-capacity")
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
        $.validator.addMethod(
            "departureDateTimeCheck",
            function (value, element) {
                // Retrieve pickup date and time values
                const pickupDate = $("#pick-up-date").val();
                const pickupTime = $("#pick-up-time").val();
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

        $("#updateBookingForm").validate({
            rules: {
                service_type_id: {
                    required: true,
                },
                // event_id: {
                //     required: true,
                // },
                attachment: {
                    extension: "jpg|jpeg|png|gif|doc|docx|txt|pdf|xls|xlsx",
                    validFileSize: true,
                },
                flight_detail: {
                    required: {
                        depends: function (element) {
                            const serviceTypeId = $("#service-types").val();
                            const pickupLocationId = $("#pick-up-location-id").val();

                            return (
                                serviceTypeId === "3" ||
                                (serviceTypeId === "1" && ["1", "2", "3", "4", "5"].includes(pickupLocationId))
                            );
                        }
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
                            return $("#service-types").val() != 4;
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
                },
                pickup_time: {
                    required: true,
                    timeFormat: true,
                },
                trip_ended: {
                    dateTimeFormat: true,
                    departureDateTimeCheck: true,
                },
                departure_time: {
                    dateTimeFormat: true,
                    departureDateTimeCheck: true,
                },
                no_of_hours: {
                    required: true,
                    digits: true,
                    customMin: true,
                    max: 24,
                },
                "country_code[]": {
                    minlength: 1,
                    // maxlength: 3,
                    // digits: true,
                },
                "phone[]": {
                    required: true,
                    // digits: true,
                    minlength: 6,
                    // maxlength: 10,
                },
                total_pax: {
                    required: {
                        depends: function (element) {
                            return $("#service-types").val() != 5;
                        },
                    },
                    digits: true,
                    min: 1,
                    max: 45,
                },
                total_luggage: {
                    required: {
                        depends: function (element) {
                            return $("#service-types").val() != 5;
                        },
                    },
                    digits: true,
                    max: 100,
                },
                "guest_name": {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                // driver_id: {
                //     required: true,
                // },
                // vehicle_id: {
                //     required: true,
                // },
                status: {
                    required: true,
                },
                departure_charge: {
                    required: true,
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                arrival_charge: {
                    required: true,
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                transfer_charge: {
                    required: true,
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                disposal_charge: {
                    required: true,
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                delivery_charge: {
                    required: true,
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                peak_period_surcharge: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                mid_night_surcharge: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                arrivel_waiting_time_surcharge: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                outside_city_surcharge: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                last_minute_surcharge: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                additional_stop_surcharge: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                misc_surcharge: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                client_instructions: {
                    required: {
                        depends: function (element) {
                            return (
                                $("#service-types").val() === "1" &&
                                $("#pick-up-location-id").val() === "12"
                            );
                        }
                    },
                }
            },
            messages: {
                departure_time: {
                    // required: this.languageMessage.departure_time.required,
                },
                departure_charge: {
                    required: this.languageMessage.departure_charge.required,
                    pattern: this.languageMessage.decimal.pattern,
                },
                arrival_charge: {
                    required: this.languageMessage.arrival_charge.required,
                    pattern: this.languageMessage.decimal.pattern,
                },
                transfer_charge: {
                    required: this.languageMessage.transfer_charge.required,
                    pattern: this.languageMessage.decimal.pattern,
                },
                disposal_charge: {
                    required: this.languageMessage.disposal_charge.required,
                    pattern: this.languageMessage.decimal.pattern,
                },
                delivery_charge: {
                    required: this.languageMessage.delivery_charge.required,
                    pattern: this.languageMessage.decimal.pattern,
                },
                peak_period_surcharge: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                mid_night_surcharge: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                arrivel_waiting_time_surcharge: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                outside_city_surcharge: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                last_minute_surcharge: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                additional_stop_surcharge: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                misc_surcharge: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
                // vehicle_id: {
                //     required: this.languageMessage.vehicle_id.required,
                // },
                // driver_id: {
                //     required: this.languageMessage.select_driver.required,
                // },
                service_type_id: {
                    required: this.languageMessage.service_type_id.required,
                },
                // event_id: {
                //     required: this.languageMessage.event_id.required,
                // },
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
                "country_code[]": {
                    minlength: this.languageMessage.country_code.min,
                    // maxlength: this.languageMessage.country_code.max,
                    // digits: this.languageMessage.country_code.integer,
                },
                "phone[]": {
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
                "guest_name[]": {
                    required: this.languageMessage.guest_name.required,
                    minlength: this.languageMessage.guest_name.min,
                    maxlength: this.languageMessage.guest_name.max,
                },
                client_instructions: {
                    required: this.languageMessage.client_instructions.required,
                }
                // Add custom error messages for other fields here
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.closest(".col-sm-6").length) {
                    element.closest(".col-sm-6").append(error);
                } else if (element.closest(".col-sm-2").length) {
                    element.closest(".col-sm-2").append(error);
                } else if (element.closest(".col-sm-4").length) {
                    element.closest(".col-sm-4").append(error);
                } else if (element.closest(".col-sm-3").length) {
                    element.closest(".col-sm-3").append(error);
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

    handleOnLoad = () => {
        this.initializeDatePicker(
            "pickup-date-picker",
            "pick-up-time-picker",
            this.handleDriverOffDay
        );
        this.initializeTimePicker("pick-up-time-picker");
        if($('#service-types').val() == 3 || $('#service-types').val() == 7)
        {
            this.initializeDateTimePicker("flight-departure-time-picker");
        }
        const loggedUser = this.props.loggedUser;
        const loggedUserSlug = loggedUser.user_type?.slug ?? null;
        if(loggedUserSlug === null || ["admin", "admin-staff"].includes(loggedUserSlug))
        {
            this.initializeDateTimePicker("trip-ended-picker");
        }
        this.initializeGoogleMapAutoComplete("pick-up-location");
        this.initializeGoogleMapAutoComplete("drop-off-location");
        $(".additional-stops[id^='additionalStops_']").each((_, element) => {
            this.handleGoogleSearchOnAdditionalStops({ id: element.id });
        });
        
        
        this.calculatedBilling();
        this.validateEditBooking();

        

        $(function() {
            $("input[name='child_seat_required']").on("change", function() {
                if ($("#childSeatYes").is(":checked")) {
                    $('#child_1_age').val('<1 yo').trigger('change');
                    $('#child_2_age').val('<1 yo').trigger('change');
                    $('#childSeatCount').val(1);
                    
                    $("#childSeatContainer").show();
                    $('#childSeatOptions').css('display', 'flex');
                    $('#child1Age').show();
                    $('#child2Age').hide();
                } else {
                    $('#child_1_age').val('<1 yo').trigger('change');
                    $('#child_2_age').val('<1 yo').trigger('change');
                    $('#childSeatCount').val(1);
                    
                    $("#childSeatContainer").hide();
                    $('#child1Age').hide();
                    $('#child2Age').hide();
                    $('#extra-child-seat-charges-div').hide();
                }
            });
        
            $("#childSeatCount").on("change", function() {
                if ($("#childSeatCount").val() == 1) {
                    $('#child1Age').show();
                    $('#child2Age').hide();
                    $('#extra-child-seat-charges-div').hide();
                } else {
                    $('#child1Age').show();
                    $('#child2Age').show();
                    $('#extra-child-seat-charges-div').show();
                }
            });
        });
    };

    handleGoogleSearchOnAdditionalStops = ({id}) => {
        this.initializeGoogleMapAutoComplete(id);
    }
    
}
window.service = new EditBookings(props);
