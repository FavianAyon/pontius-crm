<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Lead;
use App\Policies\LeadPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\Development;
use App\Models\Listing;
use App\Models\DevelopmentUnit;
use App\Policies\DevelopmentPolicy;
use App\Policies\ListingPolicy;
use App\Policies\DevelopmentUnitPolicy;
use App\Models\CaseFile;
use App\Models\CaseFileDocument;
use App\Policies\CaseFilePolicy;
use App\Policies\CaseFileDocumentPolicy;

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
        Gate::policy(Lead::class, LeadPolicy::class);
        Gate::policy(Development::class, DevelopmentPolicy::class);
        Gate::policy(Listing::class, ListingPolicy::class);
        Gate::policy(DevelopmentUnit::class, DevelopmentUnitPolicy::class);
        Gate::policy(CaseFile::class, CaseFilePolicy::class);
        Gate::policy(CaseFileDocument::class, CaseFileDocumentPolicy::class);

    }
}
