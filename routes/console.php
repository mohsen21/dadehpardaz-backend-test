<?php

use App\Jobs\ProcessPaymentJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    dispatch(new ProcessPaymentJob());
})->dailyAt(config('payment.auto_time', '10:00'));
