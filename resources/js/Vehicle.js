import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox.js";

/**
 * Represents the Vehicle.
 * @extends BaseClass
 */
export default class Vehicle extends BaseClass {
    /**
     * Constructor for the Vehicle.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $("#vehicleTable").checkboxTable();

        $(document).on(
            "click",
            "#submitVehicleFormButton",
            this.handleSaveVehicle
        );
        $(document).on(
            "click",
            "#updateVehicleFormButton",
            this.handleEditVehicle
        );

        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeleteVehicle
        );
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on(
            "change",
            ".vehicleStatusToggal",
            this.handleVehicleStatusToggal
        );
        $(document).on("change", "#bulkAction", this.handleBulkAction);
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmVehicle
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on(
            "click",
            "#sortClass, #sortNumber, #sortBrand, #sortModel, #sortStatus",
            this.handleSorting
        );
        $(document).on(
            "click",
            "#vehiclePagination .pagination a",
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
            const url = this.props.routes.filterVehicle + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortField);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleBulkDeleteConfirmVehicle = () => {
        let vehicleIds = $("#deleteConfirmationTitle").data("vehicle-id");
        vehicleIds = vehicleIds.split(",");
        const formData = new FormData();
        $.each(vehicleIds, (index, vehicleId) => {
            formData.append("vehicle_ids[]", vehicleId);
        });
        this.sendDeleteRequest(formData);
    };
    handleBulkAction = ({ target }) => {
        try {
            let vehicleIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    vehicleIds.push($(this).closest("tr").data("vehicle-id")); // Push the ID of the checked checkbox into the array
                }
            });
            if (vehicleIds.length) {
                const formData = new FormData();
                $.each(vehicleIds, (index, vehicleId) => {
                    formData.append("vehicle_ids[]", vehicleId);
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
                            "Are you sure you want to delete these vehicles?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "vehicle-id",
                            vehicleIds.join(",")
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
        $("#loader").show();
        let url = this.props.routes.updateBulkStatus;
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    const vehicleIds = Array.from(
                        formData.getAll("vehicle_ids[]")
                    );
                    $(".vehicleTableCheckbox").prop("checked", false);
                    const status = formData.get("status");
                    const isChecked = status === "ACTIVE" ? true : false;
                    $.each(vehicleIds, (index, vehicleId) => {
                        $(`#vehicleStatusToggal_${vehicleId}`).prop(
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
    handleDeleteVehicle = () => {
        try {
            const vehicleId = $("#deleteConfirmationTitle").data("vehicle-id");
            const formData = new FormData();
            formData.append("vehicle_ids[]", vehicleId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };
    sendDeleteRequest = (formData) => {
        try {
            $("#loader").show();
            const url = this.props.routes.deleteVehicle;
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                        $(".vehicleTableCheckbox").prop("checked", false);
                        this.closeModal("deleteConfirmModal");
                        const vehicletIds = Array.from(
                            formData.getAll("vehicle_ids[]")
                        );
                        $.each(vehicletIds, (index, vehicletId) => {
                            $(`tr[data-vehicle-id="${vehicletId}"]`).remove();
                        });
                        $("#loader").hide();
                    }
                    throw flash;
                })
                .catch((error) => {
                    $(".vehicleTableCheckbox").prop("checked", false);
                    this.closeModal("deleteConfirmModal");
                    $("#loader").hide();
                    this.handleException(error);
                });
        } catch (error) {
            $(".vehicleTableCheckbox").prop("checked", false);
            this.closeModal("deleteConfirmModal");
            $("#loader").hide();
            this.handleException(error);
        }
    };
    handleDeleteModal = ({ target }) => {
        const vehicleId = $(target).data("vehicle-id");
        this.openModal("deleteConfirmModal");
        $("#deleteConfirmationTitle").text(
            "Are you sure you want to delete this vehicle?"
        );
        $("#deleteConfirmationTitle").data("vehicle-id", vehicleId);
    };
    handleVehicleStatusToggal = ({ target }) => {
        try {
            const vehicleId = $(target).closest("tr").attr("data-vehicle-id");
            const isChecked = target.checked;
            const status = isChecked ? "ACTIVE" : "INACTIVE";
            const formData = new FormData();
            formData.append("vehicle_ids[]", vehicleId);
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
            const sortField = $(target).attr("id");
            const params = {
                sortField: sortField,
                sortDirection: sortOrder,
                search: $("#search").val(),
                page: $("#currentPage").val(),
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterVehicle + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortField);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleFilter = () => {
        try {
            const sortOrder = $("#sortOrder").val();
            const sortField = $("#sortColumn").val();
            const params = {
                search: $("#search").val(),
                sortField: sortField,
                sortDirection: sortOrder,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterVehicle + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortField);
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
    handleSaveVehicle = () => {
        $("#createVehicleForm").validate({
            rules: {
                vehicle_class: {
                    required: true,
                },
                vehicle_number: {
                    required: true,
                    remote: {
                        url: this.props.routes.checkUniqueVehicleNumber,
                        type: "post",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        data: {
                            vehicle_number: function () {
                                return $("#vehicle_number").val();
                            },
                        },
                        dataFilter: function (data) {
                            const response = JSON.parse(data);
                            return response.data.isvalid ? "true" : "false";
                        },
                    },
                },
                image: {
                    accept: "image/jpeg, image/png, image/jpg",
                },
                status: {
                    required: true,
                },
            },
            messages: {
                vehicle_class: {
                    required: this.languageMessage.vehicle_class.required,
                },
                vehicle_number: {
                    required: this.languageMessage.vehicle_number.required,
                    remote: this.languageMessage.vehicle_number.already_exist,
                },
                image: {
                    accept: this.languageMessage.image.accept,
                },
                status: this.languageMessage.status.required,
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
    handleEditVehicle = () => {
        $("#updateVehicleForm").validate({
            rules: {
                vehicle_class: {
                    required: true,
                },
                vehicle_number: {
                    required: true,
                    remote: {
                        url: this.props.routes.checkUniqueVehicleNumber,
                        type: "post",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        data: {
                            vehicle_number: function () {
                                return $("#vehicle_number").val();
                            },
                            vehicle_id: function () {
                                return $("#vehicleId").val();
                            },
                        },
                        dataFilter: function (data) {
                            const response = JSON.parse(data);
                            return response.data.isvalid ? "true" : "false";
                        },
                    },
                },
                image: {
                    accept: "image/jpeg, image/png, image/jpg",
                },
                status: {
                    required: true,
                },
            },
            messages: {
                vehicle_class: {
                    required: this.languageMessage.vehicle_class.required,
                },
                vehicle_number: {
                    required: this.languageMessage.vehicle_number.required,
                    remote: this.languageMessage.vehicle_number.already_exist,
                },
                image: {
                    accept: this.languageMessage.image.accept,
                },
                status: this.languageMessage.status.required,
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
window.service = new Vehicle(props);
