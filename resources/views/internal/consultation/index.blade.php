@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Consultation Requests</h5>
                <p class="mb-0 text-muted">This data comes from the homepage consultation form and contact page.</p>
            </div>

            <div class="card-datatable table-responsive">
                <table id="TableConsultation" class="datatables-consultation table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Services</th>
                            <th>Sumber</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal fade text-start" id="consultationModal" tabindex="-1" aria-labelledby="consultationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="consultationModalLabel">Add Consultation Request</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formConsultation">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="name">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name">
                                        <div class="text-danger small" id="name-error"></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                        <div class="text-danger small" id="email-error"></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="phone">Nomor Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                        <div class="text-danger small" id="phone-error"></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="service_name">Services</label>
                                        <input type="text" class="form-control" id="service_name" name="service_name">
                                        <div class="text-danger small" id="service_name-error"></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="source">Sumber</label>
                                        <select id="source" name="source" class="form-select">
                                            <option value="homepage">Homepage</option>
                                            <option value="contact">Contact Page</option>
                                        </select>
                                        <div class="text-danger small" id="source-error"></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label" for="status">Status</label>
                                        <select id="status" name="status" class="form-select">
                                            <option value="new">Baru</option>
                                            <option value="contacted">Sudah Dihubungi</option>
                                            <option value="closed">Selesai</option>
                                        </select>
                                        <div class="text-danger small" id="status-error"></div>
                                    </div>
                                    <div class="col-12 mb-4">
                                        <label class="form-label" for="message">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="5"></textarea>
                                        <div class="text-danger small" id="message-error"></div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label class="form-label" for="internal_notes">Catatan Internal</label>
                                        <textarea class="form-control" id="internal_notes" name="internal_notes" rows="4"></textarea>
                                        <div class="text-danger small" id="internal_notes-error"></div>
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
    <script src="{{ asset('assets/ajax/consultation.js') }}"></script>
@endsection
