<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Admin Panel · Import Data</p>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Import Data dari QGIS
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload File GeoJSON</h3>
                
                <form action="{{ route('admin.import.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tipe Data</label>
                        <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Pilih tipe data</option>
                            <option value="boundary">Batas Wilayah (Polygon)</option>
                            <option value="infrastructure">Infrastruktur (LineString)</option>
                            <option value="land_use">Penggunaan Lahan (Polygon)</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File GeoJSON</label>
                        <input type="file" name="file" accept=".json,.geojson" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        <p class="text-xs text-gray-500 mt-2">Format: GeoJSON (.json atau .geojson), maksimal 10MB.</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-blue-900 mb-2">Petunjuk:</p>
                        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                            <li>Export data dari QGIS dalam format GeoJSON</li>
                            <li>Pastikan geometry type sesuai dengan tipe data yang dipilih</li>
                            <li>File harus berformat FeatureCollection</li>
                            <li>Properties harus mengandung field 'name' minimal</li>
                        </ul>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                            Import Data
                        </button>
                    </div>
                </form>
            </div>

            @if($imports->count() > 0)
                <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Import</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($imports as $import)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $import->filename }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $import->type }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $import->records_count }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $import->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $import->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $import->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $imports->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

