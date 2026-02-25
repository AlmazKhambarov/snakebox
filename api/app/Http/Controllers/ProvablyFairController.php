<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ProvablyFairSeed;

class ProvablyFairController extends Controller
{
  public function index(Request $request)
  {
    $seed = ProvablyFairSeed::where('user_id', $request->user()->id)
      ->where('active', true)
      ->first();

    if (!$seed) {
      $seed = $this->createNewSeed($request->user()->id);
    }

    return response()->json([
      'server_seed_hashed' => $seed->server_seed_hashed,
      'client_seed' => $seed->client_seed,
      'games_count' => $seed->nonce,
    ]);
  }

  public function updateClientSeed(Request $request)
  {
    $request->validate([
      'client_seed' => 'required|string|max:128'
    ]);

    $seed = ProvablyFairSeed::where('user_id', $request->user()->id)
      ->where('active', true)
      ->firstOrFail();

    $seed->update(['client_seed' => $request->client_seed]);

    return [
      'client_seed' => $request->client_seed,
      'message' => 'Client seed updated successfully',
      'success' => true,
    ];
  }

  public function rotateServerSeed(Request $request)
  {
    $user = $request->user();

    $currentSeed = ProvablyFairSeed::where('user_id', $user->id)
      ->where('active', true)
      ->firstOrFail();

    $currentSeed->update([
      'active' => false,
      'revealed_at' => now(),
    ]);

    $newSeed = $this->createNewSeed($user->id, $currentSeed->client_seed);

    return response()->json([
      'message' => 'Server seed rotated successfully',
      'server_seed_hashed' => $newSeed->server_seed_hashed,
      'old_server_seed' => $currentSeed->server_seed,
      'result' => true,
      'status' => 200,
    ]);
  }

  public function generateResult(Request $request)
  {
    $seed = ProvablyFairSeed::where('user_id', $request->user()->id)
      ->where('active', true)
      ->firstOrFail();

    $input = $seed->client_seed . '-' . $seed->nonce;
    $hash = hash_hmac('sha256', $input, $seed->server_seed);

    $seed->increment('nonce');

    return response()->json([
      'result_hash' => $hash,
      'nonce' => $seed->nonce,
    ]);
  }

  private function createNewSeed($userId)
  {
    $serverSeed = Str::random(64);
    $serverSeedHashed = hash('sha256', $serverSeed);

    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    $length = random_int(15, 20);

    // Генерация строки
    $clientSeed = '';
    for ($i = 0; $i < $length; $i++) {
      $clientSeed .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return ProvablyFairSeed::create([
      'user_id' => $userId,
      'server_seed' => $serverSeed,
      'server_seed_hashed' => $serverSeedHashed,
      'client_seed' => $clientSeed,
      'nonce' => 0,
      'active' => true,
    ]);
  }
}
