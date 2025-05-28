import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox.js";

/**
 * Represents the VehicleClass class.
 * @extends BaseClass
 */
export default class VehicleClass extends BaseClass {
    /**
     * Constructor for the VehicleClass class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $("#vehicleClassTable").checkboxTable();

        $(document).on(
            "click",
            "#submitVehicleClassFormButton",
            this.handleSaveVehicleClass
        );
        $(document).on(
            "click",
            "#updateVehicleClassFormButton",
            this.handleEditVehicleClass
        );

        // $(document).on("click", ".fa-edit", this.handleEditModal);
        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeleteVehicleClass
        );
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on(
            "change",
            ".vehicleClassStatusToggal",
            this.handleVehicleClassStatusToggal
        );
        $(document).on("change", "#bulkAction", this.handleBulkAction);
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmVehicleClass
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on(
            "click",
            "#sortClass, #sortSeating, #sortPax, #sortLuggages, #sortStatus",
            this.handleSorting
        );
        $(document).on(
            "click",
            "#vehicleClassPagination .pagination a",
            this.handlePagnation
        );
    }

    handlePagnation = (event) => {
        event.preventDefault();
        try {
            const page = $(event.target).attr("href").split("page=")[1];
            const sortOrder = $("#sortOrder").val();
            const sortDirection = $("#sortColumn").val();
            const params = {
                sortField: sortDirection,
                sortDirection: sortOrder,
                search: $("#search").val(),
                page: page,
            };
            $("#currentPage").val(page);
            const queryParams = $.param(params);
            const url =
                this.props.routes.filterVehicleClass + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortDirection);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleBulkDeleteConfirmVehicleClass = () => {
        let vehicleClassIds = $("#deleteConfirmationTitle").data(
            "vehicle-class-id"
        );
        vehicleClassIds = vehicleClassIds.split(",");
        const formData = new FormData();
        $.each(vehicleClassIds, (index, vehicleClassId) => {
            formData.append("vehicle_class_ids[]", vehicleClassId);
        });
        this.sendDeleteRequest(formData);
    };
    handleBulkAction = ({ target }) => {
        try {
            let vehicleClassIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    vehicleClassIds.push(
                        $(this).closest("tr").data("vehicle-class-id")
                    ); // Push the ID of the checked checkbox into the array
                }
            });
            if (vehicleClassIds.length) {
                const formData = new FormData();
                $.each(vehicleClassIds, (index, vehicleClassId) => {
                    formData.append("vehicle_class_ids[]", vehicleClassId);
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
                            "Are you sure you want to delete these vehicle classes?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "vehicle-class-id",
                            vehicleClassIds.join(",")
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
    handleBulkStatusUpdate = (formData) => {
        let url = this.props.routes.updateBulkStatus;
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    const vehicleClassIds = Array.from(
                        formData.getAll("vehicle_class_ids[]")
                    );
                    $(".vehicleClassTableCheckbox").prop("checked", false);
                    const status = formData.get("status");
                    const isChecked = status === "ACTIVE" ? true : false;
                    $.each(vehicleClassIds, (index, vehicleClassId) => {
                        $(`#vehicleClassStatusToggal_${vehicleClassId}`).prop(
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
    };
    handleDeleteVehicleClass = () => {
        try {
            const vehicleClassId = $("#deleteConfirmationTitle").data(
                "vehicle-class-id"
            );
            const formData = new FormData();
            formData.append("vehicle_class_ids[]", vehicleClassId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };
    sendDeleteRequest = (formData) => {
        try {
            $("#loader").show();
            const url = this.props.routes.deleteVehicleClass;
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                        this.closeModal("deleteConfirmModal");
                        $(".vehicleClassTableCheckbox").prop("checked", false);
                        const vehicleClassIds = Array.from(
                            formData.getAll("vehicle_class_ids[]")
                        );
                        $.each(vehicleClassIds, (index, vehicleClassId) => {
                            $(
                                `tr[data-vehicle-class-id="${vehicleClassId}"]`
                            ).remove();
                        });
                        $("#loader").hide();
                    }
                    throw flash;
                })
                .catch((error) => {
                    this.closeModal("deleteConfirmModal");
                    $("#loader").hide();
                    this.handleException(error);
                });
        } catch (error) {
            this.closeModal("deleteConfirmModal");
            $("#loader").hide();
            this.handleException(error);
        }
    };
    handleDeleteModal = ({ target }) => {
        const vehicleClassId = $(target).data("vehicle-class-id");
        this.openModal("deleteConfirmModal");
        $("#deleteConfirmationTitle").text(
            "Are you sure you want to delete this vehicle class?"
        );
        $("#deleteConfirmationTitle").data("vehicle-class-id", vehicleClassId);
    };
    handleVehicleClassStatusToggal = ({ target }) => {
        try {
            const vehicleClassId = $(target)
                .closest("tr")
                .attr("data-vehicle-class-id");
            const isChecked = target.checked;
            const status = isChecked ? "ACTIVE" : "INACTIVE";
            const formData = new FormData();
            formData.append("vehicle_class_ids[]", vehicleClassId);
            formData.append("status", status);
            const url = this.props.routes.updateBulkStatus;
            $("#loader").show();
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
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
            const url =
                this.props.routes.filterVehicleClass + "?" + queryParams;
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
            const url =
                this.props.routes.filterVehicleClass + "?" + queryParams;
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

    handleSaveVehicleClass = () => {
        $("#createVehicleClassForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                seating_capacity: {
                    required: true,
                    digits: true,
                    min: 1,
                    max: 45,
                },
                total_luggage: {
                    required: true,
                    digits: true,
                },
                total_pax: {
                    required: true,
                    digits: true,
                    max: {
                        depends: function (element) {
                            // Check if total_pax is greater than seating_capacity
                            return (
                                parseInt($(element).val()) >
                                parseInt(
                                    $("input[name='seating_capacity']").val()
                                )
                            );
                        },
                    },
                },
                status: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: this.languageMessage.class_name.required,
                    minlength: this.languageMessage.class_name.min,
                    maxlength: this.languageMessage.class_name.max,
                },
                seating_capacity: {
                    required: this.languageMessage.seating_capacity.required,
                    digits: this.languageMessage.seating_capacity.digits,
                    min: this.languageMessage.seating_capacity.min,
                    max: this.languageMessage.seating_capacity.max,
                },
                total_luggage: {
                    required: this.languageMessage.total_luggage.required,
                    digits: this.languageMessage.total_luggage.digits,
                },
                total_pax: {
                    required: this.languageMessage.total_pax.required,
                    digits: this.languageMessage.total_pax.digits,
                    max: this.languageMessage.total_pax.max,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                element.closest(".form-group").append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            },
        });
    };
    handleEditVehicleClass = () => {
        $("#updateVehicleClassForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                seating_capacity: {
                    required: true,
                    digits: true,
                    min: 1,
                    max: 45,
                },
                total_luggage: {
                    required: true,
                    digits: true,
                },
                total_pax: {
                    required: true,
                    digits: true,
                    max: {
                        depends: function (element) {
                            // Check if total_pax is greater than seating_capacity
                            return (
                                parseInt($(element).val()) >
                                parseInt(
                                    $("input[name='seating_capacity']").val()
                                )
                            );
                        },
                    },
                },
                status: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: this.languageMessage.class_name.required,
                    minlength: this.languageMessage.class_name.min,
                    maxlength: this.languageMessage.class_name.max,
                },
                seating_capacity: {
                    required: this.languageMessage.seating_capacity.required,
                    digits: this.languageMessage.seating_capacity.digits,
                    min: this.languageMessage.seating_capacity.min,
                    max: this.languageMessage.seating_capacity.max,
                },
                total_luggage: {
                    required: this.languageMessage.total_luggage.required,
                    digits: this.languageMessage.total_luggage.digits,
                },
                total_pax: {
                    required: this.languageMessage.total_pax.required,
                    digits: this.languageMessage.total_pax.digits,
                    max: this.languageMessage.total_pax.max,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                element.closest(".form-group").append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            },
        });
    };
    handleCloseModal = () => {
        this.closeModal("deleteConfirmModal");
        $("#bulkAction").val("");
    };
}
window.service = new VehicleClass(props);
