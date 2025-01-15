<?php


use Illuminate\Support\Facades\Route;

Route::get('/', [\BasePackage\Controllers\BasePackageController::class, 'index']);

