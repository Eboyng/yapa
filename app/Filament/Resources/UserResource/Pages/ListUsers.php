<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users')
                ->badge(User::count()),
            'admins' => Tab::make('Admins')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_admin', true))
                ->badge(User::where('is_admin', true)->count()),
            'flagged' => Tab::make('Flagged')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_flagged_for_ads', true))
                ->badge(User::where('is_flagged_for_ads', true)->count()),
            'verified' => Tab::make('Verified')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('email_verified_at'))
                ->badge(User::whereNotNull('email_verified_at')->count()),
            'unverified' => Tab::make('Unverified')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('email_verified_at'))
                ->badge(User::whereNull('email_verified_at')->count()),
        ];
    }
}