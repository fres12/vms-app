<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - HMMI VMS</title>
    @vite('resources/css/app.css')
    <style>
        @font-face {
            font-family: 'HyundaiSansHead';
            src: url('/fonts/HyundaiSansHead-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'HyundaiSansHead';
            src: url('/fonts/HyundaiSansHead-Medium.ttf') format('truetype');
            font-weight: 500;
            font-style: normal;
        }

        body {
            font-family: 'HyundaiSansHead', sans-serif;
            background-color: #fcfdfdff;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">
    <div class="relative w-full max-w-md">
        <!-- Login Form Container -->
        <div class="login-container relative z-10 p-6 rounded-2xl shadow-2xl">
            <div class="flex items-center mb-4">
                <img src="{{ asset('storage/logo-header.svg') }}" alt="HMMI" class="h-7 mr-4">
                
            </div>
            <p class="text-gray-600 text-sm mb-8">Please sign in to continue</p>

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#003368] focus:border-transparent transition @error('email') border-red-500 @enderror" 
                           required 
                           autofocus
                           maxlength="255"
                           pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
                           title="Please enter a valid email address"
                           oninput="this.value = this.value.replace(/[<>\"'&]/g, '')">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                    <input type="password" 
                           name="password" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#003368] focus:border-transparent transition @error('password') border-red-500 @enderror" 
                           required
                           minlength="6"
                           maxlength="255"
                           oninput="this.value = this.value.replace(/[<>\"'&]/g, '')">
                </div>
                <button type="submit" 
                        class="w-full bg-[#003368] text-white py-2 px-4 rounded-lg hover:bg-[#002244] transition duration-200 font-medium">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>