<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor Response</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 dark:bg-neutral-900 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full bg-white dark:bg-neutral-900 p-8 rounded-xl shadow">
        <div class="text-center">
            @if($success)
                <div class="mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">Action Completed</h2>
                </div>
            @else
                <div class="mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">Action Failed</h2>
                </div>
            @endif

            <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">{{ $message }}</p>

            @if($visitor)
                <div class="bg-gray-50 dark:bg-neutral-800 p-6 rounded-lg mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Visitor Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Name:</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $visitor->full_name }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">NIK:</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $visitor->nik }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Company:</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $visitor->company ?? 'Not provided' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Department:</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $visitor->department_purpose }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Visit Date:</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ \Illuminate\Support\Carbon::parse($visitor->visit_datetime)->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($visitor->status === 'Accepted') bg-green-100 text-green-800
                                @elseif($visitor->status === 'Rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $visitor->status }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('visitors.index') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 text-center">
                    View All Visitors
                </a>
                <a href="{{ route('visitor.form') }}" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition duration-200 text-center">
                    New Registration
                </a>
            </div>

            <div class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                <p>This page will automatically redirect to the visitor list in 10 seconds...</p>
            </div>
        </div>
    </div>

    <script>
        // Auto redirect after 10 seconds
        setTimeout(function() {
            window.location.href = "{{ route('visitors.index') }}";
        }, 10000);
    </script>
</body>
</html> 