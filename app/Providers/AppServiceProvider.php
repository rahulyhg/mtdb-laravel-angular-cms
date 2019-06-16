<?php

namespace App\Providers;

use App\Services\Admin\GetAnalyticsHeaderData;
use App\Services\Data\Contracts\DataProvider;
use App\Services\Data\Contracts\NewsProviderInterface;
use App\Services\Data\Local\LocalDataProvider;
use App\Services\Data\News\ImdbNewsProvider;
use App\Services\Data\Tmdb\TmdbApi;
use Common\Admin\Analytics\Actions\GetAnalyticsHeaderDataAction;
use Common\Settings\Settings;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // bind data provider
        $concrete = app(Settings::class)->get('content.automation') ?
            TmdbApi::class :
            LocalDataProvider::class;

        $this->app->bind(
            DataProvider::class,
            $concrete
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // bind analytics
        $this->app->bind(
            GetAnalyticsHeaderDataAction::class,
            GetAnalyticsHeaderData::class
        );

        // bind news provider
        $this->app->bind(
            NewsProviderInterface::class,
            ImdbNewsProvider::class
        );
    }
}
