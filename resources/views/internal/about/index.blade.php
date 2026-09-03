@extends('layouts.main')
@section('css')
<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @include('internal.partials.stat-cards', ['stats' => $stats])

    <div class="nav-align-top nav-tabs mb-6">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-journey" aria-selected="true">
                    Company Journey
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-milestones" aria-selected="false">
                    Project Timeline
                </button>
            </li>
        </ul>

        <div class="tab-content p-0">
            {{-- Tab: Company Journey Settings --}}
            <div class="tab-pane fade show active" id="tab-journey" role="tabpanel">
                <div class="card mb-0 border-0 shadow-none">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Company Journey Settings</h5>
                        <small class="text-muted">This content appears on the homepage — section header, profile video, and timeline title.</small>
                    </div>
                    <div class="card-body">
                        <form id="formJourney" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="section_subtitle">Section Subtitle</label>
                                    <input type="text" class="form-control" id="section_subtitle" name="section_subtitle" value="{{ old('section_subtitle', $journey->section_subtitle) }}" placeholder="Our Story">
                                    <div class="text-danger small" id="section_subtitle-error"></div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="section_title">Section Title</label>
                                    <input type="text" class="form-control" id="section_title" name="section_title" value="{{ old('section_title', $journey->section_title) }}" placeholder="Company">
                                    <div class="text-danger small" id="section_title-error"></div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="section_title_highlight">Highlight Word</label>
                                    <input type="text" class="form-control" id="section_title_highlight" name="section_title_highlight" value="{{ old('section_title_highlight', $journey->section_title_highlight) }}" placeholder="Journey">
                                    <div class="form-text">Displayed with highlight color, e.g. Company <strong>Journey</strong></div>
                                    <div class="text-danger small" id="section_title_highlight-error"></div>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="form-label" for="section_description">Section Description</label>
                                    <textarea class="form-control" id="section_description" name="section_description" rows="3">{{ old('section_description', $journey->section_description) }}</textarea>
                                    <div class="text-danger small" id="section_description-error"></div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3">Company Profile Video</h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="video_url">YouTube Video URL</label>
                                    <input type="text" class="form-control" id="video_url" name="video_url" value="{{ old('video_url', $journey->video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                                    <div class="text-danger small" id="video_url-error"></div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="video_poster">Video Poster</label>
                                    <input type="file" class="form-control" id="video_poster" name="video_poster" accept="image/*">
                                    @if(!empty($journey->poster_url))
                                        <div class="mt-2">
                                            <img src="{{ $journey->poster_url }}" alt="Poster" class="rounded" style="max-height: 80px;">
                                        </div>
                                    @endif
                                    <div class="text-danger small" id="video_poster-error"></div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="video_poster_tag">Poster Tag</label>
                                    <input type="text" class="form-control" id="video_poster_tag" name="video_poster_tag" value="{{ old('video_poster_tag', $journey->video_poster_tag) }}" placeholder="Company Profile">
                                    <div class="text-danger small" id="video_poster_tag-error"></div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="video_poster_title">Poster Title</label>
                                    <input type="text" class="form-control" id="video_poster_title" name="video_poster_title" value="{{ old('video_poster_title', $journey->video_poster_title) }}" placeholder="EPIKEPC Engineering">
                                    <div class="text-danger small" id="video_poster_title-error"></div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="video_duration">Video Duration</label>
                                    <input type="text" class="form-control" id="video_duration" name="video_duration" value="{{ old('video_duration', $journey->video_duration) }}" placeholder="2:48 MIN">
                                    <div class="text-danger small" id="video_duration-error"></div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="video_established">Established</label>
                                    <input type="text" class="form-control" id="video_established" name="video_established" value="{{ old('video_established', $journey->video_established) }}" placeholder="Est. 2005">
                                    <div class="text-danger small" id="video_established-error"></div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="video_location">Location</label>
                                    <input type="text" class="form-control" id="video_location" name="video_location" value="{{ old('video_location', $journey->video_location) }}" placeholder="Jakarta, Indonesia">
                                    <div class="text-danger small" id="video_location-error"></div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label" for="video_caption">Video Caption</label>
                                    <input type="text" class="form-control" id="video_caption" name="video_caption" value="{{ old('video_caption', $journey->video_caption) }}" placeholder="Building the future...">
                                    <div class="text-danger small" id="video_caption-error"></div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3">Teameline Title Milestones</h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="timeline_subtitle">Teameline Subtitle</label>
                                    <input type="text" class="form-control" id="timeline_subtitle" name="timeline_subtitle" value="{{ old('timeline_subtitle', $journey->timeline_subtitle) }}" placeholder="Company History">
                                    <div class="text-danger small" id="timeline_subtitle-error"></div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="timeline_title">Teameline Title</label>
                                    <input type="text" class="form-control" id="timeline_title" name="timeline_title" value="{{ old('timeline_title', $journey->timeline_title) }}" placeholder="Our Milestones">
                                    <div class="text-danger small" id="timeline_title-error"></div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $journey->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Show section on homepage</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="btn-save-journey">
                                    <i class="ti ti-device-floppy me-1"></i> Save Journey Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tab: Company Milestones --}}
            <div class="tab-pane fade" id="tab-milestones" role="tabpanel">
                <div class="card mb-0 border-0 shadow-none">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Company Milestones List</h5>
                        <small class="text-muted">Manage company timeline milestones displayed on the homepage.</small>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table id="TableMilestones" class="datatables-milestones table">
                            <thead class="border-top">
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    <th>Year</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Milestone --}}
<div class="modal fade text-start" id="milestoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="milestone-modal-title">Add Milestone</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formMilestone">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="milestone_id" id="milestone_id">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label" for="year">Year</label>
                            <input type="text" class="form-control" id="year" name="year" placeholder="2005" maxlength="10">
                            <div class="text-danger small" id="year-error"></div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label" for="sort_order">Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" min="0" placeholder="1">
                            <div class="text-danger small" id="sort_order-error"></div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="milestone_is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="milestone_is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label" for="milestone_title">Milestone Title</label>
                            <input type="text" class="form-control" id="milestone_title" name="title" placeholder="Company Founded">
                            <div class="text-danger small" id="title-error"></div>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label" for="milestone_description">Description</label>
                            <textarea class="form-control" id="milestone_description" name="description" rows="4" placeholder="Description milestone..."></textarea>
                            <div class="text-danger small" id="description-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-milestone">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
</script>
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
<script src="{{ asset('assets/ajax/about.js') }}"></script>
@endsection
