<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="qrScanner()">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Admin Gatekeeper 📷</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <!-- Status Indicator -->
                <div class="flex items-center gap-2 px-3 py-1 bg-white border border-slate-200 rounded-full shadow-sm">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-xs font-medium text-slate-500">Camera Ready</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Scanner Section -->
            <div class="md:col-span-2 space-y-4">
                <!-- Camera Viewfinder -->
                <div class="bg-white p-4 rounded-2xl shadow-lg border border-slate-200 relative overflow-hidden group">
                    <!-- Scanner Container -->
                    <div id="reader" class="w-full rounded-xl overflow-hidden bg-slate-100 min-h-[300px] md:min-h-[500px] relative z-0"></div>

                    <!-- Camera Controls (Overlay) -->
                    <div class="absolute top-4 right-4 z-20">
                        <button @click="toggleScanner" 
                                class="flex items-center gap-2 px-4 py-2 rounded-full font-medium transition-all shadow-lg backdrop-blur-sm"
                                :class="isScanning ? 'bg-red-500/90 text-white hover:bg-red-600' : 'bg-emerald-500/90 text-white hover:bg-emerald-600'">
                            <i class="fa-solid" :class="isScanning ? 'fa-video-slash' : 'fa-video'"></i>
                            <span x-text="isScanning ? 'Matikan Kamera' : 'Hidupkan Kamera'"></span>
                        </button>
                    </div>

                    <!-- Scanner Laser Overlay (Removed) -->
                    <!-- The scanner works on the full video feed -->
                    
                    <!-- Error Message Overlay -->
                    <div x-show="cameraError" x-cloak 
                         class="absolute inset-0 flex items-center justify-center bg-slate-100 z-50 rounded-xl p-6 text-center">
                        <div>
                            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-camera-slash text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-2">Akses Kamera Bermasalah</h3>
                            <p class="text-slate-500 text-sm mb-4" x-text="cameraError"></p>
                            <button @click="startScanner" class="btn bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg px-4 py-2 text-sm">
                                <i class="fa-solid fa-rotate-right mr-2"></i> Coba Lagi
                            </button>
                        </div>
                    </div>
                    
                    <!-- Scan Guidelines Overlay -->
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center translate-y-8 opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <div class="w-64 h-64 border-2 border-white/50 rounded-3xl relative">
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-emerald-400 -mt-1 -ml-1 rounded-tl-xl"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-emerald-400 -mt-1 -mr-1 rounded-tr-xl"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-emerald-400 -mb-1 -ml-1 rounded-bl-xl"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-emerald-400 -mb-1 -mr-1 rounded-br-xl"></div>
                        </div>
                    </div>
                </div>

                <!-- Manual Input Fallback -->
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-keyboard text-slate-400"></i> Manual Input / Upload
                    </h3>
                    <div class="flex flex-col md:flex-row gap-3">
                        <form @submit.prevent="handleManualInput" class="flex-1 flex gap-2">
                            <input type="text" x-model="manualInput" 
                                class="form-input w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" 
                                placeholder='Scan manual atau ketik kode...'>
                            <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl px-4">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                        
                        <!-- Upload Button -->
                        <div class="relative">
                            <input type="file" id="qr-input-file" accept="image/*" class="hidden" @change="handleFileUpload">
                            <label for="qr-input-file" class="btn bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl px-4 cursor-pointer flex items-center gap-2 h-full">
                                <i class="fa-solid fa-image"></i> Upload QR
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Scans Sidebar -->
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 h-full">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Riwayat Scan Baru
                    </h3>
                    <div class="space-y-3">
                        <template x-if="recentScans.length === 0">
                            <div class="text-center py-8 text-slate-400 text-sm">
                                Belum ada scan
                            </div>
                        </template>
                        <template x-for="scan in recentScans" :key="scan.timestamp">
                            <div class="p-3 rounded-xl border border-slate-100 flex items-start gap-3"
                                :class="scan.valid ? 'bg-emerald-50/50' : 'bg-red-50/50'">
                                <div class="mt-1">
                                    <i class="fa-solid" :class="scan.valid ? 'fa-circle-check text-emerald-500' : 'fa-circle-xmark text-red-500'"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800" x-text="scan.ticketName || 'Unknown Ticket'"></p>
                                    <p class="text-xs text-slate-500" x-text="scan.orderNumber"></p>
                                    <p class="text-[10px] text-slate-400 mt-1" x-text="scan.time"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- CUSTOM VALIDATION MODAL -->
        <div x-show="showModal" 
             style="display: none;"
             class="fixed inset-0 flex items-center justify-center px-4 sm:px-6 z-[9999]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

            <!-- Modal Panel -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all w-full max-w-lg relative z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Status Header -->
                <div class="px-6 py-8 text-center relative overflow-hidden"
                     :class="isValid ? 'bg-emerald-500' : 'bg-rose-500'">
                    
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path>
                        </svg>
                    </div>

                    <!-- Icon -->
                    <div class="relative z-10 w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm shadow-inner">
                        <i class="fa-solid text-5xl text-white" 
                           :class="isValid ? 'fa-check' : 'fa-xmark'"></i>
                    </div>

                    <h2 class="relative z-10 text-3xl font-bold text-white mb-1" x-text="isValid ? 'TIKET VALID' : 'TIKET DITOLAK'"></h2>
                    <p class="relative z-10 text-white/90 font-medium text-lg" x-text="statusMessage"></p>
                </div>

                <!-- Ticket Details -->
                <div class="p-6 md:p-8 space-y-4" x-show="isValid">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Pengunjung</p>
                            <p class="font-bold text-slate-800 truncate" x-text="scanData.customer_name"></p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-right">
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Jumlah</p>
                            <p class="font-bold text-slate-800"><span x-text="scanData.quantity"></span> Orang</p>
                        </div>
                    </div>
                    
                    <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                        <p class="text-xs text-indigo-500 uppercase tracking-wide mb-1">Tiket Masuk</p>
                        <p class="font-bold text-indigo-900 text-lg" x-text="scanData.ticket_name"></p>
                        <p class="text-sm text-indigo-700" x-text="scanData.place_name"></p>
                    </div>

                    <div class="text-center pt-2">
                        <p class="text-xs text-slate-400">Order ID: <span x-text="scanData.order_number" class="font-mono"></span></p>
                        <p class="text-xs text-slate-400">Check-in: <span x-text="scanData.check_in_time" class="font-mono"></span></p>
                    </div>
                </div>

                <!-- Footer / Action -->
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-center">
                    <button @click="closeModal" 
                            class="w-full py-4 text-white font-bold rounded-xl text-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 active:scale-95"
                            :class="isValid ? 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/30' : 'bg-rose-500 hover:bg-rose-600 shadow-rose-500/30'">
                        Scan Selanjutnya <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                    
                    <!-- Auto close timer bar -->
                    <div class="absolute bottom-0 left-0 h-1 bg-black/10 transition-all duration-[3000ms] ease-linear w-full"
                         :style="showModal ? 'width: 0%' : 'width: 100%'"></div>
                </div>
            </div>
        </div>

        <!-- Audio Elements -->
        <audio id="scan-success" src="https://cdn.freesound.org/previews/341/341695_5858296-lq.mp3"></audio>
        <audio id="scan-error" src="https://cdn.freesound.org/previews/456/456561_6142149-lq.mp3"></audio>

    </div>

    <!-- Load Modular Scan Script -->


    <!-- Note: html5-qrcode is now imported in the JS module -->
    
    <style>
        @keyframes scan {
            0% { top: 0%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        
        /* Force hide html5-qrcode overlays */
        #reader__scan_region {
            display: none !important;
        }
        #reader div[style*="border"] {
            /* This targets the border box added by library sometimes */
            display: none !important;
        }
        
        /* Ensure video covers the container */
        #reader video {
            object-fit: cover !important;
            width: 100% !important;
            height: 100% !important;
            border-radius: 0.75rem; /* rounded-xl */
        }
    </style>
</x-app-layout>
