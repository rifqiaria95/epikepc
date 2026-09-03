@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <style>
        /* Select2 fix for modal with body parent */
        .select2-dropdown {
            z-index: 10060 !important;
        }

        /* Ensure modal appears above header - navbar fixed usually has z-index 1030-1050 */
        /* Using very high z-index to ensure it's above everything */
        #tambahModal.modal {
            z-index: 9999 !important;
            position: fixed !important;
        }

        #tambahModal.modal.show {
            z-index: 9999 !important;
        }

        .modal-backdrop {
            z-index: 9998 !important;
        }

        .modal-backdrop.show {
            z-index: 9998 !important;
        }

        /* Fix select2 container styling in modal */
        #tambahModal .select2-container {
            z-index: 10000 !important;
        }

        #tambahModal .select2-container--open {
            z-index: 10000 !important;
        }

        /* Ensure modal dialog appears above everything */
        #tambahModal .modal-dialog {
            z-index: 9999 !important;
            position: relative !important;
        }

        /* Ensure modal content appears above dialog */
        #tambahModal .modal-content {
            z-index: 9999 !important;
            position: relative !important;
        }

        /* Override any navbar z-index if needed - ensure navbar stays below modal */
        #layout-navbar {
            z-index: 1030 !important;
        }

        /* Ensure body doesn't create stacking context issues */
        body.modal-open {
            overflow: hidden;
        }

        /* TinyMCE z-index fix for modal */
        .tox-tinymce,
        .tox-tinymce-aux,
        .tox-menu,
        .tox-pop,
        .tox-toolbar {
            z-index: 10001 !important;
        }

        .tox .tox-menu {
            z-index: 10002 !important;
        }

        .tox .tox-pop {
            z-index: 10002 !important;
        }

        /* Service Detail Card - Remove box-shadow, use outline */
        #service-details-container .detail-item.card {
            box-shadow: none !important;
            border: 1px solid #d9dee3 !important;
            outline: none !important;
        }

        #service-details-container .detail-item.card:hover {
            border-color: #b4bdc6 !important;
        }

        /* Swal2 z-index fix to appear above modal */
        .swal2-container {
            z-index: 10010 !important;
        }

        .swal2-container.swal2-backdrop-show {
            z-index: 10010 !important;
        }

        .swal2-popup {
            z-index: 10011 !important;
            position: relative !important;
        }

        .swal2-backdrop {
            z-index: 10009 !important;
        }

        .swal2-backdrop-show {
            z-index: 10009 !important;
        }

        /* Ensure Swal appears above modal */
        body.modal-open .swal2-container {
            z-index: 10010 !important;
        }

        body.modal-open .swal2-popup {
            z-index: 10011 !important;
        }

        body.modal-open .swal2-backdrop {
            z-index: 10009 !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('internal.partials.stat-cards', ['stats' => $stats])
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Filters</h5>
                <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                    <div class="col-md-4 services_role"></div>
                    <div class="col-md-4 services_plan"></div>
                    <div class="col-md-4 services_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableServices" class="datatables-services table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Description</th>
                            <th>Service Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="myModalLabel18"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-judul">Add Service</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formServices" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <input type="hidden" name="user_id" id="user_id">
                                <ul id="save_errorList"></ul>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="nav-align-top nav-tabs mb-6">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <button type="button" class="nav-link active" role="tab"
                                                        data-bs-toggle="tab" data-bs-target="#navs-top-home"
                                                        aria-controls="navs-top-home" aria-selected="true">
                                                        Service
                                                    </button>
                                                </li>
                                                <li class="nav-item">
                                                    <button type="button" class="nav-link" role="tab"
                                                        data-bs-toggle="tab" data-bs-target="#navs-top-profile"
                                                        aria-controls="navs-top-profile" aria-selected="false">
                                                        Service Details
                                                    </button>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="navs-top-home"
                                                    role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="title">Title</label>
                                                            <input type="text" class="form-control" id="title"
                                                                placeholder="Enter judul service" name="title"
                                                                aria-label="Title" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="subtitle">Subtitle</label>
                                                            <input type="text" class="form-control" id="subtitle"
                                                                placeholder="Enter subtitle service" name="subtitle"
                                                                aria-label="Subtitle" />
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label" for="description">Description</label>
                                                            <textarea class="form-control" id="description" placeholder="Enter deskripsi service..." name="description"
                                                                aria-label="Content" rows="10"></textarea>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="image">Image</label>
                                                            <input type="file" id="image" class="form-control"
                                                                aria-label="Image" name="image"
                                                                accept="image/*" />
                                                            <div class="form-text">Upload image for service
                                                                (JPG, PNG, GIF)
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="service_type_id">Service Type</label>
                                                            <select id="service_type_id" class="form-select"
                                                                name="service_type_id">
                                                                <option value="">Select Service Type</option>
                                                                @foreach ($service_type as $st)
                                                                    <option value="{{ $st->id }}">
                                                                        {{ $st->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                                                    <div id="service-details-container">
                                                        <!-- Service detail items will be added here dynamically -->
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <button type="button" class="btn btn-primary" id="btn-add-detail">
                                                                <i class="ti ti-plus me-2"></i>Add Detail
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-block" id="btn-simpan"
                                    value="create">Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection
@section('script')
    <script>
        window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
        window.storage_url = "{{ config('app.url') }}/storage";
    </script>
    <!-- TinyMCE Self-hosted -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/ajax/services.js') }}"></script>
@endsection
