import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox.js";

/**
 * Represents the PeakPeriod class.
 * @extends BaseClass
 */
export default class PeakPeriod extends BaseClass {
    /**
     * Constructor for the PeakPeriod class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        this.handleOnLoad();

        $("#peakPeriodTable").checkboxTable();

        $(document).on(
            "click",
            "#submitPeakPeriodFormButton, #updatePeakPeriodFormButton",
            this.handleSavePeakPeriod
        );

        $(document).on("keyup", "#search", this.handleFilter);

        $(document).on(
            "click",
            "#sortEvent, #sortStartDate, #sortEndDate, #sortStatus",
            this.handleSorting
        );

        $(document).on("click", ".fa-trash", this.handleDeleteModal);

        $(document).on(
            "click",
            "#deleteConfirmButton",
            this.handleDeletePeakPeriod
        );

        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );

        $(document).on(
            "change",
            ".peakPeriodStatusToggal",
            this.handlePeakPeriodStatusToggal
        );

        $(document).on("change", "#bulkAction", this.handleBulkAction);

        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmPeakPeriod
        );

        $.validator.addMethod(
            "dateFormat",
            function (value, element) {
                // Check if the value matches the dd/MM/yyyy format
                return value.match(/^\d{2}\/\d{2}\/\d{4}$/);
            },
            this.languageMessage.event_start_date.regex,
            this.languageMessage.event_end_date.regex
        );
    }

    handleOnLoad = () => {
        if (this.props.isCreatePage) {
            this.initializeDatePicker("start_date");
            this.initializeDatePicker("end_date");
        }        
    }

    handleSavePeakPeriod = () => {
        $.validator.addMethod(
            "greaterThan",
            function(value, element, params) {
                const startDateStr = $(params).val();
                const endDateStr = value;
                
                const startDate = new Date(startDateStr.split('/').reverse().join('-'));
                const endDate = new Date(endDateStr.split('/').reverse().join('-'));

                // Check if both dates are valid
                if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                    return false;
                }
    
                return endDate > startDate;
            },
            "End date must be greater than or equal to the start date"
        );
        $("#createPeakPeriodForm").validate({
            rules: {
                event: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                },
                start_date: {
                    required: true,
                    dateFormat: true,
                },
                end_date: {
                    required: true,
                    dateFormat: true,
                    greaterThan: "#start_date",
                },
            },
            messages: {
                event: {
                    required: this.languageMessage.event_name.required,
                    minlength: this.languageMessage.event_name.min,
                    maxlength: this.languageMessage.event_name.max,
                },
                start_date: {
                    required: this.languageMessage.event_start_date.required
                },
                end_date: {
                    required: this.languageMessage.event_end_date.required
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

    handleSorting = ({ target }) => {
        try {
            const sortOrder = $("#sortOrder").val() === "asc" ? "desc" : "asc";
            const params = {
                sortField: $(target).attr("id"),
                sortDirection: sortOrder,
                search: $("#search").val(),
            };
            const queryParams = $.param(params);
            const url =
                this.props.routes.filterPeakPeriod + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleFilter = () => {
        try {
            const params = {
                search: $("#search").val(),
            };
            const queryParams = $.param(params);
            const url =
                this.props.routes.filterPeakPeriod + "?" + queryParams;
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
                    const tbody = $("#peakPeriodTable tbody");
                    tbody.html(response.data.data.html);
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

    handleBulkDeleteConfirmPeakPeriod = () => {
        let peakPeriodIds = $("#deleteConfirmationTitle").data(
            "peak-period-id"
        );
        peakPeriodIds = peakPeriodIds.split(",");
        const formData = new FormData();
        $.each(peakPeriodIds, (index, peakPeriodId) => {
            formData.append("peak_period_ids[]", peakPeriodId);
        });
        this.sendDeleteRequest(formData);
    };
    handleBulkAction = ({ target }) => {
        try {
            let peakPeriodIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    peakPeriodIds.push(
                        $(this).closest("tr").data("peak-period-id")
                    ); // Push the ID of the checked checkbox into the array
                }
            });
            if (peakPeriodIds.length) {
                const formData = new FormData();
                $.each(peakPeriodIds, (index, peakPeriodId) => {
                    formData.append("peak_period_ids[]", peakPeriodId);
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
                            "Are you sure you want to delete these peak periods?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "peak-period-id",
                            peakPeriodIds.join(",")
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
                    const peakPeriodIds = Array.from(
                        formData.getAll("peak_period_ids[]")
                    );
                    $(".peakPeriodTableCheckbox").prop("checked", false);
                    const status = formData.get("status");
                    const isChecked = status === "ACTIVE" ? true : false;
                    $.each(peakPeriodIds, (index, peakPeriodId) => {
                        $(`#peakPeriodStatusToggal_${peakPeriodId}`).prop(
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
    handleDeletePeakPeriod = () => {
        try {
            const peakPeriodId = $("#deleteConfirmationTitle").data(
                "peak-period-id"
            );
            const formData = new FormData();
            formData.append("peak_period_ids[]", peakPeriodId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };
    sendDeleteRequest = (formData) => {
        try {
            $("#loader").show();
            const url = this.props.routes.deletePeakPeriod;
            axios
                .post(url, formData)
                .then((response) => {
                    const statusCode = response.data.status.code;
                    const message = response.data.status.message;
                    const flash = new ErrorHandler(statusCode, message);
                    if (statusCode === 200) {
                        this.closeModal("deleteConfirmModal");
                        $(".peakPeriodTableCheckbox").prop("checked", false);
                        const peakPeriodIds = Array.from(
                            formData.getAll("peak_period_ids[]")
                        );
                        $.each(peakPeriodIds, (index, peakPeriodId) => {
                            $(
                                `tr[data-peak-period-id="${peakPeriodId}"]`
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
        const peakPeriodId = $(target).data("peak-period-id");
        this.openModal("deleteConfirmModal");
        $("#deleteConfirmationTitle").text(
            "Are you sure you want to delete this peak period?"
        );
        $("#deleteConfirmationTitle").data("peak-period-id", peakPeriodId);
    };
    handlePeakPeriodStatusToggal = ({ target }) => {
        try {
            const peakPeriodId = $(target)
                .closest("tr")
                .attr("data-peak-period-id");
            const isChecked = target.checked;
            const status = isChecked ? "ACTIVE" : "INACTIVE";
            const formData = new FormData();
            formData.append("peak_period_ids[]", peakPeriodId);
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

    handleCloseModal = () => {
        this.closeModal("deleteConfirmModal");
        $("#bulkAction").val("");
    };
}
window.service = new PeakPeriod(props);