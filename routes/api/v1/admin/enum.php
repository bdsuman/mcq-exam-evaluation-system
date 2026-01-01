<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Common\EnumController;

Route::get('enums/gender', [EnumController::class, 'genderOptions'])->name('enums.gender');

