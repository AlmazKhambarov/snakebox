<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Items;
use App\Services\MarketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ItemsController extends Controller
{
    protected MarketService $marketService;
    public function __construct(
        MarketService $marketService
    ) {
        $this->marketService = $marketService;
    }

    public function get()
    {
        return datatables(Items::query())->toJson();
    }
}
