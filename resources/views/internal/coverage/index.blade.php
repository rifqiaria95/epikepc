@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
    <style>
        #coverageModal.modal {
            z-index: 9999 !important;
        }

        .modal-backdrop {
            z-index: 9998 !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Coverage Area</h5>
                <p class="mb-0 text-muted">Manage locations yang sudah tercover dan wilayah referensi autocomplete.</p>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableCoverage" class="datatables-coverage table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Regencies/Kota</th>
                            <th>Villages</th>
                            <th>Location Name</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal fade text-start" id="coverageModal" tabindex="-1" aria-labelledby="coverageModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="coverageModalLabel">Add Location Coverage</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formCoverage">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="type">Location Type</label>
                                        <select id="type" name="type" class="form-select">
                                            <option value="dukuh">Dukuh (Tercover)</option>
                                            <option value="perumahan">Perumahan (Tercover)</option>
                                            <option value="reference">Referensi Autocomplete</option>
                                        </select>
                                        <div class="text-danger small" id="type-error"></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="sort_order">Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                                            min="0" value="0">
                                        <div class="text-danger small" id="sort_order-error"></div>
                                    </div>
                                    <div class="col-md-6 mb-4 coverage-area-field">
                                        <label class="form-label" for="kabupaten">Regencies/Kota</label>
                                        <input type="text" class="form-control" id="kabupaten" name="kabupaten"
                                            placeholder="e.g. Boyolali">
                                        <div class="text-danger small" id="kabupaten-error"></div>
                                    </div>
                                    <div class="col-md-6 mb-4 coverage-area-field">
                                        <label class="form-label" for="kelurahan">Villages</label>
                                        <input type="text" class="form-control" id="kelurahan" name="kelurahan"
                                            placeholder="e.g. Sawahan">
                                        <div class="text-danger small" id="kelurahan-error"></div>
                                    </div>
                                    <div class="col-12 mb-4">
                                        <label class="form-label" for="name">Location Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="e.g. Meletan / Jakarta Selatan">
                                        <div class="text-danger small" id="name-error"></div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                                checked>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="btn-simpan">Save</button>
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
    </script>
    <script src="{{ asset('assets/ajax/coverage.js') }}"></script>
@endsection
