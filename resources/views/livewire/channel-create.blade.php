<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Submit Your Channel</h1>
        <p class="text-gray-600">Share your WhatsApp channel with our community</p>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Error!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form wire:submit="submit" class="space-y-6 p-6">
            <!-- Channel Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Channel Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       wire:model="name" 
                       placeholder="Enter your channel name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Niche -->
            <div>
                <label for="niche" class="block text-sm font-medium text-gray-700 mb-1">
                    Niche <span class="text-red-500">*</span>
                </label>
                <select id="niche" 
                        wire:model="niche" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('niche') border-red-500 @enderror">
                    <option value="">Select a niche</option>
                    <option value="technology">Technology</option>
                    <option value="business">Business</option>
                    <option value="entertainment">Entertainment</option>
                    <option value="education">Education</option>
                    <option value="health">Health & Fitness</option>
                    <option value="lifestyle">Lifestyle</option>
                    <option value="news">News</option>
                    <option value="sports">Sports</option>
                    <option value="travel">Travel</option>
                    <option value="food">Food & Cooking</option>
                    <option value="fashion">Fashion</option>
                    <option value="finance">Finance</option>
                    <option value="gaming">Gaming</option>
                    <option value="music">Music</option>
                    <option value="art">Art & Design</option>
                    <option value="science">Science</option>
                    <option value="politics">Politics</option>
                    <option value="religion">Religion</option>
                    <option value="comedy">Comedy</option>
                    <option value="other">Other</option>
                </select>
                @error('niche')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Follower Count -->
            <div>
                <label for="follower_count" class="block text-sm font-medium text-gray-700 mb-1">
                    Follower Count <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="follower_count" 
                       wire:model="follower_count" 
                       placeholder="Enter number of followers"
                       min="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('follower_count') border-red-500 @enderror">
                @error('follower_count')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- WhatsApp Link -->
            <div>
                <label for="whatsapp_link" class="block text-sm font-medium text-gray-700 mb-1">
                    WhatsApp Channel Link <span class="text-red-500">*</span>
                </label>
                <input type="url" 
                       id="whatsapp_link" 
                       wire:model="whatsapp_link" 
                       placeholder="https://whatsapp.com/channel/..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('whatsapp_link') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Enter the invite link to your WhatsApp channel</p>
                @error('whatsapp_link')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" 
                          wire:model="description" 
                          rows="4" 
                          placeholder="Describe your channel content and what followers can expect..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sample Screenshot -->
            <div>
                <label for="sample_screenshot" class="block text-sm font-medium text-gray-700 mb-1">
                    Sample Screenshot <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md @error('sample_screenshot') border-red-500 @enderror">
                    <div class="space-y-1 text-center">
                        @if ($sample_screenshot)
                            <div class="mb-4">
                                <img src="{{ $sample_screenshot->temporaryUrl() }}" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                                <button type="button" wire:click="$set('sample_screenshot', null)" class="mt-2 text-sm text-red-600 hover:text-red-800">
                                    Remove
                                </button>
                            </div>
                        @else
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        @endif
                        <div class="flex text-sm text-gray-600">
                            <label for="sample_screenshot" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Upload a screenshot</span>
                                <input id="sample_screenshot" wire:model="sample_screenshot" type="file" accept="image/*" class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                    </div>
                </div>
                <p class="mt-1 text-sm text-gray-500">Upload a screenshot showing your channel content</p>
                @error('sample_screenshot')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Loading State -->
            <div wire:loading wire:target="sample_screenshot" class="text-sm text-blue-600">
                Uploading image...
            </div>

            <!-- Terms and Conditions -->
            <div class="bg-gray-50 p-4 rounded-md">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Submission Guidelines</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Your channel will be reviewed by our team before approval</li>
                    <li>• Ensure your channel content is appropriate and follows community guidelines</li>
                    <li>• Provide accurate information about your channel</li>
                    <li>• The screenshot should represent your actual channel content</li>
                    <li>• Spam or inappropriate channels will be rejected</li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('channels.index') }}" class="text-gray-600 hover:text-gray-800">
                    ← Back to Channels
                </a>
                
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:target="submit,sample_screenshot"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium py-2 px-6 rounded-md transition duration-200 flex items-center">
                    <span wire:loading.remove wire:target="submit">Submit Channel</span>
                    <span wire:loading wire:target="submit" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Submitting...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>