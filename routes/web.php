<?php

use App\Http\Controllers\Admin\ComponentController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\PembinaAssignmentController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pembina\EvaluationController;
use App\Http\Controllers\Pembina\ProgressController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Superadmin\AuditLogController;
use App\Http\Controllers\Superadmin\EvaluationLockController;
use App\Http\Controllers\Superadmin\ImpersonationController;
use App\Http\Controllers\Superadmin\PermissionController;
use App\Http\Controllers\Superadmin\SettingsController;
use App\Http\Controllers\Superadmin\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// Auth routes (using Laravel Breeze/Fortify later, simple login for now)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/sso/login', [SsoController::class, 'redirect'])->name('sso.login');
Route::get('/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');

Route::post('/login', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (auth()->attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ]);
})->name('login.post');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {
    // Dashboard (role-based)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        // Pembina Assignments
        Route::get('pembina-assignments', [PembinaAssignmentController::class, 'index'])
            ->middleware('permission:subject.create,subject.edit,subject.deactivate,component.create,component.edit,component.toggle_active,component.adjust_weight')
            ->name('pembina-assignments.index');
        Route::put('pembina-assignments/{subject}', [PembinaAssignmentController::class, 'update'])
            ->middleware('permission:subject.edit')
            ->name('pembina-assignments.update');

        // Programs
        Route::get('programs', [ProgramController::class, 'index'])
            ->middleware('permission:program.edit')
            ->name('programs.index');
        Route::get('programs/create', [ProgramController::class, 'create'])
            ->middleware('permission:program.create')
            ->name('programs.create');
        Route::post('programs', [ProgramController::class, 'store'])
            ->middleware('permission:program.create')
            ->name('programs.store');
        Route::get('programs/{program}', [ProgramController::class, 'show'])
            ->middleware('permission:program.edit')
            ->name('programs.show');
        Route::get('programs/{program}/edit', [ProgramController::class, 'edit'])
            ->middleware('permission:program.edit')
            ->name('programs.edit');
        Route::put('programs/{program}', [ProgramController::class, 'update'])
            ->middleware('permission:program.edit')
            ->name('programs.update');
        Route::delete('programs/{program}', [ProgramController::class, 'destroy'])
            ->middleware('permission:program.deactivate')
            ->name('programs.destroy');
        Route::post('programs/{program}/toggle-status', [ProgramController::class, 'toggleStatus'])
            ->middleware('permission:program.deactivate')
            ->name('programs.toggle-status');

        // Subjects
        Route::get('subjects', [SubjectController::class, 'index'])
            ->middleware('permission:subject.create,subject.edit,subject.deactivate,component.create,component.edit,component.toggle_active,component.adjust_weight')
            ->name('subjects.index');
        Route::get('subjects/create', [SubjectController::class, 'create'])
            ->middleware('permission:subject.create')
            ->name('subjects.create');
        Route::post('subjects', [SubjectController::class, 'store'])
            ->middleware('permission:subject.create')
            ->name('subjects.store');
        Route::get('subjects/{subject}', [SubjectController::class, 'show'])
            ->middleware('permission:subject.edit')
            ->name('subjects.show');
        Route::get('subjects/{subject}/edit', [SubjectController::class, 'edit'])
            ->middleware('permission:subject.edit')
            ->name('subjects.edit');
        Route::put('subjects/{subject}', [SubjectController::class, 'update'])
            ->middleware('permission:subject.edit')
            ->name('subjects.update');
        Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])
            ->middleware('permission:subject.deactivate')
            ->name('subjects.destroy');
        Route::post('subjects/{subject}/toggle-status', [SubjectController::class, 'toggleStatus'])
            ->middleware('permission:subject.deactivate')
            ->name('subjects.toggle-status');

        // Components (nested under subjects)
        Route::prefix('subjects/{subject}/components')->name('subjects.components.')->group(function () {
            Route::get('/', [ComponentController::class, 'index'])
                ->middleware('permission:component.edit')
                ->name('index');
            Route::get('/create', [ComponentController::class, 'create'])
                ->middleware('permission:component.create')
                ->name('create');
            Route::post('/', [ComponentController::class, 'store'])
                ->middleware('permission:component.create')
                ->name('store');
            Route::get('/{component}/edit', [ComponentController::class, 'edit'])
                ->middleware('permission:component.edit')
                ->name('edit');
            Route::put('/{component}', [ComponentController::class, 'update'])
                ->middleware('permission:component.edit')
                ->name('update');
            Route::delete('/{component}', [ComponentController::class, 'destroy'])
                ->middleware('permission:component.toggle_active')
                ->name('destroy');
            Route::post('/{component}/toggle-status', [ComponentController::class, 'toggleStatus'])
                ->middleware('permission:component.toggle_active')
                ->name('toggle-status');
            Route::post('/reorder', [ComponentController::class, 'reorder'])
                ->middleware('permission:component.edit')
                ->name('reorder');
        });

        // Enrollments
        Route::get('enrollments', [EnrollmentController::class, 'index'])
            ->middleware('permission:enrollment.assign_subject')
            ->name('enrollments.index');
        Route::get('enrollments/create', [EnrollmentController::class, 'create'])
            ->middleware('permission:enrollment.assign_subject')
            ->name('enrollments.create');
        Route::post('enrollments', [EnrollmentController::class, 'store'])
            ->middleware('permission:enrollment.assign_subject')
            ->name('enrollments.store');
        Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show'])
            ->middleware('permission:enrollment.assign_subject')
            ->name('enrollments.show');
        Route::post('enrollments/{enrollment}/toggle-status', [EnrollmentController::class, 'toggleStatus'])
            ->middleware('permission:enrollment.deactivate')
            ->name('enrollments.toggle-status');
        Route::post('enrollments/bulk-enroll', [EnrollmentController::class, 'bulkEnroll'])
            ->middleware('permission:enrollment.assign_subject')
            ->name('enrollments.bulk-enroll');

        // Students
        Route::get('students', [StudentController::class, 'index'])
            ->middleware('permission:student.edit_profile')
            ->name('students.index');
        Route::get('students/create', [StudentController::class, 'create'])
            ->middleware('permission:student.create')
            ->name('students.create');
        Route::post('students', [StudentController::class, 'store'])
            ->middleware('permission:student.create')
            ->name('students.store');
        Route::get('students/{student}/edit', [StudentController::class, 'edit'])
            ->middleware('permission:student.edit_profile')
            ->name('students.edit');
        Route::put('students/{student}', [StudentController::class, 'update'])
            ->middleware('permission:student.edit_profile')
            ->name('students.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Pembina Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('pembina')->name('pembina.')->middleware(['role:pembina,superadmin'])->group(function () {
        // Evaluations
        Route::get('evaluations', [EvaluationController::class, 'index'])
            ->middleware('permission:evaluation.view_all_scoped')
            ->name('evaluations.index');
        Route::get('evaluations/select-student', [EvaluationController::class, 'selectStudent'])
            ->middleware('permission:evaluation.create')
            ->name('evaluations.select-student');
        Route::get('evaluations/create', [EvaluationController::class, 'create'])
            ->middleware('permission:evaluation.create')
            ->name('evaluations.create');
        Route::post('evaluations', [EvaluationController::class, 'store'])
            ->middleware('permission:evaluation.create')
            ->name('evaluations.store');
        Route::get('evaluations/{evaluation}', [EvaluationController::class, 'show'])
            ->middleware('permission:evaluation.view_all_scoped')
            ->name('evaluations.show');
        Route::get('evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])
            ->middleware('permission:evaluation.edit_before_lock')
            ->name('evaluations.edit');
        Route::put('evaluations/{evaluation}', [EvaluationController::class, 'update'])
            ->middleware('permission:evaluation.edit_before_lock')
            ->name('evaluations.update');
        Route::post('evaluations/{evaluation}/lock', [EvaluationController::class, 'lock'])
            ->middleware('permission:evaluation.edit_before_lock')
            ->name('evaluations.lock');

        // Progress
        Route::get('progress', [ProgressController::class, 'index'])
            ->middleware('permission:analytics.view_trend')
            ->name('progress.index');
        Route::get('progress/subject/{subject}', [ProgressController::class, 'subject'])
            ->middleware('permission:analytics.compare_students')
            ->name('progress.subject');
        Route::get('progress/student/{enrollment}', [ProgressController::class, 'student'])
            ->middleware('permission:analytics.view_trend')
            ->name('progress.student');
        Route::get('progress/chart-data/{enrollment}', [ProgressController::class, 'chartData'])
            ->middleware('permission:analytics.view_trend')
            ->name('progress.chart-data');
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
        Route::get('dashboard', [StudentDashboardController::class, 'index'])
            ->middleware('permission:analytics.view_trend')
            ->name('dashboard');
        Route::get('progress/{enrollment}', [StudentDashboardController::class, 'progress'])
            ->middleware('permission:analytics.view_trend')
            ->name('progress');
        Route::get('progress/{enrollment}/evaluation/{evaluation}', [StudentDashboardController::class, 'evaluation'])
            ->middleware('permission:analytics.view_trend')
            ->name('evaluation');
    });

    /*
    |--------------------------------------------------------------------------
    | Superadmin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('superadmin')->name('superadmin.')->middleware(['role:superadmin'])->group(function () {
        // Permission Management
        Route::get('permissions', [PermissionController::class, 'index'])
            ->middleware('permission:access.manage_permissions')
            ->name('permissions.index');
        Route::put('permissions/{role}', [PermissionController::class, 'update'])
            ->middleware('permission:access.manage_permissions')
            ->name('permissions.update');
        Route::get('permissions/matrix', [PermissionController::class, 'matrix'])
            ->middleware('permission:access.manage_permissions')
            ->name('permissions.matrix');

        // Audit Logs
        Route::get('audit-logs', [AuditLogController::class, 'index'])
            ->middleware('permission:access.view_audit_log')
            ->name('audit-logs.index');
        Route::get('audit-logs/export', [AuditLogController::class, 'export'])
            ->middleware('permission:access.view_audit_log')
            ->name('audit-logs.export');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])
            ->middleware('permission:access.view_audit_log')
            ->name('audit-logs.show');

        // Evaluation Unlock (override)
        Route::post('evaluations/{evaluation}/unlock', [EvaluationLockController::class, 'unlock'])
            ->middleware('permission:access.manage_permissions')
            ->name('evaluations.unlock');

        // Impersonation
        Route::post('impersonate/{user}', [ImpersonationController::class, 'start'])
            ->middleware('permission:access.impersonate_user')
            ->name('impersonate.start');
        Route::post('impersonate/stop', [ImpersonationController::class, 'stop'])
            ->middleware('permission:access.impersonate_user')
            ->name('impersonate.stop');

        // Settings
        Route::get('settings', [SettingsController::class, 'index'])
            ->middleware('permission:access.manage_settings')
            ->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])
            ->middleware('permission:access.manage_settings')
            ->name('settings.update');

        // User Management
        Route::get('users', [UserManagementController::class, 'index'])
            ->middleware('permission:access.manage_users')
            ->name('users.index');
        Route::get('users/create', [UserManagementController::class, 'create'])
            ->middleware('permission:access.manage_users')
            ->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])
            ->middleware('permission:access.manage_users')
            ->name('users.store');
        Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])
            ->middleware('permission:access.manage_users')
            ->name('users.edit');
        Route::put('users/{user}', [UserManagementController::class, 'update'])
            ->middleware('permission:access.manage_users')
            ->name('users.update');
    });
});
