<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PubgItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('pubg_files.txt');
        if (!file_exists($filePath)) {
            return;
        }

        $files = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $storagePath = public_path('storage/items/pubg');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        foreach ($files as $sourcePath) {
            if (!file_exists($sourcePath)) {
                continue;
            }

            $filename = basename($sourcePath);
            $destinationPath = $storagePath . '/' . $filename;

            // Copy file to public storage
            copy($sourcePath, $destinationPath);

            // Extract name from filename (e.g., Item_Weapon_AK47_C.png -> AK47)
            $name = str_replace(['Item_Weapon_', '_C.png', '.png'], '', $filename);
            $name = str_replace('_', ' ', $name);

            // Create record
            \App\Models\Items::create([
                'title' => $name,
                'image' => config('app.url') . '/storage/items/pubg/' . $filename,
                'rarity' => $this->getRandomRarity(),
                'weapon' => $name,
                'game' => 'pubg',
                'steam_price' => rand(500, 15000), // Random price in coins/cents
                'steam_price_before' => rand(16000, 25000),
            ]);
        }
    }

    private function getRandomRarity(): string
    {
        $rarities = ['Mil-Spec', 'Restricted', 'Classified', 'Covert', 'Extraordinary'];
        return $rarities[array_rand($rarities)];
    }
}
