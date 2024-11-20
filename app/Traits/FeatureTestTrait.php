<?php

namespace App\Traits;

use Illuminate\Support\Facades\Artisan;

trait FeatureTestTrait
{
    protected function setUp(): void
    {
        parent::setUp();

        if ($this->hasPendingMigrations()) {
            // Run the database migrations
            print_r('Migrate' . "\n");

            Artisan::call('migrate');
        }
    }

    protected function hasPendingMigrations(): bool
    {
        Artisan::call('migrate', ['--pretend' => true, '--force' => true]);

        return !str_contains(trim(Artisan::output()), 'Nothing to migrate');
    }
}
