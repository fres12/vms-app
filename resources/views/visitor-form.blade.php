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
        <form action="{{ route('visitors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block mb-1 font-medium">Full Name</label>
                <input type="text" name="full_name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">National Identification Number</label>
                <input type="text" name="nik" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">National ID Card Photo</label>
                <input type="file" name="id_card_photo" accept="image/*" class="w-full border rounded px-3 py-2" onchange="previewImage(event, 'id_card_photo_preview')">
                <img id="id_card_photo_preview" class="mt-2 rounded max-h-40 hidden" alt="Preview ID Card Photo">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Self Photo</label>
                <input type="file" name="self_photo" accept="image/*" class="w-full border rounded px-3 py-2" onchange="previewImage(event, 'self_photo_preview')">
                <img id="self_photo_preview" class="mt-2 rounded max-h-40 hidden" alt="Preview Self Photo">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Company</label>
                <input type="text" name="company" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Phone</label>
                <input type="text" name="phone" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Department Purpose</label>
                <input type="text" name="department_purpose" class="w-full border rounded px-3 py-2">
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
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Submit</button>
        </form>
    </div>
    <script>
        function previewImage(event, previewElementId) {
            const input = event.target;
            const preview = document.getElementById(previewElementId);
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
    </script>
</body>
</html>
