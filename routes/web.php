<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Staff;
use App\Http\Controllers\Adopter;
use App\Http\Controllers\Vet;
use App\Http\Controllers\NotificationController;

// ─────────────────────────────────────────────
// PUBLIC ROUTES
// ─────────────────────────────────────────────
Route::get('/', function () {
    return view('landing');
})->name('home');

// ─────────────────────────────────────────────
// AUTHENTICATED ROUTES — redirect to role dashboard
// ─────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin())   return redirect()->route('admin.dashboard');
        if ($user->isStaff())   return redirect()->route('staff.dashboard');
        if ($user->isVet())     return redirect()->route('vet.dashboard');
        if ($user->isAdopter()) return redirect()->route('adopter.dashboard');
        abort(403, 'No role assigned. Contact administrator.');
    })->name('dashboard');

    // ─── Notifications ───
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');

    // ─────────────────────────────────────────────
    // ADMIN ROUTES
    // ─────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', Admin\UserController::class);
        Route::post('users/{user}/assign-role', [Admin\UserController::class, 'assignRole'])->name('users.assignRole');
        Route::post('users/{user}/toggle-status', [Admin\UserController::class, 'toggleStatus'])->name('users.toggleStatus');

        // Pet Categories
        Route::resource('categories', Admin\CategoryController::class);

        // Breeds
        Route::resource('breeds', Admin\BreedController::class);

        // Pets (admin oversight)
        Route::get('pets', [Admin\PetController::class, 'index'])->name('pets.index');
        Route::post('pets/{pet}/approve', [Admin\PetController::class, 'approve'])->name('pets.approve');
        Route::post('pets/{pet}/reject', [Admin\PetController::class, 'reject'])->name('pets.reject');

        // Applications (admin oversight)
        Route::get('applications', [Admin\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [Admin\ApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{application}/approve', [Admin\ApplicationController::class, 'approve'])->name('applications.approve');
        Route::patch('applications/{application}/reject', [Admin\ApplicationController::class, 'reject'])->name('applications.reject');
        Route::patch('applications/{application}/review', [Admin\ApplicationController::class, 'review'])->name('applications.review');

        // Payments
        Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [Admin\PaymentController::class, 'show'])->name('payments.show');

        // Reports
        Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');

        // Activity Logs
        Route::get('activity-logs', [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // ─────────────────────────────────────────────
    // STAFF ROUTES
    // ─────────────────────────────────────────────
    Route::prefix('staff')->name('staff.')->middleware('role:admin,staff')->group(function () {

        Route::get('/dashboard', [Staff\DashboardController::class, 'index'])->name('dashboard');

        // Pets
        Route::resource('pets', Staff\PetController::class);
        Route::post('pets/{pet}/update-status', [Staff\PetController::class, 'updateStatus'])->name('pets.updateStatus');

        // Applications
        Route::get('applications', [Staff\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [Staff\ApplicationController::class, 'show'])->name('applications.show');
        Route::post('applications/{application}/review', [Staff\ApplicationController::class, 'review'])->name('applications.review');
        Route::post('applications/{application}/schedule-interview', [Staff\ApplicationController::class, 'scheduleInterview'])->name('applications.scheduleInterview');

        // Payments
        Route::resource('payments', Staff\PaymentController::class)->except(['destroy']);
    });

    // ─────────────────────────────────────────────
    // VET ROUTES
    // ─────────────────────────────────────────────
    Route::prefix('vet')->name('vet.')->middleware('role:admin,vet')->group(function () {

        Route::get('/dashboard', [Vet\DashboardController::class, 'index'])->name('dashboard');

        // Medical Records
        Route::get('pets', [Vet\PetController::class, 'index'])->name('pets.index');
        Route::get('pets/{pet}', [Vet\PetController::class, 'show'])->name('pets.show');
        Route::resource('pets.medical-records', Vet\MedicalRecordController::class)->shallow();
        Route::resource('pets.vaccinations', Vet\VaccinationController::class)->shallow();

        // Approve/mark fitness
        Route::post('pets/{pet}/mark-fit', [Vet\PetController::class, 'markFit'])->name('pets.markFit');
        Route::post('pets/{pet}/mark-unfit', [Vet\PetController::class, 'markUnfit'])->name('pets.markUnfit');
    });

    // ─────────────────────────────────────────────
    // ADOPTER ROUTES
    // ─────────────────────────────────────────────
    Route::prefix('adopter')->name('adopter.')->middleware('role:adopter')->group(function () {

        Route::get('/dashboard', [Adopter\DashboardController::class, 'index'])->name('dashboard');

        // Browse pets
        Route::get('pets', [Adopter\PetController::class, 'index'])->name('pets.index');
        Route::get('pets/{pet}', [Adopter\PetController::class, 'show'])->name('pets.show');

        // Applications
        Route::get('applications', [Adopter\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/create/{pet}', [Adopter\ApplicationController::class, 'create'])->name('applications.create');
        Route::post('applications', [Adopter\ApplicationController::class, 'store'])->name('applications.store');
        Route::get('applications/{application}', [Adopter\ApplicationController::class, 'show'])->name('applications.show');
        Route::post('applications/{application}/withdraw', [Adopter\ApplicationController::class, 'withdraw'])->name('applications.withdraw');

        // Profile
        Route::get('profile', [Adopter\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [Adopter\ProfileController::class, 'update'])->name('profile.update');

        // Return
        // Return
        Route::post('applications/{application}/return', [Adopter\ApplicationController::class, 'returnPet'])
        ->name('applications.return');
    });
});

// Breeze Auth Routes
require __DIR__.'/auth.php';