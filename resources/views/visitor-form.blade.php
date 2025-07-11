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

        <form action="{{ route('visitors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block mb-1 font-medium">Full Name</label>
                <input type="text" name="full_name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">National ID Number (NIK)</label>
                <input type="text" name="nik" id="nikInput" inputmode="numeric" pattern="[0-9]*" class="w-full border rounded px-3 py-2" required>
                <span id="nikError" class="text-red-600 text-xs hidden">Only numbers are allowed.</span>
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
                <input type="text" name="company" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Phone</label>
                <input type="text" name="phone" id="phoneInput" inputmode="numeric" pattern="[0-9]*" class="w-full border rounded px-3 py-2">
                <span id="phoneError" class="text-red-600 text-xs hidden">Only numbers are allowed.</span>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Department Purpose</label>
                <select name="department_purpose" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select Department</option>
                    <option value="Dept A">Dept A</option>
                    <option value="Dept B">Dept B</option>
                    <option value="Dept C">Dept C</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Section Purpose</label>
                <input type="text" name="section_purpose" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Visit Date</label>
                <input type="date" name="visit_date" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Visit Time</label>
                <input type="time" name="visit_time" class="w-full border rounded px-3 py-2" required>
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

        // Realtime validation for Visit Year (4 digit only)
        document.addEventListener('DOMContentLoaded', function() {
            const visitYearInput = document.getElementById('visitYearInput');
            const visitYearError = document.getElementById('visitYearError');
            visitYearInput.addEventListener('input', function() {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                // Show error if not exactly 4 digits
                if (this.value.length !== 4) {
                    visitYearError.classList.remove('hidden');
                } else {
                    visitYearError.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
