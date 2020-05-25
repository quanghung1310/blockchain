<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServices();
        $this->registerHelpers();
        $this->morphMap();
        $this->fixMigrateMakeCommand();
    }

    private function registerServices()
    {
        foreach(config('app.services') as $name => $class) {
            $this->app->singleton($name, function () use ($class) {
                return new $class;
            });
        }
    }

    private function registerHelpers()
    {
        foreach(config('app.helpers') as $name => $class) {
            $this->app->singleton($name, function () use ($class) {
                return new $class;
            });
        }
    }

    private function morphMap()
    {
        Relation::morphMap(config('app.morph_map'));
    }

    private function fixMigrateMakeCommand()
    {
        $this->app->when(MigrationCreator::class)
            ->needs('$customStubPath')
            ->give(function ($app) {
                return $app->basePath('stubs');
            });
    }
}
