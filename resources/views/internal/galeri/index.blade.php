@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('internal.partials.stat-cards', [
            'stats' => [
                ['label' => 'Total Gallery', 'value' => $totalGallery, 'hint' => 'All gallery items', 'icon' => 'ti-photo', 'color' => 'primary'],
                ['label' => 'With Category', 'value' => $withCategory, 'hint' => 'Has category assigned', 'icon' => 'ti-category', 'color' => 'success'],
                ['label' => 'With Image', 'value' => $withImage, 'hint' => 'Has photo', 'icon' => 'ti-camera', 'color' => 'info'],
                ['label' => 'Recent', 'value' => $recentGallery, 'hint' => 'Last 30 days', 'icon' => 'ti-clock', 'color' => 'warning'],
            ],
        ])
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Filters</h5>
                <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                    <div class="col-md-4 Gallery_role"></div>
                    <div class="col-md-4 Gallery_plan"></div>
                    <div class="col-md-4 Gallery_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableGallery" class="datatables-galeri table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Subtitle</th>
                            <th>Description</th>
                            <th>Created By</th>
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
                            <h4 class="modal-title" id="modal-judul">Add Gallery</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formGallery" class="form-horizontal" enctype="multipart/form-data">
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
                                                        Gallery Details
                                                    </button>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="navs-top-home"
                                                    role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-4 mb-6">
                                                            <label class="form-label" for="title">Title</label>
                                                            <input type="text" class="form-control" id="title"
                                                                placeholder="Enter judul galeri" name="title"
                                                                aria-label="Title" />
                                                            <div class="text-danger small" id="title-error"></div>
                                                        </div>
                                                        <div class="col-xl-4 mb-6">
                                                            <label class="form-label" for="subtitle">Subtitle</label>
                                                            <input type="text" class="form-control" id="subtitle"
                                                                placeholder="Enter subtitle galeri" name="subtitle"
                                                                aria-label="Subtitle" />
                                                            <div class="text-danger small" id="subtitle-error"></div>
                                                        </div>
                                                        <div class="col-xl-4 mb-6">
                                                            <label class="form-label"
                                                                for="kategori_galeri_id">Category</label>
                                                            <select id="kategori_galeri_id" class="form-select select2"
                                                                name="kategori_galeri_id">
                                                                <option value="">Select Category</option>
                                                                @foreach ($kategoriGaleri as $kategori)
                                                                    <option value="{{ $kategori->id }}">
                                                                        {{ $kategori->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label" for="image">Image</label>
                                                            <input type="file" id="image" class="form-control"
                                                                aria-label="Image" name="image" accept="image/*" />
                                                            <div class="form-text">Upload image (JPG, PNG,
                                                                GIF)</div>
                                                            <div class="text-danger small" id="image-error"></div>
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label"
                                                                for="description">Description</label>
                                                            <textarea class="form-control" id="description" placeholder="Enter deskripsi galeri..." name="description"
                                                                aria-label="Description" rows="10"></textarea>
                                                            <div class="text-danger small" id="description-error"></div>
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
        window.images_path = "{{ asset('images') }}";
    </script>
    <!-- TinyMCE Self-hosted -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#description',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code fullscreen preview',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | code fullscreen preview | removeformat',
            menubar: false,
            height: 400,
            branding: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            image_advtab: true,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
            noneditable_noneditable_class: 'mceNonEditable',
            toolbar_mode: 'sliding',
            contextmenu: 'link image table',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });

                // Auto-save content on blur
                editor.on('blur', function() {
                    editor.save();
                });
            },
            // Prevent form submission when pressing Enter in title fields
            init_instance_callback: function(editor) {
                editor.getContainer().style.transition =
                    'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out';
            }
        });

        // Function to handle TinyMCE error styling
        function setTinyMCEError(hasError) {
            const editor = tinymce.get('description');
            if (editor) {
                const container = editor.getContainer();
                if (hasError) {
                    container.style.borderColor = '#dc3545';
                    container.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                } else {
                    container.style.borderColor = '';
                    container.style.boxShadow = '';
                }
            }
        }
    </script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/ajax/galeri.js') }}"></script>
@endsection
