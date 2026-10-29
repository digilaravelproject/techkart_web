<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('testament:setup', function () {
    $this->info('Starting 1-Click Testament Shopify Theme Setup...');
    $this->call(\Database\Seeders\TestamentThemeSeeder::class);
    $this->call('optimize:clear');
    $this->info('====================================================');
    $this->info('The Testament Shopify Theme is now ACTIVE & READY!');
    $this->info('====================================================');
})->purpose('1-Click Setup and Seeder for Testament Shopify Theme');
