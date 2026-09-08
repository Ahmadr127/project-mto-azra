<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mcu\Master\McuMedicalActionController;
use App\Http\Controllers\Mcu\Master\McuLabController;
use App\Http\Controllers\Mcu\Master\McuRadiologyController;
use App\Http\Controllers\Mcu\Master\McuAnamnesisController;
use App\Http\Controllers\Mcu\Master\McuPhysicalExamController;
use App\Http\Controllers\Mcu\Master\McuPackageController;

Route::resource('mcu-medical-actions', McuMedicalActionController::class);
Route::resource('mcu-labs', McuLabController::class);
Route::resource('mcu-radiologies', McuRadiologyController::class);
Route::resource('mcu-anamneses', McuAnamnesisController::class)->parameters([
    'mcu-anamneses' => 'mcuAnamnesis'
]);
Route::resource('mcu-physical-exams', McuPhysicalExamController::class);
Route::resource('mcu-packages', McuPackageController::class);
