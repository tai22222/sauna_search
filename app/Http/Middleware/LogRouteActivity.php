<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogRouteActivity
{
    public function handle($request, Closure $next)
    {
        // ルーティングのアクション名を取得
        $action = $request->route()->getActionName();

        // ルーティングのアクション名をログに記録
        Log::info("Route accessed: $action");

        return $next($request);
    }
}