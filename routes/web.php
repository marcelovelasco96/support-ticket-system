<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Perfil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- ADMIN ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/usuarios', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/sla', [TicketController::class, 'slaDashboard'])->name('admin.sla');

        // Cerrar ticket solo admin
        Route::post('/tickets/{ticket}/cerrar', [TicketController::class, 'close'])
            ->name('tickets.close');
    });

    // --- USUARIO (y admin para pruebas) ---
    Route::middleware('role:usuario|admin')->group(function () {
        Route::get('/tickets/crear', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('/mis-tickets', [TicketController::class, 'index'])->name('tickets.index');
    });

    // Ver detalle (acceso real lo controla el controller)
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    Route::post('/tickets/{ticket}/comentario', [TicketController::class, 'comment'])
    ->name('tickets.comment');

    // Resolver ticket (admin o t�cnico)
    Route::post('/tickets/{ticket}/resolver', [TicketController::class, 'resolve'])
        ->middleware('role:admin|tecnico')
        ->name('tickets.resolve');

    //Devolver ticket (t�cnico)
    Route::post('/tickets/{ticket}/devolver', [TicketController::class, 'release'])
    ->middleware('role:admin|tecnico')
    ->name('tickets.release');

    // --- T�CNICO ---
    Route::middleware('role:tecnico')->group(function () {
        Route::get('/soporte/bandeja', [TicketController::class, 'inbox'])->name('tickets.inbox');
        Route::post('/soporte/tickets/{ticket}/tomar', [TicketController::class, 'take'])->name('tickets.take');
	Route::get('/soporte/mis-tickets', [TicketController::class, 'myWork'])->name('tickets.mywork');
	Route::get('/soporte/historial', [TicketController::class, 'history'])->name('tickets.history');
    });

    Route::get('/admin/tickets', [TicketController::class, 'adminTickets'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.tickets');

    Route::get('/tickets/attachments/{attachment}', [\App\Http\Controllers\TicketAttachmentController::class, 'download'])
    ->name('tickets.attachments.download');

    Route::get('/tickets/attachments/{attachment}/view', [\App\Http\Controllers\TicketAttachmentController::class, 'view'])
    ->name('tickets.attachments.view');

    Route::delete('/tickets/attachments/{attachment}', [\App\Http\Controllers\TicketAttachmentController::class, 'destroy'])
    ->name('tickets.attachments.destroy');

});

// Auth routes (login, logout, etc.)
require __DIR__.'/auth.php';