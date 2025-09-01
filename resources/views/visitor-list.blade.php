<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor List - {{ $isMasterAdmin ? 'All Departments' : $deptInfo->nameDept }}</title>
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#fbfbfc]">
    @include('layouts.admin-header')

    <!-- Cover section -->
    <div class="container-fluid px-4">
        <div class="relative w-full flex flex-col items-center justify-center mt-4">
            <div class="absolute inset-0 z-10 bg-[#003368] opacity-70 rounded-xl"></div>
            <img src="/storage/cover.jpg" alt="Hyundai" class="w-full h-24 sm:h-32 md:h-36 object-cover object-center z-0 rounded-xl"/>
            <div class="absolute inset-0 flex items-center justify-center z-20 px-2">
                <h2 class="text-white font-medium text-center text-base sm:text-xl md:text-2xl leading-tight break-words w-full max-w-xl">
                    @if($isMasterAdmin)
                        All Departments Visitor List
                    @else
                        {{ $deptInfo->nameDept }} Visitor List
                    @endif
                </h2>
            </div>
        </div>

        <!-- Table section -->
        <div class="bg-white dark:bg-neutral-900 p-8 px-4 sm:px-8 rounded-xl shadow mt-4">
            <div class="flex justify-end items-center gap-1 mb-2">
                <button type="button" 
                        onclick="updateSelectedStatus('Approve Selected')" 
                        style="background-color: #003368;"
                        class="text-white px-3 py-1.5 rounded text-xs hover:bg-[#002244] transition-colors">
                    Approve
                </button>
                <button type="button"
                        onclick="updateSelectedStatus('Decline Selected')" 
                        style="background-color: #003368;"
                        class="text-white px-3 py-1.5 rounded text-xs hover:bg-[#002244] transition-colors">
                    Decline
                </button>
                <button type="button"
                        onclick="updateSelectedStatus('Export Selected')"
                   style="background-color: #003368;"
                   class="text-white px-3 py-1.5 rounded text-xs hover:bg-[#002244] transition-colors">
                    Export
                </button>
            </div>

            <div class="overflow-x-auto relative">
                <table class="w-full border-collapse border text-xs">
                    <thead>
                        <tr class="bg-[#003368] dark:bg-neutral-800">
                            <th class="sticky left-0 bg-[#003368] border border-white px-1.5 py-1 z-20">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300" onclick="toggleAllCheckboxes()">
                            </th>
                            <th class="px-4 py-1 border text-white">Full Name</th>
                            <th class="px-6 py-1 border text-white">Email</th>
                            <th class="px-6 py-1 border text-white">ID Number</th>
                            <th class="px-4 py-1 border text-white">Company</th>
                            <th class="px-6 py-1 border text-white">Phone</th>
                            @if($isMasterAdmin)
                                <th class="px-6 py-1 border text-white">Department</th>
                            @endif
                            <th class="px-10 py-1 border text-white">Visit Purpose</th>
                            <th class="px-6 py-1 border text-center text-white">Start Period</th>
                            <th class="px-6 py-1 border text-center text-white">End Period</th>
                            <th class="px-3 py-1 border text-white">Equipment</th>
                            <th class="px-3 py-1 border text-white">Brand</th>
                            <th class="px-3 py-1 border text-white">ID Card</th>
                            <th class="px-3 py-1 border text-white">Self Photo</th>
                            <th class="px-6 py-1 border text-center text-white">Submit Date</th>
                            <th class="px-6 py-1 border text-center text-white">Status</th>
                            <th class="px-6 py-1 border text-center text-white">Approved Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitors as $visitor)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-neutral-800">
                                <td class="sticky left-0 bg-white border border-gray-200 p-1.5 text-center z-10">
                                    <input type="checkbox" class="visitor-checkbox rounded border-gray-300" 
                                           value="{{ $visitor->id }}"
                                           data-status="{{ $visitor->status }}">
                                </td>
                                <td class="p-1.5 border">{{ $visitor->fullname }}</td>
                                <td class="p-1.5 border">{{ $visitor->email }}</td>
                                <td class="p-1.5 border">{{ $visitor->nik }}</td>
                                <td class="p-1.5 border">{{ $visitor->company }}</td>
                                <td class="p-1.5 border">{{ $visitor->phone }}</td>
                                @if($isMasterAdmin)
                                    <td class="p-1.5 border">{{ $visitor->department_name }}</td>
                                @endif
                                <td class="p-1.5 border">{{ $visitor->visit_purpose }}</td>
                                <td class="p-1.5 border">{{ \Carbon\Carbon::parse($visitor->startdate)->format('d-m-Y H:i') }}</td>
                                <td class="p-1.5 border">{{ \Carbon\Carbon::parse($visitor->enddate)->format('d-m-Y H:i') }}</td>
                                <td class="p-1.5 border">{{ $visitor->equipment_type }}</td>
                                <td class="p-1.5 border">{{ $visitor->brand }}</td>
                                <td class="p-1.5 border text-center">
                                    @if($visitor->idcardphoto)
                                        <a href="{{ asset('storage/' . $visitor->idcardphoto) }}" target="_blank" class="text-blue-600 underline">View</a>
                                    @endif
                                </td>
                                <td class="p-1.5 border text-center">
                                    @if($visitor->selfphoto)
                                        <a href="{{ asset('storage/' . $visitor->selfphoto) }}" target="_blank" class="text-blue-600 underline">View</a>
                                    @endif
                                </td>
                                <td class="p-1.5 border">{{ \Carbon\Carbon::parse($visitor->submit_date)->format('d-m-Y H:i') }}</td>
                                <td class="p-1.5 border text-center">
                                    <span class="text-center 
                                        @if($isMasterAdmin && $visitor->status === 'Approved (1/2)')
                                            text-blue-600
                                        @elseif(!$isMasterAdmin && $visitor->status === 'For Review')
                                            text-blue-600
                                        @endif
                                    ">{{ $visitor->status }}</span>
                                </td>
                                <td class="p-1.5 border">
                                    @if($visitor->approved_date)
                                        {{ \Carbon\Carbon::parse($visitor->approved_date)->format('d-m-Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isMasterAdmin ? 18 : 17 }}" class="text-center p-1.5">No visitors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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

            function showLoadingOverlay() {
                const overlay = document.createElement('div');
                overlay.id = 'loadingOverlay';
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                `;
                
                const spinner = document.createElement('div');
                spinner.style.cssText = `
                    width: 50px;
                    height: 50px;
                    border: 5px solid #f3f3f3;
                    border-top: 5px solid #003368;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                `;
                
                const style = document.createElement('style');
                style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
                
                document.head.appendChild(style);
                overlay.appendChild(spinner);
                document.body.appendChild(overlay);
            }

            function hideLoadingOverlay() {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.remove();
                }
            }

            async function reloadTableData() {
                try {
                    const response = await fetch(window.location.href);
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Get the new table content
                    const newTable = doc.querySelector('.overflow-x-auto table');
                    const currentTable = document.querySelector('.overflow-x-auto table');
                    
                    if (newTable && currentTable) {
                        currentTable.innerHTML = newTable.innerHTML;
                    }
                } catch (error) {
                    console.error('Error reloading table:', error);
                }
            }

            async function updateSelectedStatus(action) {
            const checkboxes = document.getElementsByClassName('visitor-checkbox');
            const selectedIds = [];
                const invalidStatusSelected = [];
            
            for (let checkbox of checkboxes) {
                if (checkbox.checked) {
                        const status = checkbox.getAttribute('data-status');
                        const isMasterAdmin = {{ $isMasterAdmin ? 'true' : 'false' }};
                        
                        // Check if status can be changed
                        if (action === 'Approve Selected') {
                            if (isMasterAdmin && status !== 'Approved (1/2)') {
                                invalidStatusSelected.push('Request approval to department admin first');
                                continue;
                            } else if (!isMasterAdmin && status !== 'For Review') {
                                invalidStatusSelected.push('Invalid state to change status');
                                continue;
                            }
                        } else if (action === 'Decline Selected') {
                            if (status !== 'For Review') {
                                invalidStatusSelected.push('Invalid state to change status');
                                continue;
                            }
                        }
                        
                    selectedIds.push(checkbox.value);
                }
            }

                if (selectedIds.length === 0 && invalidStatusSelected.length > 0) {
                    alert(invalidStatusSelected[0]);
                    return;
                }

            if (selectedIds.length === 0) {
                    alert('Please select at least one visitor');
                    return;
                }

                if (action !== 'Export Selected' && invalidStatusSelected.length > 0) {
                    alert(invalidStatusSelected[0]);
                return;
            }

            // Map action to status
            const status = action === 'Approve Selected' ? 'Accepted' : 'Rejected';

                // If it's an export action, handle differently
                if (action === 'Export Selected') {
                    window.location.href = '{{ route('visitors.export') }}?' + selectedIds.map(id => 'selected_ids[]=' + id).join('&');
                    return;
                }

                // Show loading overlay
                showLoadingOverlay();

                try {
                    // Process all updates in parallel
                    const updatePromises = selectedIds.map(id =>
                fetch('/visitors/' + id + '/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status })
                }).then(res => res.json())
                    );

                    // Wait for all updates to complete
                    const results = await Promise.all(updatePromises);
                    
                    // Check if any update failed
                    const failures = results.filter(result => !result.success);
                    if (failures.length > 0) {
                        throw new Error(failures[0].message || 'Failed to update some statuses');
                    }

                    // Reload table data
                    await reloadTableData();

                    // Uncheck all checkboxes
                    document.getElementById('selectAll').checked = false;
                    for (let checkbox of checkboxes) {
                        checkbox.checked = false;
                    }

                } catch (error) {
                    alert(error.message || 'An error occurred while updating status');
                console.error('Error:', error);
                } finally {
                    // Hide loading overlay
                    hideLoadingOverlay();
                }
        }
    </script>
</body>
</html>