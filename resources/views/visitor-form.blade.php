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
        <form>
            <div class="mb-4">
                <label class="block mb-1 font-medium">NIK</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter NIK">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Foto KTP</label>
                <input type="file" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400">
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
                <label class="block mb-1 font-medium">Dept Purpose</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter department purpose">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Section Purpose</label>
                <input type="text" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Enter section purpose">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">Foto Diri</label>
                <input type="file" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400">
            </div>
            <div class="mb-6 flex gap-4">
                <div class="w-1/2">
                    <label class="block mb-1 font-medium">Visit Date</label>
                    <input type="date" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400">
                </div>
                <div class="w-1/2">
                    <label class="block mb-1 font-medium">Visit Time <span class="text-xs text-gray-500">(24h)</span></label>
                    <input type="time" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" step="60" placeholder="HH:MM">
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Submit</button>
        </form>
    </div>
</body>
</html>