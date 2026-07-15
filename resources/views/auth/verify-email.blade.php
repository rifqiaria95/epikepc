<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        @if(session('message'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ session('message') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        @if(session('email'))
            <p class="mb-2">
                We have sent a verification link to:
            </p>
            <p class="font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ session('email') }}</p>
        @endif

        {{ __('Please check your email and click the verification link we sent. If you did not receive the email, we will gladly send another one.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ __('A new verification link has been sent to the email address you registered.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        @if(session('email'))
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <input type="hidden" name="email" value="{{ session('email') }}">
                
                <div>
                    <x-primary-button>
                        {{ __('Resend Verification Email') }}
                    </x-primary-button>
                </div>
            </form>
        @else
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-primary-button>
                        {{ __('Resend Verification Email') }}
                    </x-primary-button>
                </div>
            </form>
        @endif

        <a href="{{ route('login') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
            {{ __('Back to Login') }}
        </a>
    </div>
</x-guest-layout>
