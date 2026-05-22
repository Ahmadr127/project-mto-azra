<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\McuMedicalActionController;
use App\Http\Controllers\McuLabController;
use App\Http\Controllers\McuRadiologyController;
use App\Http\Controllers\McuAnamnesisController;
use App\Http\Controllers\McuPhysicalExamController;
use App\Http\Controllers\McuPackageController;

Route::resource('mcu-medical-actions', McuMedicalActionController::class);
Route::resource('mcu-labs', McuLabController::class);
Route::resource('mcu-radiologies', McuRadiologyController::class);
Route::resource('mcu-anamneses', McuAnamnesisController::class)->parameters([
    'mcu-anamneses' => 'mcuAnamnesis'
]);
Route::resource('mcu-physical-exams', McuPhysicalExamController::class);
Route::resource('mcu-packages', McuPackageController::class);
