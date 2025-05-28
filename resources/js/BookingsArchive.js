import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import $ from "jquery";
import "./JqueryCheckBox";
import "daterangepicker";

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

        $(document).on(
            "click",
            "#sortComment, #sortDriverRemark,#sortPickUpDate,#sortInstructions, #sortStatus, #sortVehicleType, #sortDriver, #sortContact, #sortClient, #sortDropOf, #sortPikUp, #sortType, #sortTime, #sortBooking",
            this.handleSorting
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on("change", "#driversList", this.handleFilter);

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
        const url = this.props.routes.permanentDeleteBookings;
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    this.closeModal("deleteConfirmModal");
                    $(".bookingArchiveTableCheckbox").prop("checked", false);
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
            const url = this.props.routes.filterBookingsArchive + "?" + queryParams;
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
            const url = this.props.routes.filterBookingsArchive + "?" + queryParams;
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
                    const tbody = $("#bookingArchiveTable tbody");
                    tbody.html(response.data.data.html);
                    if (sortOrder) {
                        $("#sortOrder").val(sortOrder);
                    }
                    $("#loader").hide();
                    $("#bookingArchiveTable").checkboxTable();
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };
    

    handleOnLoad = () => {
        $("#bookingArchiveTable").checkboxTable();
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
                startDate: moment("2000-01-01").startOf("year"), // Start from Jan 1, 2000
                endDate: moment().add(100, "years").endOf("year"), // End 100 years from now
                parentEl: ".wrapper",
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 1,
                ranges: {
                    "Max Range": [
                        moment("2000-01-01").startOf("year"), 
                        moment().add(100, "years").endOf("year")
                    ],
                    Today: [moment().startOf("day"), moment().endOf("day")],
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
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month"),
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
                    format: "DD/MM/YYYY HH:mm",
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
}

window.service = new Bookings(props);
