<?php

use Illuminate\Support\Facades\Route;

// ─── Public / Customer Routes ─────────────────────────────────────────────
Route::get('/', [\App\Http\Controllers\Customer\HomeController::class, 'index'])->name('home');
Route::get('/hotels/search', [\App\Http\Controllers\Customer\SearchController::class, 'index'])->name('hotels.search');
Route::get('/hotels/{hotel:slug}', [\App\Http\Controllers\Customer\HotelController::class, 'show'])->name('hotels.show');

// ─── Auth Routes (Breeze) ──────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Authenticated Customer Routes ────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {

    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Customer\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Customer\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/book/{room}', [\App\Http\Controllers\Customer\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/book/{room}', [\App\Http\Controllers\Customer\BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{booking}/upload-proof', [\App\Http\Controllers\Customer\BookingController::class, 'uploadProof'])->name('bookings.upload-proof');
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\Customer\BookingController::class, 'cancel'])->name('bookings.cancel');

    // Reviews
    Route::post('/bookings/{booking}/review', [\App\Http\Controllers\Customer\ReviewController::class, 'store'])->name('reviews.store');

    // Wishlist
    Route::get('/wishlist', [\App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{hotel}/toggle', [\App\Http\Controllers\Customer\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Hotel Owner Routes ────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:hotel_owner'])->prefix('owner')->name('owner.')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('dashboard');

    // Hotels
    Route::resource('hotels', \App\Http\Controllers\Owner\HotelController::class)->except(['show']);
    Route::delete('/hotels/images/{image}', [\App\Http\Controllers\Owner\HotelController::class, 'deleteImage'])->name('hotels.images.delete');

    // Rooms (nested under hotel)
    Route::prefix('hotels/{hotel}/rooms')->name('hotels.rooms.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Owner\RoomController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Owner\RoomController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Owner\RoomController::class, 'store'])->name('store');
        Route::get('/{room}/edit', [\App\Http\Controllers\Owner\RoomController::class, 'edit'])->name('edit');
        Route::put('/{room}', [\App\Http\Controllers\Owner\RoomController::class, 'update'])->name('update');
        Route::delete('/{room}', [\App\Http\Controllers\Owner\RoomController::class, 'destroy'])->name('destroy');
    });

    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Owner\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Owner\BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [\App\Http\Controllers\Owner\BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/check-in', [\App\Http\Controllers\Owner\BookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('/bookings/{booking}/complete', [\App\Http\Controllers\Owner\BookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\Owner\BookingController::class, 'cancel'])->name('bookings.cancel');

    // Reviews — owner reply
    Route::post('/reviews/{review}/reply', [\App\Http\Controllers\Customer\ReviewController::class, 'replyOwner'])->name('reviews.reply');

    // Reports & Expenses
    Route::get('/reports', [\App\Http\Controllers\Owner\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/expense', [\App\Http\Controllers\Owner\ReportController::class, 'addExpense'])->name('reports.expense');
    Route::get('/reports/export-pdf', [\App\Http\Controllers\Owner\ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// ─── Super Admin Routes ────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/approve-owner', [\App\Http\Controllers\Admin\UserController::class, 'approveOwner'])->name('users.approve-owner');
    Route::post('/users/{user}/reject-owner', [\App\Http\Controllers\Admin\UserController::class, 'rejectOwner'])->name('users.reject-owner');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Hotels
    Route::get('/hotels', [\App\Http\Controllers\Admin\HotelController::class, 'index'])->name('hotels.index');
    Route::get('/hotels/{hotel}', [\App\Http\Controllers\Admin\HotelController::class, 'show'])->name('hotels.show');
    Route::post('/hotels/{hotel}/approve', [\App\Http\Controllers\Admin\HotelController::class, 'approve'])->name('hotels.approve');
    Route::post('/hotels/{hotel}/reject', [\App\Http\Controllers\Admin\HotelController::class, 'reject'])->name('hotels.reject');
    Route::post('/hotels/{hotel}/suspend', [\App\Http\Controllers\Admin\HotelController::class, 'suspend'])->name('hotels.suspend');
    Route::post('/hotels/{hotel}/feature', [\App\Http\Controllers\Admin\HotelController::class, 'feature'])->name('hotels.feature');
    Route::delete('/hotels/{hotel}', [\App\Http\Controllers\Admin\HotelController::class, 'destroy'])->name('hotels.destroy');

    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');

    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/flag', [\App\Http\Controllers\Admin\ReviewController::class, 'flag'])->name('reviews.flag');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/export-excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('reports.excel');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

});

// ─── Role-based redirect after login ──────────────────────────────────────
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isSuperAdmin())  return redirect()->route('admin.dashboard');
    if ($user->isHotelOwner())  return redirect()->route('owner.dashboard');
    return redirect()->route('customer.bookings.index');
})->name('dashboard');

// ─── Global Authenticated Routes ──────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::get('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['action'] ?? url('/'));
    })->name('notifications.read');

    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.mark-all-read');
});

