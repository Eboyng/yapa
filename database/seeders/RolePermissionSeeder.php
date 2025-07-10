<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage permissions',
            'manage wallets',
            'view transactions',
            'manage transactions',
            'manage settings',
            'view audit logs',
            'manage ads',
            'manage batches',
            'manage channels',
            'moderate content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $moderatorRole->givePermissionTo([
            'moderate content',
            'view transactions',
            'manage ads',
            'manage batches',
            'manage channels',
        ]);
        
        $userRole->givePermissionTo([
            // Users have basic permissions by default
        ]);

        // Create admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@yapa.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'whatsapp_notifications_enabled' => true,
                'email_notifications_enabled' => true,
                // Wallets are automatically created with default balances via User::boot()
                'avatar' => 'https://ui-avatars.com/api/?name=Admin+User&color=7F9CF5&background=EBF4FF',
            ]
        );

        $adminUser->assignRole('admin');

        // Create moderator user if it doesn't exist
        $moderatorUser = User::firstOrCreate(
            ['email' => 'moderator@yapa.com'],
            [
                'name' => 'Moderator User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'whatsapp_notifications_enabled' => true,
                'email_notifications_enabled' => true,
                // Wallets are automatically created with default balances via User::boot()
                'avatar' => 'https://ui-avatars.com/api/?name=Moderator+User&color=10B981&background=D1FAE5',
            ]
        );

        $moderatorUser->assignRole('moderator');

        $this->command->info('Roles, permissions, and users created successfully!');
    }
}