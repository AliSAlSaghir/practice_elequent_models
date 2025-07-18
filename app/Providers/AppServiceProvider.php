<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
  /**
   * Register any application services.
   */
  public function register(): void {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void {
    //Model::preventLazyLoading(! $this->app->isProduction()); to disable lazy loading in non production environments
    // Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction()); to throw error if user enters non fillable attribute
  }
}
