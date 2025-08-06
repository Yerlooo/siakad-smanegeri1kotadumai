<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingController;

/*
|--------------------------------------------------------------------------
| Testing Routes
|--------------------------------------------------------------------------
|
| Routes untuk halaman pengujian fungsional aplikasi SIAKAD
|
*/

Route::prefix('testing')->name('testing.')->group(function () {
    // Halaman utama testing
    Route::get('/functional', function () {
        return view('testing.functional-test');
    })->name('functional');
    
    // API endpoints untuk testing
    Route::get('/api/session-status', [TestingController::class, 'sessionStatus'])->name('session.status');
    Route::post('/api/test-login', [TestingController::class, 'testLogin'])->name('test.login');
    Route::get('/api/test-performance/{endpoint}', [TestingController::class, 'testPerformance'])->name('test.performance');
    Route::post('/api/test-crud/{entity}', [TestingController::class, 'testCrud'])->name('test.crud');
    Route::get('/api/system-health', [TestingController::class, 'systemHealth'])->name('system.health');
    Route::post('/api/validate-input', [TestingController::class, 'validateInput'])->name('validate.input');
});

// Routes untuk testing individual modules (hanya dalam mode development)
if (app()->environment('local')) {
    Route::prefix('test')->name('test.')->group(function () {
        // Authentication testing
        Route::post('/auth/login', [TestingController::class, 'simulateLogin'])->name('auth.login');
        Route::post('/auth/logout', [TestingController::class, 'simulateLogout'])->name('auth.logout');
        Route::get('/auth/session', [TestingController::class, 'checkSession'])->name('auth.session');
        
        // CRUD testing endpoints
        Route::apiResource('/siswa', TestingController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('/guru', TestingController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('/kelas', TestingController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('/mata_pelajaran', TestingController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        
        // Validation testing
        Route::post('/validate/{type}', [TestingController::class, 'testValidation'])->name('validation');
        
        // Performance testing
        Route::get('/performance/memory', [TestingController::class, 'memoryUsage'])->name('performance.memory');
        Route::get('/performance/response-time', [TestingController::class, 'responseTime'])->name('performance.response');
        
        // Security testing
        Route::post('/security/xss', [TestingController::class, 'testXSS'])->name('security.xss');
        Route::post('/security/sql-injection', [TestingController::class, 'testSQLInjection'])->name('security.sql');
        Route::post('/security/csrf', [TestingController::class, 'testCSRF'])->name('security.csrf');
        
        // Integration testing
        Route::get('/integration/database', [TestingController::class, 'testDatabase'])->name('integration.database');
        Route::get('/integration/api', [TestingController::class, 'testAPI'])->name('integration.api');
    });
}
