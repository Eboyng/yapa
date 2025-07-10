<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        
        {{-- <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                <span class="bg-gradient-to-r from-orange-500 to-purple-600 bg-clip-text text-transparent">
                    💡 Tips & Insights
                </span>
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Discover inspirational and business-related tips to help you grow and succeed.
            </p>
        </div> --}}

        <!-- Search -->
        <div class="mb-8">
            <div class="relative max-w-md mx-auto" x-data="{ searchFocused: false }">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       @focus="searchFocused = true; $wire.loadSuggestions()"
                       @blur="setTimeout(() => { searchFocused = false; $wire.hideSuggestions() }, 200)"
                       class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white shadow-sm"
                       placeholder="Search tips...">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                
                <!-- Search Suggestions -->
                @if($showSuggestions && count($suggestions) > 0)
                    <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                        @foreach($suggestions as $suggestion)
                            <button wire:click="selectSuggestion('{{ $suggestion }}')"
                                    class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $suggestion }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Tips List -->
        @if($tips->count() > 0)
            <div class="space-y-8" x-data="{ 
                init() {
                    this.setupInfiniteScroll();
                },
                setupInfiniteScroll() {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && @this.hasMorePages) {
                                @this.loadMore();
                            }
                        });
                    }, { threshold: 0.1 });
                    
                    this.$nextTick(() => {
                        const sentinel = this.$refs.loadMoreSentinel;
                        if (sentinel) observer.observe(sentinel);
                    });
                }
            }">
                @foreach($tips as $tip)
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <!-- Tip Header -->
                        <div class="p-6 sm:p-8">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 leading-tight">
                                        <a href="{{ route('tips.show', $tip->slug) }}" 
                                           class="hover:text-orange-600 transition-colors">
                                            {{ $tip->title }}
                                        </a>
                                    </h2>
                                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $tip->author->name }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $tip->published_at ? $tip->published_at->format('M j, Y') : $tip->created_at->format('M j, Y') }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($tip->image)
                                    <div class="ml-4 flex-shrink-0">
                                        <img src="{{ asset('storage/' . $tip->image) }}" 
                                             alt="{{ $tip->title }}" 
                                             class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-xl">
                                    </div>
                                @endif
                            </div>

                            <!-- Content Preview -->
                            <div class="prose prose-gray max-w-none">
                                @php
                                    $words = explode(' ', strip_tags($tip->content));
                                    $isExpanded = in_array($tip->id, $expandedTips);
                                    $wordLimit = $isExpanded ? 150 : 50;
                                    $totalWords = count($words);
                                    
                                    // Ensure we never show more than 150 words
                                    if ($wordLimit > 150) $wordLimit = 150;
                                    
                                    $displayContent = $totalWords > $wordLimit 
                                        ? implode(' ', array_slice($words, 0, $wordLimit)) . '...' 
                                        : strip_tags($tip->content);
                                @endphp
                                
                                <div class="text-gray-700 leading-relaxed">
                                    {!! nl2br(e($displayContent)) !!}
                                </div>
                            </div>

                            <!-- Read More Toggle -->
                            @if(str_word_count(strip_tags($tip->content)) > 50)
                                <div class="mt-4">
                                    <button wire:click="toggleExpanded('{{ $tip->id }}')"
                                            class="text-orange-600 hover:text-orange-700 font-medium text-sm transition-colors">
                                        @if(in_array($tip->id, $expandedTips))
                                            @if(str_word_count(strip_tags($tip->content)) > 150)
                                                Read Full Article
                                            @else
                                                Show Less
                                            @endif
                                        @else
                                            Read More
                                        @endif
                                    </button>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                                <!-- Clap Button -->
                                <div class="flex items-center space-x-4">
                                    @livewire('tips.clap', ['tip' => $tip], key($tip->id))
                                </div>

                                <!-- Share Button -->
                                <div class="relative" x-data="{ shareOpen: false }">
                                    <button @click="shareOpen = !shareOpen"
                                            class="flex items-center space-x-2 px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Share</span>
                                    </button>

                                    <!-- Share Menu -->
                                    <div x-show="shareOpen" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         @click.away="shareOpen = false"
                                         class="absolute right-0 bottom-full mb-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10">
                                        
                                        <!-- WhatsApp -->
                                        <a href="https://wa.me/?text={{ urlencode($tip->title . ' - ' . route('tips.show', $tip->slug)) }}"
                                           target="_blank"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                            </svg>
                                            WhatsApp
                                        </a>

                                        <!-- Facebook -->
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('tips.show', $tip->slug)) }}"
                                           target="_blank"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            Facebook
                                        </a>

                                        <!-- Twitter/X -->
                                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($tip->title) }}&url={{ urlencode(route('tips.show', $tip->slug)) }}"
                                           target="_blank"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                            </svg>
                                            X (Twitter)
                                        </a>

                                        <!-- Copy Link -->
                                        <button onclick="copyToClipboard('{{ route('tips.show', $tip->slug) }}')"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            Copy Link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Infinite Scroll Sentinel -->
            @if($hasMorePages)
                <div x-ref="loadMoreSentinel" class="flex justify-center py-8">
                    <div wire:loading wire:target="loadMore" class="flex items-center space-x-2 text-gray-500">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading more tips...</span>
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="text-gray-400 text-6xl mb-6">
                    💡
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No tips found</h3>
                <p class="text-gray-600 mb-6">
                    @if($search)
                        No tips match your search criteria. Try adjusting your search terms.
                    @else
                        No tips have been published yet. Check back soon for inspiring content!
                    @endif
                </p>
                @if($search)
                    <button wire:click="$set('search', '')"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        Clear Search
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show a simple notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.textContent = 'Link copied to clipboard!';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        });
    }
</script>