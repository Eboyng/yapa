<!-- Security Tab -->
<div id="securityTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
        </svg>
        Change Password
    </h2>

    <!-- Change Password Form -->
    <div class="border border-purple-200 rounded-2xl p-4 sm:p-6 mb-4 sm:mb-6">
        <form onsubmit="updatePassword(event)" class="space-y-3 sm:space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label for="currentPassword" class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                    <input type="password" id="currentPassword" 
                           class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                           placeholder="Enter current password">
                </div>
                
                <div>
                    <label for="newPassword" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                    <input type="password" id="newPassword" 
                           class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                           placeholder="Enter new password">
                </div>
            </div>
            
            <div>
                <label for="newPasswordConfirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" id="newPasswordConfirmation" 
                       class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                       placeholder="Confirm new password">
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105">
                    <span class="flex items-center">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Update Password
                    </span>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Security Info -->
    <div class="bg-blue-50 rounded-xl p-3 sm:p-4">
        <div class="flex items-start">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-medium text-blue-900 text-sm sm:text-base">Password Requirements</h4>
                <ul class="text-xs sm:text-sm text-blue-800 mt-1 space-y-1">
                    <li>• At least 8 characters long</li>
                    <li>• Contains uppercase and lowercase letters</li>
                    <li>• Includes at least one number</li>
                    <li>• Has at least one special character</li>
                </ul>
            </div>
        </div>
    </div>
</div>