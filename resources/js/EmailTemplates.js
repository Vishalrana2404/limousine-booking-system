import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";

/**
 * Represents the EmailTemplates class.
 * @extends BaseClass
 */
export default class EmailTemplates extends BaseClass {
    /**
     * Constructor for the EmailTemplates class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $("#emailTemplatesTable").checkboxTable();
        $(document).on("click", "#addEmailTemplateFormButton", this.handleSaveEmailTemplate);
        $(document).on("click", "#updateEmailTemplateFormButton", this.handleEditEmailTemplate);
        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on("change", "#qr_code", this.handleQrCodeImagePreview);
        $(document).on("click", "#deleteConfirmButton", this.handleDeleteEmailTemplate);
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on(
            "change",
            ".emailTemplateStatusToggal",
            this.handleEmailTemplateStatusToggal
        );
        $(document).on("change", "#bulkAction", this.handleBulkAction);
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmEmailTemplate
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on(
            "click",
            "#sortName, #sortSubject, #sortStatus",
            this.handleSorting
        );
        $(document).on(
            "click",
            "#emailTemplatesPagination .pagination a",
            this.handlePagnation
        );
    }

    handleQrCodeImagePreview = (event) => {
        const preview = $('#qrCodePreview');
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.attr('src', e.target.result);
            };

            reader.readAsDataURL(file);
        } else {
            preview.attr('src', "{{ asset('images/default-preview.png') }}");
        }
    }

    handlePagnation = (event) => {
        event.preventDefault();
        try {
            const page = $(event.target).attr("href").split("page=")[1];
            const sortOrder = $("#sortOrder").val();
            const sortColumn = $("#sortColumn").val();
            const params = {
                sortField: sortColumn,
                sortDirection: sortOrder,
                search: $("#search").val(),
                page: page,
            };
            $("#currentPage").val(page);
            const queryParams = $.param(params);
            const url = this.props.routes.filterEmailTemplates + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder, sortColumn);
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
            const url = this.props.routes.filterEmailTemplates + "?" + queryParams;
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
                search: $("#search").val(),
                sortDirection: sortOrder,
                sortField: sortColumn,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterEmailTemplates + "?" + queryParams;
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

    handleBulkAction = ({ target }) => {
        try {
            let templateIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    templateIds.push($(this).closest("tr").data("email-template-id")); // Push the ID of the checked checkbox into the array
                }
            });
            if (templateIds.length) {
                const formData = new FormData();
                $.each(templateIds, (index, templateId) => {
                    formData.append("template_ids[]", templateId);
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
                            "Are you sure you want to delete these templates?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "email-template-id",
                            templateIds.join(",")
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

    handleBulkDeleteConfirmEmailTemplate = () => {
        try {
            let templateIds = $("#deleteConfirmationTitle").data("email-template-id");
            templateIds = templateIds.split(",");
            const formData = new FormData();
            $.each(templateIds, (index, templateId) => {
                formData.append("template_ids[]", templateId);
            });
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleBulkStatusUpdate = (formData) => {
        let url = this.props.routes.updateBulkEmailTemplateStatus;
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    const templateIds = Array.from(formData.getAll("template_ids[]"));
                    const status = formData.get("status");
                    $(".emailTemplatesTableCheckbox").prop("checked", false);
                    const isChecked = status === "ACTIVE" ? true : false;
                    $.each(templateIds, (index, templateId) => {
                        $(`#emailTemplateStatusToggal_${templateId}`).prop(
                            "checked",
                            isChecked
                        );
                    });
                    $("#loader").hide();
                }
                throw flash;
            })
            .catch((error) => {
                this.handleException(error);
            });
    };
    handleEmailTemplateStatusToggal = ({ target }) => {
        try {
            const templateId = $(target).closest("tr").attr("data-email-template-id");
            const isChecked = target.checked;
            const status = isChecked ? "ACTIVE" : "INACTIVE";
            const formData = new FormData();
            formData.append("template_ids[]", templateId);
            formData.append("status", status);
            const url = this.props.routes.updateBulkEmailTemplateStatus;
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

    handleDeleteEmailTemplate = () => {
        try {
            const templateId = $("#deleteConfirmationTitle").data("email-template-id");
            const formData = new FormData();
            formData.append("template_ids[]", templateId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    sendDeleteRequest = (formData) => {
        $("#loader").show();
        const url = this.props.routes.deleteEmailTemplates;
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    this.closeModal("deleteConfirmModal");
                    const templateIds = Array.from(formData.getAll("template_ids[]"));
                    $(".emailTemplatesTableCheckbox").prop("checked", false);
                    $.each(templateIds, (index, templateId) => {
                        $(`tr[data-email-template-id="${templateId}"]`).remove();
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
    handleDeleteModal = ({ target }) => {
        try {
            const templateId = $(target).data("email-template-id");
            this.openModal("deleteConfirmModal");
            $("#deleteConfirmationTitle").text(
                "Are you sure you want to delete this email template?"
            );
            $("#deleteConfirmationTitle").data("email-template-id", templateId);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleSaveEmailTemplate = () => {
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
        $("#createEmailTemplateForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                subject: {
                    required: true,
                },
                header: {
                    required: true,
                },
                footer: {
                    required: true,
                },
                message: {
                    required: true,
                },
                status: {
                    required: true,
                    customStatus: true,
                },
                qr_code: {
                    extension: "jpg|jpeg|png"
                },
            },
            messages: {
                name: {
                    required: this.languageMessage.email_template_name.required,
                    minlength: this.languageMessage.email_template_name.min,
                    maxlength: this.languageMessage.email_template_name.max,
                },
                header: {
                    required: this.languageMessage.email_template_header.required,
                },
                subject: {
                    required: this.languageMessage.email_template_subject.required,
                },
                footer: {
                    required: this.languageMessage.email_template_footer.required,
                },
                message: {
                    required: this.languageMessage.email_template_message.required,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
                qr_code: {
                    extension: this.languageMessage.qr_code.extension
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
    handleEditEmailTemplate = () => {
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
        $("#updateEmailTemplateForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                subject: {
                    required: true,
                },
                header: {
                    required: true,
                },
                footer: {
                    required: true,
                },
                message: {
                    required: true,
                },
                status: {
                    required: true,
                    customStatus: true,
                },
                qr_code: {
                    extension: "jpg|jpeg|png"
                },
            },
            messages: {
                name: {
                    required: this.languageMessage.email_template_name.required,
                    minlength: this.languageMessage.email_template_name.min,
                    maxlength: this.languageMessage.email_template_name.max,
                },
                header: {
                    required: this.languageMessage.email_template_header.required,
                },
                subject: {
                    required: this.languageMessage.email_template_subject.required,
                },
                footer: {
                    required: this.languageMessage.email_template_footer.required,
                },
                message: {
                    required: this.languageMessage.email_template_message.required,
                },
                status: {
                    required: this.languageMessage.status.required,
                },
                qr_code: {
                    extension: this.languageMessage.qr_code.extension
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
window.service = new EmailTemplates(props);
