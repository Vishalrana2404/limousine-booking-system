import STATUS from "./Status.js";
export default class ErrorHandler extends Error {


    constructor(code, message = '') {
        super(message);
        this.number = code;
        this.name = STATUS[code];

    }

    flashMessage = () => {
        switch (this.name) {
            case 'OK':
                return this.success();
            case 'RESOURCE_CREATED':
                return this.success();
            case 'ACTION_ACCEPTED':
                return this.success();
            case 'NO_CONTENT':
                this.message = 'Something went wrong, please try again!'
                return this.error();
            case 'BAD_REQUEST':
                return this.error();
            case 'FAILED_AUTHENTICATION':
                this.message = 'Invalid email or password!';
                return this.error();

            case 'FAILED_VALIDATION':
                this.message = (this.message != '') ? this.message : "Server Validation Failed!";
                return this.error();
            case 'NOT_FOUND':
                this.message = 'Details not found!'
                return this.error();
            case 'DUPLICATE_RESOURCE':
                return this.error();
            case 'SERVER_ERROR':
                this.message = 'Server error';
                return this.error();
            case 'METHOD_NOT_ALLOW':
                this.message = 'Server error';
                return this.error();
            case 'PERMISSION_DENIED':
                this.message = (this.message != '') ? this.message : 'Permission denied!';
                return this.error();
            case 'WARNING':
                this.message = (this.message != '') ? this.message : 'Something went wrong';
                return this.warning();

        }
        return true;
    }
    success = () => {
        toastr.success(this.message, { timeOut: 4000 })
        return true;
    }

    error = () => {
        toastr.error(this.message, { timeOut: 4000 })
        return false;
    }

    warning = () => {
        toastr.warning(this.message, { timeOut: 4000 })
        return false;
    }


}
