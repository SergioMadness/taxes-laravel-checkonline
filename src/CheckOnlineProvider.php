<?php namespace professionalweb\chekonline;

use Illuminate\Support\ServiceProvider;
use professionalweb\chekonline\services\CheckOnline;
use professionalweb\chekonline\interfaces\CheckOnline as ICheckOnline;

class CheckOnlineProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->app->bind(ICheckOnline::class, function () {
            return new CheckOnline(
                config('chekonline.url'),
                config('chekonline.cert'),
                config('chekonline.key')
            );
        });
    }
}
