<div class="p-6">
    <div class="flex flex-col items-center justify-center">
        @if($record->qr_code_url)
            <img src="{{ $record->qr_code_url }}" 
                 alt="QR Code" 
                 class="w-64 h-64 border border-gray-300 rounded-lg p-4 mb-4">
            <div class="space-y-2">
                <a href="{{ $record->qr_code_download_url }}" 
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                    Download QR Code
                </a>
                <button onclick="window.print()"
                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition ml-2">
                    <x-heroicon-o-printer class="w-4 h-4 mr-2" />
                    Print
                </button>
            </div>
        @else
            <p class="text-gray-500">No QR code generated for this visitor.</p>
        @endif
    </div>
</div>