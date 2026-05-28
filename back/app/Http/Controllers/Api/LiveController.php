<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Lives;
use App\Models\User;
use App\Models\Upgrade;
use App\Models\Promocode;
use App\Models\Setting;
use App\Services\LiveService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LiveController extends Controller
{
  protected LiveService $liveService;
  public function __construct(
    LiveService $liveService
  ) {
    $this->liveService = $liveService;
  }

  public function index(): array
  {

    $lives = $this->liveService->getLive();

    return [
      'status' => 200,
      'result' => true,
      'lives' => $lives,
    ];
  }

  public function stats(): array
  {
    $players = User::query()->count('id');
    $opens = Lives::query()->where('from_where', Lives::CASE_TYPE)->count('id');
    $upgrades = Upgrade::query()->count('id');
    $total_games = $opens + $upgrades;
    $withdraws = Lives::query()->where('status', Lives::WITHDRAWN)->count('id');

    return [
      'status' => 200,
      'result' => true,
      'statistics' => [
        'players' => $players + 294,
        'total_games' => $total_games + 2049,
        'withdraws' => $withdraws + 22213,
      ],
    ];
  }
}
