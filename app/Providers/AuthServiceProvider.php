<?php

namespace App\Providers;

use App\Models\ChannelAd;
use App\Models\ChannelAdApplication;
use App\Models\ChannelPurchase;
use App\Models\ChannelSale;
use App\Policies\ChannelAdApplicationPolicy;
use App\Policies\ChannelAdPolicy;
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
        ChannelAd::class => ChannelAdPolicy::class,
        ChannelAdApplication::class => ChannelAdApplicationPolicy::class,
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