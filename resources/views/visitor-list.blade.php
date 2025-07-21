<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    @include('layouts.admin-header')

    <!-- Existing content -->
    <div class="container-fluid px-4 py-8">
        <div class="bg-white dark:bg-neutral-900 p-8 px-4 sm:px-8 rounded-xl shadow">
            <h2 class="text-2xl font-bold mb-6 text-center">Visitor Registration List</h2>
            <div class="flex justify-end items-center gap-4 mb-4">
                <button onclick="updateSelectedStatus('Approve Selected')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Approve Selected</button>
                <button onclick="updateSelectedStatus('Decline Selected')" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Decline Selected</button>
                <a href="{{ route('visitors.export') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Export to Excel</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border text-sm">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-neutral-800">
                            <th class="px-3 py-2 border">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300" onclick="toggleAllCheckboxes()">
                            </th>
                            <th class="px-4 py-2 border">Full Name</th>
                            <th class="px-6 py-2 border">Email</th>
                            <th class="px-6 py-2 border">ID Number</th>
                            <th class="px-4 py-2 border">Company</th>
                            <th class="px-6 py-2 border">Phone</th>
                            <th class="px-6 py-2 border">Department</th>
                            <th class="px-14 py-2 border">Visit Purpose</th>
                            <th class="px-12 py-2 border text-center">Start Period</th>
                            <th class="px-12 py-2 border text-center">End Period</th>
                            <th class="px-3 py-2 border">Equipment</th>
                            <th class="px-3 py-2 border">Brand</th>
                            <th class="px-5 py-2 border">ID Card</th>
                            <th class="px-5 py-2 border">Self Photo</th>
                            <th class="px-12 py-2 border text-center">Submit Date</th>
                            <th class="px-12 py-2 border text-center">Status</th>
                            <th class="px-12 py-2 border text-center">Approved Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitors as $visitor)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-neutral-800">
                                <td class="px-3 py-2 border text-center">
                                    <input type="checkbox" class="visitor-checkbox rounded border-gray-300" value="{{ $visitor->id }}">
                                </td>
                                <td class="px-6 py-2 border">{{ $visitor->fullname }}</td>
                                <td class="px-6 py-2 border">{{ $visitor->email }}</td>
                                <td class="px-6 py-2 border">{{ $visitor->nik }}</td>
                                <td class="px-6 py-2 border">{{ $visitor->company }}</td>
                                <td class="px-6 py-2 border">{{ $visitor->phone }}</td>
                                <td class="px-6 py-2 border">
                                    @php
                                        $deptName = DB::table('depts')->where('deptID', $visitor->deptpurpose)->value('nameDept');
                                    @endphp
                                    {{ $deptName }}
                                </td>
                                <td class="px-14 py-2 border">{{ $visitor->visit_purpose }}</td>
                                <td class="px-8 py-2 border text-center">{{ \Carbon\Carbon::parse($visitor->startdate)->format('d-m-Y H:i') }}</td>
                                <td class="px-8 py-2 border text-center">{{ \Carbon\Carbon::parse($visitor->enddate)->format('d-m-Y H:i') }}</td>
                                <td class="px-6 py-2 border">{{ $visitor->equipment_type }}</td>
                                <td class="px-6 py-2 border">{{ $visitor->brand }}</td>
                                <td class="px-3 py-2 border text-center">
                                    @if($visitor->idcardphoto)
                                        <a href="{{ asset('storage/' . $visitor->idcardphoto) }}" target="_blank" class="text-blue-600 underline">View</a>
                                    @endif
                                </td>
                                <td class="px-3 py-2 border text-center">
                                    @if($visitor->selfphoto)
                                        <a href="{{ asset('storage/' . $visitor->selfphoto) }}" target="_blank" class="text-blue-600 underline">View</a>
                                    @endif
                                </td>
                                <td class="px-8 py-2 border text-center">{{ \Carbon\Carbon::parse($visitor->submit_date)->format('d-m-Y H:i') }}</td>
                                <td class="px-12 py-2 border text-center">
                                    <span class="text-center">{{ $visitor->status }}</span>
                                </td>
                                <td class="px-8 py-2 border text-center">
                                    @if($visitor->approved_date)
                                        {{ \Carbon\Carbon::parse($visitor->approved_date)->format('d-m-Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="text-center py-4">No visitors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleAllCheckboxes() {
            const mainCheckbox = document.getElementById('selectAll');
            const checkboxes = document.getElementsByClassName('visitor-checkbox');
            for (let checkbox of checkboxes) {
                checkbox.checked = mainCheckbox.checked;
            }
        }

        function updateSelectedStatus(action) {
            const checkboxes = document.getElementsByClassName('visitor-checkbox');
            const selectedIds = [];
            
            for (let checkbox of checkboxes) {
                if (checkbox.checked) {
                    selectedIds.push(checkbox.value);
                }
            }

            if (selectedIds.length === 0) {
                return;
            }

            // Map action to status
            const status = action === 'Approve Selected' ? 'Accepted' : 'Rejected';

            // Update status for each selected visitor
            Promise.all(selectedIds.map(id => 
                fetch('/visitors/' + id + '/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status })
                }).then(res => res.json())
            ))
            .then(() => {
                // Refresh halaman setelah update
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html> 