<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Page Not Found | EPIKEPC</title>
    <link rel="stylesheet preload" as="style" href="{{ asset('frontend/css/preload.min.css') }}" />
    <link rel="stylesheet preload" as="style" href="{{ asset('frontend/css/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/error.min.css') }}" />
</head>
<body>
    <main class="error d-lg-flex align-items-start">
        <div class="container p-lg-0 m-lg-0 d-flex flex-wrap justify-content-center align-items-center">
            <span class="error_number col-12 col-md-auto">{{ $exception->getStatusCode() ?? '404' }}</span>
            <div class="error_message col-12 col-md-auto d-flex flex-column align-items-start">
                <div class="error_message-header">
                    <span class="subtitle">Oops!</span>
                    <h3 class="title">Page Not Found</h3>
                </div>
                <p class="error_message-text">Sorry, the page you are looking for could not be found.</p>
                <a class="btn" href="{{ url('/') }}">Back to Home</a>
            </div>
        </div>
    </main>
</body>
</html>
