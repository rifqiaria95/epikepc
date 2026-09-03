@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
@endsection
@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
                @include('internal.partials.stat-cards', ['stats' => $stats])

        <!-- Testimoni List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Filters</h5>
                <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                    <div class="col-md-4 news_role"></div>
                    <div class="col-md-4 news_plan"></div>
                    <div class="col-md-4 news_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableTestimoni" class="datatables-testimoni table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Gambar</th>
                            <th>Name</th>
                            <th>Testimoni</th>
                            <th>Institution</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="myModalLabel18"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-judul">Add Testimoni</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formTestimoni" class="form-horizontal" enctype="multipart/form-data">
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
                                                        Detail Testimoni
                                                    </button>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="navs-top-home"
                                                    role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="nama">Name</label>
                                                            <input type="text" class="form-control" id="nama"
                                                                placeholder="Enter Subjek" name="nama"
                                                                aria-label="Name" />
                                                            <div class="text-danger small" id="nama-error"></div>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="instansi">Institution</label>
                                                            <input type="text" class="form-control" id="instansi"
                                                                placeholder="Enter institusi" aria-label="Institution"
                                                                name="instansi" />
                                                            <div class="text-danger small" id="instansi-error"></div>
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label" for="testimoni">Testimoni</label>
                                                            <textarea class="form-control" id="testimoni" placeholder="Enter testimoni" name="testimoni"
                                                                aria-label="Testimoni" rows="10"></textarea>
                                                            <div class="text-danger small" id="testimoni-error"></div>
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label" for="gambar">Gambar</label>
                                                            <input type="file" id="gambar" class="form-control"
                                                                aria-label="Gambar" name="gambar" accept="image/*" />
                                                            <div class="form-text">Upload image (JPG, PNG,
                                                                GIF)</div>
                                                            <div class="text-danger small" id="gambar-error"></div>
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
            selector: '#testimoni',
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
            const editor = tinymce.get('testimoni');
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
    <script src="{{ asset('assets/ajax/testimoni.js') }}"></script>
@endsection
