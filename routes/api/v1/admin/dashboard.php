<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\DashboardController;

Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('admin.dashboard.stats');
