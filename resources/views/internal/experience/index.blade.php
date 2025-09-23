@extends('layouts.main')
@section('css')
<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
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
                <table id="TableExperience" class="datatables-experience table">
                <thead class="border-top">
                    <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Company</th>
                    <th>Description</th>
                    <th>Year</th>
                    <th>Created By</th>
                    <th>Aksi</th>
                    </tr>
                </thead>
                </table>
            </div>
            <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="myModalLabel18" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-judul">Tambah Pengalaman</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formExperience" class="form-horizontal" enctype="multipart/form-data">
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
                                                    <button
                                                    type="button"
                                                    class="nav-link active"
                                                    role="tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#navs-top-home"
                                                    aria-controls="navs-top-home"
                                                    aria-selected="true">
                                                    Detail Pengalaman
                                                    </button>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="navs-top-home" role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="title">Title</label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="title"
                                                                placeholder="Masukkan judul pengalaman"
                                                                name="title"
                                                                aria-label="Title" />
                                                            <div class="text-danger small" id="title-error"></div>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="subtitle">Subtitle</label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="subtitle"
                                                                placeholder="Masukkan subtitle pengalaman"
                                                                name="subtitle"
                                                                aria-label="Subtitle" />
                                                            <div class="text-danger small" id="subtitle-error"></div>
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label" for="description">Description</label>
                                                            <textarea
                                                                class="form-control"
                                                                id="description"
                                                                placeholder="Masukkan deskripsi pengalaman..."
                                                                name="description"
                                                                aria-label="Description"
                                                                rows="10"
                                                            ></textarea>
                                                            <div class="text-danger small" id="description-error"></div>
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label" for="company">Company</label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="company"
                                                                placeholder="Masukkan perusahaan pengalaman"
                                                                aria-label="Company"
                                                                name="company"
                                                            />
                                                            <div class="text-danger small" id="company-error"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="year">Year</label>
                                                            <input
                                                                type="text"
                                                                id="year"
                                                                class="form-control"
                                                                aria-label="Year"
                                                                name="year"
                                                            />
                                                            <div class="text-danger small" id="year-error"></div>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="image">Image</label>
                                                            <input
                                                                type="file"
                                                                id="image"
                                                                class="form-control"
                                                                aria-label="Image"
                                                                name="image"
                                                                accept="image/*"
                                                            />
                                                            <div class="form-text">Upload gambar untuk image (JPG, PNG, GIF)</div>
                                                            <div class="text-danger small" id="image-error"></div>
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
                                <button type="submit" class="btn btn-primary btn-block" id="btn-simpan" value="create">Simpan
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
            editor.getContainer().style.transition = 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out';
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
<script src="{{ asset('assets/ajax/experience.js') }}"></script>
@endsection
