<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingService;

class AdsEnabledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settingService = app(SettingService::class);
        
        if (!$settingService->get('ads_feature_enabled', true)) {
            abort(404, 'Ads feature is currently disabled.');
        }

        return $next($request);
    }
}