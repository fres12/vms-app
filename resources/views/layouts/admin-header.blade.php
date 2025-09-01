<header class="w-full bg-white flex items-center justify-between px-8 py-3 shadow-sm">
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <div class="flex items-center">
        <img src="/storage/logo-header.svg" alt="Logo" class="h-4 w-auto mr-4">
    </div>
    
    <div class="flex items-center gap-6">
        @php
            $admin = auth()->guard('admin')->user();
            $deptName = DB::table('depts')
                ->where('deptID', $admin->deptID)
                ->value('nameDept');
        @endphp
        
        <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" 
                    class="flex items-center gap-2 text-gray-600 text-sm hover:text-gray-900 transition-colors duration-200">
                {{ ucwords(strtolower($deptName)) }}
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                
                <div class="py-2">
                    <!-- Department Info -->
                    <div class="px-4 py-2 border-b border-gray-100">
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Department</div>
                        <div class="text-sm font-medium text-gray-900">{{ ucwords(strtolower($deptName)) }}</div>
                    </div>
                    
                    <!-- PIC Info -->
                    <div class="px-4 py-2 border-b border-gray-100">
                        <div class="text-xs text-gray-500 uppercase tracking-wide">PIC</div>
                        <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                    </div>
                    
                    <!-- Account Info -->
                    <div class="px-4 py-2 border-b border-gray-100">
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Account</div>
                        <div class="text-sm font-medium text-gray-900">{{ $admin->email }}</div>
                        <div class="text-xs text-gray-500">{{ $admin->position }}</div>
                    </div>
                    
                    <!-- Change Password Button -->
                    <div class="px-4 py-2">
                        <button onclick="openChangePasswordModal()" 
                                class="w-full text-left text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            Change Password
                        </button>
                    </div>
                    
                    <!-- Logout Button -->
                    <div class="px-4 py-2 border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}" class="inline w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left text-sm text-red-600 hover:text-red-800 hover:bg-red-50 px-2 py-1 rounded transition-colors duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="backdrop-filter: blur(6px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 relative animate-fade-in-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-[#003368]">Change Password</h3>
            <button onclick="closeChangePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="changePasswordForm" class="space-y-4">
            @csrf
            <div>
                <label for="current_password" class="block text-sm font-medium mb-1">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium mb-1">New Password</label>
                <input type="password" id="new_password" name="new_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium mb-1">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div id="changePasswordError" class="text-xs text-red-600"></div>
            <div id="changePasswordSuccess" class="text-xs text-green-600 font-semibold"></div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeChangePasswordModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Change Password
                </button>
            </div>
        </form>
    </div>
    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(40px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .animate-fade-in-up { animation: fade-in-up 0.3s ease; }
    </style>
</div>

<script>
function openChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
    document.getElementById('changePasswordError').textContent = '';
    document.getElementById('changePasswordSuccess').textContent = '';
    document.getElementById('changePasswordForm').reset();
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
    document.getElementById('changePasswordForm').reset();
    document.getElementById('changePasswordError').textContent = '';
    document.getElementById('changePasswordSuccess').textContent = '';
}

document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const errorDiv = document.getElementById('changePasswordError');
    const successDiv = document.getElementById('changePasswordSuccess');
    errorDiv.textContent = '';
    successDiv.textContent = '';

    const current = document.getElementById('current_password').value;
    const newPass = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;

    // Client-side validation
    if (newPass !== confirm) {
        errorDiv.textContent = 'New password and confirmation do not match';
        return;
    }

    if (newPass.length < 6) {
        errorDiv.textContent = 'New password must be at least 6 characters';
        return;
    }

    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="inline-flex items-center">Changing... <svg class="animate-spin ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>';

    try {
        const response = await fetch('{{ route("admin.change-password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                current_password: current,
                new_password: newPass,
                new_password_confirmation: confirm
            })
        });

        const result = await response.json();

        if (result.success) {
            errorDiv.textContent = '';
            successDiv.textContent = 'Password changed successfully!';
            
            // Show success message then redirect
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
        } else {
            errorDiv.textContent = result.message;
            submitButton.disabled = false;
            submitButton.textContent = 'Change Password';
        }
    } catch (error) {
        errorDiv.textContent = 'An error occurred while changing password';
        submitButton.disabled = false;
        submitButton.textContent = 'Change Password';
    }
});
</script>