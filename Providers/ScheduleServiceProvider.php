<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

use Log;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
         $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->call(function () {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => url(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@publishItemPromotion')),
                  CURLOPT_ENCODING => "",
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_RETURNTRANSFER => true,
                ));

                $response = curl_exec($curl);

                curl_close($curl);
            })
            ->onFailure(function () {
                Log::info('Gagal Update Data Shopee');
            })
            ->everyTenMinutes();;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
