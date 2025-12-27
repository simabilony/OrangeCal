<?php

namespace App\Http\Controllers;

use App\Http\Resources\DailySyncResource;
use App\Models\DailySync;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function dailySync(Request $request): DailySyncResource
    {
        $user = $request->user();
        $date = $request->input('date', today()->toDateString());

        $sync = DailySync::updateOrCreateForDate($user->id, $date);

        return new DailySyncResource($sync);
    }
}

