@extends('components.layout')
@section('content')
<div class="content-wrapper">
    <section class="content-header border-bottom">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <a class="dark-color back-btn" href="{{ route('email-templates') }}" title="Email Templates">
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
                    <h6 class="head-sm medium">View Email Template</h6>
                </div>
            </div>
        </div>
    </section>

    <section class="content mt-3">
        <div class="container-fluid">
            <div class="card p-4 position-relative">
                <!-- Test Email Button top-right -->
                <button type="button" class="btn btn-primary" style="position: absolute; top: 20px; right: 20px;" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                    Test Email
                </button>

                <div class="mb-3">
                    <label><strong>Template Name:</strong></label>
                    <div>{{ $emailTemplate->name }}</div>
                </div>

                <div class="mb-3">
                    <label><strong>Subject:</strong></label>
                    <div>{{ $emailTemplate->subject }}</div>
                </div>

                <div class="mb-3">
                    <label><strong>Header:</strong></label>
                    <div>{!! $emailTemplate->header !!}</div>
                </div>

                <div class="mb-3">
                    <label><strong>Footer:</strong></label>
                    <div>{!! $emailTemplate->footer !!}</div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="testEmailModalLabel">Send Test Email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="testEmailInput" class="form-label">Email</label>
        <input type="hidden" class="form-control" id="testEmailTemplateId" name="testEmailTemplateId" value="{{ $emailTemplate->id }}">
        <input type="email" class="form-control" id="testEmailInput" name="email" placeholder="Enter email">
        <span id="testEmailInput-error" class="error invalid-feedback" style="display:none;"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="test-email-button">Send Email</button>
      </div>
    </div>
  </div>
</div>

<!-- content -->
@vite(['resources/js/EmailTemplates.js'])

<script>
    const props = {
        routes: {
            checkUniqueEmailTemplateName: "{{route('check-unique-template-name')}}",
            sendTestEmail: "{{'send-test-email'}}"
        },
    };
</script>

@endsection
