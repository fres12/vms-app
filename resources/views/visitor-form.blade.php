<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor Registration</title>
    @vite('resources/css/app.css')
    <style>
        body.modal-open {
            overflow: hidden;
            width: 100%;
            /* Prevent content shift by adding padding equal to scrollbar width */
            padding-right: var(--scrollbar-width);
        }

        /* Backdrop styles */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(4px);
        }

        /* Hide default scrollbar and add custom styling */
        #pledgeModal {
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        #pledgeModal::-webkit-scrollbar {
            width: 8px;
        }

        #pledgeModal::-webkit-scrollbar-track {
            background: transparent;
        }

        #pledgeModal::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 20px;
            border: transparent;
        }

        .modal-content-wrapper {
            min-height: 100vh;
            padding: 2.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Custom file input styling */
        .custom-file-input::-webkit-file-upload-button {
            visibility: hidden;
            width: 0;
        }
        
        .custom-file-input::before {
            content: '';
            display: none;
        }

        .custom-file-input {
            padding-left: 10px !important;
            color: #6B7280;
        }

        .custom-file-input:not(:placeholder-shown) {
            color: #000000;
        }

        .dark .custom-file-input:not(:placeholder-shown) {
            color: #FFFFFF;
        }

        /* For Firefox */
        .custom-file-input {
            color-scheme: normal;
        }

        @-moz-document url-prefix() {
            .custom-file-input {
                opacity: 1;
            }
        }
    </style>
</head>
<body class="bg-[#fbfbfc] dark:bg-neutral-900 min-h-screen">
    @include('layouts.header')
    <div class="relative w-full flex flex-col items-center justify-center">
        <div class="absolute inset-0 z-10 bg-[#003368] opacity-70"></div>
        <img src="/storage/cover.jpg" alt="Hyundai" class="w-full h-32 sm:h-44 md:h-56 lg:h-64 object-cover object-center z-0"/>
        <div class="absolute inset-0 flex items-center justify-center z-20 px-2">
            <h2 class="text-white font-medium text-center text-base xs:text-lg sm:text-2xl md:text-3xl leading-tight break-words w-full max-w-xl">
                Visitor Registration<br class="block sm:hidden"/>
            </h2>
        </div>
    </div>
    <div class="flex items-center justify-center">
        <div class="w-full max-w-md sm:max-w-lg md:max-w-4xl lg:max-w-6xl xl:max-w-8xl bg-white dark:bg-neutral-900 p-4 sm:p-8 shadow-xl rounded-xl mt-4 sm:mt-6 md:mt-8 z-10 mb-6 relative">
            <h2 class="text-lg sm:text-xl md:text-2xl lg:text-[23px] font-bold text-[#003368] mb-4">Visitor Registration Form</h2>
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('visitors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="relative mb-4">
                    <input
                        type="text"
                        name="full_name"
                        id="full_name"
                        value="{{ old('full_name') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('full_name') border-red-500 @enderror"
                        placeholder="Full Name"
                        required
                    >
                    <label
                        for="full_name"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white"
                    >
                        Full Name
                    </label>
                    @error('full_name')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="relative mb-4">
                    <input
                        type="text"
                        name="nik"
                        id="nikInput"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        value="{{ old('nik') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('nik') border-red-500 @enderror"
                        placeholder="National ID Number (NIK)"
                        required
                    >
                    <label
                        for="nik"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white"
                    >
                        National ID Number (NIK)
                    </label>
                    <span id="nikError" class="text-red-600 text-xs hidden">Only numbers are allowed.</span>
                    @error('nik')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="relative mb-4">
                    <input
                        type="file"
                        name="id_card_photo"
                        id="id_card_photo"
                        accept="image/*"
                        class="peer custom-file-input w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('id_card_photo') border-red-500 @enderror"
                        placeholder="ID Card Photo"
                        required
                    >
                    <label
                        for="id_card_photo"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white dark:bg-neutral-900 px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            dark:peer-focus:bg-neutral-900
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white
                            dark:peer-not-placeholder-shown:bg-neutral-900"
                    >
                        ID Card Photo
                    </label>
                    <img id="id_card_preview" class="mt-2 rounded max-h-40 hidden">
                </div>
                <div class="relative mb-4">
                    <input
                        type="file"
                        name="self_photo"
                        id="self_photo"
                        accept="image/*"
                        class="peer custom-file-input w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('self_photo') border-red-500 @enderror"
                        placeholder="Self Photo"
                        required
                    >
                    <label
                        for="self_photo"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white dark:bg-neutral-900 px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            dark:peer-focus:bg-neutral-900
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white
                            dark:peer-not-placeholder-shown:bg-neutral-900"
                    >
                        Self Photo
                    </label>
                    <img id="self_photo_preview" class="mt-2 rounded max-h-40 hidden">
                </div>
                <div class="relative mb-4">
                    <input
                        type="text"
                        name="company"
                        id="company"
                        value="{{ old('company') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('company') border-red-500 @enderror"
                        placeholder="Company"
                    >
                    <label
                        for="company"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white"
                    >
                        Company
                    </label>
                    @error('company')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="relative mb-4">
                    <input
                        type="text"
                        name="phone"
                        id="phoneInput"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        value="{{ old('phone') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('phone') border-red-500 @enderror"
                        placeholder="Phone"
                    >
                    <label
                        for="phone"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white"
                    >
                        Phone
                    </label>
                    <span id="phoneError" class="text-red-600 text-xs hidden">Only numbers are allowed.</span>
                    @error('phone')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="relative mb-4">
                    <select
                        name="department_purpose"
                        id="department_purpose"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('department_purpose') border-red-500 @enderror"
                        required
                    >
                        <option value="">Select Department</option>
                        <option value="Dept A" {{ old('department_purpose') == 'Dept A' ? 'selected' : '' }}>Dept A</option>
                        <option value="Dept B" {{ old('department_purpose') == 'Dept B' ? 'selected' : '' }}>Dept B</option>
                        <option value="Dept C" {{ old('department_purpose') == 'Dept C' ? 'selected' : '' }}>Dept C</option>
                    </select>
                    <label
                        for="department_purpose"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white"
                    >
                        Department Purpose
                    </label>
                    @error('department_purpose')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="relative mb-4">
                    <input
                        type="text"
                        name="section_purpose"
                        id="section_purpose"
                        value="{{ old('section_purpose') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('section_purpose') border-red-500 @enderror"
                        placeholder="Section Purpose"
                    >
                    <label
                        for="section_purpose"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white"
                    >
                        Section Purpose
                    </label>
                    @error('section_purpose')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="relative mb-4">
                    <input
                        type="date"
                        name="visit_date"
                        id="visit_date"
                        value="{{ old('visit_date') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('visit_date') border-red-500 @enderror"
                        placeholder="Visit Date"
                        required
                    >
                    <label
                        for="visit_date"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white"
                    >
                        Visit Date
                    </label>
                    @error('visit_date')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="relative mb-4">
                    <input
                        type="time"
                        name="visit_time"
                        id="visit_time"
                        value="{{ old('visit_time') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('visit_time') border-red-500 @enderror"
                        placeholder="Visit Time"
                        required
                    >
                    <label
                        for="visit_time"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-500 bg-white px-1 transition-all duration-200 pointer-events-none
                            peer-placeholder-shown:top-1/2
                            peer-placeholder-shown:text-lg
                            peer-placeholder-shown:text-gray-500
                            peer-focus:top-2
                            peer-focus:text-sm
                            peer-focus:text-black
                            peer-focus:bg-white
                            peer-not-placeholder-shown:top-2
                            peer-not-placeholder-shown:text-sm
                            peer-not-placeholder-shown:text-black
                            peer-not-placeholder-shown:bg-white"
                    >
                        Visit Time
                    </label>
                    @error('visit_time')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center font-normal">
                        <input type="checkbox" id="pledgeCheckbox" name="pledge_agreement" required class="form-checkbox mr-2 accent-[#003368]">
                        <span>I have read and agree with to the the HMMI <button type="button" id="showPledgeBtn" class="underline text-[#003368] hover:text-[#002244]">visitor pledge</button></span>
                    </label>
                </div>
                
                <button id="submitBtn" type="submit" class="w-full bg-[#003368] text-white py-2 rounded hover:bg-[#002244] transition opacity-50 cursor-not-allowed" disabled>Submit</button>
            </form>
        </div>
    </div>

    <!-- Pledge Modal -->
    <div id="pledgeModal" class="hidden fixed inset-0 z-50">
        <!-- Backdrop with transparency and transition -->
        <div class="modal-backdrop opacity-0 transition-opacity duration-300" id="modalBackdrop"></div>
        
        <!-- Modal content wrapper for better scrolling -->
        <div class="modal-content-wrapper relative">
            <!-- Modal content with animation -->
            <div class="bg-white dark:bg-neutral-800 p-6 rounded-lg shadow-xl max-w-lg w-full mx-auto relative opacity-0 translate-y-4 transition-all duration-300 ease-out" id="modalContent">
                <button type="button" id="closePledgeBtn" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <h3 class="text-xl font-bold mb-4 text-[#003368] dark:text-white text-center">Visitor Pledge</h3>
                <div class="mb-6 text-gray-700 dark:text-gray-300">
                    <p>By entering Hyundai Motor Manufacturing Indonesia, I pledge to comply with all visitor rules and regulations, respect the safety and security protocols, and conduct myself responsibly during my visit.</p>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelPledgeBtn" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="agreePledgeBtn" class="px-4 py-2 bg-[#003368] text-white rounded hover:bg-[#002244] transition-colors duration-200">
                        I Agree
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event, previewId) {
            const input = event.target;
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        }

        // Enable/disable submit button based on pledge checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const pledgeCheckbox = document.getElementById('pledgeCheckbox');
            const submitBtn = document.getElementById('submitBtn');
            pledgeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        });

        // Realtime validation for NIK and Phone (only numbers allowed)
        function onlyNumberInput(inputId, errorId) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(errorId);
            input.addEventListener('input', function(e) {
                if (/[^0-9]/.test(this.value)) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    error.classList.remove('hidden');
                } else {
                    error.classList.add('hidden');
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            onlyNumberInput('nikInput', 'nikError');
            onlyNumberInput('phoneInput', 'phoneError');
        });

        // Date and time validation
        document.addEventListener('DOMContentLoaded', function() {
            const visitDateInput = document.querySelector('input[name="visit_date"]');
            const visitTimeInput = document.querySelector('input[name="visit_time"]');
            
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            visitDateInput.setAttribute('min', today);
            
            // Validate date and time combination
            function validateDateTime() {
                const selectedDate = visitDateInput.value;
                const selectedTime = visitTimeInput.value;
                
                if (selectedDate && selectedTime) {
                    const selectedDateTime = new Date(selectedDate + 'T' + selectedTime);
                    const now = new Date();
                    
                    if (selectedDateTime <= now) {
                        visitDateInput.setCustomValidity('Visit date and time must be in the future');
                        visitTimeInput.setCustomValidity('Visit date and time must be in the future');
                    } else {
                        visitDateInput.setCustomValidity('');
                        visitTimeInput.setCustomValidity('');
                    }
                }
            }
            
            visitDateInput.addEventListener('change', validateDateTime);
            visitTimeInput.addEventListener('change', validateDateTime);
        });

        // Updated Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('pledgeModal');
            const modalContent = document.getElementById('modalContent');
            const modalBackdrop = document.getElementById('modalBackdrop');
            const showPledgeBtn = document.getElementById('showPledgeBtn');
            const closePledgeBtn = document.getElementById('closePledgeBtn');
            const cancelPledgeBtn = document.getElementById('cancelPledgeBtn');
            const agreePledgeBtn = document.getElementById('agreePledgeBtn');
            const pledgeCheckbox = document.getElementById('pledgeCheckbox');

            // Calculate scrollbar width and set it as CSS variable
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.documentElement.style.setProperty('--scrollbar-width', `${scrollbarWidth}px`);

            // Get the scroll position before opening modal
            let scrollPosition = 0;

            function showModal() {
                // Store current scroll position
                scrollPosition = window.pageYOffset;
                
                // First show the modal container
                modal.classList.remove('hidden');
                
                // Force a reflow to enable the transition
                modal.offsetHeight;
                
                // Then animate in the backdrop and content
                modalBackdrop.classList.add('opacity-100');
                modalContent.classList.add('opacity-100', 'translate-y-0');
                
                // Add modal-open class to body
                document.body.classList.add('modal-open');
            }

            function hideModal() {
                // First animate out
                modalBackdrop.classList.remove('opacity-100');
                modalContent.classList.remove('opacity-100', 'translate-y-0');
                
                // Wait for animation to finish before hiding
                setTimeout(() => {
                    modal.classList.add('hidden');
                    // Remove modal-open class from body
                    document.body.classList.remove('modal-open');
                    // Restore scroll position
                    window.scrollTo(0, scrollPosition);
                }, 300);
            }

            showPledgeBtn.addEventListener('click', showModal);
            
            closePledgeBtn.addEventListener('click', hideModal);
            cancelPledgeBtn.addEventListener('click', () => {
                pledgeCheckbox.checked = false;
                hideModal();
            });
            
            agreePledgeBtn.addEventListener('click', () => {
                pledgeCheckbox.checked = true;
                hideModal();
                // Trigger the change event to update submit button state
                pledgeCheckbox.dispatchEvent(new Event('change'));
            });

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target === modalBackdrop) {
                    hideModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    hideModal();
                }
            });
        });

        // Add this to your existing script section
        document.addEventListener('DOMContentLoaded', function() {
            // Function to update file input text
            function updateFileInput(input) {
                if (input.files.length > 0) {
                    input.style.color = getComputedStyle(document.documentElement).getPropertyValue('--color-accent');
                } else {
                    input.style.color = '#6B7280';
                }
            }

            // Add event listeners to file inputs
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', () => updateFileInput(input));
                // Set initial state
                updateFileInput(input);
            });
        });
    </script>
</body>
</html>
