<?php

namespace App\Console\Commands;

use App\Models\Boxes;
use App\Models\CaseItems;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecalcDropChances extends Command
{
    protected $signature = 'cases:recalc-chances';
    protected $description = 'Recalculate drop chances for all cases, normalizing only across droppable items';

    public function handle()
    {
        $boxes = Boxes::all();
        $fixedCount = 0;

        foreach ($boxes as $box) {
            $droppableItems = CaseItems::query()
                ->with(['item'])
                ->where('box_id', $box->id)
                ->where('droppable', true)
                ->get();

            if ($droppableItems->isEmpty()) {
                $this->warn("Box #{$box->id} ({$box->name}): no droppable items, skipping");
                continue;
            }

            $rawChances = [];

            foreach ($droppableItems as $caseItem) {
                $itemPrice = $caseItem->item->steam_price ?? 0;

                if ($itemPrice <= 0) {
                    $chance = 1;
                } else {
                    $chance = 1 / ($itemPrice / $box->price);
                }

                $chance = min(100, $chance);
                $chance = max(0.001, $chance);

                $rawChances[$caseItem->id] = $chance;
            }

            $sum = array_sum($rawChances);
            if ($sum <= 0) continue;

            $totalBlocked = CaseItems::where('box_id', $box->id)->where('droppable', false)->count();

            foreach ($droppableItems as $caseItem) {
                $normalizedChance = ($rawChances[$caseItem->id] / $sum) * 100;
                $normalizedChance = round($normalizedChance, 4);
                $caseItem->update(['chance' => $normalizedChance]);
            }

            $fixedCount++;
            $this->info("Box #{$box->id} ({$box->name}): recalculated {$droppableItems->count()} droppable items, {$totalBlocked} blocked");
        }

        $this->info("Done! Recalculated chances for {$fixedCount} cases.");
        Log::info("RecalcDropChances: recalculated {$fixedCount} cases");

        return 0;
    }
}
