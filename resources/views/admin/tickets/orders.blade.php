<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500">E-Tiket</p>
                <h2 class="font-semibold text-xl md:text-2xl text-gray-800 leading-tight">
                    Pesanan Masuk
                </h2>
            </div>
            <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center gap-2 px-3 py-2 md:px-4 md:py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm" wire:navigate>
                <i class="fa-solid fa-arrow-left text-xs md:text-sm"></i>
                <span class="hidden md:inline">Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        @livewire('ticket-orders')
    </div>

    <!-- QR Code Modal (Alpine.js controlled) -->
    <style> [x-cloak] { display: none !important; } </style>
    <div x-data="{
            open: false,
            orderNumber: '',
            init() {
                // Listen for event from Livewire or other components
                window.addEventListener('open-qr', (e) => {
                    this.show(e.detail);
                });
            },
            show(number) {
                this.orderNumber = number;
                this.open = true;
                
                // Generate QR after modal is visible
                setTimeout(() => {
                    const container = document.getElementById('qrcode-container');
                    if(container && typeof QRCode !== 'undefined') {
                        container.innerHTML = '';
                        new QRCode(container, {
                            text: number,
                            width: 256,
                            height: 256,
                            colorDark : '#000000',
                            colorLight : '#ffffff',
                            correctLevel : QRCode.CorrectLevel.H
                        });
                        
                        // Fix style
                        const canvas = container.querySelector('canvas');
                        if(canvas) {
                            canvas.style.width = '100%';
                            canvas.style.height = '100%';
                        }
                    } else if (!typeof QRCode === 'undefined') {
                        console.error('QRCode library not loaded');
                        if(container) container.innerHTML = '<p class=\'text-red-500\'>Error: QR Library not loaded</p>';
                    }
                }, 50);
            },
            download() {
                const sourceCanvas = document.querySelector('#qrcode-container canvas');
                if (!sourceCanvas) return;

                const padding = 20;
                const size = sourceCanvas.width;
                const newSize = size + (padding * 2);

                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = newSize;
                finalCanvas.height = newSize;
                const ctx = finalCanvas.getContext('2d');

                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, newSize, newSize);
                ctx.drawImage(sourceCanvas, padding, padding);

                const url = finalCanvas.toDataURL('image/jpeg', 1.0);
                const link = document.createElement('a');
                link.download = `ticket-qr-${this.orderNumber}.jpg`;
                link.href = url;
                link.click();
            }
        }"
        x-show="open" 
        x-cloak
        class="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center p-4 backdrop-blur-sm"
        @keydown.escape.window="open = false">
        
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl transform transition-all"
             @click.away="open = false">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">QR Code Tiket</h3>
                <button @click="open = false" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="text-center mb-6">
                <div class="bg-gray-50 rounded-xl p-2 inline-block mb-4">
                    <span class="font-mono font-bold text-gray-700 text-lg tracking-wider" x-text="orderNumber"></span>
                </div>
                
                <div id="qrcode-container" class="mx-auto bg-white p-2 rounded-lg border border-gray-100 shadow-sm" style="width: 200px; height: 200px;"></div>
                
                <p class="text-sm text-gray-400 mt-4">Scan QR code ini di loket untuk verifikasi</p>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <button @click="open = false" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
                <button @click="download()" class="px-4 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-download"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- QRCode.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</x-app-layout>
