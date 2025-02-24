<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\JobFrontController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TokenController;
use App\Models\Candidate;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


Route::get('/', function () {
    return view('pages.guest.index');
});

// routes/web.php
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');


    Route::middleware('auth')->group(function () {
        // Route::get('verify-email', EmailVerificationPromptController::class)
        //             ->name('verification.notice');

        // Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        //             ->middleware(['signed', 'throttle:6,1'])
        //             ->name('verification.verify');

        // Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        //             ->middleware('throttle:6,1')
        //             ->name('verification.send');

        // Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        //             ->name('password.confirm');

        // Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        // Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                    ->name('logout');
    });


// Route::get('/jobs', [JobFrontController::class, 'FrontJobList'])->name('frontjoblist');
// Route::get('/apply/{job_id}', [JobFrontController::class, 'apply'])->name('apply');
// Route::post('/user-apply/{job_id}', [JobFrontController::class, 'user_apply'])->name('user_apply');
// Route::post('/guest_apply/{job_id}', [JobFrontController::class, 'guest_apply'])->name('guest_apply');
// Route::get('/applied_edit/{id}', [JobFrontController::class, 'applied_edit'])->name('applied_edit');
// Route::delete('/applied_destroy/{id}', [JobFrontController::class, 'applied_distroy'])->name('applied_destroy');
// Route::get('/view-applied', [JobFrontController::class, 'view_applied'])->name('view-applied');

// Route::middleware(['auth'])->group(function () {



//     Route::middleware(['role:organization|super-admin'])->group(function () {
//         //Candidate selection and token generater routes
//         Route::get('/generate-token', [TokenController::class, 'generateToken'])->name('generate-token');
//         Route::get('/applier_candidates/{id}', [ApplicationController::class, 'index'])->name('applier_candidates');
//         Route::get('/view_candidates/{form_id}', [ApplicationController::class, 'view'])->name('view_candidates');
//         Route::put('/select_candidate/{id}', [ApplicationController::class, 'select'])->name('select_candidate');

//         //invitation Controller Routes
//         Route::get('/invite', [InviteController::class, 'create'])->name('invite_create');
//         Route::post('/invite-sent', [InviteController::class, 'store'])->name('invite_sent');
//         Route::get('/accept-invitation/{token}', [InviteController::class, 'accept_invitation'])->name('accept_invitation');
//         Route::post('/invitation-registred', [InviteController::class, 'store_invitation'])->name('store_invitation');
//         //permission Contoller Routes
//         Route::resource('employee', EmployeeController::class);
//         Route::get('user-select', [PermissionController::class, 'userSelect'])->name('user-select');
//         Route::get('role-select', [PermissionController::class, 'roleSelect'])->name('role-select');
//         Route::any('user-permission', [PermissionController::class, 'userPermission'])->name('user-permission');
//         Route::post('role-permission', [PermissionController::class, 'rolePermission'])->name('role-permission');
//         Route::PUT('user-permission-set/{id}', [PermissionController::class, 'userPermissionSet'])->name('user-permission-set');
//         Route::PUT('role-permission-set/{id}', [PermissionController::class, 'rolePermissionSet'])->name('role-permission-set');
//     });

//     Route::middleware(['role:super-admin'])->group(function () {
//         //Job Crud Control for Admin
//         Route::get('org-jobs/{id}', [JobController::class, 'indexForAdmin'])->name('org-jobs');
//         Route::get('org-job-create/{id}', [JobController::class, 'adminCreate'])->name('org-job-create');
//         Route::post('org-job-store', [JobController::class, 'adminStore'])->name('org-job-store');
//         Route::get('org-job-edit/{job_id}/{id}', [JobController::class, 'adminEdit'])->name('org-job-edit');
//         Route::PUT('org-job-update/{id}', [JobController::class, 'Update'])->name('org-job-update');
//         Route::delete('org-job-destroy/{job_id}/{id}', [JobController::class, 'adminDestroy'])->name('org-job-destroy');
//         //Employee CRUD Control for Admin
//         Route::get('org-employees/{id}', [EmployeeController::class, 'indexForAdmin'])->name('org-employees');
//         Route::get('org-employee-create/{id}', [EmployeeController::class, 'adminCreate'])->name('org-employee-create');
//         Route::post('org-employee-store', [EmployeeController::class, 'adminStore'])->name('org-employee-store');
//         Route::get('org-employee-edit/{emp_id}/{id}', [EmployeeController::class, 'adminEdit'])->name('org-employee-edit');
//         Route::PUT('org-employee-update/{id}', [EmployeeController::class, 'Update'])->name('org-employee-update');
//         Route::delete('org-employee-destroy/{emp_id}/{id}', [EmployeeController::class, 'adminDestroy'])->name('org-employee-destroy');
//         //CRUD for organization, categories
//         Route::resource('organization', OrganizationController::class);
//         Route::resource('categories', CategoriesController::class);
//         Route::resource('job', JobController::class);
//     });

//     // Job Route Controller
//     Route::middleware(['role:organization|employee|super-admin'])->group(function () {
//         Route::resource('job', JobController::class);
//     });
// });




// Route::get('/dashboard', function () {
//     return view('pages.controlpanel.dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::post('/profile', [ProfileController::class, 'upload'])->name('profile-upload');
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

