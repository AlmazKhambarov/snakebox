<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventScores;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
  public function index()
  {
    $event = Event::getCurrentEvent();

    if (!$event) {
      return response()->json([
        'active' => false,
        'message' => 'Ивент не активен'
      ]);
    }

    $leaderboard = $this->getLeaderboard($event->id);

    return response()->json([
      'active' => true,
      'success' => true,
      'event' => [
        'name' => $event->name,
        'end_date' => $event->end_date,
        'time_remaining' => $event->getTimeRemaining()
      ],
      'leaderboard' => $leaderboard
    ]);
  }


  private function getLeaderboard($eventId, $limit = 50)
  {
    // Получаем событие один раз
    $event = Event::find($eventId);

    return EventScores::with('user')
      ->where('event_id', $eventId)
      ->orderBy('points', 'DESC')
      ->orderBy('updated_at', 'ASC')
      ->limit($limit)
      ->get()
      ->map(function ($score, $index) use ($event) {
        $position = $index + 1;
        
        // Получаем приз за это место (конкретный предмет)
        $prize = $event ? $event->getPrizeForPosition($position) : null;
        $prizeItem = null;
        
        if ($prize && $prize->item_id) {
          $prizeItem = $prize->item;
        }

        return [
          'position' => $position,
          'user_name' => $score->user->username,
          'id' => $score->user->id,
          'avatar' => $score->user->avatar,
          'points' => $score->points,
          'prize_item' => $prizeItem ? [
            'id' => $prizeItem->id,
            'title' => $prizeItem->title,
            'image' => $prizeItem->image,
            'rarity' => $prizeItem->rarity,
            'weapon' => $prizeItem->weapon,
            'skin_name' => $prizeItem->skin_name,
            'steam_price' => $prizeItem->steam_price,
          ] : null,
          'database_position' => $score->position
        ];
      });
  }
}
