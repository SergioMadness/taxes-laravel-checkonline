<?php namespace professionalweb\taxes\checkonline;

use Illuminate\Support\ServiceProvider;
use professionalweb\taxes\interfaces\TaxFacade;
use professionalweb\taxes\checkonline\services\CheckOnline;
use professionalweb\taxes\checkonline\interfaces\CheckOnline as ICheckOnline;

class CheckOnlineProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        app(TaxFacade::class)->registerDriver(ICheckOnline::DRIVER_CHECKONLINE, CheckOnline::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ICheckOnline::class, function () {
            return new CheckOnline(
                config('chekonline.url', ''),
                config('chekonline.cert', ''),
                config('chekonline.key', ''),
                config('chekonline.device', 'auto')
            );
        });
    }
}
