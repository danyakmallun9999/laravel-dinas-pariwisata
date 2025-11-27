<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Admin Panel · Laporan</p>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Export Laporan
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Data ke CSV (Excel)</h3>
                
                <form action="{{ route('admin.reports.export.csv') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tipe Data</label>
                        <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="all">Semua Data</option>
                            <option value="places">Titik Lokasi</option>
                            <option value="boundaries">Batas Wilayah</option>
                            <option value="infrastructures">Infrastruktur</option>
                            <option value="land_uses">Penggunaan Lahan</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full px-5 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700">
                        Download CSV
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Laporan HTML (untuk PDF)</h3>
                <p class="text-sm text-gray-600 mb-4">Generate laporan lengkap dengan statistik untuk dicetak sebagai PDF.</p>
                
                <a href="{{ route('admin.reports.export.html') }}" target="_blank" class="block w-full px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 text-center">
                    Buka Laporan HTML
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

