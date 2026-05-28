<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UpgradeRtpStats;
use Illuminate\Http\Request;

class UpgradeSettingsController extends Controller
{
    public function get(): array
    {
        $stats = UpgradeRtpStats::getStats();

        return [
            'success' => true,
            'settings' => [
                'chance_boost' => (float) $stats->chance_boost,
                'target_rtp'   => (float) $stats->target_rtp,
                'min_rtp'      => (float) $stats->min_rtp,
                'max_rtp'      => (float) $stats->max_rtp,
            ],
        ];
    }

    public function save(Request $request): array
    {
        $request->validate([
            'chance_boost' => 'required|numeric|min:0|max:50',
            'target_rtp'   => 'required|numeric|min:10|max:100',
            'min_rtp'      => 'required|numeric|min:10|max:100',
            'max_rtp'      => 'required|numeric|min:10|max:100',
        ]);

        $stats = UpgradeRtpStats::getStats();
        $stats->update([
            'chance_boost' => $request->chance_boost,
            'target_rtp'   => $request->target_rtp,
            'min_rtp'      => $request->min_rtp,
            'max_rtp'      => $request->max_rtp,
        ]);

        return ['success' => true, 'message' => 'Настройки апгрейда сохранены!'];
    }
}
