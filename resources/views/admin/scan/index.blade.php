<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8  lg:py-8 w-full max-w-9xl mx-auto" x-data="qrScanner()">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Admin Gatekeeper 📷</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <!-- Status Indicator -->
                <div class="flex items-center gap-2 px-4 py-1.5 bg-white/80 border border-slate-200 rounded-full backdrop-blur-sm">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Camera Ready</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Scanner Section -->
            <div class="md:col-span-2 space-y-4">
                <!-- Camera Viewfinder -->
                <div class="bg-slate-900 p-1 rounded-[2rem] border border-slate-800 relative overflow-hidden group">
                    <!-- Scanner Container -->
                    <div id="reader" class="w-full rounded-[1.8rem] overflow-hidden bg-black h-[300px] md:h-[500px] relative z-0"></div>

                    <!-- Camera Controls (Overlay) -->
                    <div class="absolute top-6 right-6 z-30">
                        <button @click="toggleScanner" 
                                class="flex items-center gap-2 px-5 py-2.5 rounded-full font-bold text-sm transition-all backdrop-blur-md hover:scale-105 active:scale-95"
                                :class="isScanning ? 'bg-red-500/90 text-white hover:bg-red-600 ring-1 ring-white/20' : 'bg-emerald-500/90 text-white hover:bg-emerald-600 ring-1 ring-white/20'">
                            <i class="fa-solid" :class="isScanning ? 'fa-video-slash' : 'fa-video'"></i>
                            <span x-text="isScanning ? 'Stop Kamera' : 'Mulai Kamera'"></span>
                        </button>
                    </div>

                    <!-- Scan Laser Animation -->
                    <div x-show="isScanning" x-transition.opacity class="absolute inset-0 pointer-events-none z-10 overflow-hidden rounded-[1.8rem]">
                        <div class="scan-laser"></div>
                        <div class="scan-grid"></div>
                    </div>
                    
                    <!-- Scanner Frame Guidelines -->
                    <div x-show="isScanning" x-transition.opacity class="absolute inset-0 pointer-events-none flex items-center justify-center z-20">
                        <div class="relative w-64 h-64 border border-white/20 rounded-3xl">
                            <!-- Corners -->
                            <div class="absolute top-0 left-0 w-12 h-12 border-t-2 border-l-2 border-emerald-400 rounded-tl-3xl -mt-px -ml-px"></div>
                            <div class="absolute top-0 right-0 w-12 h-12 border-t-2 border-r-2 border-emerald-400 rounded-tr-3xl -mt-px -mr-px"></div>
                            <div class="absolute bottom-0 left-0 w-12 h-12 border-b-2 border-l-2 border-emerald-400 rounded-bl-3xl -mb-px -ml-px"></div>
                            <div class="absolute bottom-0 right-0 w-12 h-12 border-b-2 border-r-2 border-emerald-400 rounded-br-3xl -mb-px -mr-px"></div>
                            
                            <!-- Scanning Text -->
                            <div class="absolute -bottom-12 left-0 right-0 text-center">
                                <span class="bg-black/40 text-white/90 text-[10px] uppercase tracking-widest px-4 py-1.5 rounded-full backdrop-blur-md border border-white/10">
                                    Arahkan QR Code
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message Overlay -->
                    <div x-show="cameraError" x-cloak 
                         class="absolute inset-0 flex items-center justify-center bg-slate-900/95 z-30 rounded-[1.8rem] p-8 text-center backdrop-blur-sm">
                        <div class="max-w-xs">
                            <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                                <i class="fa-solid fa-video-slash text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Akses Kamera Bermasalah</h3>
                            <p class="text-slate-400 text-xs mb-6 leading-relaxed" x-text="cameraError"></p>
                            <button @click="startScanner" class="w-full bg-white text-slate-900 font-bold rounded-xl px-4 py-2.5 text-xs hover:bg-slate-50 transition-colors">
                                <i class="fa-solid fa-rotate-right mr-2"></i> Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manual Input Fallback -->
                <div class="bg-white p-1 rounded-[2rem] border border-slate-200">
                    <div class="p-5 rounded-[1.8rem] border border-slate-100 bg-slate-50/50">
                        <h3 class="text-xs font-bold text-slate-500 mb-4 flex items-center gap-2 uppercase tracking-widest pl-1">
                            <i class="fa-solid fa-keyboard text-indigo-500"></i> Manual Input
                        </h3>
                        <div class="flex flex-col md:flex-row gap-3">
                            <form @submit.prevent="handleManualInput" class="flex-1 flex gap-2">
                                <div class="relative flex-1">
                                    <i class="fa-solid fa-barcode absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                                    <input type="text" x-model="manualInput" 
                                        class="w-full pl-10 pr-4 py-3 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-white focus:bg-white transition-all font-mono text-sm shadow-sm" 
                                        placeholder='Ketikan Kode Order...'>
                                </div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl px-5 font-bold shadow-sm shadow-indigo-200 transition-all active:scale-95">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </form>
                            
                            <!-- Upload Button -->
                            <div class="relative w-full md:w-auto">
                                <input type="file" id="qr-input-file" accept="image/*" class="hidden" @change="handleFileUpload">
                                <label for="qr-input-file" class="bg-white border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 text-slate-600 hover:text-indigo-600 rounded-2xl px-5 py-3 md:py-0 cursor-pointer flex items-center justify-center gap-2 w-full md:w-auto h-full font-semibold transition-all group shadow-sm text-sm">
                                    <i class="fa-solid fa-image group-hover:scale-110 transition-transform text-slate-400 group-hover:text-indigo-500"></i> <span class="whitespace-nowrap">Upload QR</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Scans Sidebar -->
            <div class="md:col-span-1">
                <div class="bg-white p-1 rounded-[2rem] border border-slate-200 h-full">
                    <div class="p-5 rounded-[1.8rem] border border-slate-100 bg-slate-50/50 h-full">
                        <h3 class="text-xs font-bold text-slate-500 mb-6 flex items-center gap-2 uppercase tracking-widest pl-1">
                            <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i> Riwayat Scan
                        </h3>

                    <div class="space-y-4 relative">
                        <!-- Timeline line -->
                        <div class="absolute left-[19px] top-2 bottom-2 w-0.5 bg-slate-100 z-0"></div>

                        <template x-if="recentScans.length === 0">
                            <div class="text-center py-12 text-slate-400">
                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-history text-2xl text-slate-300"></i>
                                </div>
                                <p class="text-sm">Belum ada riwayat scan</p>
                            </div>
                        </template>
                        <template x-for="(scan, index) in recentScans" :key="scan.timestamp">
                            <div class="relative z-10 flex items-start gap-4 group" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm border-2 transition-colors"
                                    :class="scan.valid ? 'bg-emerald-50 border-emerald-100 text-emerald-500' : 'bg-red-50 border-red-100 text-red-500'">
                                    <i class="fa-solid text-lg" :class="scan.valid ? 'fa-check' : 'fa-xmark'"></i>
                                </div>
                                <div class="flex-1 min-w-0 bg-slate-50 rounded-2xl p-3 border border-slate-100 group-hover:border-indigo-100 group-hover:bg-indigo-50/30 transition-colors">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-sm font-bold text-slate-800 truncate pr-2" x-text="scan.ticketName || 'Unknown Ticket'"></p>
                                        <span class="text-[10px] font-mono text-slate-400 bg-white px-1.5 py-0.5 rounded-md border border-slate-100" x-text="scan.time"></span>
                                    </div>
                                    <p class="text-xs font-mono text-slate-500" x-text="scan.orderNumber"></p>
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
        /* Laser Animation */
        .scan-laser {
            position: absolute;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, #ef4444, transparent);
            box-shadow: 0 0 15px #ef4444, 0 0 30px #ef4444;
            top: 0%;
            animation: scanning 2.5s ease-in-out infinite;
            z-index: 15;
            opacity: 0.8;
        }

        .scan-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 10;
        }

        @keyframes scanning {
            0% { top: 0%; opacity: 0; }
            15% { opacity: 1; }
            85% { opacity: 1; }
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
            border-radius: 1rem; /* rounded-2xl */
            transform: scaleX(-1) !important;
        }
    </style>
</x-app-layout>
