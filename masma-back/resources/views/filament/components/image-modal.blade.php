@props([
    'imageUrl' => '#',
    'title' => 'Image',
    'thumbnailSize' => 200,
    'thumbnailClass' => 'rounded-lg border border-gray-200 cursor-pointer hover:opacity-80 mx-auto',
])

<div x-data="{ modalOpen: false }" class="flex flex-col items-center">
    <!-- Thumbnail -->
    <img 
        src="{{ $imageUrl }}" 
        alt="{{ $title }}"
        style="max-height: {{ $thumbnailSize }}px; width: auto;"
        class="{{ $thumbnailClass }}"
        @click="modalOpen = true"
    />
    
    <!-- Full Screen Modal -->
    <div 
        x-show="modalOpen"
        x-cloak
        @keydown.escape.window="modalOpen = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-90 transition-opacity" @click="modalOpen = false"></div>
        
        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative max-w-7xl w-full bg-white rounded-lg shadow-2xl" @click.stop>
                <!-- Header -->
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900 truncate max-w-2xl">
                        {{ $title }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <a 
                            href="{{ $imageUrl }}" 
                            download
                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </a>
                        <button 
                            @click="modalOpen = false"
                            class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Close
                        </button>
                    </div>
                </div>
                
                <!-- Image Container with Zoom -->
                <div class="p-4" x-data="{ zoom: false }">
                    <div class="relative overflow-auto max-h-[70vh] flex justify-center bg-gray-100 rounded-lg">
                        <img 
                            src="{{ $imageUrl }}" 
                            alt="{{ $title }}"
                            :class="{ 'cursor-zoom-in': !zoom, 'cursor-zoom-out': zoom }"
                            :style="{ 
                                transform: zoom ? 'scale(2)' : 'scale(1)',
                                transition: 'transform 0.2s ease-in-out'
                            }"
                            class="max-w-full h-auto object-contain"
                            @click="zoom = !zoom"
                        />
                    </div>
                    
                    <!-- Zoom Controls -->
                    <div class="mt-4 flex justify-center space-x-4">
                        <button 
                            @click="zoom = !zoom"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors"
                        >
                            <span x-text="zoom ? 'Zoom Out' : 'Zoom In'"></span>
                        </button>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="p-4 border-t text-sm text-gray-500 flex justify-between">
                    <span>Click image to zoom • Use scroll to pan when zoomed</span>
                    <span>Original Size: Click Download to save</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>