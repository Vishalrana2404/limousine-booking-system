<form id="changePasswordForm" action="javascript:void(0);">
    <div class="modal-body p-1">
        <div class="card-body p-0">
            <h2 class="head-xs semibold mb-1 border-bottom px-4 py-3">Change Password</h2>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="head-xs semibold mb-1" for="current_password">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <input type="password" id="current_password"
                                    class="form-control  @error('current_password') is-invalid @enderror"
                                    name="current_password" placeholder="Current Password" autocomplete="off" autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fa fa-eye-slash passwordIcon"></span>
                                    </div>
                                </div>
                            </div>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                    </div>
                </div>
                <div class="row mt-3 mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="head-xs semibold mb-1" for="new_password">New Password <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <input type="password" id="new_password" placeholder="New Password"
                                    class="form-control @error('new_password') is-invalid @enderror" name="new_password"
                                    autocomplete="off" autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fa fa-eye-slash passwordIcon"></span>
                                    </div>
                                </div>
                            </div>
                            @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="head-xs semibold mb-1" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <input type="password" id="confirm_password" placeholder="Confirm Password"
                                    class="form-control  @error('confirm_password') is-invalid @enderror"
                                    name="confirm_password" autocomplete="off" autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fa fa-eye-slash passwordIcon"></span>
                                    </div>
                                </div>
                                @error('confirm_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-5">
                    <button type="submit" id="submitChangePasswordFormButton" class="inner-theme-btn" title="Save changes">Save
                        changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
