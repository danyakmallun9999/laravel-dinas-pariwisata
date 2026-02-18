<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::all();

        foreach ($tickets as $ticket) {
            // Simple translation logic for demo purposes
            $nameEn = $this->translateName($ticket->name);
            $descriptionEn = $ticket->description ? 'Experience the beauty of ' . $nameEn . '. ' . $ticket->description : null;
            $termsEn = $ticket->terms_conditions ? 'Valid for one person. Non-refundable. ' . $ticket->terms_conditions : null;

            $ticket->update([
                'name_en' => $nameEn,
                'description_en' => $descriptionEn,
                'terms_conditions_en' => $termsEn,
            ]);
        }
    }

    private function translateName($name)
    {
        // Basic mapping for common terms
        $translations = [
            'Tiket Masuk' => 'Entrance Ticket',
            'Dewasa' => 'Adult',
            'Anak' => 'Child',
            'Anak-anak' => 'Child',
            'Wisatawan Asing' => 'Foreigner',
            'Parkir' => 'Parking',
            'Motor' => 'Motorcycle',
            'Mobil' => 'Car',
            'Bus' => 'Bus',
            'Camping' => 'Camping',
            'Sewa' => 'Rent',
            'Perahu' => 'Boat',
            'Pelampung' => 'Life Jacket',
            'Ban' => 'Tube',
            'Spot Foto' => 'Photo Spot',
            'Wahana' => 'Attraction',
        ];

        $translated = $name;
        foreach ($translations as $id => $en) {
            $translated = str_replace($id, $en, $translated);
        }
        
        return $translated;
    }
}
