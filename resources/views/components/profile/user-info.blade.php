<div class="flex items-center space-x-3">
    <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-purple-600 rounded-full flex items-center justify-center">
        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
    </div>
    <div>
        <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
        <p class="text-sm text-gray-500">{{ $user->email }}</p>
    </div>
</div>