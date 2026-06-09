<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\DailyAssignment;

Schedule::call(function () {
    DailyAssignment::truncate(); 
})->dailyAt('13:00');