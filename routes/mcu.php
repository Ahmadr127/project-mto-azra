<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Mcu\Registration\McuRegistrationController;
use App\Http\Controllers\Mcu\Examination\McuExaminationController;
use App\Http\Controllers\Mcu\Resume\McuResumeController;

// Registrasi
Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
Route::resource('patients', PatientController::class);
Route::resource('mcu-registrations', McuRegistrationController::class);

// Pemeriksaan
Route::prefix('mcu-examinations')->name('mcu-examinations.')->group(function () {
    // Lab
    Route::get('lab', [McuExaminationController::class, 'labIndex'])->name('lab.index');
    Route::get('lab/{mcuRegistration}/form', [McuExaminationController::class, 'labForm'])->name('lab.form');
    Route::post('lab/{mcuRegistration}/store', [McuExaminationController::class, 'labStore'])->name('lab.store');

    // Radiology
    Route::get('radiology', [McuExaminationController::class, 'radiologyIndex'])->name('radiology.index');
    Route::get('radiology/{mcuRegistration}/form', [McuExaminationController::class, 'radiologyForm'])->name('radiology.form');
    Route::post('radiology/{mcuRegistration}/store', [McuExaminationController::class, 'radiologyStore'])->name('radiology.store');

    // Anamnesis
    Route::get('anamnesis', [McuExaminationController::class, 'anamnesisIndex'])->name('anamnesis.index');
    Route::get('anamnesis/{mcuRegistration}/form', [McuExaminationController::class, 'anamnesisForm'])->name('anamnesis.form');
    Route::post('anamnesis/{mcuRegistration}/store', [McuExaminationController::class, 'anamnesisStore'])->name('anamnesis.store');

    // Physical
    Route::get('physical', [McuExaminationController::class, 'physicalIndex'])->name('physical.index');
    Route::get('physical/{mcuRegistration}/form', [McuExaminationController::class, 'physicalForm'])->name('physical.form');
    Route::post('physical/{mcuRegistration}/store', [McuExaminationController::class, 'physicalStore'])->name('physical.store');

    // Doctor
    Route::get('doctor', [McuExaminationController::class, 'doctorIndex'])->name('doctor.index');
    Route::get('doctor/{mcuRegistration}/form', [McuExaminationController::class, 'doctorForm'])->name('doctor.form');
    Route::post('doctor/{mcuRegistration}/store', [McuExaminationController::class, 'doctorStore'])->name('doctor.store');
});

Route::middleware(['web', 'auth'])->group(function () {
    // Resume & PDF
    Route::get('mcu-resumes', [McuResumeController::class, 'index'])->name('mcu-resumes.index');
    Route::get('mcu-resumes/{mcuRegistration}/show', [McuResumeController::class, 'show'])->name('mcu-resumes.show');
    Route::post('mcu-resumes/{mcuRegistration}/store', [McuResumeController::class, 'store'])->name('mcu-resumes.store');
    Route::get('mcu-resumes/{mcuRegistration}/pdf', [McuResumeController::class, 'printPdf'])->name('mcu-resumes.pdf');
});