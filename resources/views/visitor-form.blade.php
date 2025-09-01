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

            <form id="visitorForm" action="{{ route('visitor.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
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
                        maxlength="255"
                        pattern="[a-zA-Z\s\.\-\']+"
                        title="Only letters, spaces, dots, hyphens, and apostrophes are allowed"
                        oninput="this.value = this.value.replace(/[<>\"'&;()]/g, '')"
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
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('email') border-red-500 @enderror"
                        placeholder="Email"
                        required
                        maxlength="255"
                        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
                        title="Please enter a valid email address"
                        oninput="this.value = this.value.replace(/[<>\"'&;()]/g, '')"
                    >
                    <label
                        for="email"
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
                        Email
                    </label>
                    @error('email')
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
                        maxlength="16"
                        title="NIK must contain only numbers"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
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
                        name="deptpurpose"
                        id="deptpurpose"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('deptpurpose') border-red-500 @enderror"
                        required
                    >
                        <option value="">Select Department</option>
                        @foreach($depts as $dept)
                            @if($dept->deptID != 1)
                                <option value="{{ $dept->deptID }}" {{ old('deptpurpose') == $dept->deptID ? 'selected' : '' }}>
                                    {{ $dept->nameDept }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <label
                        for="deptpurpose"
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
                    @error('deptpurpose')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="relative mb-4">
                    <input
                        type="text"
                        name="visit_purpose"
                        id="visit_purpose"
                        value="{{ old('visit_purpose') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('visit_purpose') border-red-500 @enderror"
                        placeholder="Visit Purpose"
                        required
                    >
                    <label
                        for="visit_purpose"
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
                        Visit Purpose
                    </label>
                    @error('visit_purpose')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="relative mb-4">
                    <input
                        type="datetime-local"
                        name="startdate"
                        id="startdate"
                        value="{{ old('startdate') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('startdate') border-red-500 @enderror"
                        required
                    >
                    <label
                        for="startdate"
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
                        Start Date & Time
                    </label>
                    @error('startdate')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="relative mb-4">
                    <input
                        type="datetime-local"
                        name="enddate"
                        id="enddate"
                        value="{{ old('enddate') }}"
                        class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('enddate') border-red-500 @enderror"
                        required
                    >
                    <label
                        for="enddate"
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
                        End Date & Time
                    </label>
                    @error('enddate')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Additional Requirements</h3>
                    <p class="text-gray-600 mb-4">Please specify if you are bringing additional items (e.g. electronic devices, tools, etc.) or have any special requirements</p>
                    
                    <div class="relative mb-4">
                        <input
                            type="text"
                            name="equipment_type"
                            id="equipment_type"
                            value="{{ old('equipment_type') }}"
                            class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('equipment_type') border-red-500 @enderror"
                            placeholder="Equipment Type"
                        >
                        <label
                            for="equipment_type"
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
                            Equipment Type
                        </label>
                    </div>

                    <div class="relative mb-4">
                        <input
                            type="text"
                            name="brand"
                            id="brand"
                            value="{{ old('brand') }}"
                            class="peer w-full border rounded px-3 py-4 pt-6 text-base bg-transparent placeholder-transparent focus:border-black focus:ring-0 @error('brand') border-red-500 @enderror"
                            placeholder="Brand"
                        >
                        <label
                            for="brand"
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
                            Brand
                        </label>
                    </div>
                </div>

                <!-- Hidden fields for status and submit date -->
                <input type="hidden" name="status" value="For Review">
                <input type="hidden" name="submit_date" value="{{ now() }}">
                <input type="hidden" name="approved_date" value="">
                <input type="hidden" name="pledge_agreement" id="pledge_agreement_hidden" value="0">
                
                <button id="nextBtn" type="button" class="w-full bg-[#003368] text-white py-3 rounded hover:bg-[#002244] transition font-medium text-lg">Next</button>
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
                    <p class="mb-4">By entering Hyundai Motor Manufacturing Indonesia, I pledge to comply with all visitor rules and regulations, respect the safety and security protocols, and conduct myself responsibly during my visit.</p>
                    
                    <div class="bg-gray-50 dark:bg-neutral-700 p-4 rounded-lg">
                        <h4 class="font-semibold mb-2 text-gray-800 dark:text-gray-200">Visitor Rules & Regulations:</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• Always wear visitor badge and safety equipment as required</li>
                            <li>• Follow all safety instructions and emergency procedures</li>
                            <li>• Stay within designated areas and respect restricted zones</li>
                            <li>• Maintain professional behavior and dress code</li>
                            <li>• Report any incidents or concerns immediately</li>
                            <li>• Follow COVID-19 protocols if applicable</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="inline-flex items-center font-normal">
                        <input type="checkbox" id="pledgeCheckbox" name="pledge_agreement" required class="form-checkbox mr-3 accent-[#003368] w-5 h-5">
                        <span class="text-gray-700 dark:text-gray-300">I have read and accept the visitor pledge and agree to comply with all rules and regulations.</span>
                    </label>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelPledgeBtn" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="submitBtn" class="px-4 py-2 bg-[#003368] text-white rounded hover:bg-[#002244] transition-colors duration-200 opacity-50 cursor-not-allowed" disabled>Submit Application</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation function
        function validateForm() {
            const form = document.getElementById('visitorForm');
            
            // Get form elements
            const fullName = document.getElementById('full_name');
            const email = document.getElementById('email');
            const nik = document.getElementById('nikInput');
            const company = document.getElementById('company');
            const phone = document.getElementById('phoneInput');
            const visitPurpose = document.getElementById('visit_purpose');
            const equipmentType = document.getElementById('equipment_type');
            const brand = document.getElementById('brand');
            
            // Validate full name (only letters, spaces, dots, hyphens, apostrophes)
            const nameRegex = /^[a-zA-Z\s\.\-\']+$/;
            if (!nameRegex.test(fullName.value.trim())) {
                alert('Full name contains invalid characters. Only letters, spaces, dots, hyphens, and apostrophes are allowed.');
                fullName.focus();
                return false;
            }
            
            // Validate email
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value.trim())) {
                alert('Please enter a valid email address.');
                email.focus();
                return false;
            }
            
            // Validate NIK (only numbers)
            const nikRegex = /^[0-9]+$/;
            if (!nikRegex.test(nik.value.trim())) {
                alert('NIK must contain only numbers.');
                nik.focus();
                return false;
            }
            
            // Validate company name (if provided)
            if (company.value.trim()) {
                const companyRegex = /^[a-zA-Z0-9\s\.\-\&\,]+$/;
                if (!companyRegex.test(company.value.trim())) {
                    alert('Company name contains invalid characters.');
                    company.focus();
                    return false;
                }
            }
            
            // Validate phone (if provided)
            if (phone.value.trim()) {
                const phoneRegex = /^[0-9\-\+\(\)\s]+$/;
                if (!phoneRegex.test(phone.value.trim())) {
                    alert('Phone number contains invalid characters.');
                    phone.focus();
                    return false;
                }
            }
            
            // Validate visit purpose
            const purposeRegex = /^[a-zA-Z0-9\s\.\-\,\!\?]+$/;
            if (!purposeRegex.test(visitPurpose.value.trim())) {
                alert('Visit purpose contains invalid characters.');
                visitPurpose.focus();
                return false;
            }
            
            // Validate equipment type (if provided)
            if (equipmentType.value.trim()) {
                const equipmentRegex = /^[a-zA-Z0-9\s\.\-\&\,]+$/;
                if (!equipmentRegex.test(equipmentType.value.trim())) {
                    alert('Equipment type contains invalid characters.');
                    equipmentType.focus();
                    return false;
                }
            }
            
            // Validate brand (if provided)
            if (brand.value.trim()) {
                const brandRegex = /^[a-zA-Z0-9\s\.\-\&\,]+$/;
                if (!brandRegex.test(brand.value.trim())) {
                    alert('Brand contains invalid characters.');
                    brand.focus();
                    return false;
                }
            }
            
            // Check for dangerous characters
            const dangerousChars = /[<>\"'&;()]/;
            const allInputs = [fullName, email, nik, company, phone, visitPurpose, equipmentType, brand];
            
            for (let input of allInputs) {
                if (input.value && dangerousChars.test(input.value)) {
                    alert('Invalid characters detected in ' + input.name + '.');
                    input.focus();
                    return false;
                }
            }
            
            // Check length limits
            if (fullName.value.length > 255 || email.value.length > 255 || nik.value.length > 16) {
                alert('Input too long.');
                return false;
            }
            
            return true;
        }
        
        // Sanitize input function
        function sanitizeInput(input) {
            return input.replace(/[<>\"'&;()]/g, '');
        }
        
        // Apply sanitization to all text inputs
        document.addEventListener('DOMContentLoaded', function() {
            const textInputs = document.querySelectorAll('input[type="text"], input[type="email"]');
            textInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = sanitizeInput(this.value);
                });
            });
        });

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
            const nextBtn = document.getElementById('nextBtn');
            
            // Show pledge modal when Next button is clicked
            nextBtn.addEventListener('click', function() {
                // Validate form first
                const form = document.getElementById('visitorForm');
                if (form.checkValidity()) {
                    // Additional validation for required fields
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.focus();
                        }
                    });
                    
                    if (isValid) {
                        showModal();
                    }
                } else {
                    // Trigger validation display
                    form.reportValidity();
                }
            });
            
            // Enable/disable submit button based on pledge checkbox
            pledgeCheckbox.addEventListener('change', function() {
                const pledgeHiddenField = document.getElementById('pledge_agreement_hidden');
                
                if (this.checked) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    pledgeHiddenField.value = '1';
                    console.log('Pledge checkbox checked, hidden field value:', pledgeHiddenField.value);
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    pledgeHiddenField.value = '0';
                    console.log('Pledge checkbox unchecked, hidden field value:', pledgeHiddenField.value);
                }
            });
            
            // Handle form submission from pledge modal
            submitBtn.addEventListener('click', function() {
                if (pledgeCheckbox.checked) {
                    // Ensure hidden field is set
                    document.getElementById('pledge_agreement_hidden').value = '1';
                    
                    // Debug: log the value before submission
                    console.log('Pledge agreement value before submit:', document.getElementById('pledge_agreement_hidden').value);
                    
                    // Submit the form
                    document.getElementById('visitorForm').submit();
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
            const startDateInput = document.querySelector('input[name="startdate"]');
            const endDateInput = document.querySelector('input[name="enddate"]');
            
            // Set minimum date to today
            const today = new Date().toISOString().slice(0, 16); // YYYY-MM-DDTHH:MM
            startDateInput.setAttribute('min', today);
            endDateInput.setAttribute('min', today);
            
            // Validate date and time combination
            function validateDateTime() {
                const selectedStartDate = startDateInput.value;
                const selectedEndDate = endDateInput.value;
                
                if (selectedStartDate && selectedEndDate) {
                    const selectedStartDateTime = new Date(selectedStartDate);
                    const selectedEndDateTime = new Date(selectedEndDate);
                    const now = new Date();
                    
                    if (selectedStartDateTime <= now) {
                        startDateInput.setCustomValidity('Start date and time must be in the future');
                    } else {
                        startDateInput.setCustomValidity('');
                    }

                    if (selectedEndDateTime <= now) {
                        endDateInput.setCustomValidity('End date and time must be in the future');
                    } else {
                        endDateInput.setCustomValidity('');
                    }

                    if (selectedEndDateTime <= selectedStartDateTime) {
                        endDateInput.setCustomValidity('End date and time must be after start date and time');
                    } else {
                        endDateInput.setCustomValidity('');
                    }
                }
            }
            
            startDateInput.addEventListener('change', validateDateTime);
            endDateInput.addEventListener('change', validateDateTime);
        });

        // Updated Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('pledgeModal');
            const modalContent = document.getElementById('modalContent');
            const modalBackdrop = document.getElementById('modalBackdrop');
            const closePledgeBtn = document.getElementById('closePledgeBtn');
            const cancelPledgeBtn = document.getElementById('cancelPledgeBtn');
            const pledgeCheckbox = document.getElementById('pledgeCheckbox');

            // Calculate scrollbar width and set it as CSS variable
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.documentElement.style.setProperty('--scrollbar-width', `${scrollbarWidth}px`);

            // Get the scroll position before opening modal
            let scrollPosition = 0;

            // Make showModal function globally accessible
            window.showModal = function() {
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
            };

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
                    
                    // Reset pledge agreement if modal is closed without submission
                    if (!pledgeCheckbox.checked) {
                        document.getElementById('pledge_agreement_hidden').value = '0';
                    }
                }, 300);
            }

            closePledgeBtn.addEventListener('click', hideModal);
            cancelPledgeBtn.addEventListener('click', () => {
                pledgeCheckbox.checked = false;
                // Reset hidden field
                document.getElementById('pledge_agreement_hidden').value = '0';
                hideModal();
                // Reset submit button state
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').classList.add('opacity-50', 'cursor-not-allowed');
            });

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target === modalBackdrop) {
                    // Reset pledge agreement when closing without submission
                    if (!pledgeCheckbox.checked) {
                        document.getElementById('pledge_agreement_hidden').value = '0';
                    }
                    hideModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    // Reset pledge agreement when closing without submission
                    if (!pledgeCheckbox.checked) {
                        document.getElementById('pledge_agreement_hidden').value = '0';
                    }
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
            
                    // Add image preview functionality
        const idCardInput = document.getElementById('id_card_photo');
        const selfPhotoInput = document.getElementById('self_photo');
        
        if (idCardInput) {
            idCardInput.addEventListener('change', (e) => {
                if (validateImageFile(e.target)) {
                    previewImage(e, 'id_card_preview');
                } else {
                    e.target.value = ''; // Clear the input
                }
            });
        }
        
        if (selfPhotoInput) {
            selfPhotoInput.addEventListener('change', (e) => {
                if (validateImageFile(e.target)) {
                    previewImage(e, 'self_photo_preview');
                } else {
                    e.target.value = ''; // Clear the input
                }
            });
        }
    });

    // Validate image file function
    function validateImageFile(input) {
        const file = input.files[0];
        if (!file) return false;

        // Check file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return false;
        }

        // Check file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG, JPEG, and PNG files are allowed');
            return false;
        }

        // Check file extension
        const fileName = file.name.toLowerCase();
        const allowedExtensions = ['.jpg', '.jpeg', '.png'];
        const hasValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
        
        if (!hasValidExtension) {
            alert('Invalid file extension. Only .jpg, .jpeg, and .png are allowed');
            return false;
        }

        return true;
    }
        });
    </script>
</body>
</html>
