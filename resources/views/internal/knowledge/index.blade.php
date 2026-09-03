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
        <table id="Tableknowledge" class="datatables-users table">
            <thead class="border-top">
            <tr>
                <th>#</th>
                <th>Question</th>
                <th>Jawaban</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
        </div>
        <!-- Offcanvas to add new user -->
        <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="offcanvasAddknowledge"
        aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header border-bottom">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Knowledge</h5>
                <button
                type="button"
                class="btn-close text-reset"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                <form id="formknowledge" name="formknowledge" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="mb-6">
                        <label class="form-label" for="question">Question</label>
                        <input type="text" class="form-control" id="question" name="question" placeholder="Enter pertanyaan">
                        <div id="question-error" class="text-danger small"></div>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="answer">Jawaban</label>
                        <textarea class="form-control" id="answer" name="answer" rows="3"></textarea>
                        <div id="answer-error" class="text-danger small"></div>
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
<script src="{{ url('assets/ajax/knowledge.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
@endsection
