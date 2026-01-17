<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
}
);


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});
// ->middleware('auth:sanctum');


















// Route::get('/login', function () {
//     return view(view: 'auth.login');
// })->name('login');
// Route::get('/admin/dashboard', function () {
//     return view('admin.dashboard');
// });
// Route::get('/admin/dashboard', function () {
//     return view('admin.dashboard');
// })->middleware('auth:sanctum'); // اختيارياً حماية الرابط برمجياً أيضاً

// routes/web.php