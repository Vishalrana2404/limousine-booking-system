import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";
import "daterangepicker";

/**
 * Represents the DriverSchedule class.
 * @extends BaseClass
 */
export default class DriverSchedule extends BaseClass {
    /**
     * Constructor for the Bookings class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        this.handleOnLoad();

        $(document).on("change", "#driversList", this.handleDriverFilter);
        $(document).on("change", "#hotelsList", this.handleHotelsFilter);
        $(document).on("change", "#eventsList", this.handleEventsFilter);
        $(document).on("change", "#usersList", this.handleUsersFilter);
        $(document).on("keyup", "#search", this.handleSearchFilter);
        $(document).on("keyup", "#search_by_booking_id", this.handleSearchFilter);
        $(document).on("change", "#exportFormat", this.handleExport);
        $(document).on("change", "#hideContact", this.toggalContact);
        $(document).on("change", "#hidePickup", this.toggalPickup);
        $(document).on("change", "#hideDropOff", this.toggalDropOff);
        $(document).on("change", "#hideAdditionalStops", this.toggalAdditionalStops);
        $(document).on("change", "#hideGuest", this.toggalGuest);
        $(document).on("change", "#hideEvent", this.toggalEvent);

        let self = this;

        $(document).on("click", ".pagination a", function (event) {
            event.preventDefault();
            let pageUrl = $(this).attr("href");

            if (pageUrl.includes("filter-reports")) {
                let pickupDateRange = $("#pickupDate").val();
                const hotelId = $("#hotelsList").val();
                const eventId = $("#eventsList").val();
                const driverId = $("#driversList").val();
                const userId = $("#usersList").val();
                const search = $("#search").val();
                const searchByBookingId = $("#search_by_booking_id").val();

                const params = {
                    pickupDateRange: pickupDateRange,
                    hotelId: hotelId,
                    eventId: eventId,
                    driverId: driverId,
                    userId: userId,
                    search: search,
                    searchByBookingId: searchByBookingId
                };

                const queryParams = $.param(params);

                self.handleFilterRequest(pageUrl + "&" + queryParams);
            } else {
                window.location.href = pageUrl;
            }
        });

        $(document).on(
            "click",
            "#sortBooking, #sortTime, #sortType,#sortPickUp,#sortDropOff, #sortAdditionalStops, #sortGuestName, #sortCorporate, #sortEvent, #sortContact, #sortRemarks, #sortDriver, #sortVehicle, #sortStatus, #sortBookedBy, #sortAccessGivenClients, #sortBookingDate",
            this.handleSorting
        );
    }
    toggalContact = ({ target }) => {
        const isChecked = target.checked;
        if (isChecked) {
            $(".toggalContact").show();
        } else {
            $(".toggalContact").hide();
        }
    };
    toggalPickup = ({ target }) => {
        const isChecked = target.checked;
        if (isChecked) {
            $(".toggalPickup").show();
        } else {
            $(".toggalPickup").hide();
        }
    };
    toggalDropOff = ({ target }) => {
        const isChecked = target.checked;
        if (isChecked) {
            $(".toggalDropOff").show();
        } else {
            $(".toggalDropOff").hide();
        }
    };
    toggalAdditionalStops = ({ target }) => {
        const isChecked = target.checked;
        if (isChecked) {
            $(".toggalAdditionalStops").show();
        } else {
            $(".toggalAdditionalStops").hide();
        }
    };
    toggalGuest = ({ target }) => {
        const isChecked = target.checked;
        if (isChecked) {
            $(".toggalGuest").show();
        } else {
            $(".toggalGuest").hide();
        }
    };
    toggalEvent = ({ target }) => {
        const isChecked = target.checked;
        if (isChecked) {
            $(".toggalEvent").show();
        } else {
            $(".toggalEvent").hide();
        }
    };

    handleSorting = ({ target }) => {
        try {
            const sortOrder = $("#sortOrder").val() === "asc" ? "desc" : "asc";
            let pickupDateRange = $("#pickupDate").val();
            const driverId = $("#driversList").val();
            const hotelId = $("#hotelsList").val();
            const userId = $("#usersList").val();
            const eventId = $("#eventsList").val();
            const params = {
                pickupDateRange: pickupDateRange,
                sortField: $(target).attr("id"),
                sortDirection: sortOrder,
                search: $("#search").val(),
                searchByBookingId: $("#search_by_booking_id").val(),
                driverId: driverId,
                hotelId: hotelId,
                userId: userId,
                eventId: eventId,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterReports + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleExportOptions = () => {
        const html = `<select id="exportFormat" class="form-control form-select custom-select">
                                <option value="">Export</option>
                                <option value="excel">Excel</option>
                            </select>`;
        $("#exportOptions").html(html);
    };

    handleOnLoad = () => {
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

        $("#pickupDate").daterangepicker(
            {
                autoUpdateInput: false, // Prevents automatic input filling with invalid dates
                parentEl: ".wrapper",
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 1,
                ranges: {
                    "Select A Range": [null, null],
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
                        moment()
                            .subtract(1, "month")
                            .startOf("month")
                            .startOf("day"),
                        moment()
                            .subtract(1, "month")
                            .endOf("month")
                            .endOf("day"),
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
                        moment()
                            .add(1, "month")
                            .startOf("month")
                            .startOf("day"),
                        moment().add(1, "month").endOf("month").endOf("day"),
                    ],
                    "This Week": [
                        moment().startOf("week").startOf("day"),
                        moment().endOf("week").endOf("day"),
                    ],
                    "Last Week": [
                        moment()
                            .subtract(1, "week")
                            .startOf("week")
                            .startOf("day"),
                        moment().subtract(1, "week").endOf("week").endOf("day"),
                    ],
                },
                locale: {
                    format: "DD/MM/YYYY HH:mm", // Format of the date displayed in the input
                },
            },
            (start, end, label) => {
                if (label === "Select A Range") {
                    $("#pickupDate").val("");
                } else {
                    $("#pickupDate").val(
                        start.format("DD/MM/YYYY HH:mm") + " - " + end.format("DD/MM/YYYY HH:mm")
                    );
                    this.handleDriverFilter(
                        start.format("DD/MM/YYYY HH:mm"),
                        end.format("DD/MM/YYYY HH:mm")
                    );
                }
            }
        );

        let calendarShown = 0;

        $("#pickupDate").on(
            "showCalendar.daterangepicker",
            function (ev, picker) {
                calendarShown++;

                if (calendarShown == 2) {
                    picker.setStartDate(
                        picker.startDate.clone().startOf("day")
                    );
                    picker.setEndDate(picker.endDate.clone().endOf("day"));
                    $(".drp-calendar.left .calendar-time .hourselect").val(0);
                    $(".drp-calendar.left .calendar-time .minuteselect").val(0);
                    $(".drp-calendar.right .calendar-time .hourselect").val(23);
                    $(".drp-calendar.right .calendar-time .minuteselect").val(
                        59
                    );
                }
            }
        );

        $("#pickupDate").on("hide.daterangepicker", function (ev, picker) {
            calendarShown = 0;
        });
    };

    handleDriverFilter = (startDate, endDate) => {
        try {
            let pickupDateRange = $("#pickupDate").val();
            const hotelId = $("#hotelsList").val();
            const eventId = $("#eventsList").val();
            const driverId = $("#driversList").val();
            const userId = $("#usersList").val();
            const search = $("#search").val();
            const searchByBookingId = $("#search_by_booking_id").val();

            if (startDate && endDate) {
                pickupDateRange = startDate + " - " + endDate;
            }

            const params = {
                pickupDateRange: pickupDateRange,
                hotelId: hotelId,
                eventId: eventId,
                driverId: driverId,
                userId: userId,
                search: search,
                searchByBookingId: searchByBookingId
            };

            const queryParams = $.param(params);
            const url =
                this.props.routes.filterReports + "?" + queryParams;
            this.handleFilterRequest(url);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleHotelsFilter = (startDate, endDate) => {
        try {
            let pickupDateRange = $("#pickupDate").val();
            const hotelId = $("#hotelsList").val();
            const eventId = $("#eventsList").val();
            const driverId = $("#driversList").val();
            const userId = $("#usersList").val();
            const search = $("#search").val();
            const searchByBookingId = $("#search_by_booking_id").val();

            if (startDate && endDate) {
                pickupDateRange = startDate + " - " + endDate;
            }

            const params = {
                pickupDateRange: pickupDateRange,
                hotelId: hotelId,
                eventId: eventId,
                driverId: driverId,
                userId: userId,
                search: search,
                searchByBookingId: searchByBookingId
            };

            const queryParams = $.param(params);
            const url =
                this.props.routes.filterReports + "?" + queryParams;
            this.handleFilterRequest(url);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleEventsFilter = (startDate, endDate) => {
        try {
            let pickupDateRange = $("#pickupDate").val();
            const hotelId = $("#hotelsList").val();
            const eventId = $("#eventsList").val();
            const driverId = $("#driversList").val();
            const userId = $("#usersList").val();
            const search = $("#search").val();
            const searchByBookingId = $("#search_by_booking_id").val();

            if (startDate && endDate) {
                pickupDateRange = startDate + " - " + endDate;
            }

            const params = {
                pickupDateRange: pickupDateRange,
                hotelId: hotelId,
                eventId: eventId,
                driverId: driverId,
                userId: userId,
                search: search,
                searchByBookingId: searchByBookingId
            };

            const queryParams = $.param(params);
            const url =
                this.props.routes.filterReports + "?" + queryParams;
            this.handleFilterRequest(url);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleUsersFilter = (startDate, endDate) => {
        try {
            let pickupDateRange = $("#pickupDate").val();
            const hotelId = $("#hotelsList").val();
            const eventId = $("#eventsList").val();
            const driverId = $("#driversList").val();
            const userId = $("#usersList").val();
            const search = $("#search").val();
            const searchByBookingId = $("#search_by_booking_id").val();

            if (startDate && endDate) {
                pickupDateRange = startDate + " - " + endDate;
            }

            const params = {
                pickupDateRange: pickupDateRange,
                hotelId: hotelId,
                eventId: eventId,
                driverId: driverId,
                userId: userId,
                search: search,
                searchByBookingId: searchByBookingId
            };

            const queryParams = $.param(params);
            const url =
                this.props.routes.filterReports + "?" + queryParams;
            this.handleFilterRequest(url);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleSearchFilter = (startDate, endDate) => {
        try {
            let pickupDateRange = $("#pickupDate").val();
            const hotelId = $("#hotelsList").val();
            const eventId = $("#eventsList").val();
            const driverId = $("#driversList").val();
            const userId = $("#usersList").val();
            const search = $("#search").val();
            const searchByBookingId = $("#search_by_booking_id").val();

            if (startDate && endDate) {
                pickupDateRange = startDate + " - " + endDate;
            }

            const params = {
                pickupDateRange: pickupDateRange,
                hotelId: hotelId,
                eventId: eventId,
                driverId: driverId,
                userId: userId,
                search: search,
                searchByBookingId: searchByBookingId
            };

            const queryParams = $.param(params);
            const url =
                this.props.routes.filterReports + "?" + queryParams;
            this.handleFilterRequest(url);
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
                    const tbody = $("#reportsTable tbody");
                    tbody.html(response.data.data.html);

                    const pagination = $(".card-footer");
                    pagination.html(response.data.data.pagination);

                    if (response.data.data.total > 0) {
                        this.handleExportOptions();
                    }
                    if (sortOrder) {
                        $("#sortOrder").val(sortOrder);
                    }
                    $("#loader").hide();
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };    

    handleExport = (startDate, endDate) => {
        try {
            let pickupDateRange = $("#pickupDate").val();
            let format = $("#exportFormat").val();
            const hotelId = $("#hotelsList").val();
            const eventId = $("#eventsList").val();
            const driverId = $("#driversList").val();
            const search = $("#search").val();
            const userId = $("#usersList").val();
            const searchByBookingId = $("#search_by_booking_id").val();
            const isDisplayContact = $("#hideContact").prop("checked")
                ? true
                : false;
            const isDisplayPickup = $("#hidePickup").prop("checked")
                ? true
                : false;
            const isDisplayDropOff = $("#hideDropOff").prop("checked")
                ? true
                : false;
            const isDisplayAdditionalStops = $("#hideAdditionalStops").prop("checked")
                ? true
                : false;
            const isDisplayGuest = $("#hideGuest").prop("checked")
                ? true
                : false;
            const isDisplayEvent = $("#hideEvent").prop("checked")
                ? true
                : false;
            if (startDate && endDate) {
                pickupDateRange = startDate + " - " + endDate;
            }
            if (!format) {
                return false;
            }
            const formData = new FormData();
            formData.append("pickupDateRange", pickupDateRange);
            formData.append("format", format);
            formData.append("driverId", driverId);
            formData.append("hotelId", hotelId);
            formData.append("eventId", eventId);
            formData.append("userId", userId);
            formData.append("search", search);
            formData.append("searchByBookingId", searchByBookingId);
            formData.append("isDisplayContact", isDisplayContact);
            formData.append("isDisplayPickup", isDisplayPickup);
            formData.append("isDisplayDropOff", isDisplayDropOff);
            formData.append("isDisplayAdditionalStops", isDisplayAdditionalStops);
            formData.append("isDisplayGuest", isDisplayGuest);
            formData.append("isDisplayEvent", isDisplayEvent);
            const url = this.props.routes.exportData;
            this.sendPostRequest(url, formData, true, format);
        } catch (error) {
            $("#loader").hide();
            this.handleException(error);
        }
    };
    sendPostRequest = (url, formData, idsDownload = false, format = "") => {
        $("#loader").show();

        const config = {
            method: "post",
            url: url,
            data: formData,
        };
        // Conditionally set the responseType to 'blob' if format is not null
        if (format) {
            config.responseType = "blob";
        }
        axios(config)
            .then((response) => {
                $("#loader").hide();
                if (idsDownload) {
                    this.handleDownload(response, format);
                    $("#exportFormat").val("");
                } else {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    throw flash;
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };

    handleDownload = (response, format) => {
        let fileName = "schedule";
        let contentType = "image/jpeg";
        if (format === "excel") {
            fileName += ".xlsx";
        } else {
            fileName += ".jpg";
        }
        // $contentType = (format === 'excel') ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'image/jpeg';
        // Create a blob object from the response data
        const blob = new Blob([response.data], {
            type: response.headers["content-type"],
        });
        // Use the URL.createObjectURL to create a temporary URL for the blob
        const url = window.URL.createObjectURL(blob);
        // Create a temporary <a> element to trigger the download
        const a = document.createElement("a");
        a.style.display = "none";
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    };
}

window.service = new DriverSchedule(props);
