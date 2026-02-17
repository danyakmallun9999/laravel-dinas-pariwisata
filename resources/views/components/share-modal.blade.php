@props(['url', 'title', 'text' => ''])

<div x-data="{ 
    shareOpen: false, 
    copied: false,
    url: '{{ $url }}',
    title: '{{ $title }}',
    toggle() {
        this.shareOpen = !this.shareOpen;
    },
    copyToClipboard() {
        navigator.clipboard.writeText(this.url).then(() => {
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        });
    }
}" class="relative inline-block">

    <!-- Trigger -->
    <div @click="toggle()" class="cursor-pointer">
        {{ $slot }}
    </div>

    <template x-teleport="body">

        <div 
            x-show="shareOpen" 
            x-cloak
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-300" 
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[11000] bg-black/60 backdrop-blur-md flex items-center justify-center"
        >
            <div 
                @click.outside="shareOpen = false"
                x-show="shareOpen"
                x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-500"
                x-transition:enter-start="opacity-0 scale-90" 
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300" 
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="relative w-[90%] max-w-sm bg-white dark:bg-slate-900 rounded-[2rem] p-8 shadow-2xl border border-white/20 dark:border-slate-800"
            >
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white font-display">Bagikan</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Pilih platform tujuan</p>
                </div>
                <button @click="shareOpen = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <!-- Social Grid -->
            <div class="grid grid-cols-4 gap-y-6 gap-x-4 mb-8">
                <!-- WhatsApp -->
                <a href="https://wa.me/?text={{ urlencode($title . ' ' . $url) }}" target="_blank" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-[#25D366]/10 flex items-center justify-center text-[#25D366] group-hover:scale-110 group-hover:bg-[#25D366] group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fa-brands fa-whatsapp text-2xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">WhatsApp</span>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}" target="_blank" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2] group-hover:scale-110 group-hover:bg-[#1877F2] group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fa-brands fa-facebook-f text-2xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Facebook</span>
                </a>

                <!-- Twitter / X -->
                <a href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}&text={{ urlencode($title) }}" target="_blank" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-black/5 dark:bg-white/10 flex items-center justify-center text-slate-900 dark:text-white group-hover:scale-110 group-hover:bg-black group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition-all duration-300 shadow-sm">
                        <i class="fa-brands fa-x-twitter text-2xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">X</span>
                </a>

                <!-- Instagram (Copy Link) -->
                <button @click="copyToClipboard()" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#FFD600] via-[#FF0069] to-[#D300C5] p-[1px] group-hover:scale-110 transition-all duration-300 shadow-sm">
                        <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[15px] group-hover:bg-transparent transition-colors flex items-center justify-center">
                             <i class="fa-brands fa-instagram text-2xl bg-clip-text text-transparent bg-gradient-to-tr from-[#FFD600] via-[#FF0069] to-[#D300C5] group-hover:text-white transition-colors"></i>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Stories</span>
                </button>

                <!-- TikTok (Copy Link) -->
                <button @click="copyToClipboard()" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-black/5 dark:bg-white/10 flex items-center justify-center text-slate-900 dark:text-white group-hover:scale-110 group-hover:bg-black group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition-all duration-300 shadow-sm">
                        <i class="fa-brands fa-tiktok text-2xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">TikTok</span>
                </button>

                 <!-- Email -->
                 <a href="mailto:?subject={{ urlencode($title) }}&body={{ urlencode($text . ' ' . $url) }}" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 group-hover:scale-110 group-hover:bg-slate-200 dark:group-hover:bg-slate-700 transition-all duration-300 shadow-sm">
                        <span class="material-symbols-outlined text-2xl">mail</span>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Email</span>
                </a>
            </div>

            <!-- Copy Link -->
            <div class="relative group">
                <input 
                    type="text" 
                    readonly 
                    value="{{ $url }}" 
                    class="w-full bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-2xl py-4 pl-5 pr-14 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-medium"
                >
                <button 
                    @click="copyToClipboard()" 
                    class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-xl hover:bg-white dark:hover:bg-slate-700 shadow-sm transition-all"
                    :class="copied ? 'text-green-500 bg-green-50 dark:bg-green-900/20' : 'text-slate-400'"
                >
                    <span class="material-symbols-outlined text-xl" x-text="copied ? 'check' : 'content_copy'">content_copy</span>
                </button>
            </div>
            
            <p x-show="copied" x-transition class="text-center text-green-500 text-sm font-bold mt-4 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                Link tersalin ke clipboard!
            </p>
        </div>
        </div>
    </template>
</div>
