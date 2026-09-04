<?php

use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

// Ana Sayfa (Wireframe'e tam uyumlu ana sayfa)
Route::get('/', [RestaurantController::class, 'index'])->name('home');

// Tüm Restoranlar / Keşfet Sayfası
Route::get('/restaurants', [RestaurantController::class, 'list'])->name('restaurants.index');
Route::get('/kesfet', [RestaurantController::class, 'list'])->name('discover');

// Restoran Detay Sayfası
Route::get('/restaurant/{restaurant:slug}', [RestaurantController::class, 'show'])->name('restaurant.show');

// Restoran Özel Dijital Menü Sayfası
Route::get('/restaurant/{restaurant:slug}/menu', [RestaurantController::class, 'menu'])->name('restaurant.menu');

// Şube Anonim Puan & Yorum Ekleme
Route::post('/branches/{branch}/reviews', [RestaurantController::class, 'storeBranchReview'])->name('branches.reviews.store');

// Haritada Keşfet Sayfası
Route::get('/harita', [RestaurantController::class, 'mapView'])->name('map');

// Kategoriler Sayfası
Route::get('/kategoriler', [RestaurantController::class, 'categoriesView'])->name('categories');
