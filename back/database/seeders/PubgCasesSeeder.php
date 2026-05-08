<?php

namespace Database\Seeders;

use App\Models\Boxes;
use App\Models\CaseItems;
use App\Models\Categories;
use App\Models\Items;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PubgCasesSeeder extends Seeder
{
    /**
     * Seed 65 PUBG cases with random prices, placeholder names, and auto-filled items.
     */
    public function run(): void
    {
        // Create or find a PUBG category
        $category = Categories::firstOrCreate(
            ['name' => 'PUBG Cases'],
            ['position' => 10]
        );

        $this->command->info("Using category: {$category->name} (ID: {$category->id})");

        // Get all PUBG items for filling cases
        $pubgItems = Items::where('game', 'pubg')->where('steam_price', '>', 0)->get();

        if ($pubgItems->isEmpty()) {
            $this->command->error('No PUBG items found! Run PubgItemsSeeder first.');
            return;
        }

        $this->command->info("Found {$pubgItems->count()} PUBG items to use for case contents.");

        // Themed PUBG case name prefixes for variety
        $themes = [
            'Battleground', 'Erangel', 'Miramar', 'Sanhok', 'Vikendi', 'Karakin',
            'Taego', 'Deston', 'Haven', 'Rondo', 'Paramo',
            'Chicken Dinner', 'Airdrop', 'Supply Crate', 'Flare Gun',
            'Ghillie', 'Pan', 'AWM', 'M416', 'Groza', 'Kar98k',
            'Level 3', 'Military', 'Tactical', 'Squad', 'Solo',
            'Hot Drop', 'Pochinki', 'School', 'Novorepnoye', 'Sosnovka',
            'Golden', 'Silver', 'Bronze', 'Diamond', 'Platinum',
            'Fire', 'Storm', 'Thunder', 'Frost', 'Shadow',
            'Neon', 'Cyber', 'Retro', 'Classic', 'Elite',
            'Lucky', 'Mega', 'Ultra', 'Super', 'Premium',
            'Sniper', 'Assault', 'Shotgun', 'SMG', 'Melee',
            'Desert', 'Jungle', 'Arctic', 'Urban', 'Night',
            'Champion', 'Veteran', 'Rookie', 'Legend', 'Myth',
        ];

        // Price tiers in kopecks (cents): covers cheap to expensive
        $priceTiers = [
            ['min' => 5000,   'max' => 15000,   'count' => 15], // 50–150₽ — budget
            ['min' => 15000,  'max' => 35000,   'count' => 15], // 150–350₽ — low-mid
            ['min' => 35000,  'max' => 75000,   'count' => 12], // 350–750₽ — mid
            ['min' => 75000,  'max' => 150000,  'count' => 10], // 750–1500₽ — mid-high
            ['min' => 150000, 'max' => 350000,  'count' => 8],  // 1500–3500₽ — high
            ['min' => 350000, 'max' => 1000000, 'count' => 5],  // 3500–10000₽ — premium
        ];

        $caseIndex = 1;
        $createdCount = 0;
        $usedNames = [];

        foreach ($priceTiers as $tier) {
            for ($i = 0; $i < $tier['count']; $i++) {
                // Pick a unique name
                $name = null;
                while ($name === null || in_array($name, $usedNames)) {
                    if (!empty($themes)) {
                        $nameKey = array_rand($themes);
                        $name = $themes[$nameKey];
                        unset($themes[$nameKey]);
                    } else {
                        $name = "PUBG Case {$caseIndex}";
                    }
                }
                $usedNames[] = $name;

                // Random price within tier (rounded to nearest 100 kopecks)
                $price = round(rand($tier['min'], $tier['max']) / 100) * 100;

                // Generate a unique URL slug
                $url = 'pubg-' . Str::slug($name);
                // Ensure uniqueness
                $existingUrl = Boxes::where('url', $url)->exists();
                if ($existingUrl) {
                    $url .= '-' . $caseIndex;
                }

                // Use a placeholder image path pointing to api-assets/cases/
                $image = '/api-assets/cases/pubg_case_' . $caseIndex . '.png';

                $box = Boxes::create([
                    'category_id'  => $category->id,
                    'name'         => $name,
                    'url'          => $url,
                    'image'        => $image,
                    'price'        => $price,
                    'is_active'    => true,
                    'is_visible'   => true,
                    'type'         => 'default',
                    'game'         => 'pubg',
                    'sound_pack'   => 'default',
                    'target_rtp'   => 95,
                    'min_rtp'      => 85,
                    'max_rtp'      => 98,
                    'current_rtp'  => 95,
                    'total_opened' => 0,
                    'total_spent'  => 0,
                    'total_won'    => 0,
                ]);

                // Fill the case with PUBG items
                $this->fillCaseWithItems($box, $pubgItems);

                $createdCount++;
                $caseIndex++;

                $this->command->info("Created: {$name} — " . ($price / 100) . '₽ [' . $url . ']');
            }
        }

        $this->command->info("Done! Created {$createdCount} PUBG cases in category '{$category->name}'.");
    }

    /**
     * Fill a case with a balanced selection of PUBG items and calculate chances.
     */
    private function fillCaseWithItems(Boxes $box, $allItems): void
    {
        $boxPrice = $box->price;

        // Select items that make sense for this case's price range
        $maxItemPrice = $boxPrice * 25;
        $minItemPrice = max(100, (int)($boxPrice / 10));

        $eligibleItems = $allItems->filter(function ($item) use ($minItemPrice, $maxItemPrice) {
            return $item->steam_price >= $minItemPrice && $item->steam_price <= $maxItemPrice;
        });

        // If not enough eligible items, relax the filter
        if ($eligibleItems->count() < 8) {
            $eligibleItems = $allItems;
        }

        // Pick 15-22 items per case
        $count = min($eligibleItems->count(), rand(15, 22));
        $selectedItems = $eligibleItems->random($count);

        // Insert items and calculate proper chances
        $rawChances = [];
        $caseItemRecords = [];

        foreach ($selectedItems as $item) {
            $itemPrice = $item->steam_price;

            if ($itemPrice <= 0) {
                $chance = 1;
            } else {
                $chance = 1 / ($itemPrice / $boxPrice);
            }

            $chance = min(100, $chance);
            $chance = max(0.001, $chance);

            $caseItemRecords[] = [
                'item' => $item,
                'raw_chance' => $chance,
            ];

            $rawChances[] = $chance;
        }

        $sum = array_sum($rawChances);
        if ($sum <= 0) return;

        foreach ($caseItemRecords as $idx => $record) {
            $normalizedChance = round(($record['raw_chance'] / $sum) * 100, 4);

            CaseItems::create([
                'box_id'    => $box->id,
                'skin_id'   => $record['item']->id,
                'chance'    => $normalizedChance,
                'droppable' => true,
            ]);
        }
    }
}
