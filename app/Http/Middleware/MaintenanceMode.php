<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip maintenance check for admin routes (comprehensive list)
        if ($request->is('admin/*') || 
            $request->is('filament/*') || 
            $request->is('livewire/*') ||
            $request->is('_debugbar/*') ||
            $request->routeIs('filament.*') ||
            $request->routeIs('admin.*')) {
            return $next($request);
        }

        // Skip maintenance check for API routes that might be needed
        if ($request->is('api/*')) {
            return $next($request);
        }

        // Skip maintenance check for essential system routes
        if ($request->is('up') || $request->is('health')) {
            return $next($request);
        }

        // Check if maintenance mode is enabled
        if (!$this->settingService->isMaintenanceMode()) {
            return $next($request);
        }

        // Check if maintenance period has ended
        if ($this->settingService->isMaintenancePeriodEnded()) {
            // Auto-disable maintenance mode if period has ended
            $this->settingService->set('maintenance_mode', false, 'boolean');
            return $next($request);
        }

        // Check if current IP is allowed
        $clientIp = $request->ip();
        if ($this->settingService->isIpAllowedDuringMaintenance($clientIp)) {
            return $next($request);
        }

        // Show maintenance page
        return response()->view('maintenance', [
            'maintenanceSettings' => $this->settingService->getMaintenanceSettings(),
            'brandingSettings' => $this->settingService->getBrandingSettings(),
        ], 503);
    }
}