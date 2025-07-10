<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-purple-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <x-profile.flash-messages />

        <!-- New Profile Layout: Avatar and Wallet on Same Line -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8 border border-gray-100">
            <!-- Top Section: Avatar and Wallet Cards -->
            <div class="flex  lg:flex-row items-start gap-3 mb-6">
                <!-- Avatar Section -->
                <div class="flex-shrink-0">
                    <div class="relative">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-r from-orange-400 to-purple-500 flex items-center justify-center text-white text-xl sm:text-2xl font-bold shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Compact Wallet Cards -->
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Credits Card -->
                    <div class="bg-gradient-to-br flex justify-between from-blue-500 to-blue-600 rounded-xl p-3 text-white transform transition-all duration-300 hover:scale-105">
                        
                        <div class="text-xs sm:text-xl font-bold">{{ number_format($user->credits) }}</div>
                        <div class="text-xs opacity-75">Credits</div>
                    </div>

                    <!-- Naira Balance Card -->
                    <div class="bg-gradient-to-br flex justify-between from-green-500 to-green-600 rounded-xl p-3 text-white transform transition-all duration-300 hover:scale-105">

                        <div class="text-xs sm:text-xl font-bold">₦{{ number_format($user->getNairaWallet()->balance, 2) }}</div>
                        <div class="text-xs opacity-75">Balance</div>
                    </div>

                    <!-- Earnings Card -->
                    <div class="bg-gradient-to-br flex justify-between from-purple-500 to-purple-600 rounded-xl p-3 text-white transform transition-all duration-300 hover:scale-105">
                
                        <div class="text-xs sm:text-xl font-bold">₦{{ number_format($user->total_earnings, 2) }}</div>
                        <div class="text-xs opacity-75">Earnings</div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: User Details -->
            <div class="border-t border-gray-100 pt-4">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">{{ $user->name }}</h1>
                <p class="text-sm text-gray-600 mb-3">{{ $user->email }}</p>
                
                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                    @if($user->location)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $user->location }}, Nigeria</span>
                    </div>
                    @endif
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h6a2 2 0 012 2v4M5 9v10a2 2 0 002 2h10a2 2 0 002-2V9M5 9h14"></path>
                        </svg>
                        <span>Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <x-profile.tab-navigation />

            <!-- Edit Profile Tab -->
            <div id="profileTab" class="p-4 sm:p-6 lg:p-8 tab-content">
                <x-profile.tabs.edit-profile />
            </div>

            <!-- Security Tab -->
            <div id="securityTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
                <x-profile.tabs.security :user="$user" :emailVerificationEnabled="$emailVerificationEnabled" />
            </div>


            <!-- WhatsApp Tab -->
            <div id="whatsappTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
                <x-profile.tabs.whatsapp :user="$user" :notifyWhatsapp="$notifyWhatsapp" />
            </div>



            <!-- Integrations Tab -->
            <div id="integrationsTab" class="p-4 sm:p-6 lg:p-8 tab-content hidden">
                <x-profile.tabs.integrations :user="$user" />
            </div>
        </div>
    </div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Tab transition effects */
.tab-content {
    transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
}

.tab-content.hidden {
    opacity: 0;
    transform: translateY(10px);
}

.tab-content:not(.hidden) {
    opacity: 1;
    transform: translateY(0);
}

/* Interest selection styles */
.interest-option {
    position: relative;
    overflow: hidden;
}

.interest-option:has(.interest-checkbox:checked) {
    border-color: #f97316;
    background-color: #fff7ed;
}

.interest-option:has(.interest-checkbox:checked) .checkmark {
    opacity: 1;
    transform: scale(1);
}

.interest-option .checkmark {
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.2s ease-in-out;
}

.interest-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Custom toggle switch */
.peer:checked + div {
    background: linear-gradient(to right, #f97316, #a855f7);
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(to right, #f97316, #a855f7);
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to right, #ea580c, #9333ea);
}

/* Ripple animation */
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* Pulse animation */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

/* Mobile-specific adjustments */
@media (max-width: 640px) {
    .grid-cols-2 {
        gap: 0.5rem;
    }
    
    .interest-option {
        padding: 0.5rem;
    }
    
    .interest-option span {
        font-size: 0.75rem;
    }
}
</style>

<script>
// Tab functionality
let currentActiveTab = 'profile';

function setActiveTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active styles from all buttons
    const tabButtons = document.querySelectorAll('[id$="TabBtn"]');
    tabButtons.forEach(button => {
        button.classList.remove('text-orange-600', 'border-orange-500', 'bg-orange-50');
        button.classList.add('text-gray-500');
    });
    
    // Show selected tab content
    const selectedTab = document.getElementById(tabName + 'Tab');
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }
    
    // Add active styles to selected button
    const selectedButton = document.getElementById(tabName + 'TabBtn');
    if (selectedButton) {
        selectedButton.classList.remove('text-gray-500');
        selectedButton.classList.add('text-orange-600', 'border-orange-500', 'bg-orange-50');
    }
    
    currentActiveTab = tabName;
}

// Interest management
// Interest data and management moved to scripts.blade.php component
// to prevent conflicts and ensure proper functionality

// This function is handled by the scripts.blade.php component
// Removed to prevent conflicts with the working implementation

// Interest management is handled by scripts.blade.php component
// Removed to prevent conflicts

// toggleMoreInterests function is handled by scripts.blade.php component

// Interest selection event handling is managed by scripts.blade.php component

// Form submissions
function updateProfile(event) {
    event.preventDefault();
    const successMsg = document.getElementById('success-message');
    successMsg.classList.remove('hidden');
    setTimeout(() => successMsg.classList.add('hidden'), 3000);
}

function updatePassword(event) {
    event.preventDefault();
    const successMsg = document.getElementById('success-message');
    successMsg.querySelector('span').textContent = 'Password updated successfully!';
    successMsg.classList.remove('hidden');
    setTimeout(() => successMsg.classList.add('hidden'), 3000);
}

function initiateNumberChange(event) {
    event.preventDefault();
    const successMsg = document.getElementById('success-message');
    successMsg.querySelector('span').textContent = 'OTP sent to your new WhatsApp number!';
    successMsg.classList.remove('hidden');
    setTimeout(() => successMsg.classList.add('hidden'), 3000);
}

function connectGoogle() {
    const successMsg = document.getElementById('success-message');
    successMsg.querySelector('span').textContent = 'Redirecting to Google authentication...';
    successMsg.classList.remove('hidden');
    setTimeout(() => successMsg.classList.add('hidden'), 3000);
}

// Referral and Batch Sharing Functions
function copyReferralLink() {
    const referralInput = document.querySelector('input[value*="/register?ref="]');
    if (referralInput) {
        copyToClipboard(referralInput.value);
        showNotification('Referral link copied to clipboard!', 'success');
    }
}

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Link copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.position = 'fixed';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Link copied to clipboard!', 'success');
    } catch (err) {
        showNotification('Failed to copy link', 'error');
    }
    
    document.body.removeChild(textArea);
}

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    setActiveTab('profile');
    
    // Add smooth scrolling behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add intersection observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe wallet cards for scroll animations
    const walletCards = document.querySelectorAll('.grid.grid-cols-1.sm\\:grid-cols-3 > div');
    walletCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
        observer.observe(card);
    });
    
    // Add click ripple effect for buttons
    const buttons = document.querySelectorAll('button, a[class*="bg-gradient"]');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.disabled) return;
            
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add form field focus animations
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.2s ease-out';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
</div>