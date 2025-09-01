<header class="w-full bg-white flex items-center justify-between px-8 py-3 shadow-sm">
    @vite('resources/css/app.css')
    <div class="flex items-center">
        <img src="/storage/logo-header.svg" alt="Logo" class="h-4 w-auto mr-4">
    </div>
    
    <div class="flex items-center gap-6">
        <div class="text-gray-700 text-sm">
                @php
                    $admin = auth()->guard('admin')->user();
                    $deptName = DB::table('depts')
                        ->where('deptID', $admin->deptID)
                        ->value('nameDept');
                    echo ucwords(strtolower($deptName));
                @endphp
        </div>
        
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" 
                    class="p-2 rounded-full hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center"
                    title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </form>
    </div>
</header> 