import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";

/**
 * Represents the Hotels class.
 * @extends BaseClass
 */
export default class Hotels extends BaseClass {
    /**
     * Constructor for the Hotels class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $("#hotelsTable").checkboxTable();
        $(document).on(
            "click",
            "#addHotelFormButton, #updateHotelFormButton",
            this.validateHotelCreateUpdateForm
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on("click", "#sortName, #sortStatus", this.handleSorting);
        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeleteHotels
        );
        $(document).on("change", "#bulkAction", this.handleBulkAction);
        $(document).on(
            "change",
            ".hotelStatusToggal",
            this.handleHotelStatusToggal
        );
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmHotel
        );
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on(
            "click",
            "#hotelPagination .pagination a",
            this.handlePagnation
        );
    }

    handlePagnation = (event) => {
        event.preventDefault();
        try {
            const page = $(event.target).attr("href").split("page=")[1];
            const sortOrder = $("#sortOrder").val();
            const sortField = $("#sortColumn").val();
            const params = {
                sortField: sortField,
                sortDirection: sortOrder,
                search: $("#search").val(),
                page: page,
            };
            $("#currentPage").val(page);
            const queryParams = $.param(params);
            const url = this.props.routes.filterHotels + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortField);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleCloseModal = () => {
        this.closeModal("deleteConfirmModal");
        $("#bulkAction").val("");
    };
    handleBulkDeleteConfirmHotel = () => {
        try {
            let hotelIds = $("#deleteConfirmationTitle").data("id");
            hotelIds = hotelIds.split(",");
            const formData = new FormData();
            $.each(hotelIds, (index, hotelId) => {
                formData.append("hotel_ids[]", hotelId);
            });
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleHotelStatusToggal = ({ target }) => {
        try {
            $("#loader").show();
            const hotelId = $(target).closest("tr").attr("data-id");
            const isChecked = target.checked;
            const status = isChecked ? "ACTIVE" : "INACTIVE";
            const formData = new FormData();
            formData.append("hotel_ids[]", hotelId);
            formData.append("status", status);
            const url = this.props.routes.upadateBulkStatus;
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                    }
                    throw flash;
                })
                .catch((error) => {
                    $("#loader").hide();
                    this.handleException(error);
                });
        } catch (error) {
            $("#loader").hide();
            this.handleException(error);
        }
    };

    handleBulkStatusUpdate = (formData) => {
        try {
            let url = this.props.routes.upadateBulkStatus;
            $("#loader").show();
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                        const hotelIds = Array.from(
                            formData.getAll("hotel_ids[]")
                        );
                        const status = formData.get("status");
                        $(".hotelTableCheckbox").prop("checked", false);
                        const isChecked = status === "ACTIVE" ? true : false;
                        $.each(hotelIds, (index, hotelId) => {
                            $(`#hotelStatusToggal_${hotelId}`).prop(
                                "checked",
                                isChecked
                            );
                        });
                        $("#loader").hide();
                    }
                    throw flash;
                })
                .catch((error) => {
                    $("#loader").hide();
                    this.handleException(error);
                });
        } catch (error) {
            $("#loader").hide();
            this.handleException(error);
        }
    };

    handleBulkAction = ({ target }) => {
        try {
            let hotelsIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    hotelsIds.push(parseInt($(this).closest("tr").data("id"))); // Push the ID of the checked checkbox into the array
                }
            });
            if (hotelsIds.length) {
                const formData = new FormData();
                $.each(hotelsIds, (index, hotelsId) => {
                    formData.append("hotel_ids[]", hotelsId);
                });
                switch ($(target).val()) {
                    case "active":
                        formData.append("status", "ACTIVE");
                        this.handleBulkStatusUpdate(formData);
                        break;
                    case "inactive":
                        formData.append("status", "INACTIVE");
                        this.handleBulkStatusUpdate(formData);
                        break;
                    case "delete":
                        this.openModal("deleteConfirmModal");
                        $("#deleteConfirmationTitle").text(
                            "Are you sure you want to delete these Corporates?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "id",
                            hotelsIds.join(",")
                        );
                        $("#deleteConfirmButton").attr(
                            "id",
                            "deleteBulkConfirmButton"
                        );
                        break;
                    default:
                        break;
                }
            } else {
                throw new ErrorHandler(422, this.languageMessage.atleast_one);
            }
        } catch (error) {
            this.handleException(error);
        }
    };

    handleDeleteHotels = () => {
        try {
            const hotelId = $("#deleteConfirmationTitle").data("id");
            const formData = new FormData();
            formData.append("hotel_ids[]", hotelId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    sendDeleteRequest = (formData) => {
        try {
            const url = this.props.routes.deleteHotels;
            $("#loader").show();
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                        this.closeModal("deleteConfirmModal");
                        $(".hotelTableCheckbox").prop("checked", false);
                        const hotelIds = Array.from(
                            formData.getAll("hotel_ids[]")
                        );
                        $.each(hotelIds, (index, hotelId) => {
                            $(`tr[data-id="${hotelId}"]`).remove();
                        });
                        $("#loader").hide();
                    }
                    throw flash;
                })
                .catch((error) => {
                    $("#loader").hide();
                    this.handleException(error);
                });
        } catch (error) {
            $("#loader").hide();
            this.handleException(error);
        }
    };

    handleDeleteModal = ({ target }) => {
        try {
            const hotelId = $(target).data("id");
            this.openModal("deleteConfirmModal");
            $("#deleteConfirmationTitle").text(
                "Are you sure you want to delete this Corporate?"
            );
            $("#deleteConfirmationTitle").data("id", hotelId);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleSorting = ({ target }) => {
        try {
            const sortOrder = $("#sortOrder").val() === "asc" ? "desc" : "asc";
            const sortColumn = $(target).attr("id");
            const params = {
                sortField: sortColumn,
                sortDirection: sortOrder,
                search: $("#search").val(),
                page: $("#currentPage").val(),
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterHotels + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortColumn);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleFilter = () => {
        try {
            const sortOrder = $("#sortOrder").val();
            const sortColumn = $("#sortColumn").val();
            const params = {
                sortDirection: sortOrder,
                sortField: sortColumn,
                search: $("#search").val(),
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterHotels + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortColumn);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleFilterRequest = (url, sortOrder = null, sortColumn = null) => {
        $("#loader").show();
        axios
            .get(url)
            .then((response) => {
                const statusCode = response.data.status.code;
                if (statusCode === 200) {
                    const dyanmicHtml = $("#dyanmicHtml");
                    dyanmicHtml.html(response.data.data.html);
                    if (sortOrder) {
                        $("#sortOrder").val(sortOrder);
                        $("#sortColumn").val(sortColumn);
                    }
                    $("#loader").hide();
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };

    validateHotelCreateUpdateForm = () => {
        $("#hotelForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                term_conditions: {
                    required: true,
                    minlength: 3,
                },
                status: {
                    required: true,
                },
                per_trip_arr: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                per_trip_dep: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                per_trip_transfer: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                per_trip_delivery: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                per_hour_rate: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                peak_period_surcharge: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                mid_night_surcharge_23_seats: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                midnight_surcharge_greater_then_23_seats: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                arrivel_waiting_time: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                departure_and_transfer_waiting: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                last_min_request_23_seats: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                last_min_request_greater_then_23_seats: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                outside_city_surcharge_23_seats: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                outside_city_surcharge_greater_then_23_seats: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                additional_stop: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
                misc_charges: {
                    pattern: /^\d{1,8}(?:\.\d{1,2})?$/,
                },
            },
            messages: {
                name: {
                    required: this.languageMessage.name.required,
                    minlength: this.languageMessage.name.min,
                    maxlength: this.languageMessage.name.max,
                },
                term_conditions: {
                    required: this.languageMessage.term_conditions.required,
                    minlength: this.languageMessage.term_conditions.min,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
                per_trip_arr: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                per_trip_dep: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                per_trip_transfer: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                per_trip_delivery: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                per_hour_rate: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                peak_period_surcharge: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                mid_night_surcharge_23_seats: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                midnight_surcharge_greater_then_23_seats: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                arrivel_waiting_time: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                departure_and_transfer_waiting: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                last_min_request_23_seats: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                last_min_request_greater_then_23_seats: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                outside_city_surcharge_23_seats: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                outside_city_surcharge_greater_then_23_seats: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                additional_stop: {
                    pattern: this.languageMessage.decimal.pattern,
                },
                misc_charges: {
                    pattern: this.languageMessage.decimal.pattern,
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                if (element.closest(".form-group.row").length) {
                    if (element.closest(".col-sm-8").length) {
                        element.closest(".col-sm-8").append(error);
                    } else if (element.closest(".col-sm-5").length) {
                        element.closest(".col-sm-5").append(error);
                    } else {
                        element.closest(".form-group").append(error);
                    }
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
        this.initializeFairBillingValidation();
    };

    initializeFairBillingValidation = () => {
        // $(".hotel-vehicle-fair-arrival").each((i, e) => {
        //     $(e).rules("add", {
        //         required: true,
        //         // digits: true,
        //         messages: {
        //             required: this.languageMessage.hotel_vehicle_fair_arrival.required,
        //             // digits: this.languageMessage.hotel_vehicle_fair_arrival.digits,
        //         },
        //     });
        // });
        // $(".hotel-vehicle-fair-departure").each((i, e) => {
        //     $(e).rules("add", {
        //         required: true,
        //         // digits: true,
        //         messages: {
        //             required: this.languageMessage.hotel_vehicle_fair_departure.required,
        //             // digits: this.languageMessage.hotel_vehicle_fair_departure.digits,
        //         },
        //     });
        // });
        // $(".hotel-vehicle-fair-transfer").each((i, e) => {
        //     $(e).rules("add", {
        //         required: true,
        //         // digits: true,
        //         messages: {
        //             required: this.languageMessage.hotel_vehicle_fair_transfer.required,
        //             // digits: this.languageMessage.hotel_vehicle_fair_transfer.digits,
        //         },
        //     });
        // });
        // $(".hotel-vehicle-fair-per-hour").each((i, e) => {
        //     $(e).rules("add", {
        //         required: true,
        //         // digits: true,
        //         messages: {
        //             required: this.languageMessage.hotel_vehicle_fair_per_hour.required,
        //             // digits: this.languageMessage.hotel_vehicle_fair_per_hour.digits,
        //         },
        //     });
        // });
    }
}

window.service = new Hotels(props);
