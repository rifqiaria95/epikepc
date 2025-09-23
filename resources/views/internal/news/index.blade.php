@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <style>
        /* Select2 fix for modal with body parent */
        .select2-dropdown {
            z-index: 9999 !important;
        }

        /* Ensure modal has lower z-index than dropdown */
        .modal {
            z-index: 1050;
        }

        .modal-backdrop {
            z-index: 1040;
        }

        /* Fix select2 container styling in modal */
        .modal .select2-container {
            z-index: 1051;
        }

        .select2-container--open {
            z-index: 1051;
        }
    </style>
@endsection
@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Session</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">21,459</h4>
                                    <p class="text-success mb-0">(+29%)</p>
                                </div>
                                <small class="mb-0">Total News</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-pegawais ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Paid News</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">4,567</h4>
                                    <p class="text-success mb-0">(+18%)</p>
                                </div>
                                <small class="mb-0">Last week analytics </small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti ti-pegawai-plus ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Active News</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalPegawai() }}</h4>
                                    <p class="text-danger mb-0">(-14%)</p>
                                </div>
                                <small class="mb-0">Last week analytics</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-pegawai-check ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Pending News</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">237</h4>
                                    <p class="text-success mb-0">(+42%)</p>
                                </div>
                                <small class="mb-0">Last week analytics</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti ti-pegawai-search ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- News List Table -->
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
                <table id="TableNews" class="datatables-news table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Thumbnail</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Content</th>
                            <th>Summary</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Tag</th>
                            <th>Status</th>
                            <th>Published At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="myModalLabel18"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-judul">Tambah Berita</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formNews" class="form-horizontal" enctype="multipart/form-data">
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
                                                        Detail Berita
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
                                                                placeholder="Masukkan judul berita" name="title"
                                                                aria-label="Title" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="slug">Slug</label>
                                                            <input type="text" class="form-control" id="slug"
                                                                placeholder="slug-berita-otomatis" name="slug"
                                                                aria-label="Slug" />
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label" for="content">Content</label>
                                                            <textarea class="form-control" id="content" placeholder="Masukkan konten berita..." name="content"
                                                                aria-label="Content" rows="10"></textarea>
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label" for="summary">Summary</label>
                                                            <textarea class="form-control" id="summary" placeholder="Masukkan ringkasan berita..." aria-label="Summary"
                                                                name="summary" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="thumbnail">Thumbnail</label>
                                                            <input type="file" id="thumbnail" class="form-control"
                                                                aria-label="Thumbnail" name="thumbnail"
                                                                accept="image/*" />
                                                            <div class="form-text">Upload gambar untuk thumbnail berita
                                                                (JPG, PNG, GIF)</div>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="status">Status</label>
                                                            <select class="form-select" id="status" name="status">
                                                                <option value="">Pilih Status</option>
                                                                <option value="draft">Draft</option>
                                                                <option value="published">Published</option>
                                                                <option value="archived">Archived</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="category_id">Kategori</label>
                                                            <select id="category_id" class="form-select"
                                                                name="category_id">
                                                                <option value="">Pilih Kategori</option>
                                                                @foreach ($kategori as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="tags_id">Tag</label>
                                                            <select class="form-select" id="tags_id" name="tags_id[]"
                                                                multiple>
                                                                @foreach ($tags as $tag)
                                                                    <option value="{{ $tag->id }}">{{ $tag->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="published_at">Published
                                                                At</label>
                                                            <input type="datetime-local" id="published_at"
                                                                class="form-control" aria-label="Published At"
                                                                name="published_at" />
                                                            <div class="form-text">Otomatis terisi saat status = Published
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="archived_at">Archived
                                                                At</label>
                                                            <input type="datetime-local" id="archived_at"
                                                                class="form-control" aria-label="Archived At"
                                                                name="archived_at" />
                                                            <div class="form-text">Otomatis terisi saat status = Archived
                                                            </div>
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
                                    value="create">Simpan
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
    <script>
        // Function to generate slug from title
        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                .replace(/^-+|-+$/g, ''); // Remove leading and trailing hyphens
        }

        // Auto-generate slug when title changes
        $(document).on('input', '#title', function() {
            let title = $(this).val();
            let slug = generateSlug(title);
            $('#slug').val(slug);
        });

        // Auto-fill published_at and archived_at based on status
        $(document).on('change', '#status', function() {
            let status = $(this).val();
            let now = new Date();

            // Format datetime untuk input datetime-local (YYYY-MM-DDTHH:MM)
            let formattedDateTime = now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0') + 'T' +
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0');

            if (status === 'published') {
                // Jika status published dan published_at masih kosong, isi dengan datetime sekarang
                if (!$('#published_at').val()) {
                    $('#published_at').val(formattedDateTime);
                }
                // Clear archived_at jika ada
                $('#archived_at').val('');
            } else if (status === 'archived') {
                // Jika status archived dan archived_at masih kosong, isi dengan datetime sekarang
                if (!$('#archived_at').val()) {
                    $('#archived_at').val(formattedDateTime);
                }
            } else if (status === 'draft') {
                // Jika status draft, clear both fields
                $('#published_at').val('');
                $('#archived_at').val('');
            }
        });

        tinymce.init({
            selector: '#content',
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
    </script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/ajax/news.js') }}"></script>
@endsection
