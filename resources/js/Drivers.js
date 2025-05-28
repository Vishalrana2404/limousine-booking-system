import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";

/**
 * Represents the Drivers class.
 * @extends BaseClass
 */
export default class Drivers extends BaseClass {
    /**
     * Constructor for the Drivers class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $("#driverTable").checkboxTable();
        $(document).on("click", "#addDriverFormButton", this.handleSaveDriver);
        $(document).on("click", "#updateUserFormButton", this.handleEditDriver);
        $(document).on(
            "click",
            "#sortName, #sortPhone, #sortVehicle, #sortClass, #sortRace, #sortDriverType",
            this.handleSorting
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on("change", "#bulkAction", this.handleBulkAction);

        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeleteDrivers
        );
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmDriver
        );
        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on(
            "click",
            "#telegramInfo",
            this.handleTelegramInfoOpenModal
        );
        $(document).on(
            "click",
            "#closeTelegramInfoButton, #closeTelegramInfoIcon",
            this.handleTelegramInfoCloseModal
        );
        $(document).on(
            "click",
            "#driverPagination .pagination a",
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
            const url = this.props.routes.filterDrivers + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortField);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleTelegramInfoCloseModal = () => {
        this.closeModal("telegramInfoModal");
    };
    handleTelegramInfoOpenModal = () => {
        this.openModal("telegramInfoModal");
    };
    handleDeleteModal = ({ target }) => {
        try {
            const driverId = $(target).data("id");
            this.openModal("deleteConfirmModal");
            $("#deleteConfirmationTitle").text(
                "Are you sure you want to delete these Driver?"
            );
            $("#deleteConfirmationTitle").data("id", driverId);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleBulkDeleteConfirmDriver = () => {
        try {
            let driverIds = $("#deleteConfirmationTitle").data("id");
            driverIds = driverIds.split(",");
            const formData = new FormData();
            $.each(driverIds, (index, driverId) => {
                formData.append("driver_ids[]", driverId);
            });
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleDeleteDrivers = () => {
        try {
            const driverId = $("#deleteConfirmationTitle").data("id");
            const formData = new FormData();
            formData.append("driver_ids[]", driverId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    sendDeleteRequest = (formData) => {
        try {
            const url = this.props.routes.deleteDrivers;
            $("#loader").show();
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                        this.closeModal("deleteConfirmModal");
                        $(".driverTableCheckbox").prop("checked", false);
                        const driverIds = Array.from(
                            formData.getAll("driver_ids[]")
                        );
                        $.each(driverIds, (index, driverId) => {
                            $(`tr[data-id="${driverId}"]`).remove();
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
            let driverIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    driverIds.push(parseInt($(this).closest("tr").data("id"))); // Push the ID of the checked checkbox into the array
                }
            });
            if (driverIds.length) {
                const formData = new FormData();
                $.each(driverIds, (index, driverId) => {
                    formData.append("driver_ids[]", driverId);
                });
                switch ($(target).val()) {
                    case "delete":
                        this.openModal("deleteConfirmModal");
                        $("#deleteConfirmationTitle").text(
                            "Are you sure you want to delete these drivers?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "id",
                            driverIds.join(",")
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

    handleFilter = () => {
        try {
            const sortOrder = $("#sortOrder").val();
            const sortField = $("#sortColumn").val();
            const params = {
                search: $("#search").val(),
                sortDirection: sortOrder,
                sortField: sortField,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterDrivers + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortField);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleSorting = ({ target }) => {
        try {
            const sortOrder = $("#sortOrder").val() === "asc" ? "desc" : "asc";
            const sortField = $(target).attr("id");
            const params = {
                sortField: $(target).attr("id"),
                sortDirection: sortOrder,
                search: $("#search").val(),
                page: $("#currentPage").val(),
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterDrivers + "?" + queryParams;
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

    handleSaveDriver = () => {
        $.validator.addMethod(
            "customStatus",
            function (value, element) {
                return value === "ACTIVE" || value === "INACTIVE";
            },
            this.languageMessage.status.custum_status
        );
        $.validator.addMethod(
            "onlyString",
            function (value, element) {
                return (
                    this.optional(element) ||
                    /^[a-zA-Z\s!@#$%^&*()_+\-=\[\]{};:'",.<>/?\\|`~]*$/.test(
                        value
                    )
                );
            },
            this.languageMessage.only_string
        );
        $.validator.addMethod(
            "validateChatID",
            function (value, element) {
                return this.optional(element) || /^-?\d+$/.test(value);
            },
            this.languageMessage.chat_id.valid
        );
        $("#createDriverForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                status: {
                    required: true,
                    customStatus: true,
                },
                country_code: {
                    required: true,
                    minlength: 1,
                    maxlength: 3,
                    integer: true,
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 10,
                },
                chat_id: {
                    required: true,
                    minlength: 11,
                    maxlength: 11,
                    validateChatID: true,
                },
                email: {
                    email: true,
                },
                type: {
                    required: true,
                },
                gender: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: this.languageMessage.driver_name.required,
                    minlength: this.languageMessage.driver_name.min,
                    maxlength: this.languageMessage.driver_name.max,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
                country_code: {
                    required: this.languageMessage.country_code.required,
                    minlength: this.languageMessage.country_code.min,
                    maxlength: this.languageMessage.country_code.max,
                    integer: this.languageMessage.country_code.integer,
                },
                phone: {
                    required: this.languageMessage.phone.required,
                    digits: this.languageMessage.phone.regex,
                    minlength: this.languageMessage.phone.min,
                    maxlength: this.languageMessage.phone.max,
                },
                chat_id: {
                    required: this.languageMessage.chat_id.required,
                    minlength: this.languageMessage.chat_id.min,
                    maxlength: this.languageMessage.chat_id.max,
                },
                email: {
                    email: this.languageMessage.email.email,
                },
                type: {
                    required: this.languageMessage.type,
                },
                gender: {
                    required: this.languageMessage.gender,
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

    handleEditDriver = () => {
        $.validator.addMethod(
            "customStatus",
            function (value, element) {
                return value === "ACTIVE" || value === "INACTIVE";
            },
            this.languageMessage.status.custum_status
        );
        $.validator.addMethod(
            "onlyString",
            function (value, element) {
                return (
                    this.optional(element) ||
                    /^[a-zA-Z\s!@#$%^&*()_+\-=\[\]{};:'",.<>/?\\|`~]*$/.test(
                        value
                    )
                );
            },
            this.languageMessage.only_string
        );
        $.validator.addMethod(
            "validateChatID",
            function (value, element) {
                return this.optional(element) || /^-?\d+$/.test(value);
            },
            this.languageMessage.chat_id.valid
        );
        $("#editDriverForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                status: {
                    required: true,
                    customStatus: true,
                },
                country_code: {
                    required: true,
                    minlength: 1,
                    maxlength: 3,
                    integer: true,
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 10,
                },
                chat_id: {
                    required: true,
                    minlength: 11,
                    maxlength: 11,
                    validateChatID: true,
                },
                email: {
                    email: true,
                },
                type: {
                    required: true,
                },
                gender: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: this.languageMessage.driver_name.required,
                    minlength: this.languageMessage.driver_name.min,
                    maxlength: this.languageMessage.driver_name.max,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
                country_code: {
                    required: this.languageMessage.country_code.required,
                    minlength: this.languageMessage.country_code.min,
                    maxlength: this.languageMessage.country_code.max,
                    integer: this.languageMessage.country_code.integer,
                },
                phone: {
                    required: this.languageMessage.phone.required,
                    digits: this.languageMessage.phone.regex,
                    minlength: this.languageMessage.phone.min,
                    maxlength: this.languageMessage.phone.max,
                },
                chat_id: {
                    required: this.languageMessage.chat_id.required,
                    minlength: this.languageMessage.chat_id.min,
                    maxlength: this.languageMessage.chat_id.max,
                },
                email: {
                    email: this.languageMessage.email.email,
                },
                type: {
                    required: this.languageMessage.type,
                },
                gender: {
                    required: this.languageMessage.gender,
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
window.service = new Drivers(props);
