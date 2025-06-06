@extends('components.layout')
@section('content')
<form id="updateEmailTemplateForm" method="post" action="{{ route('update-email-template',$emailTemplate) }}">
    @csrf
    <div class="content-wrapper">
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <input type="hidden" id="templateId" value="{{$emailTemplate->id}}">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <a class="dark-color back-btn" href="{{ route('email-templates') }}" title="Email Template">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <rect x="25" y="25" width="25" height="25" transform="rotate(-180 25 25)" fill="url(#pattern0_31_250)" />
                                <defs>
                                    <pattern id="pattern0_31_250" patternContentUnits="objectBoundingBox" width="1" height="1">
                                        <use xlink:href="#image0_31_250" transform="scale(0.0078125)" />
                                    </pattern>
                                    <image id="image0_31_250" width="128" height="128" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJwSURBVHic7d09axRRGIbhe+aAla3B0r8hYmcjGFJbib1so+IXaBtN/GhUrPwW7ESsxcJC1F9gof4CESIKIo7FoASzS3Zmc+bN5twXnDLDA+/DJpmdOQckSZIkSZIkSZIkSZIkSZIkSZIkSZIkSZIkbVABx4G3wA/gK/ACOBAZSsOogUdAM2b9AkZx0TSEEeOHv36dDEun7D6xeQEa4FRUQOWzh+mG/3edjompXLoWoAHOhCRVNtP+Cli/zuYKk3JdWBP9Bg53/JlDwE/g9dbH0dBq4CndPwUa4HxAXmWQgMf0K8HFgLzKYJYSXArIqwwSk+8KWoJCJOAh/UqwHJBXGVgCWQK1JXhAvxJcDsirDBJwn34luBKQVxlYApGAe/QrwcrwcZVDTf8SrA4fVznUwF0sQdEq4Db9SnA1IK8ysASiAm7RrwTXAvIqgwq4Sb8SXA/IqwxmLUE17qJLwCvgW88Lu+Zn3eC/Eixvg1CuYdcKtC1YAp6hEi0m4A6wLziIYuytgDVgd3QShViroxMoVFMD76NTKMy7BHwBjkYnUYhRAj4Au4CDwWE0rFXam0n/HAFe0v5RGP0/qivv2nAjSPPF7wMK5jeCBZvlmQCHP+d8IKRgPhdYMJ8MLpjvBhTMt4MK5vAL5hvCBZtljwCHP+fcIKJgDr9g7hRWMPcKLJi7hRYsAU9w+EVys+jCTXNm0Lh1LiKstt5nug8/24ERGtYC3Yd/ISSpsvDQKPGR6YbvsXE71Ak2H74HR+5gFZPv+3t0bCEq4BjwBvhO+37mc2B/ZChJkiRJkiRJkiRJkiRJkiRJkiRJkiRJkiRtQ38A9GwPyk+0wKwAAAAASUVORK5CYII=" />
                                </defs>
                            </svg>
                            Email Templates
                        </a>
                        <h6 class="head-sm medium">Edit Email Template</h6>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="updateEmailTemplateFormButton" class="btn btn-outline-primary float-right mx-2" title="Save">Save</button>
                        {{-- <button type="button" class="btn btn-outline-danger float-right mx-2"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button> --}}
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card no-box-shadow">
                            <div class="card-header px-0">
                                <div class="row">
                                    <h3>Email Template Information</h3>
                                </div>
                            </div>
                            <div class="card-body px-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="template_name">Name <span class="text-danger">*</span></label>
                                            <input type="text" id="template_name" name="name" value="{{ $emailTemplate->name}}" class="form-control @error('name') is-invalid @enderror" placeholder="Name" autofocus>
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="subject">Subject <span class="text-danger">*</span></label>
                                            <input type="text" id="subject" name="subject" value="{{ $emailTemplate->subject}}" class="form-control @error('subject') is-invalid @enderror" placeholder="Subject" autofocus>
                                            @error('subject')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="header">Header <span class="text-danger">*</span></label>
                                            <input type="text" id="header" name="header" value="{{ $emailTemplate->header}}" class="form-control @error('header') is-invalid @enderror" placeholder="Header" autofocus>
                                            @error('header')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="footer">Footer <span class="text-danger">*</span></label>
                                            <input type="text" id="footer" name="footer" value="{{ $emailTemplate->footer}}" class="form-control @error('footer') is-invalid @enderror" placeholder="Footer" autofocus>
                                            @error('footer')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="message">Message <span class="text-danger">*</span></label>
                                            <input type="text" id="message" name="message" value="{{ $emailTemplate->message}}" class="form-control @error('message') is-invalid @enderror" placeholder="Message" autofocus>
                                            @error('message')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control form-select custom-select @error('status') is-invalid @enderror">
                                                <option value="ACTIVE" {{ $emailTemplate->status === "ACTIVE" ? 'selected' : '' }}>Active</option>
                                                <option value="INACTIVE" {{ $emailTemplate->status === "INACTIVE" ? 'selected' : '' }}>Inactive</option>

                                            </select>
                                            @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</form>
@vite(['resources/js/EmailTemplates.js'])
<script>
    const props = {
        routes: {
            checkUniqueEmailTemplateName: "{{route('check-unique-template-name')}}",
        },
    }
</script>
@endsection