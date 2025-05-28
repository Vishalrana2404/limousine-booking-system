import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
/**
 * Represents the Login class.
 * @extends BaseClass
 */
export default class Login extends BaseClass {
    /**
     * Constructor for the Login class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        $(document).on("click", "#loginButton", this.validateLoginForm);
        $(document).on(
            "click",
            "#resetPasswordFormButton",
            this.validateResetPasswordForm
        );
        $(document).on(
            "click",
            "#forgotPasswordFormButton",
            this.validateforgotPasswordForm
        );
        $(document).on(
            "click",
            "#registerButton",
            this.validateRegisterForm
        );
        $(document).on("click", ".passwordIcon", this.handlePasswordInput);
        $(document).on("keyup", "input[name='email']", this.trimEmailField);
    }

    trimEmailField() {
        let trimmedValue = $.trim($(this).val());
        $(this).val(trimmedValue);
    }

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

    validateforgotPasswordForm = () => {
        $("#forgotPasswordForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
            },
            messages: {
                email: {
                    required: this.languageMessage.email.required,
                    email: this.languageMessage.email.email,
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
        });
    };

    validateRegisterForm = () => {
        $("#registerForm").validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50,
                },
                country_code: {
                    minlength: 1,
                    maxlength: 3,
                    integer: true,
                    required: true,
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 10,
                },
                email: {
                    required: true,
                    email: true,
                },
            },
            messages: {
                first_name: {
                    required: this.languageMessage.first_name.required,
                    minlength: this.languageMessage.first_name.min,
                    maxlength: this.languageMessage.first_name.max,
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
        });
    };

    validateResetPasswordForm = () => {
        $("#resetPasswordForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true,
                    minlength: 8,
                    pattern:
                        /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+\\\|\[\]{};:\'",.<>/?]).{8,}$/,
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password",
                },
            },
            messages: {
                email: {
                    required: this.languageMessage.email.required,
                    email: this.languageMessage.email.email,
                },
                password: {
                    required: this.languageMessage.password_required,
                    minlength: this.languageMessage.password_min_length,
                    pattern: this.languageMessage.password_regex,
                },
                password_confirmation: {
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
        });
    };
    validateLoginForm = () => {
        $("#loginForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true,
                },
            },
            messages: {
                email: {
                    required: this.languageMessage.email.required,
                    email: this.languageMessage.email.email,
                },
                password: {
                    required: this.languageMessage.password_required,
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                element.closest(".input-group").append(error);
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
window.service = new Login(props);
