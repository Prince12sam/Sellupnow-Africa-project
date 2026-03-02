<?php

namespace App\Providers;

use App\Enums\OrderStatus;
use App\Enums\Roles;
use App\Models\GeneraleSetting;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Observers\NotificationObserver;
use App\Repositories\LanguageRepository;
use App\Repositories\ThemeColorRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        Notification::observe(NotificationObserver::class);

        if(request()->ip() != '127.0.0.1'){
            Schema::defaultStringLength(191);
            if (!file_exists(base_path('storage/installed')) && !request()->is('install') && !request()->is('install/*')) {
                header("Location: install");
                exit;
            }
        }

        View::composer('*', function ($view) {
            $viewData = $view->getData();

            $generaleSetting = $viewData['generaleSetting'] ?? null;
            if (!$generaleSetting) {
                try {
                    $generaleSetting = generaleSetting('setting');
                } catch (\Throwable $th) {
                    $generaleSetting = null;
                }
            }

            if (!array_key_exists('businessModel', $viewData)) {
                $view->with('businessModel', $generaleSetting?->business_based_on ?? 'single');
            }

            if (!array_key_exists('generaleSetting', $viewData)) {
                $view->with('generaleSetting', $generaleSetting);
            }

            if (!array_key_exists('languages', $viewData)) {
                try {
                    $languages = Cache::remember('languages', now()->addHours(12), function () {
                        return Language::query()->get();
                    });
                } catch (\Throwable $th) {
                    $languages = collect();
                }

                $view->with('languages', $languages);
            }

            if (!array_key_exists('seederRun', $viewData)) {
                $view->with('seederRun', false);
            }

            if (!array_key_exists('storageLink', $viewData)) {
                $view->with('storageLink', false);
            }
        });
    }
}
