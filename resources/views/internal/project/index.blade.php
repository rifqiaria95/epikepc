@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Summary Stats --}}
    <div class="row g-6 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">Total Project</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $totalProjects }}</h4>
                            </div>
                            <small class="mb-0">All projects</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-briefcase ti-26px"></i>
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
                            <span class="text-heading">Published</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $published }}</h4>
                            </div>
                            <small class="mb-0">Published projects</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-eye ti-26px"></i>
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
                            <span class="text-heading">Draft</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $unpublished }}</h4>
                            </div>
                            <small class="mb-0">Unpublished</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-secondary">
                                <i class="ti ti-eye-off ti-26px"></i>
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
                            <span class="text-heading">Recent</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $recentProjects }}</h4>
                            </div>
                            <small class="mb-0">Last 30 days</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ti ti-clock ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable Card --}}
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Project List</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table id="TableProject" class="datatables-project table">
                <thead class="border-top">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Client</th>
                        <th>Project Status</th>
                        <th>Location</th>
                        <th>Publish</th>
                        <th>Tanggal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>

        {{-- Modal Add/Edit --}}
        <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="modal-judul" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-judul">Add Project</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formProject" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id">
                            <ul id="save_errorList"></ul>

                            <div class="nav-align-top nav-tabs mb-4">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" role="tab"
                                            data-bs-toggle="tab" data-bs-target="#tab-info"
                                            aria-controls="tab-info" aria-selected="true">
                                            <i class="ti ti-info-circle me-1"></i>Informasi Utama
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab"
                                            data-bs-toggle="tab" data-bs-target="#tab-konten"
                                            aria-controls="tab-konten" aria-selected="false">
                                            <i class="ti ti-file-text me-1"></i>Content
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab"
                                            data-bs-toggle="tab" data-bs-target="#tab-media"
                                            aria-controls="tab-media" aria-selected="false">
                                            <i class="ti ti-photo me-1"></i>Media
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    {{-- Tab 1: Informasi Utama --}}
                                    <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-xl-8 mb-4">
                                                <label class="form-label" for="title">Project Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                    placeholder="Enter judul project" />
                                                <div class="text-danger small" id="title-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="sort_order">Sort Order</label>
                                                <input type="number" class="form-control" id="sort_order" name="sort_order"
                                                    placeholder="0" min="0" max="9999" value="0" />
                                                <div class="text-danger small" id="sort_order-error"></div>
                                            </div>
                                            <div class="col-xl-12 mb-4">
                                                <label class="form-label" for="excerpt">Excerpt</label>
                                                <textarea class="form-control" id="excerpt" name="excerpt"
                                                    placeholder="Brief project summary..." rows="3"></textarea>
                                                <div class="text-danger small" id="excerpt-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="category">Category</label>
                                                <input type="text" class="form-control" id="category" name="category"
                                                    placeholder="e.g. EPC, Pipeline" />
                                                <div class="text-danger small" id="category-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="client">Client</label>
                                                <input type="text" class="form-control" id="client" name="client"
                                                    placeholder="Client name" />
                                                <div class="text-danger small" id="client-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="project_date">Project Date</label>
                                                <input type="date" class="form-control" id="project_date" name="project_date" />
                                                <div class="text-danger small" id="project_date-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="project_value">Project Value (IDR)</label>
                                                <input type="number" class="form-control" id="project_value" name="project_value"
                                                    placeholder="e.g. 8500000000" min="0" step="1" />
                                                <div class="form-text">Used to rank portfolio (highest value first).</div>
                                                <div class="text-danger small" id="project_value-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="location">Location</label>
                                                <input type="text" class="form-control" id="location" name="location"
                                                    placeholder="e.g. Batam, Kendal" />
                                                <div class="text-danger small" id="location-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="latitude">Latitude</label>
                                                <input type="number" step="any" class="form-control" id="latitude" name="latitude"
                                                    placeholder="-6.2000000" />
                                                <div class="text-danger small" id="latitude-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="longitude">Longitude</label>
                                                <input type="number" step="any" class="form-control" id="longitude" name="longitude"
                                                    placeholder="106.8160000" />
                                                <div class="text-danger small" id="longitude-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="status">Project Status <span class="text-danger">*</span></label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="completed">Completed</option>
                                                    <option value="ongoing">On Going</option>
                                                </select>
                                                <div class="text-danger small" id="status-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="website_url">Website URL</label>
                                                <input type="url" class="form-control" id="website_url" name="website_url"
                                                    placeholder="https://contoh.com" />
                                                <div class="text-danger small" id="website_url-error"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label d-block" for="is_published">Status Publish</label>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" id="is_published"
                                                        name="is_published" value="1" />
                                                    <label class="form-check-label" for="is_published">Published</label>
                                                </div>
                                                <div class="text-danger small" id="is_published-error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tab 2: Content --}}
                                    <div class="tab-pane fade" id="tab-konten" role="tabpanel">
                                        <div class="row">
                                            <div class="col-xl-12 mb-4">
                                                <label class="form-label" for="content">Main Content</label>
                                                <textarea class="form-control" id="content" name="content"
                                                    placeholder="Full project description..." rows="8"></textarea>
                                                <div class="text-danger small" id="content-error"></div>
                                            </div>
                                            <div class="col-xl-12 mb-4">
                                                <label class="form-label" for="content_secondary">Secondary Content</label>
                                                <textarea class="form-control" id="content_secondary" name="content_secondary"
                                                    placeholder="Additional project content..." rows="8"></textarea>
                                                <div class="text-danger small" id="content_secondary-error"></div>
                                            </div>
                                            <div class="col-xl-6 mb-4">
                                                <label class="form-label" for="challenge_solution">Challenge & Solution</label>
                                                <textarea class="form-control" id="challenge_solution" name="challenge_solution"
                                                    placeholder="Describe project challenges and solutions..." rows="5"></textarea>
                                                <div class="text-danger small" id="challenge_solution-error"></div>
                                            </div>
                                            <div class="col-xl-6 mb-4">
                                                <label class="form-label" for="final_result">Final Result</label>
                                                <textarea class="form-control" id="final_result" name="final_result"
                                                    placeholder="Final project outcome..." rows="5"></textarea>
                                                <div class="text-danger small" id="final_result-error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tab 3: Media --}}
                                    <div class="tab-pane fade" id="tab-media" role="tabpanel">
                                        <div class="row">
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="image">Gambar Utama</label>
                                                <input type="file" class="form-control" id="image" name="image"
                                                    accept="image/jpg,image/jpeg,image/png,image/webp" />
                                                <div class="form-text">JPG, PNG, WEBP. Maks 5 MB.</div>
                                                <div class="text-danger small" id="image-error"></div>
                                                <div id="image-preview" class="mt-2"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="image_secondary">Gambar Sekunder</label>
                                                <input type="file" class="form-control" id="image_secondary" name="image_secondary"
                                                    accept="image/jpg,image/jpeg,image/png,image/webp" />
                                                <div class="form-text">JPG, PNG, WEBP. Maks 5 MB.</div>
                                                <div class="text-danger small" id="image_secondary-error"></div>
                                                <div id="image_secondary-preview" class="mt-2"></div>
                                            </div>
                                            <div class="col-xl-4 mb-4">
                                                <label class="form-label" for="image_tertiary">Gambar Tersier</label>
                                                <input type="file" class="form-control" id="image_tertiary" name="image_tertiary"
                                                    accept="image/jpg,image/jpeg,image/png,image/webp" />
                                                <div class="form-text">JPG, PNG, WEBP. Maks 5 MB.</div>
                                                <div class="text-danger small" id="image_tertiary-error"></div>
                                                <div id="image_tertiary-preview" class="mt-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="btn-simpan">
                                <i class="ti ti-device-floppy me-1"></i>Save
                            </button>
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

    {{-- TinyMCE --}}
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <script>
        const tinyMCEConfig = {
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code fullscreen preview',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | link image media table | align lineheight | numlist bullist indent outdent | code fullscreen preview | removeformat',
            menubar: false,
            height: 350,
            branding: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            toolbar_mode: 'sliding',
            setup: function (editor) {
                editor.on('change blur', function () { editor.save(); });
                editor.on('init', function () {
                    editor.getContainer().style.transition = 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out';
                });
            }
        };

        tinymce.init({ ...tinyMCEConfig, selector: '#content' });
        tinymce.init({ ...tinyMCEConfig, selector: '#content_secondary' });

        function saveTinyMCE() {
            ['content', 'content_secondary'].forEach(function (id) {
                if (tinymce.get(id)) tinymce.get(id).save();
            });
        }

        function clearTinyMCE() {
            ['content', 'content_secondary'].forEach(function (id) {
                if (tinymce.get(id)) tinymce.get(id).setContent('');
            });
        }

        function setTinyMCEContent(field, value) {
            if (tinymce.get(field)) {
                tinymce.get(field).setContent(value || '');
            } else {
                $('#' + field).val(value || '');
            }
        }

        function setTinyMCEError(field, hasError) {
            const editor = tinymce.get(field);
            if (editor) {
                const container = editor.getContainer();
                container.style.borderColor = hasError ? '#dc3545' : '';
                container.style.boxShadow   = hasError ? '0 0 0 0.2rem rgba(220,53,69,.25)' : '';
            }
        }
    </script>

    <script src="{{ asset('assets/ajax/project.js') }}"></script>
@endsection
