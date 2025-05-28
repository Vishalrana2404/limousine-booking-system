import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";

/**
 * Represents the Events class.
 * @extends BaseClass
 */
export default class Events extends BaseClass {
    /**
     * Constructor for the Events class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $("#eventsTable").checkboxTable();
        $(document).on(
            "click",
            "#addEventFormButton, #updateEventFormButton",
            this.validateEventCreateUpdateForm
        );
        $(document).on(
            "click",
            "#sortCorporate, #sortName, #sortStatus",
            this.handleSorting
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeleteEvents
        );
        $(document).on("change", "#bulkAction", this.handleBulkAction);
        $(document).on(
            "change",
            ".eventStatusToggal",
            this.handleEventStatusToggal
        );
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmEvent
        );
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on(
            "click",
            "#eventPagination .pagination a",
            this.handlePagnation
        );
    }

    handlePagnation = (event) => {
        event.preventDefault();
        try {
            const page = $(event.target).attr("href").split("page=")[1];

            const loggedUser = this.props.loggedUser;
            const loggedUserSlug = loggedUser.user_type?.slug ?? null;

            const filterUrl = (loggedUserSlug === null || ["admin", "admin-staff"].includes(loggedUserSlug)) ? this.props.routes.filterEvents : this.props.routes.filterEventsForClient;

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
            const url = filterUrl + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortField);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleCloseModal = () => {
        this.closeModal("deleteConfirmModal");
        $("#bulkAction").val("");
    };
    handleBulkDeleteConfirmEvent = () => {
        try {
            let eventIds = $("#deleteConfirmationTitle").data("id");
            eventIds = eventIds.split(",");
            const formData = new FormData();
            $.each(eventIds, (index, eventId) => {
                formData.append("event_ids[]", eventId);
            });
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleEventStatusToggal = ({ target }) => {
        try {
            $("#loader").show();
            const eventId = $(target).closest("tr").attr("data-id");
            const isChecked = target.checked;
            const status = isChecked ? "ACTIVE" : "INACTIVE";
            const formData = new FormData();
            formData.append("event_ids[]", eventId);
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
                        const eventIds = Array.from(
                            formData.getAll("event_ids[]")
                        );
                        const status = formData.get("status");
                        $(".eventTableCheckbox").prop("checked", false);
                        const isChecked = status === "ACTIVE" ? true : false;
                        $.each(eventIds, (index, eventId) => {
                            $(`#eventStatusToggal_${eventId}`).prop(
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
            let eventsIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    eventsIds.push(parseInt($(this).closest("tr").data("id"))); // Push the ID of the checked checkbox into the array
                }
            });
            if (eventsIds.length) {
                const formData = new FormData();
                $.each(eventsIds, (index, eventsId) => {
                    formData.append("event_ids[]", eventsId);
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
                            "Are you sure you want to delete these Events?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "id",
                            eventsIds.join(",")
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

    handleDeleteEvents = () => {
        try {
            const eventId = $("#deleteConfirmationTitle").data("id");
            const formData = new FormData();
            formData.append("event_ids[]", eventId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    sendDeleteRequest = (formData) => {
        try {
            const url = this.props.routes.deleteEvents;
            $("#loader").show();
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                        this.closeModal("deleteConfirmModal");
                        $(".eventTableCheckbox").prop("checked", false);
                        const eventIds = Array.from(
                            formData.getAll("event_ids[]")
                        );
                        $.each(eventIds, (index, eventId) => {
                            $(`tr[data-id="${eventId}"]`).remove();
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
            const eventId = $(target).data("id");
            this.openModal("deleteConfirmModal");
            $("#deleteConfirmationTitle").text(
                "Are you sure you want to delete this Event?"
            );
            $("#deleteConfirmationTitle").data("id", eventId);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleSorting = ({ target }) => {
        try {
            const loggedUser = this.props.loggedUser;
            const loggedUserSlug = loggedUser.user_type?.slug ?? null;

            const filterUrl = (loggedUserSlug === null || ["admin", "admin-staff"].includes(loggedUserSlug)) ? this.props.routes.filterEvents : this.props.routes.filterEventsForClient;
            const sortOrder = $("#sortOrder").val() === "asc" ? "desc" : "asc";
            const sortColumn = $(target).attr("id");
            const params = {
                sortField: sortColumn,
                sortDirection: sortOrder,
                search: $("#search").val(),
                page: $("#currentPage").val(),
            };
            const queryParams = $.param(params);
            const url = filterUrl + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortColumn);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleFilter = () => {
        try {

            const loggedUser = this.props.loggedUser;
            const loggedUserSlug = loggedUser.user_type?.slug ?? null;

            const filterUrl = (loggedUserSlug === null || ["admin", "admin-staff"].includes(loggedUserSlug)) ? this.props.routes.filterEvents : this.props.routes.filterEventsForClient;
            
            const sortOrder = $("#sortOrder").val();
            const sortColumn = $("#sortColumn").val();
            const params = {
                sortDirection: sortOrder,
                sortField: sortColumn,
                search: $("#search").val(),
            };
            const queryParams = $.param(params);
            const url = filterUrl + "?" + queryParams;
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

    validateEventCreateUpdateForm = () => {
        $("#eventForm").validate({
            rules: {
                hotel_id: {
                    required: true,
                },
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                status: {
                    required: true,
                },
            },
            messages: {
                hotel_id: {
                    required: this.languageMessage.hotel_id.required,
                },
                name: {
                    required: this.languageMessage.name.required,
                    minlength: this.languageMessage.name.min,
                    maxlength: this.languageMessage.name.max,
                },
                status: {
                    required: this.languageMessage.status.required,
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
    };
}

window.service = new Events(props);
