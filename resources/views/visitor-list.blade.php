<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor List</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 dark:bg-neutral-900 min-h-screen p-8">
    <div class="max-w-7xl mx-auto bg-white dark:bg-neutral-900 p-8 px-4 sm:px-8 rounded-xl shadow">
        <h2 class="text-2xl font-bold mb-6 text-center">Visitor Registration List</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full w-full border text-sm">
                <thead>
                    <tr class="bg-gray-200 dark:bg-neutral-800">
                        <th class="px-3 py-2 border">No</th>
                        <th class="px-3 py-2 border">Full Name</th>
                        <th class="px-3 py-2 border">NIK</th>
                        <th class="px-3 py-2 border">Company</th>
                        <th class="px-3 py-2 border">Phone</th>
                        <th class="px-3 py-2 border">Department Purpose</th>
                        <th class="px-3 py-2 border">Section Purpose</th>
                        <th class="px-3 py-2 border">Visit Date</th>
                        <th class="px-3 py-2 border">Visit Time</th>
                        <th class="px-3 py-2 border">ID Card Photo</th>
                        <th class="px-3 py-2 border">Self Photo</th>
                        <th class="px-3 py-2 border">Created At</th>
                        <th class="px-3 py-2 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $i => $visitor)
                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-neutral-800">
                            <td class="px-3 py-2 border text-center">{{ $i+1 }}</td>
                            <td class="px-3 py-2 border">{{ $visitor->full_name }}</td>
                            <td class="px-3 py-2 border">{{ $visitor->nik }}</td>
                            <td class="px-3 py-2 border">{{ $visitor->company }}</td>
                            <td class="px-3 py-2 border">{{ $visitor->phone }}</td>
                            <td class="px-3 py-2 border">{{ $visitor->department_purpose }}</td>
                            <td class="px-3 py-2 border">{{ $visitor->section_purpose }}</td>
                            <td class="px-3 py-2 border">{{ \Illuminate\Support\Carbon::parse($visitor->visit_datetime)->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 border">{{ \Illuminate\Support\Carbon::parse($visitor->visit_datetime)->format('H:i') }}</td>
                            <td class="px-3 py-2 border text-center">
                                @if($visitor->id_card_photo)
                                    <a href="{{ asset('storage/' . $visitor->id_card_photo) }}" target="_blank" class="text-blue-600 underline">View</a>
                                @endif
                            </td>
                            <td class="px-3 py-2 border text-center">
                                @if($visitor->self_photo)
                                    <a href="{{ asset('storage/' . $visitor->self_photo) }}" target="_blank" class="text-blue-600 underline">View</a>
                                @endif
                            </td>
                            <td class="px-3 py-2 border">{{ $visitor->created_at }}</td>
                            <td class="px-3 py-2 border text-center">{{ $visitor->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-4">No visitors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 