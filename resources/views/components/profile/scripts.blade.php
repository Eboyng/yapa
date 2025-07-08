<!-- JavaScript for Profile Page -->
<script>
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('border-orange-500', 'text-orange-600');
        button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-content');
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    // Add active class to selected tab button
    const selectedButton = document.getElementById(tabName + '-tab');
    if (selectedButton) {
        selectedButton.classList.add('border-orange-500', 'text-orange-600');
        selectedButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    }
}

// Interest management
let selectedInterests = [];
let showingMoreInterests = false;

function toggleInterest(interest) {
    const index = selectedInterests.indexOf(interest);
    if (index > -1) {
        selectedInterests.splice(index, 1);
    } else {
        selectedInterests.push(interest);
    }
    updateSelectedInterests();
    updateInterestButtons();
}

function updateSelectedInterests() {
    const container = document.getElementById('selected-interests');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (selectedInterests.length === 0) {
        container.innerHTML = '<span class="text-gray-400 text-sm">No interests selected</span>';
        return;
    }
    
    selectedInterests.forEach(interest => {
        const tag = document.createElement('span');
        tag.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-800';
        tag.innerHTML = `
            ${interest}
            <button type="button" onclick="toggleInterest('${interest}')" class="ml-2 text-orange-600 hover:text-orange-800">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(tag);
    });
}

function updateInterestButtons() {
    const buttons = document.querySelectorAll('.interest-btn');
    buttons.forEach(button => {
        const interest = button.textContent.trim();
        if (selectedInterests.includes(interest)) {
            button.classList.add('bg-orange-100', 'border-orange-500', 'text-orange-600');
            button.classList.remove('border-gray-300');
        } else {
            button.classList.remove('bg-orange-100', 'border-orange-500', 'text-orange-600');
            button.classList.add('border-gray-300');
        }
    });
}

function toggleMoreInterests() {
    const button = document.getElementById('show-more-interests');
    const grid = document.getElementById('interests-grid');
    
    if (!showingMoreInterests) {
        // Add more interests
        const moreInterests = ['Nature', 'Meditation', 'Yoga', 'Gardening', 'Astronomy', 'Psychology', 'Philosophy', 'Economics', 'Environment', 'Volunteering'];
        
        moreInterests.forEach(interest => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'interest-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:border-orange-500 hover:text-orange-600 transition-all duration-200 text-center';
            btn.textContent = interest;
            btn.onclick = () => toggleInterest(interest);
            grid.appendChild(btn);
        });
        
        button.textContent = 'Show fewer interests';
        showingMoreInterests = true;
    } else {
        // Remove extra interests
        const buttons = grid.querySelectorAll('.interest-btn');
        for (let i = buttons.length - 1; i >= 20; i--) {
            buttons[i].remove();
        }
        
        button.textContent = 'Show more interests';
        showingMoreInterests = false;
    }
}

// Form submissions
function submitProfileForm() {
    // Add selected interests to form data
    const form = document.querySelector('form[wire\\:submit\.prevent="updateProfile"]');
    if (form) {
        // Create hidden input for interests
        let interestsInput = form.querySelector('input[name="interests"]');
        if (!interestsInput) {
            interestsInput = document.createElement('input');
            interestsInput.type = 'hidden';
            interestsInput.name = 'interests';
            form.appendChild(interestsInput);
        }
        interestsInput.value = JSON.stringify(selectedInterests);
    }
}

function submitPasswordForm() {
    const form = document.querySelector('form[wire\\:submit\.prevent="updatePassword"]');
    if (form) {
        const newPassword = form.querySelector('#new_password').value;
        const confirmPassword = form.querySelector('#confirm_password').value;
        
        if (newPassword !== confirmPassword) {
            alert('Passwords do not match!');
            return false;
        }
    }
}

function submitWhatsAppForm() {
    const form = document.querySelector('form[wire\\:submit\.prevent="updateWhatsAppNumber"]');
    if (form) {
        const number = form.querySelector('#whatsapp_number').value;
        if (!/^[0-9]{10}$/.test(number)) {
            alert('Please enter a valid 10-digit WhatsApp number');
            return false;
        }
        
        if (!confirm('This will cost 100 credits. Are you sure you want to continue?')) {
            return false;
        }
    }
}

// Google connection
function connectGoogle() {
    // Redirect to Google OAuth
    window.location.href = '/auth/google';
}

function disconnectGoogle() {
    if (confirm('Are you sure you want to disconnect your Google account?')) {
        // Make request to disconnect Google
        fetch('/auth/google/disconnect', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}

// Sharing functionality
let selectedBatchId = null;

function selectBatch(batchId) {
    selectedBatchId = batchId;
    const radio = document.querySelector(`input[value="${batchId}"]`);
    if (radio) {
        radio.checked = true;
    }
}

function generateShareUrl() {
    if (!selectedBatchId) {
        alert('Please select a batch to share');
        return;
    }
    
    // Generate share URL
    const shareUrl = `${window.location.origin}/join?ref=${btoa(selectedBatchId + '_' + Date.now())}`;
    
    // Show share URL section
    const section = document.getElementById('share-url-section');
    const input = document.getElementById('share-url');
    
    if (section && input) {
        input.value = shareUrl;
        section.classList.remove('hidden');
    }
}

function copyShareUrl() {
    const input = document.getElementById('share-url');
    if (input) {
        input.select();
        document.execCommand('copy');
        
        // Show notification
        showNotification('Share URL copied to clipboard!', 'success');
    }
}

function shareOnWhatsApp() {
    const shareUrl = document.getElementById('share-url').value;
    if (shareUrl) {
        const message = encodeURIComponent(`Join this amazing platform and earn rewards! ${shareUrl}`);
        window.open(`https://wa.me/?text=${message}`, '_blank');
    }
}

function shareOnFacebook() {
    const shareUrl = document.getElementById('share-url').value;
    if (shareUrl) {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank');
    }
}

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 animate-fade-in ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Initialize interests display
    updateSelectedInterests();
    updateInterestButtons();
    
    // Add form submit handlers
    const profileForm = document.querySelector('form[wire\\:submit\.prevent="updateProfile"]');
    if (profileForm) {
        profileForm.addEventListener('submit', submitProfileForm);
    }
    
    const passwordForm = document.querySelector('form[wire\\:submit\.prevent="updatePassword"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', submitPasswordForm);
    }
    
    const whatsappForm = document.querySelector('form[wire\\:submit\.prevent="updateWhatsAppNumber"]');
    if (whatsappForm) {
        whatsappForm.addEventListener('submit', submitWhatsAppForm);
    }
    
    // Smooth scrolling for wallet cards
    const walletCards = document.querySelectorAll('.wallet-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.transform = 'translateY(0)';
                entry.target.style.opacity = '1';
            }
        });
    });
    
    walletCards.forEach(card => {
        card.style.transform = 'translateY(20px)';
        card.style.opacity = '0';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
    
    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn-primary');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add focus animations to inputs
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
        });
    });
});
</script>

<style>
/* Additional CSS for animations and effects */
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.input-focused {
    transform: scale(1.02);
    transition: transform 0.2s ease;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
    .wallet-card {
        padding: 1rem;
    }
    
    .tab-button {
        font-size: 0.875rem;
        padding: 0.75rem 1rem;
    }
    
    .form-input {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}
</style>