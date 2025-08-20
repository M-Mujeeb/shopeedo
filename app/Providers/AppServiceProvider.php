<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        // <i class="las la-barcode aiz-side-nav-icon"></i>

        Blade::directive('barcode_menu', function ($expression) {
            return '
        <li class="aiz-side-nav-item">
            <a href="' . route('barcode.index') . '" class="aiz-side-nav-link">
              <i class="las la-barcode aiz-side-nav-icon"></i>
                <span class="aiz-side-nav-text">' . translate('Barcodes') . '</span>
            </a>
        </li>
        ';
        });
        Blade::directive('seller_barcode_menu', function ($expression) {
            // $userId = auth()->user()->id;

            return '
        <li class="aiz-side-nav-item">
            <a href="' . route('barcode.seller') . '" class="aiz-side-nav-link">
              <i class="las la-barcode aiz-side-nav-icon"></i>
                <span class="aiz-side-nav-text">' . translate('Barcodes') . '</span>
            </a>
        </li>
        ';
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sms', function ($app) {
            return new \App\Helpers\SmsHelper();
        });
    }
}
