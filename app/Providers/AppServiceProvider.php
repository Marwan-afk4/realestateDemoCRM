<?php

namespace App\Providers;

use App\Models\BuyAppartmentInstallment;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\SellRequest;
use App\Models\User;
use App\Observers\DealObserver;
use App\Observers\LeadObserver;
use App\Observers\MortgageRequestObserver;
use App\Observers\SellRequestObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Lead::observe(LeadObserver::class);
        SellRequest::observe(SellRequestObserver::class);
        BuyAppartmentInstallment::observe(MortgageRequestObserver::class);
        Deal::observe(DealObserver::class);

        Gate::before(function (?User $user) {
            if ($user && $user->role === 'SuperAdmin') {
                return true;
            }

            return null;
        });
    }
}
