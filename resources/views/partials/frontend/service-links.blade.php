@foreach (config('frontend_services.items', []) as $service)
    <li><a href="{{ route(config('frontend_services.route', 'frontend.services.index')) }}">{{ $service }}</a></li>
@endforeach
