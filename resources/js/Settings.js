import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
/**
 * Represents the Settings class.
 * @extends BaseClass
 */
export default class Settings extends BaseClass {
    /**
     * Constructor for the Settings class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $(document).on("click", ".custom-nav .nav-link", this.handleTabShow);
        $(document).on(
            "change",
            "#profileImage",
            this.handleChangeProfileImage
        );
        $(document).on(
            "click",
            "#submitProfileFormButton",
            this.handleUpdateProfile
        );
        $(document).on(
            "click",
            "#submitChangePasswordFormButton",
            this.handleChangePassword
        );
        $(document).on("click", ".passwordIcon", this.handlePasswordInput);
        $(document).on(
            "click",
            "#removeProfileImageButton",
            this.removeProfileImage
        );
    }

    removeProfileImage = () => {
        $("#loader").show();
        const url = this.props.routes.removeProfileImage;
        const defaultProfileImage = this.props.images.defaultProfileImage;
        axios
            .post(url)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    $("#selectedAvatar").attr("src", defaultProfileImage);
                    $("#navAvatar").attr("src", defaultProfileImage);
                    $("#removeProfileImageButton").hide();
                    $("#loader").hide();
                }
                throw flash;
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
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

    handleChangePassword = () => {
        $("#changePasswordForm").validate({
            rules: {
                current_password: {
                    required: true,
                    remote: {
                        url: "checkCurrentPassword",
                        type: "post",
                        async: false,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        data: {
                            current_password: function () {
                                return $("#current_password").val();
                            },
                        },
                        dataFilter: function (data) {
                            const response = JSON.parse(data);
                            return response.data.isvalid ? true : false;
                        },
                    },
                },
                new_password: {
                    required: true,
                    minlength: 8,
                    notEqualTo: "#current_password",
                },
                confirm_password: {
                    required: true,
                    equalTo: "#new_password",
                },
            },
            messages: {
                current_password: {
                    required: this.languageMessage.required_current_password,
                    remote: this.languageMessage.incorrect_current_password,
                },
                new_password: {
                    required: this.languageMessage.required_new_password,
                    minlength: this.languageMessage.password_min_length,
                    notEqualTo:
                        this.languageMessage
                            .password_diffrent_from_current_password,
                },
                confirm_password: {
                    required: this.languageMessage.confirm_password,
                    equalTo: this.languageMessage.password_not_match,
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                element.closest(".input-group").append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            submitHandler: (form) => {
                $("#overlay").fadeIn(300);
                const formData = new FormData(form);
                let url = this.props.routes.changeCurrentPassword;
                this.sendPostRequest(url, formData, "changePasswordForm");
            },
        });
    };

    handleUpdateProfile = () => {
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
        $("#profileForm").validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                last_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                email: {
                    required: true,
                    email: true,
                    customEmailValidation: true,
                    remote: {
                        url: "checkUniqueEmail",
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
                            return response.isvalid ? "true" : "false";
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
                email: {
                    required: this.languageMessage.email.required,
                    email: this.languageMessage.email.email,
                    remote: this.languageMessage.email.already_exist,
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
            submitHandler: (form) => {
                $("#overlay").fadeIn(300);
                const formData = new FormData(form);
                let url = this.props.routes.updateProfile;
                this.sendPostRequest(url, formData);
            },
        });
    };
    handleChangeProfileImage = ({ target }) => {
        try {
            const profileImageErrorElement = $("#profileImageError");
            // Get the image element
            const selectedAvatar = $("#selectedAvatar");
            const navAvatar = $("#navAvatar");
            if (target.files && target.files[0]) {
                // Check if the selected file is an image
                const file = target.files[0];
                const validImageTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/gif",
                ];
                if (!validImageTypes.includes(file.type)) {
                    // Display an error message or handle the invalid file type
                    profileImageErrorElement.show();
                    profileImageErrorElement.text(
                        this.languageMessage.invalid_image
                    );
                    return;
                } else {
                    profileImageErrorElement.hide();
                }

                // Check if the file size is within the allowed limit (e.g., 5MB)
                const maxSizeInBytes = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSizeInBytes) {
                    // Display an error message or handle the oversized image
                    profileImageErrorElement.show();
                    profileImageErrorElement.text(
                        this.languageMessage.invalid_image_size
                    );
                    return;
                } else {
                    profileImageErrorElement.hide();
                }

                // Create a FileReader object
                const reader = new FileReader();
                // Set up the FileReader onload callback
                reader.onload = (e) => {
                    // Set the image source to the FileReader result
                    selectedAvatar.attr("src", e.target.result);
                    navAvatar.attr("src", e.target.result);
                    $('#buttonContainer').html('<button type="button" id="removeProfileImageButton" class="btn btn-danger ml-2" title="Remove Profile Image">Remove Profile Image</button>');
                };
                // Read the selected file as a Data URL
                reader.readAsDataURL(file);
                const formData = new FormData();
                formData.append("profile_image", file);
                const url = this.props.routes.changeProfileImage;
                this.sendPostRequest(url, formData);
            }
        } catch (error) {
            this.handleException(error);
        }
    };
    sendPostRequest = (url, formData, formId = null) => {
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    if (formId) {
                        $("#" + formId)[0].reset();
                        // Remove validation classes
                        $("#" + formId + " .is-invalid").removeClass(
                            "is-invalid"
                        );
                        $("#" + formId + " .is-valid").removeClass("is-valid");
                    }
                    $("#loader").hide();
                }
                throw flash;
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };

    handleTabShow = ({ target }) => {
        const tabId = $(target).data("tab");
        $(".nav-link").removeClass("active");
        $(target).addClass("active");
        $(".tab-pane").removeClass("show active");
        $("#" + tabId).addClass("show active");
    };
}
window.service = new Settings(props);
