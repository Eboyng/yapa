<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    /**
     * Generate avatar URL for a user using UI Avatars API
     */
    public function generateAvatarUrl(User $user, array $options = []): string
    {
        $name = urlencode($user->name);
        $size = $options['size'] ?? 200;
        $background = $options['background'] ?? 'EBF4FF';
        $color = $options['color'] ?? '7F9CF5';
        $format = $options['format'] ?? 'png';
        $rounded = $options['rounded'] ?? true;
        $bold = $options['bold'] ?? false;
        $uppercase = $options['uppercase'] ?? true;
        
        $params = [
            'name' => $name,
            'size' => $size,
            'background' => $background,
            'color' => $color,
            'format' => $format,
        ];
        
        if ($rounded) {
            $params['rounded'] = 'true';
        }
        
        if ($bold) {
            $params['bold'] = 'true';
        }
        
        if ($uppercase) {
            $params['uppercase'] = 'true';
        }
        
        $queryString = http_build_query($params);
        
        return "https://ui-avatars.com/api/?{$queryString}";
    }
    
    /**
     * Generate avatar URL using DiceBear API (alternative)
     */
    public function generateDiceBearAvatar(User $user, string $style = 'avataaars', array $options = []): string
    {
        $seed = $options['seed'] ?? $user->email;
        $size = $options['size'] ?? 200;
        $backgroundColor = $options['backgroundColor'] ?? 'b6e3f4';
        
        $params = [
            'seed' => $seed,
            'size' => $size,
            'backgroundColor' => $backgroundColor,
        ];
        
        $queryString = http_build_query($params);
        
        return "https://api.dicebear.com/7.x/{$style}/svg?{$queryString}";
    }
    
    /**
     * Generate avatar using Gravatar
     */
    public function generateGravatarUrl(User $user, array $options = []): string
    {
        $email = strtolower(trim($user->email));
        $hash = md5($email);
        $size = $options['size'] ?? 200;
        $default = $options['default'] ?? 'identicon';
        $rating = $options['rating'] ?? 'g';
        
        $params = [
            's' => $size,
            'd' => $default,
            'r' => $rating,
        ];
        
        $queryString = http_build_query($params);
        
        return "https://www.gravatar.com/avatar/{$hash}?{$queryString}";
    }
    
    /**
     * Update user avatar with generated URL
     */
    public function updateUserAvatar(User $user, string $provider = 'ui-avatars', array $options = []): string
    {
        $avatarUrl = match ($provider) {
            'dicebear' => $this->generateDiceBearAvatar($user, $options['style'] ?? 'avataaars', $options),
            'gravatar' => $this->generateGravatarUrl($user, $options),
            default => $this->generateAvatarUrl($user, $options),
        };
        
        $user->update(['avatar' => $avatarUrl]);
        
        return $avatarUrl;
    }
    
    /**
     * Bulk update avatars for users without avatars
     */
    public function bulkUpdateAvatars(string $provider = 'ui-avatars', array $options = []): int
    {
        $users = User::whereNull('avatar')->orWhere('avatar', '')->get();
        $updated = 0;
        
        foreach ($users as $user) {
            try {
                $this->updateUserAvatar($user, $provider, $options);
                $updated++;
            } catch (\Exception $e) {
                // Log error but continue with other users
                \Log::error("Failed to update avatar for user {$user->id}: {$e->getMessage()}");
            }
        }
        
        return $updated;
    }
    
    /**
     * Download and store avatar locally (optional)
     */
    public function downloadAndStoreAvatar(User $user, string $avatarUrl): ?string
    {
        try {
            $response = Http::timeout(30)->get($avatarUrl);
            
            if ($response->successful()) {
                $extension = pathinfo(parse_url($avatarUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                $filename = "avatars/user_{$user->id}_" . Str::random(8) . ".{$extension}";
                
                Storage::disk('public')->put($filename, $response->body());
                
                $localUrl = Storage::disk('public')->url($filename);
                $user->update(['avatar' => $localUrl]);
                
                return $localUrl;
            }
        } catch (\Exception $e) {
            \Log::error("Failed to download avatar for user {$user->id}: {$e->getMessage()}");
        }
        
        return null;
    }
}