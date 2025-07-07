<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AvatarService;
use App\Models\User;

class GenerateUserAvatars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatars:generate 
                            {--provider=ui-avatars : Avatar provider (ui-avatars, dicebear, gravatar)}
                            {--style=avataaars : Style for DiceBear avatars}
                            {--force : Force regenerate all avatars}
                            {--download : Download and store avatars locally}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate avatars for users using various avatar services';

    /**
     * Execute the console command.
     */
    public function handle(AvatarService $avatarService)
    {
        $provider = $this->option('provider');
        $style = $this->option('style');
        $force = $this->option('force');
        $download = $this->option('download');
        
        $this->info("Generating avatars using {$provider} provider...");
        
        if ($force) {
            $users = User::all();
            $this->info('Force mode: Updating avatars for all users');
        } else {
            $users = User::whereNull('avatar')->orWhere('avatar', '')->get();
            $this->info('Updating avatars for users without avatars');
        }
        
        if ($users->isEmpty()) {
            $this->info('No users found that need avatar updates.');
            return;
        }
        
        $this->info("Found {$users->count()} users to update.");
        
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();
        
        $updated = 0;
        $failed = 0;
        
        foreach ($users as $user) {
            try {
                $options = [];
                if ($provider === 'dicebear') {
                    $options['style'] = $style;
                }
                
                $avatarUrl = $avatarService->updateUserAvatar($user, $provider, $options);
                
                if ($download && $avatarUrl) {
                    $avatarService->downloadAndStoreAvatar($user, $avatarUrl);
                }
                
                $updated++;
            } catch (\Exception $e) {
                $failed++;
                $this->error("\nFailed to update avatar for user {$user->id} ({$user->email}): {$e->getMessage()}");
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        
        $this->newLine(2);
        $this->info("Avatar generation completed!");
        $this->info("Successfully updated: {$updated} users");
        
        if ($failed > 0) {
            $this->warn("Failed to update: {$failed} users");
        }
        
        $this->info("Provider used: {$provider}");
        
        if ($download) {
            $this->info("Avatars downloaded and stored locally.");
        }
    }
}
