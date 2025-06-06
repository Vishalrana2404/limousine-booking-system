@extends('components.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center g-3">
                    <div class="col-sm-3">
                        <h1 class="semibold head-sm">Email Templates</h1>
                    </div>
                    <div class="col-sm-9">
                        <div class="row justify-content-end g-3">
                            <div class="col-md-3">
                                <input type="text" id="search" name="search" class="form-control"
                                    placeholder="Search">
                            </div>
                            <div class="col-md-3">
                                <select id="bulkAction" class="form-control form-select custom-select">
                                    <option value="" selected="" disabled="">Bulk Action</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <a class="w-100 inner-theme-btn" href="{{ route('create-email-template') }}" title="Add Email Template">
                                        <i class="fa fa-solid fa-plus"></i> Add Email Template</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content bg-white">
            <!-- jquery validation -->
            <div class="card">
                <div class="card-header border-0">
                    <h1 class="card-title medium head-sm">Email Templates</h1>
                </div>
                <input type="hidden" id="sortOrder">
                <input type="hidden" id="sortColumn">
                <input type="hidden" id="currentPage">
                <!-- /.card-header -->
                <div id="dyanmicHtml">
                    @include('admin.email-templates.partials.email-templates-listing')
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    @vite(['resources/js/EmailTemplates.js'])
    <script>
        const props = {
            routes: {
                updateBulkEmailTemplateStatus: "{{ route('update-bulk-email-template-status') }}",
                deleteEmailTemplates: "{{ route('delete-email-template') }}",
                filterEmailTemplates: "{{ route('filter-email-templates') }}",
            },
        }
    </script>
@endsection
