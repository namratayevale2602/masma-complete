<div class="space-y-4">
    @if($url)
        <div class="flex flex-col items-center">
            <img src="{{ $url }}" alt="QR Code" class="w-48 h-48 border border-gray-300 rounded-lg p-2">
            @if($downloadUrl)
                <a href="{{ $downloadUrl }}" target="_blank" 
                   class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
                    Download QR Code
                </a>
            @endif
        </div>
    @else
        <p class="text-gray-500 text-sm">No QR code available</p>
    @endif
</div>