@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <style>
        .select2-dropdown {
            z-index: 10060 !important;
        }

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

        #tambahModal .select2-container {
            z-index: 10000 !important;
        }

        #pricing-features-container .feature-item.card {
            box-shadow: none !important;
            border: 1px solid #d9dee3 !important;
        }

        .swal2-container {
            z-index: 10010 !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('internal.partials.stat-cards', ['stats' => $stats])
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Pricing Plans</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TablePricing" class="datatables-pricing table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Plan Name</th>
                            <th>Harga</th>
                            <th>Periode</th>
                            <th>Popular</th>
                            <th>Status</th>
                            <th>Fitur</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="pricingModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-judul">Add Pricing</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formPricing" class="form-horizontal">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <ul id="save_errorList"></ul>

                                <div class="nav-align-top nav-tabs mb-4">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <button type="button" class="nav-link active" role="tab"
                                                data-bs-toggle="tab" data-bs-target="#pricing-plan-tab"
                                                aria-selected="true">
                                                Pricing Plan
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                                data-bs-target="#pricing-features-tab" aria-selected="false">
                                                Pricing Features
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="pricing-plan-tab" role="tabpanel">
                                            <div class="row">
                                                <div class="col-xl-6 mb-4">
                                                    <label class="form-label" for="name">Plan Name</label>
                                                    <input type="text" class="form-control" id="name"
                                                        name="name" placeholder="e.g. Basic Plan">
                                                    <div class="text-danger small" id="name-error"></div>
                                                </div>
                                                <div class="col-xl-6 mb-4">
                                                    <label class="form-label" for="price">Harga</label>
                                                    <input type="text" class="form-control" id="price" name="price"
                                                        placeholder="Enter harga">
                                                    <div class="text-danger small" id="price-error"></div>
                                                </div>
                                                <div class="col-xl-6 mb-4">
                                                    <label class="form-label" for="billing_period">Periode Tagihan</label>
                                                    <select id="billing_period" class="form-select" name="billing_period">
                                                        <option value="month">Bulan</option>
                                                        <option value="year">Year</option>
                                                    </select>
                                                    <div class="text-danger small" id="billing_period-error"></div>
                                                </div>
                                                <div class="col-xl-6 mb-4">
                                                    <label class="form-label" for="sort_order">Order</label>
                                                    <input type="number" class="form-control" id="sort_order"
                                                        name="sort_order" min="0" value="0">
                                                    <div class="text-danger small" id="sort_order-error"></div>
                                                </div>
                                                <div class="col-xl-12 mb-4">
                                                    <label class="form-label" for="description">Description</label>
                                                    <textarea class="form-control" id="description" name="description" rows="4"
                                                        placeholder="Description singkat pricing plan"></textarea>
                                                    <div class="text-danger small" id="description-error"></div>
                                                </div>
                                                <div class="col-xl-12 mb-4">
                                                    <label class="form-label" for="button_url">Button URL</label>
                                                    <input type="text" class="form-control" id="button_url"
                                                        name="button_url" placeholder="https://example.com/contact">
                                                    <div class="text-danger small" id="button_url-error"></div>
                                                </div>
                                                <div class="col-xl-6 mb-4">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="is_popular"
                                                            name="is_popular" value="1">
                                                        <label class="form-check-label" for="is_popular">Tandai sebagai
                                                            Popular</label>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 mb-4">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="is_active"
                                                            name="is_active" value="1" checked>
                                                        <label class="form-check-label" for="is_active">Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="pricing-features-tab" role="tabpanel">
                                            <div id="pricing-features-container"></div>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <button type="button" class="btn btn-primary" id="btn-add-feature">
                                                        <i class="ti ti-plus me-2"></i>Add Fitur
                                                    </button>
                                                </div>
                                            </div>
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
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/ajax/pricing.js') }}"></script>
@endsection
