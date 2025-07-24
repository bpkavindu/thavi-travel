
<?php

use App\Http\Controllers\AiTourPlannerController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\AttractionMapController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\PhotoSpot;
use App\Http\Controllers\PhotoSpotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TourGuideController;
use App\Http\Controllers\TourPlansController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Qr');
});


Route::get('/admin', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/photo-spots', [PhotoSpotController::class, 'index'])->name('photo-spots.index');
    Route::post('/photo-spots', [PhotoSpotController::class, 'store'])->name('photo-spots.store');
    Route::get('/tourplanner', [AiTourPlannerController::class, 'index'])->name('tourplanner.index');
    Route::post('/tour-plan/basic-info', [AiTourPlannerController::class, 'store']);
    Route::get('/tour-plan/itinerary', [AiTourPlannerController::class, 'showItinerary'])->name('tour.itinerary');
    Route::get('/attraction', [AttractionController::class, 'index'])->name('attraction.index');
    Route::post('/attraction/store', [AttractionController::class, 'store'])->name('attraction.store');
    Route::get('/attractionsmap/map', [AttractionMapController::class, 'showMap']);
    Route::get('/users', [RegisteredUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [RegisteredUserController::class, 'update']);

    //    Route::get('/chat', [ChatController::class, 'index']);
    Route::post('/chat/send', [ChatController::class, 'store']);
    Route::get('/admin/chats', [AdminChatController::class, 'index'])->name('admin.chats');
    Route::get('/admin/chats/{user}', [AdminChatController::class, 'show'])->name('admin.chats.show');
    Route::post('/admin/chats/{user}', [AdminChatController::class, 'send'])->name('admin.chats.send');

    Route::get('/guides', [TourGuideController::class, 'index'])->name('guides.index');
    Route::post('/guides/{guide}', [TourGuideController::class, 'update']);

    // routes/web.php or routes/api.php

    Route::post('/tour-plans', [TourPlansController::class, 'store'])->name('tour-plans.store');
    Route::post('/tour-plans/{id}/update', [TourPlansController::class, 'update'])->name('tour-plans.update');
    Route::delete('/tour-plans/{id}', [TourPlansController::class, 'destroy'])->name('tour-plans.destroy');
    Route::get('/book-guide/{id}', [TourPlansController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::put('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);
    Route::put('/reservations/{id}/confirm', [ReservationController::class, 'confirm']);


});


require __DIR__ . '/auth.php';
