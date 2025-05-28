<form id="profileForm" action="javascript:void(0);">
    <div class="modal-body">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="head-xs semibold mb-1" for="firstName">First Name <span class="text-danger">*</span></label>
                        <input type="text" id="firstName" name="first_name" value="{{ $profileData->first_name }}" placeholder="First Name" class="form-control" autofocus>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="head-xs semibold mb-1" for="lastname">Last name <span class="text-danger">*</span></label>
                        <input type="text" id="lastname" name="last_name" value="{{ $profileData->last_name }}" placeholder="Last Name" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="head-xs semibold mb-1" for="email">Email</label>
                        <input type="text" id="email" name="email" value="{{ $profileData->email }}" placeholder="Email" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <p class="mb-3 head-sm semibold">Profile Picture</p>
                <label class="position-relative" for="profileImage" style="cursor: pointer;">
                    @if (Auth::user()->profile_image && Storage::disk('public')->exists(Auth::user()->profile_image))
                    <img id="selectedAvatar" src="{{ Storage::url(Auth::user()->profile_image) }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Image" />
                    @else
                    <img id="selectedAvatar" src="{{ asset('/images/profile.svg') }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" alt="Default Profile Image" />
                    @endif
                    <div class="upload-design">
                        <span class="icon upload-icon"></span>
                    </div>
                    {{-- <i class="fa fa-upload fa-2x" aria-hidden="true"></i> --}}
                    <input type="file" class="form-control d-none" id="profileImage" accept="image/*" />
                    <span id="profileImageError" class="error invalid-feedback"></span>
                </label>
            </div>
        </div>
        <div class="d-flex align-items-center mt-5">
            <button type="submit" id="submitProfileFormButton" class="inner-theme-btn" title="Save Changes">Save Changes</button>
            <div id="buttonContainer">
                @if (Auth::user()->profile_image && Storage::disk('public')->exists(Auth::user()->profile_image))
                <button type="button" id="removeProfileImageButton" class="btn btn-danger ml-2" title="Remove Profile Image">Remove Profile Image</button>
                @endif
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div>

    </div>
</div>