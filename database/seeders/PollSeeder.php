<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PollSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('polls')->updateOrInsert(
            ['id' => 1],
            [
                'question' => 'आप किस तरह की स्थानीय खबरें सबसे ज़्यादा पढ़ना पसंद करते हैं?',
                'options' => json_encode([
                    'समस्याएँ और शिकायतें',
                    'सरकारी योजनाएँ',
                    'खेल और मनोरंजन',
                    'शिक्षा और स्वास्थ्य',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
