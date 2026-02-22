<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Livewire Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">JavaScript Test</h1>
    
    <div class="space-y-4">
        <button onclick="alert('Basic onclick works!')" class="px-4 py-2 bg-green-600 text-white rounded">
            Test Basic onclick
        </button>
        
        <div id="livewire-test">
            <p>Livewire should appear below:</p>
        </div>
        
        @livewire('test-button')
    </div>
    
    <script>
        console.log('Page loaded successfully');
        console.log('Livewire object:', typeof Livewire);
        console.log('Alpine object:', typeof Alpine);
    </script>
    
    @livewireScripts
</body>
</html>