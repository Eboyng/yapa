<div wire:poll.1s="checkMaintenanceStatus" class="text-center">
    @if($endTime && !$isMaintenanceEnded && count($timeRemaining) > 0)
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Estimated Return Time</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-md mx-auto">
                <!-- Days -->
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                    <div class="text-3xl font-bold text-orange-600">{{ $timeRemaining['days'] }}</div>
                    <div class="text-sm text-gray-600 font-medium">Days</div>
                </div>
                
                <!-- Hours -->
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                    <div class="text-3xl font-bold text-orange-600">{{ str_pad($timeRemaining['hours'], 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Hours</div>
                </div>
                
                <!-- Minutes -->
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                    <div class="text-3xl font-bold text-orange-600">{{ str_pad($timeRemaining['minutes'], 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Minutes</div>
                </div>
                
                <!-- Seconds -->
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                    <div class="text-3xl font-bold text-orange-600">{{ str_pad($timeRemaining['seconds'], 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Seconds</div>
                </div>
            </div>
        </div>
    @elseif($isMaintenanceEnded)
        <div class="mb-8">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <p class="font-semibold">Maintenance period has ended! The site should be available shortly.</p>
                <button 
                    onclick="window.location.reload()" 
                    class="mt-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200"
                >
                    Refresh Page
                </button>
            </div>
        </div>
    @endif
</div>