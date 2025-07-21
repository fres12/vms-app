<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <!-- Logout Button -->
    <div class="fixed top-4 right-4 z-50">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors duration-200" title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </form>
    </div>

    <script>
        function toggleAllCheckboxes() {
            const mainCheckbox = document.getElementById('selectAll');
            const checkboxes = document.getElementsByClassName('visitor-checkbox');
            for (let checkbox of checkboxes) {
                checkbox.checked = mainCheckbox.checked;
            }
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}-${month}-${year} ${hours}:${minutes}`;
        }

        function updateVisitorRow(visitorId, newStatus, approvedDate) {
            console.log('Updating row:', { visitorId, newStatus, approvedDate });
            const row = document.querySelector(`input[value="${visitorId}"]`).closest('tr');
            const statusCell = row.querySelector('td:nth-last-child(2)');
            const approvedDateCell = row.querySelector('td:last-child');
            
            // Update status text
            statusCell.querySelector('span').textContent = newStatus;
            
            // Format dan update approved date
            if (approvedDate) {
                const date = new Date(approvedDate);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                approvedDateCell.textContent = `${day}-${month}-${year} ${hours}:${minutes}`;
            } else {
                approvedDateCell.textContent = '-';
            }
            
            console.log('Row updated successfully');
        }

        function updateSelectedStatus(action) {
            console.log('Starting status update with action:', action);
            const checkboxes = document.getElementsByClassName('visitor-checkbox');
            const selectedIds = [];
            
            for (let checkbox of checkboxes) {
                if (checkbox.checked) {
                    selectedIds.push(checkbox.value);
                }
            }

            console.log('Selected IDs:', selectedIds);

            if (selectedIds.length === 0) {
                console.log('No visitors selected');
                return;
            }

            // Map action to status
            const status = action === 'Approve Selected' ? 'Accepted' : 'Rejected';

            // Update status for each selected visitor
            selectedIds.forEach(id => {
                console.log('Processing visitor ID:', id, 'with status:', status);
                fetch('/visitors/' + id + '/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status })
                })
                .then(res => {
                    console.log('Response status:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Update UI immediately without page reload
                        updateVisitorRow(
                            id, 
                            data.status,
                            data.approved_date
                        );
                        
                        // Uncheck the checkbox
                        document.querySelector(`input[value="${id}"]`).checked = false;
                        
                        // Update selectAll checkbox if needed
                        const allUnchecked = Array.from(document.getElementsByClassName('visitor-checkbox'))
                            .every(cb => !cb.checked);
                        if (allUnchecked) {
                            document.getElementById('selectAll').checked = false;
                        }
                    } else {
                        console.error('Failed to update status:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
    </script>

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
</body>
</html> 