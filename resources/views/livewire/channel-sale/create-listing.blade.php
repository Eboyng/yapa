<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">List Your WhatsApp Channel for Sale</h2>
        
        <form wire:submit.prevent="createListing">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Channel Name -->
                <div>
                    <label for="channelName" class="block text-sm font-medium text-gray-700 mb-2">
                        Channel Name *
                    </label>
                    <input type="text" id="channelName" wire:model="channelName" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your channel name">
                    @error('channelName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- WhatsApp Number -->
                <div>
                    <label for="whatsappNumber" class="block text-sm font-medium text-gray-700 mb-2">
                        WhatsApp Number *
                    </label>
                    <input type="text" id="whatsappNumber" wire:model="whatsappNumber" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., +234XXXXXXXXXX">
                    @error('whatsappNumber') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category *
                    </label>
                    <select id="category" wire:model="category" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select a category</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Audience Size -->
                <div>
                    <label for="audienceSize" class="block text-sm font-medium text-gray-700 mb-2">
                        Audience Size *
                    </label>
                    <input type="number" id="audienceSize" wire:model="audienceSize" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Number of members">
                    @error('audienceSize') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Engagement Rate -->
                <div>
                    <label for="engagementRate" class="block text-sm font-medium text-gray-700 mb-2">
                        Engagement Rate (%)
                    </label>
                    <input type="number" step="0.01" id="engagementRate" wire:model="engagementRate" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., 15.5">
                    @error('engagementRate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Price (₦) *
                    </label>
                    <input type="number" step="0.01" id="price" wire:model="price" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter selling price">
                    @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" wire:model="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Describe your channel, its content, and why buyers should be interested..."></textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Screenshots -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Screenshots (Optional)
                </label>
                <div class="space-y-3">
                    @foreach($screenshots as $index => $screenshot)
                        <div class="flex items-center space-x-3">
                            <input type="file" wire:model="screenshots.{{ $index }}" accept="image/*"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="button" wire:click="removeScreenshot({{ $index }})"
                                    class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                Remove
                            </button>
                        </div>
                    @endforeach
                    
                    @if(count($screenshots) < 5)
                        <button type="button" wire:click="addScreenshot"
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                            Add Screenshot
                        </button>
                    @endif
                </div>
                @error('screenshots.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Visibility -->
            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="visibility" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Make this listing publicly visible</span>
                </label>
                @error('visibility') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('channel-sale.my-listings') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50">
                    <span wire:loading.remove>Create Listing</span>
                    <span wire:loading>Creating...</span>
                </button>
            </div>
        </form>
    </div>
</div>