<div class="flex items-center space-x-2">
    <button wire:click="clap" 
            @if($hasClapped) disabled @endif
            class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 transform
                   {{ $hasClapped 
                      ? 'bg-orange-100 text-orange-600 cursor-not-allowed' 
                      : 'text-gray-600 hover:text-orange-600 hover:bg-orange-50 hover:scale-105 active:scale-95' }}
                   {{ $isAnimating ? 'animate-bounce' : '' }}">
        
        <!-- Clap Icon -->
        <svg class="w-5 h-5 transition-transform duration-300 {{ $isAnimating ? 'scale-125' : '' }}" 
             fill="{{ $hasClapped ? 'currentColor' : 'none' }}" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
        </svg>
        
        <!-- Clap Count -->
        <span class="text-sm font-medium">{{ $tip->formatted_claps }}</span>
    </button>
    
    @if($hasClapped)
        <span class="text-xs text-orange-600 font-medium">Thanks!</span>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('clap-animated', () => {
            setTimeout(() => {
                @this.call('resetAnimation');
            }, 1000);
        });
    });
</script>