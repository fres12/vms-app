<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor Registration</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 dark:bg-neutral-900 min-h-screen flex items-center justify-center">
    <div class="max-w-xl w-full bg-white dark:bg-neutral-900 p-8 rounded-xl shadow">
        <h2 class="text-2xl font-bold mb-6 text-center">Visitor Registration</h2>
        
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
            <div class="mb-4">
                <label class="block mb-1 font-medium">Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" class="w-full border rounded px-3 py-2 @error('full_name') border-red-500 @enderror" required>
                @error('full_name')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">National ID Number (NIK)</label>
                <input type="text" name="nik" id="nikInput" inputmode="numeric" pattern="[0-9]*" value="{{ old('nik') }}" class="w-full border rounded px-3 py-2 @error('nik') border-red-500 @enderror" required>
                <span id="nikError" class="text-red-600 text-xs hidden">Only numbers are allowed.</span>
                @error('nik')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">ID Card Photo</label>
                <input type="file" name="id_card_photo" accept="image/*" class="w-full border rounded px-3 py-2" 
                       onchange="previewImage(event, 'id_card_preview')" required>
                <img id="id_card_preview" class="mt-2 rounded max-h-40 hidden">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Self Photo</label>
                <input type="file" name="self_photo" accept="image/*" class="w-full border rounded px-3 py-2" 
                       onchange="previewImage(event, 'self_photo_preview')" required>
                <img id="self_photo_preview" class="mt-2 rounded max-h-40 hidden">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Company</label>
                <input type="text" name="company" value="{{ old('company') }}" class="w-full border rounded px-3 py-2 @error('company') border-red-500 @enderror">
                @error('company')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Phone</label>
                <input type="text" name="phone" id="phoneInput" inputmode="numeric" pattern="[0-9]*" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2 @error('phone') border-red-500 @enderror">
                <span id="phoneError" class="text-red-600 text-xs hidden">Only numbers are allowed.</span>
                @error('phone')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Department Purpose</label>
                <select name="department_purpose" class="w-full border rounded px-3 py-2 @error('department_purpose') border-red-500 @enderror" required>
                    <option value="">Select Department</option>
                    <option value="Dept A" {{ old('department_purpose') == 'Dept A' ? 'selected' : '' }}>Dept A</option>
                    <option value="Dept B" {{ old('department_purpose') == 'Dept B' ? 'selected' : '' }}>Dept B</option>
                    <option value="Dept C" {{ old('department_purpose') == 'Dept C' ? 'selected' : '' }}>Dept C</option>
                </select>
                @error('department_purpose')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Section Purpose</label>
                <input type="text" name="section_purpose" value="{{ old('section_purpose') }}" class="w-full border rounded px-3 py-2 @error('section_purpose') border-red-500 @enderror">
                @error('section_purpose')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Visit Date</label>
                <input type="date" name="visit_date" value="{{ old('visit_date') }}" class="w-full border rounded px-3 py-2 @error('visit_date') border-red-500 @enderror" required>
                @error('visit_date')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Visit Time</label>
                <input type="time" name="visit_time" value="{{ old('visit_time') }}" class="w-full border rounded px-3 py-2 @error('visit_time') border-red-500 @enderror" required>
                @error('visit_time')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" id="pledgeCheckbox" name="pledge_agreement" required class="form-checkbox mr-2">
                    <span>I have read and agree with to the the HMMI <a href="/visitor-pledge" target="_blank" class="text-blue-600 underline hover:text-blue-800">visitor pledge</a></span>
                </label>
            </div>
            
            <button id="submitBtn" type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition opacity-50 cursor-not-allowed" disabled>Submit</button>
        </form>
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
    </script>
</body>
</html>
