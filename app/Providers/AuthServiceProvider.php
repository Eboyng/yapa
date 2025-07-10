<?php

namespace App\Providers;

use App\Models\ChannelPurchase;
use App\Models\ChannelSale;
use App\Policies\ChannelPurchasePolicy;
use App\Policies\ChannelSalePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ChannelSale::class => ChannelSalePolicy::class,
        ChannelPurchase::class => ChannelPurchasePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}