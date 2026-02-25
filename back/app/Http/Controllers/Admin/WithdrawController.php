<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lives;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class WithdrawController extends Controller
{
    public function index(Request $request)
    {
        $items = Lives::query()->whereIn('status', [
            Lives::SENDING,
            Lives::WAIT,
            Lives::ORDER_READY,
            Lives::WITHDRAWN
        ])->with('user', 'item')->get();

        Log::channel('admin_withdraw')->info($items);

     
        return datatables($items)->toJson();
    }
}
