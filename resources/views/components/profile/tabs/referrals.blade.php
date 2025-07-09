@props(['referredUsers', 'totalReferralRewards', 'referralCode', 'referralLink', 'showReferralSection', 'isLoadingReferrals'])

<h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        Referral System
    </h2>
    
    <!-- Referral Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Referrals</p>
                    <p class="text-xl font-bold text-gray-900">{{ count($referredUsers ?? []) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Rewards</p>
                    <p class="text-xl font-bold text-gray-900">₦{{ number_format($totalReferralRewards, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-2xl p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Referral Code</p>
                    <p class="text-lg font-bold text-gray-900">{{ $referralCode }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Referral Link -->
    <div class="border border-gray-200 rounded-2xl p-4 sm:p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-3">Your Referral Link</h3>
        <div class="flex items-center space-x-2">
            <input type="text" value="{{ $referralLink }}" readonly 
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
            <button onclick="copyReferralLink()" 
                    class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-2">Share this link to earn rewards when people sign up and make deposits!</p>
    </div>
    
    <!-- Referred Users -->
    <div class="border border-gray-200 rounded-2xl p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Referred Users</h3>
            @if(!$showReferralSection)
                <button wire:click="toggleReferralSection" 
                        class="text-sm text-blue-600 hover:text-blue-700">
                    Show Details
                </button>
            @endif
        </div>
        
        @if($showReferralSection)
             @if($isLoadingReferrals)
                 <div class="flex justify-center py-8">
                     <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                 </div>
             @else
                 @if(count($referredUsers ?? []) > 0)
                     <div class="space-y-3">
                         @foreach($referredUsers as $user)
                             <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                 <div class="flex items-center">
                                     <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                         <span class="text-sm font-semibold text-green-600">{{ substr($user->name, 0, 1) }}</span>
                                     </div>
                                     <div>
                                         <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                         <p class="text-sm text-gray-500">Joined {{ $user->created_at->format('M d, Y') }}</p>
                                     </div>
                                 </div>
                                 <div class="text-right">
                                     <p class="text-sm font-semibold text-green-600">₦{{ number_format($user->referral_reward ?? 0, 2) }}</p>
                                     <p class="text-xs text-gray-500">Reward</p>
                                 </div>
                             </div>
                         @endforeach
                     </div>
                 @else
                     <div class="text-center py-8">
                         <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                         </svg>
                         <p class="text-gray-500 mb-2">No referrals yet</p>
                         <p class="text-sm text-gray-400">Share your referral link to start earning rewards!</p>
                     </div>
                 @endif
             @endif
         @else
             <p class="text-sm text-gray-500">Click "Show Details" to view your referred users.</p>
         @endif
     </div>