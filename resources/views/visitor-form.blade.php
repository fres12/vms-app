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
        <form wire:submit.prevent="submit">
            <div class="mb-4">
                <label class="block mb-1 font-medium">National Identification Number</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter NIK">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">National ID card photo</label>
                <input type="file" accept="image/*" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" id="id_card_photo" onchange="previewImage(event, 'id_card_photo_preview')">
                <img id="id_card_photo_preview" class="mt-2 rounded max-h-40 hidden" alt="Preview ID Card Photo">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Full Name</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter full name">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Company</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter company">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Phone</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter phone number">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Department Purpose</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter department purpose">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Section Purpose</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter section purpose">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Self Photo</label>
                <input type="file" wire:model="self_photo" accept="image/*" class="w-full border rounded px-3 py-2">
                @if ($self_photo)
                    <img src="{{ $self_photo->temporaryUrl() }}" class="mt-2 rounded max-h-40" alt="Preview Self Photo">
                @endif
                @error('self_photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-6 flex gap-4">
                <div class="w-1/2">
                    <label class="block mb-1 font-medium">Visit Date</label>
                    <input type="date" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400">
                </div>
                <div class="w-1/2">
                    <label class="block mb-1 font-medium">Visit Time</label>
                    <input type="time" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" step="60" placeholder="HH:MM">
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Submit</button>
        </form>
    </div>
</body>
</html>
