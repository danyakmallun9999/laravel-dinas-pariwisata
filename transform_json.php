<?php

$file = __DIR__ . '/public/data_wisata_jepara.json';
$content = file_get_contents($file);
$data = json_decode($content, true);

if (!$data) {
    die("Error decoding JSON");
}

foreach ($data['data_wisata'] as &$item) {
    $lokasi = $item['lokasi'];
    $kecamatan = null;
    $newLokasiParts = [];
    
    // Split by comma to find the part with "Kecamatan"
    $parts = explode(',', $lokasi);
    
    foreach ($parts as $part) {
        $part = trim($part);
        if (stripos($part, 'Kecamatan') === 0) {
            $kecamatan = $part;
        } else {
            $newLokasiParts[] = $part;
        }
    }
    
    // If we found a kecamatan via comma splitting, use it
    if ($kecamatan) {
        $item['kecamatan'] = $kecamatan;
        $item['lokasi'] = implode(', ', $newLokasiParts);
    } else {
        // Fallback: Try regex if comma separation isn't clean but "Kecamatan" exists
        if (preg_match('/(Kecamatan\s+[A-Za-z\s]+)/i', $lokasi, $matches)) {
            $item['kecamatan'] = trim($matches[1]);
            // Remove the kecamatan part from lokasi
            $item['lokasi'] = trim(str_replace($matches[1], '', $lokasi));
            // Clean up potentially double commas
            $item['lokasi'] = trim(preg_replace('/,\s*,/', ',', $item['lokasi']), ', ');
        } else {
            // Default to empty or null if not found
            $item['kecamatan'] = "";
        }
    }
}

// Re-encode ensuring pretty print and unescaped slashes/unicode to match original format
$newContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

file_put_contents($file, $newContent);

echo "Successfully transformed data_wisata_jepara.json\n";
