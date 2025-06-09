@extends('components.layout')
@section('content')
<form id="createEmailTemplateForm" method="post" action="{{ route('save-email-template') }}" enctype="multipart/form-data">
    @csrf
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
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
                        <h6 class="head-sm medium">Add Email Template</h6>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="addEmailTemplateFormButton" class="float-right btn btn-outline-primary mx-2" title="Save">Save</button>
                        {{-- <button type="button" class="float-right btn btn-outline-danger mx-2"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button> --}}
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card no-box-shadow">
                            <div class="card-header px-0">
                                <div class="row">
                                    <h3 class="head-sm semibold">Email Template Information</h3>
                                </div>
                            </div>
                            <div class="card-body px-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder=" Name" autofocus>
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
                                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" placeholder="Subject">
                                            @error('subject')
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
                                                <option value="ACTIVE" {{ old('status') === "ACTIVE" ? 'selected' : '' }}>Active</option>
                                                <option value="INACTIVE" {{ old('status') === "INACTIVE" ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="qr_code">QR Code</label>
                                            <input type="file" id="qr_code" name="qr_code" value="{{ old('qr_code') }}" class="form-control @error('qr_code') is-invalid @enderror" placeholder="QR Code" accept="image/*">
                                            @error('qr_code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="qrCodePreview">QR Code Preview</label><br>
                                            <img id="qrCodePreview" src="{{ asset('images/default-preview.png') }}" alt="QR Code Preview" style="width:100px; height:100px; object-fit: contain; border-radius: 10px;" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="header">Message <span class="text-danger">*</span></label>
                                            <textarea id="header" name="header" class="form-control @error('header') is-invalid @enderror" placeholder="Message" style="min-height: 200px;">{{ old('header') }}</textarea>
                                            @error('header')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="footer">Footer <span class="text-danger">*</span></label>
                                            <textarea id="footer" name="footer" class="form-control @error('footer') is-invalid @enderror" placeholder="Footer" style="min-height: 200px;">{{ old('footer') }}</textarea>
                                            @error('footer')
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
    };
</script>

<script>
    function initializeEditor(selector) {
        ClassicEditor
            .create(document.querySelector(selector), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'blockQuote',
                    'bulletedList', 'numberedList', 'todoList',
                    '|',
                    'outdent', 'indent',
                    '|',
                    'undo', 'redo',
                    '|',
                    'insertTable',
                    'mediaEmbed',
                    'codeBlock',
                    'fontColor', 'fontBackgroundColor', 'fontFamily', 'fontSize',
                    'alignment',
                    'horizontalLine',
                    'pageBreak',
                ],
                removePlugins: [
                    'ImageUpload', 'EasyImage', 'ImageResize', 'ImageInsert', 'ImageStyle',
                    'CKFinder', 'CKFinderUploadAdapter', 'CKBox'
                ]
            })
            .then(editor => {
                // Apply height styling via editor editing view, not directly on DOM
                editor.editing.view.change(writer => {
                    writer.setStyle('min-height', '200px', editor.editing.view.document.getRoot());
                    writer.setStyle('resize', 'vertical', editor.editing.view.document.getRoot());
                    writer.setStyle('overflow', 'auto', editor.editing.view.document.getRoot());
                });
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });
    }

    // Initialize editors on multiple elements
    initializeEditor('#header');
    initializeEditor('#footer');
</script>

@endsection