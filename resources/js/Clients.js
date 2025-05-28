import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";

/**
 * Represents the Clients class.
 * @extends BaseClass
 */
export default class Clients extends BaseClass {
    /**
     * Constructor for the Client class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $("#clientTable").checkboxTable();
        $(document).on("click", "#addClientFormButton", this.handleSaveClient);
        $(document).on(
            "click",
            "#sortClient, #sortPhone, #sortEmail, #sortClientType, #sortContactPerson,#sortInvoice, #sortGroup, #sortStatus",
            this.handleSorting
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on("change", "#bulkAction", this.handleBulkAction);
        $(document).on(
            "change",
            ".clientStatusToggal",
            this.handleClientStatusToggal
        );
        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeleteClient
        );
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmClient
        );
        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on("click", "#editClientFormButton", this.handleEditClient);
        $(document).on("click", "#resetPassword", this.handleResetPassword);
        $(document).on("click", '#addHotel', this.handleMultiHotels);
        $(document).on("click", '.remove-hotel', this.handleRemoveHotel);
        $(document).on(
            "change",
            "#filterByUserType, #filterByClient",
            this.handleFilter
        );
        $(document).on(
            "click",
            "#clientPagination .pagination a",
            this.handlePagnation
        );
    }

    handleMultiHotels = () => {
        const lastHotelInputContainer = $(".multiple-hotels").last().parent().parent().parent();

        const lastId = parseInt(
            lastHotelInputContainer.find(".multiple-hotels").attr("id").split("_")[1]
        );

        const newId = lastId + 1;

        const hotels = this.props.hotels;

        const newHotelInput = `
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="multiHotelId_${newId}" class="form-label">Corporate</label>
                            <div class="d-flex">
                                <select name="multi_hotel_id[]" id="multiHotelId_${newId}" class="form-control form-select custom-select multiple-hotels">
                                    <option value="">Select one</option>
                                    ${hotels
                                        .map(
                                            (row) =>
                                                `<option value="${row.id}">${row.name}</option>`
                                        )
                                        .join("")}
                                </select>
                                <button type="button" class="remove-hotel btn ms-2"><span class="fas fa-times text-danger"></span></button>
                            </div>
                        </div>
                    </div>`;

        $(newHotelInput).insertAfter(lastHotelInputContainer);
    };
    

    handleRemoveHotel = ({ target }) => {
        $(target).parent().parent().parent().remove();
    };

    handlePagnation = (event) => {
        event.preventDefault();
        try {
            const page = $(event.target).attr("href").split("page=")[1];
            const sortOrder = $("#sortOrder").val();
            const sortField = $("#sortColumn").val();
            const params = {
                filterByUserType: $("#filterByUserType").val(),
                filterByClient: $("#filterByClient").val(),
                sortField: sortField,
                sortDirection: sortOrder,
                search: $("#search").val(),
                page: page,
            };
            $("#currentPage").val(page);
            const queryParams = $.param(params);
            const url = this.props.routes.filterClient + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortField);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleResetPassword = ({ target }) => {
        try {
            const email = $("#email").val();
            if (!email) {
                return;
            }
            const formData = new FormData();
            formData.append("email", email);
            const url = this.props.routes.resetPassword;
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

    handleDeleteModal = ({ target }) => {
        try {
            const clientId = $(target).data("id");
            this.openModal("deleteConfirmModal");
            $("#deleteConfirmationTitle").text(
                "Are you sure you want to delete this client?"
            );
            $("#deleteConfirmationTitle").data("id", clientId);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleBulkDeleteConfirmClient = () => {
        try {
            let clientIds = $("#deleteConfirmationTitle").data("id");
            clientIds = clientIds.split(",");
            const formData = new FormData();
            $.each(clientIds, (index, clientId) => {
                formData.append("client_ids[]", clientId);
            });
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleDeleteClient = () => {
        try {
            const clientId = $("#deleteConfirmationTitle").data("id");
            const formData = new FormData();
            formData.append("client_ids[]", clientId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    sendDeleteRequest = (formData) => {
        try {
            const url = this.props.routes.deleteClient;
            $("#loader").show();
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                        this.closeModal("deleteConfirmModal");
                        $(".clientTableCheckbox").prop("checked", false);
                        const clientIds = Array.from(
                            formData.getAll("client_ids[]")
                        );
                        $.each(clientIds, (index, clientId) => {
                            $(`tr[data-id="${clientId}"]`).remove();
                        });
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
    

    handleClientStatusToggal = ({ target }) => {
        try {
            $("#loader").show();
            const clientId = $(target).closest("tr").attr("data-id");
            const isChecked = target.checked;
            const status = isChecked ? "ACTIVE" : "INACTIVE";
            const formData = new FormData();
            formData.append("client_ids[]", clientId);
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
    handleBulkAction = ({ target }) => {
        try {
            let clientIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    clientIds.push(parseInt($(this).closest("tr").data("id"))); // Push the ID of the checked checkbox into the array
                }
            });
            if (clientIds.length) {
                const formData = new FormData();
                $.each(clientIds, (index, clientId) => {
                    formData.append("client_ids[]", clientId);
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
                            "Are you sure you want to delete these clients?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "id",
                            clientIds.join(",")
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
                        const clientIds = Array.from(
                            formData.getAll("client_ids[]")
                        );
                        const status = formData.get("status");
                        $(".clientTableCheckbox").prop("checked", false);
                        const isChecked = status === "ACTIVE" ? true : false;
                        $.each(clientIds, (index, clientId) => {
                            $(`#clientStatusToggal_${clientId}`).prop(
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

    handleFilter = () => {
        try {
            const sortDirection = $("#sortOrder").val();
            const sortField = $("#sortColumn").val();
            const params = {
                filterByUserType: $("#filterByUserType").val(),
                filterByClient: $("#filterByClient").val(),
                search: $("#search").val(),
                sortDirection: sortDirection,
                sortField: sortField,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterClient + "?" + queryParams;
            this.handleFilterRequest(url, sortDirection, sortField);
        } catch (error) {
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
                filterByClient: $("#filterByClient").val(),
                filterByUserType: $("#filterByUserType").val(),
                page: $("#currentPage").val(),
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterClient + "?" + queryParams;
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

    handleSaveClient = () => {
        $.validator.addMethod(
            "customStatus",
            function (value, element) {
                return value === "ACTIVE" || value === "INACTIVE";
            },
            this.languageMessage.status.custum_status
        );
        $.validator.addMethod(
            "customEmailValidation",
            function (value, element) {
                // Regular expression for email validation
                // This is a basic example, you can use a more comprehensive regex
                return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(
                    value
                );
            },
            this.languageMessage.email.email
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
        $("#createClientForm").validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                last_name: {
                    // required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                client_type: {
                    required: true,
                },
                hotel_id: {
                    required: true,
                },
                status: {
                    required: true,
                    customStatus: true,
                },
                country_code: {
                    minlength: 1,
                    maxlength: 3,
                    integer: true,
                },
                phone: {
                    digits: true,
                    minlength: 6,
                    maxlength: 10,
                },
                email: {
                    required: true,
                    email: true,
                    customEmailValidation: true,
                    remote: {
                        url: this.props.routes.checkUniqueEmail,
                        type: "post",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        data: {
                            email: function () {
                                return $("#email").val();
                            },
                        },
                        dataFilter: function (data) {
                            const response = JSON.parse(data);
                            return response.data.isvalid ? "true" : "false";
                        },
                    },
                },
                invoice: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                },
            },
            messages: {
                first_name: {
                    required: this.languageMessage.first_name.required,
                    minlength: this.languageMessage.first_name.min,
                    maxlength: this.languageMessage.first_name.max,
                },
                last_name: {
                    // required: this.languageMessage.last_name.required,
                    minlength: this.languageMessage.last_name.min,
                    maxlength: this.languageMessage.last_name.max,
                },
                client_type: {
                    required: this.languageMessage.client_type,
                },
                hotel_id: {
                    required: this.languageMessage.client.required,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
                country_code: {
                    minlength: this.languageMessage.country_code.min,
                    maxlength: this.languageMessage.country_code.max,
                    integer: this.languageMessage.country_code.integer,
                },
                phone: {
                    digits: this.languageMessage.phone.regex,
                    minlength: this.languageMessage.phone.min,
                    maxlength: this.languageMessage.phone.max,
                },
                email: {
                    required: this.languageMessage.email.required,
                    email: this.languageMessage.email.email,
                    remote: this.languageMessage.email.already_exist,
                },
                invoice: {
                    required: this.languageMessage.invoice.required,
                    minlength: this.languageMessage.invoice.min,
                    maxlength: this.languageMessage.invoice.max,
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

    handleEditClient = () => {
        $.validator.addMethod(
            "customStatus",
            function (value, element) {
                return value === "ACTIVE" || value === "INACTIVE";
            },
            this.languageMessage.status.custum_status
        );
        $.validator.addMethod(
            "customEmailValidation",
            function (value, element) {
                // Regular expression for email validation
                // This is a basic example, you can use a more comprehensive regex
                return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(
                    value
                );
            },
            this.languageMessage.email.email
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
        $("#editClientForm").validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                last_name: {
                    // required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                client_type: {
                    required: true,
                    integer: true,
                },
                hotel_id: {
                    required: true,
                },
                status: {
                    required: true,
                    customStatus: true,
                },
                country_code: {
                    minlength: 1,
                    maxlength: 3,
                    integer: true,
                },
                phone: {
                    digits: true,
                    minlength: 6,
                    maxlength: 10,
                },
                email: {
                    required: true,
                    email: true,
                    customEmailValidation: true,
                    remote: {
                        url: this.props.routes.checkUniqueEmail,
                        type: "post",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        data: {
                            email: function () {
                                return $("#email").val();
                            },
                            user_id: function () {
                                return $("#userId").val();
                            },
                        },
                        dataFilter: function (data) {
                            const response = JSON.parse(data);
                            return response.data.isvalid ? "true" : "false";
                        },
                    },
                },
                invoice: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                },
            },
            messages: {
                first_name: {
                    required: this.languageMessage.first_name.required,
                    minlength: this.languageMessage.first_name.min,
                    maxlength: this.languageMessage.first_name.max,
                },
                last_name: {
                    // required: this.languageMessage.last_name.required,
                    minlength: this.languageMessage.last_name.min,
                    maxlength: this.languageMessage.last_name.max,
                },
                client_type: {
                    required: this.languageMessage.client_type,
                },
                hotel_id: {
                    required: this.languageMessage.client.required,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
                country_code: {
                    minlength: this.languageMessage.country_code.min,
                    maxlength: this.languageMessage.country_code.max,
                    integer: this.languageMessage.country_code.integer,
                },
                phone: {
                    digits: this.languageMessage.phone.regex,
                    minlength: this.languageMessage.phone.min,
                    maxlength: this.languageMessage.phone.max,
                },
                email: {
                    required: this.languageMessage.email.required,
                    email: this.languageMessage.email.email,
                    remote: this.languageMessage.email.already_exist,
                },
                invoice: {
                    required: this.languageMessage.invoice.required,
                    minlength: this.languageMessage.invoice.min,
                    maxlength: this.languageMessage.invoice.max,
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
window.service = new Clients(props);
