{{-- Usage: @include('internal.partials.stat-cards', ['stats' => [...]]) --}}
<div class="row g-6 mb-6">
    @foreach ($stats as $stat)
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ $stat['label'] }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $stat['value'] }}</h4>
                            </div>
                            @if (!empty($stat['hint']))
                                <small class="mb-0">{{ $stat['hint'] }}</small>
                            @endif
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-{{ $stat['color'] ?? 'primary' }}">
                                <i class="ti {{ $stat['icon'] ?? 'ti-chart-bar' }} ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
