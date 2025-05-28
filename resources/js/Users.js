import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";

/**
 * Represents the Users class.
 * @extends BaseClass
 */
export default class Users extends BaseClass {
    /**
     * Constructor for the Users class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $("#userTable").checkboxTable();
        $(document).on("click", "#addUserFormButton", this.handleSaveUser);
        $(document).on("click", "#updateUserFormButton", this.handleEditUser);
        $(document).on("click", ".fa-trash", this.handleDeleteModal);
        $(document).on("click", "#deleteConfirmButton", this.handleDeleteUser);
        $(document).on("click", ".passwordIcon", this.handlePasswordInput);
        $(document).on(
            "click",
            "#closeDeleteIcon, #closeDeleteButton",
            this.handleCloseModal
        );
        $(document).on(
            "change",
            ".userStatusToggal",
            this.handleUserStatusToggal
        );
        $(document).on("change", "#bulkAction", this.handleBulkAction);
        $(document).on(
            "click",
            "#deleteBulkConfirmButton",
            this.handleBulkDeleteConfirmUser
        );
        $(document).on("keyup", "#search", this.handleFilter);
        $(document).on("change", "#filterByUserType", this.handleFilter);
        $(document).on(
            "click",
            "#sortName, #sortPhone, #sortEmail, #sortDepartment, #sortUserType, #sortStatus",
            this.handleSorting
        );
        $(document).on(
            "click",
            "#usersPagination .pagination a",
            this.handlePagnation
        );
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
                filterByUserType: $("#filterByUserType").val(),
                page: page,
            };
            $("#currentPage").val(page);
            const queryParams = $.param(params);
            const url = this.props.routes.filterUsers + "?" + queryParams;
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
                filterByUserType: $("#filterByUserType").val(),
                search: $("#search").val(),
                page: $("#currentPage").val(),
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterUsers + "?" + queryParams;
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
                filterByUserType: $("#filterByUserType").val(),
                search: $("#search").val(),
                sortDirection: sortOrder,
                sortField: sortColumn,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterUsers + "?" + queryParams;
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
            let userIds = [];
            $(".cellCheckbox").each(function () {
                if ($(this).is(":checked")) {
                    userIds.push($(this).closest("tr").data("user-id")); // Push the ID of the checked checkbox into the array
                }
            });
            if (userIds.length) {
                const formData = new FormData();
                $.each(userIds, (index, userId) => {
                    formData.append("user_ids[]", userId);
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
                            "Are you sure you want to delete these users?"
                        );
                        $("#deleteConfirmationTitle").data(
                            "user-id",
                            userIds.join(",")
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

    handleBulkDeleteConfirmUser = () => {
        try {
            let userIds = $("#deleteConfirmationTitle").data("user-id");
            userIds = userIds.split(",");
            const formData = new FormData();
            $.each(userIds, (index, userId) => {
                formData.append("user_ids[]", userId);
            });
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };
    handlePasswordInput = ({ target }) => {
        try {
            const input = $(target).closest(".input-group").find("input");
            if ($(target).hasClass("fa-eye-slash")) {
                $(target).removeClass("fa-eye-slash").addClass("fa-eye");
                input.attr("type", "text");
            } else {
                $(target).removeClass("fa-eye").addClass("fa-eye-slash");
                input.attr("type", "password");
            }
        } catch (error) {
            this.handleException(error);
        }
    };

    handleBulkStatusUpdate = (formData) => {
        let url = this.props.routes.updateBulkUserStatus;
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    const userIds = Array.from(formData.getAll("user_ids[]"));
                    const status = formData.get("status");
                    $(".userTableCheckbox").prop("checked", false);
                    const isChecked = status === "ACTIVE" ? true : false;
                    $.each(userIds, (index, userId) => {
                        $(`#userStatusToggal_${userId}`).prop(
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
    handleUserStatusToggal = ({ target }) => {
        try {
            const userId = $(target).closest("tr").attr("data-user-id");
            const isChecked = target.checked;
            const status = isChecked ? "ACTIVE" : "INACTIVE";
            const formData = new FormData();
            formData.append("user_ids[]", userId);
            formData.append("status", status);
            const url = this.props.routes.updateBulkUserStatus;
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

    handleDeleteUser = () => {
        try {
            const userId = $("#deleteConfirmationTitle").data("user-id");
            const formData = new FormData();
            formData.append("user_ids[]", userId);
            this.sendDeleteRequest(formData);
        } catch (error) {
            this.handleException(error);
        }
    };

    sendDeleteRequest = (formData) => {
        $("#loader").show();
        const url = this.props.routes.deleteUser;
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    this.closeModal("deleteConfirmModal");
                    const userIds = Array.from(formData.getAll("user_ids[]"));
                    $(".userTableCheckbox").prop("checked", false);
                    $.each(userIds, (index, userId) => {
                        $(`tr[data-user-id="${userId}"]`).remove();
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
            const userId = $(target).data("user-id");
            this.openModal("deleteConfirmModal");
            $("#deleteConfirmationTitle").text(
                "Are you sure you want to delete this user?"
            );
            $("#deleteConfirmationTitle").data("user-id", userId);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleSaveUser = () => {
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
        $("#createUserForm").validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                last_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                user_type: {
                    required: true,
                },
                department: {
                    required: true,
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
                status: {
                    required: true,
                    customStatus: true,
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
                password: {
                    required: true,
                    minlength: 8,
                    pattern:
                        /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+\\\|\[\]{};:\'",.<>/?]).{8,}$/,
                },
            },
            messages: {
                first_name: {
                    required: this.languageMessage.first_name.required,
                    minlength: this.languageMessage.first_name.min,
                    maxlength: this.languageMessage.first_name.max,
                },
                last_name: {
                    required: this.languageMessage.last_name.required,
                    minlength: this.languageMessage.last_name.min,
                    maxlength: this.languageMessage.last_name.max,
                },
                user_type: this.languageMessage.user_type,
                department: {
                    required: this.languageMessage.department.required,
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
                password: {
                    required: this.languageMessage.password_required,
                    minlength: this.languageMessage.password_min_length,
                    pattern: this.languageMessage.password_regex,
                },
                email: {
                    required: this.languageMessage.email.required,
                    email: this.languageMessage.email.email,
                    remote: this.languageMessage.email.already_exist,
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
    handleEditUser = () => {
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
        $("#updateUserForm").validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                last_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                    onlyString: true,
                },
                user_type: {
                    required: true,
                },
                department: {
                    required: true,
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
                status: {
                    required: true,
                    customStatus: true,
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
            },
            messages: {
                first_name: {
                    required: this.languageMessage.first_name.required,
                    minlength: this.languageMessage.first_name.min,
                    maxlength: this.languageMessage.first_name.max,
                },
                last_name: {
                    required: this.languageMessage.last_name.required,
                    minlength: this.languageMessage.last_name.min,
                    maxlength: this.languageMessage.last_name.max,
                },
                user_type: this.languageMessage.user_type,
                department: {
                    required: this.languageMessage.department.required,
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
                email: {
                    required: this.languageMessage.email.required,
                    email: this.languageMessage.email.email,
                    remote: this.languageMessage.email.already_exist,
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
window.service = new Users(props);
