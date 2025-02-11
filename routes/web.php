<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use Faker\Factory as Faker;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin', function () {
        $contacts = [];
        $faker = Faker::create();
        for ($i = 1; $i <= 10; $i++) {
            $contacts[] = [
                'nama' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'title' => $faker->randomElement(['Software Engineer', 'Quality Assurance', 'Product Manager', 'Account Manager', 'Customer Service']),
                'job' => $faker->jobTitle,
                'role' => $faker->randomElement(['Owner', 'User', 'Admin']),
                'status' => $faker->randomElement(['active', 'inactive', 'pending']),
            ];
        };
        return view('admin.dashboard',['contacts' => $contacts]);
    })->name('admin.dashboard');

    Route::get('/forms', function () {
        return view('admin.forms');
    })->name('admin.forms');
    Route::get('/tables', function () {
        return view('admin.tables');
    })->name('admin.tables');
    Route::get('/ui-elements', function () {
        return view('admin.ui-elements');
    })->name('admin.ui-elements');
});

Route::group(['middleware' => ['permission:publish articles']], function () {});

// Group routes that need admin role and authentication
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
});
// Route::get('admin.dashboard', [UserController::class, 'passing_date'])->name('home');

require __DIR__ . '/auth.php';
