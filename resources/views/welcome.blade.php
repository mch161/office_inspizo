<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Inspizo Office</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
    @inject('auth', 'Illuminate\Support\Facades\Auth')
    @inject('route', 'Illuminate\Support\Facades\Route')

</head>

<body>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Inzpizo Office</h1>
            <p class="text-lg text-gray-600">this is app for Inspizo Office management  </p>
                @if (Route::has('login'))
                <a href="{{ route('login') }}"
                    class="mt-4 inline-block px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Login</a>
                @endif
        </div>
    </div>
</body>