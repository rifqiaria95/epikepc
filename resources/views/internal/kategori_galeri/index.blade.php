@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
                @include('internal.partials.stat-cards', ['stats' => $stats])

        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Filters</h5>
                <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                    <div class="col-md-4 user_role"></div>
                    <div class="col-md-4 user_plan"></div>
                    <div class="col-md-4 user_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TablekategoriGallery" class="datatables-users table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- Offcanvas to add new user -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddkategoriGallery"
                aria-labelledby="offcanvasAddUserLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasAddkategoriGalleryLabel" class="offcanvas-title">Add Category Gallery</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                    <form id="formkategoriGallery" name="formkategoriGallery" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="mb-6">
                            <label class="form-label" for="name">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter nama kategori">
                            <div id="name-error" class="text-danger small"></div>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="slug">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                placeholder="Enter slug">
                            <div id="slug-error" class="text-danger small"></div>
                        </div>
                        <button type="submit" class="btn btn-primary me-3 data-submit">Submit</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="offcanvas">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection

@section('script')
    <script>
        window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
    </script>
    <script src="{{ asset('assets/ajax/kategori-galeri.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
@endsection
