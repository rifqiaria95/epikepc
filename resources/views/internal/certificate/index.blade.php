@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('internal.partials.stat-cards', ['stats' => $stats])

        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Filters</h5>
                <div class="row pt-4 g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="filter-status">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="filter-featured">Featured</label>
                        <select id="filter-featured" class="form-select">
                            <option value="">All</option>
                            <option value="1">Featured</option>
                            <option value="0">Not featured</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="filter-expiry">Expiry</label>
                        <select id="filter-expiry" class="form-select">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="filter-year">Issued year</label>
                        <input type="number" id="filter-year" class="form-control" min="1900" max="2100" placeholder="e.g. 2024">
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableCertificates" class="datatables-certificates table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Issuer</th>
                            <th>Order</th>
                            <th>Issued</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Homepage</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal fade text-start" id="certificateModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-title">Add Certificate</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formCertificate" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="certificate-id">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="title">Certificate title</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                        <div class="text-danger small" id="title-error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="issuer">Issuer</label>
                                        <input type="text" class="form-control" id="issuer" name="issuer" required>
                                        <div class="text-danger small" id="issuer-error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="certificate_number">Certificate number</label>
                                        <input type="text" class="form-control" id="certificate_number" name="certificate_number">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="issued_at">Issued date</label>
                                        <input type="date" class="form-control" id="issued_at" name="issued_at">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="expires_at">Expiry date</label>
                                        <input type="date" class="form-control" id="expires_at" name="expires_at">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="credential_url">Credential URL</label>
                                        <input type="url" class="form-control" id="credential_url" name="credential_url" placeholder="https://">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="display_order">Display order</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" min="1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="status">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="published_at">Published date</label>
                                        <input type="datetime-local" class="form-control" id="published_at" name="published_at">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                                            <label class="form-check-label" for="is_featured">Featured</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="image">Certificate image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                                        <div class="form-text">JPEG, PNG, or WebP. Max {{ config('certificates.max_file_size_kb') / 1024 }} MB.</div>
                                        <div class="text-danger small" id="image-error"></div>
                                        <img id="image-preview" src="" alt="" class="img-thumbnail mt-2 d-none" style="max-height:160px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="image_alt">Alternative text</label>
                                        <input type="text" class="form-control" id="image_alt" name="image_alt">
                                        <div class="text-danger small" id="image_alt-error"></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="btn-save">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
        window.certificateRoutes = {
            index: @json(route('certificates.index')),
            store: @json(route('certificates.store')),
            update: @json(url('/internal/certificates/update')),
            edit: @json(url('/internal/certificates/edit')),
            destroy: @json(url('/internal/certificates/delete')),
            publish: @json(url('/internal/certificates')),
            unpublish: @json(url('/internal/certificates')),
            archive: @json(url('/internal/certificates')),
        };
    </script>
    <script src="{{ asset('assets/ajax/certificates.js') }}"></script>
@endsection
