<?php

namespace App\Http\Controllers;

use App\Models\Promocode;
use App\Models\Settings;
use App\Services\LiveService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainController extends Controller
{
    protected LiveService $liveService;
    public function __construct(
        LiveService $liveService
    ) {
        $this->liveService = $liveService;
    }

    public function index(): array
    {

        $settings = Settings::query()->select(['domain', 'site_name', 'title', 'description', 'keywords', 'vk_group', 'tg_group'])->first();

        return [
            'settings' => $settings
        ];
    }
}
