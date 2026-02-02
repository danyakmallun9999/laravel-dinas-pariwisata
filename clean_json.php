<?php

$jsonFile = 'public/data_wisata_jepara.json';
$data = json_decode(file_get_contents($jsonFile), true);

function parseRideLine($line) {
    if (empty($line)) return null;
    $cleanLine = trim(preg_replace('/^(\d+\.|>)\s*/', '', $line));
    $rpPos = stripos($cleanLine, 'Rp');
    
    if ($rpPos !== false) {
        $namePart = substr($cleanLine, 0, $rpPos);
        $pricePart = substr($cleanLine, $rpPos);
        $name = trim($namePart, " \t\n\r\0\x0B-:");
        $price = trim($pricePart);
        
        // Fix for "Rp ..." placeholders
        if (preg_match('/^Rp[\s\.]+$/', $price)) {
            $price = 'Hubungi Pengelola';
        }
    } else {
        $name = trim($cleanLine, " \t\n\r\0\x0B-:");
        $price = null;
    }
    
    return !empty($name) ? ['name' => $name, 'price' => $price] : null;
}

function cleanRides($input) {
    if (!$input || $input === '-') return [];
    
    $rides = [];
    $lines = [];

    if (is_array($input)) {
        foreach ($input as $item) {
            $line = $item['name'];
            if (!empty($item['price'])) {
                $line .= ' - ' . $item['price'];
            }
            $lines[] = $line;
        }
    } else {
        $lines = explode("\n", $input);
    }
    
    foreach ($lines as $line) {
        $parsed = parseRideLine($line);
        if ($parsed) {
            $rides[] = $parsed;
        }
    }
    return $rides;
}

function cleanFacilities($input) {
    if (is_array($input)) return $input;
    if (!$input || $input === '-') return [];
    
    $lines = explode("\n", $input);
    $facilities = [];
    foreach ($lines as $line) {
        $clean = trim(preg_replace('/^\d+\.\s*/', '', $line)); 
        if (!empty($clean)) {
            $facilities[] = $clean;
        }
    }
    return $facilities;
}

foreach ($data['data_wisata'] as &$place) {
    $place['wahana_list'] = cleanRides($place['wahana']);
    $place['fasilitas_list'] = cleanFacilities($place['fasilitas']);
    
    $place['wahana'] = $place['wahana_list'];
    $place['fasilitas'] = $place['fasilitas_list'];
    
    unset($place['wahana_list']);
    unset($place['fasilitas_list']);
}

file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "JSON cleaned and saved to $jsonFile\n";
