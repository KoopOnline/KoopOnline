<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SellerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/merken', [BrandController::class, 'index'])->name('brands');
Route::get('/merk/{brand}', [BrandController::class, 'show'])->name('brand');
Route::get('/verkopers', [SellerController::class, 'index'])->name('sellers');
Route::get('/verkoper/{seller}', [SellerController::class, 'show'])->name('seller');
Route::get('/categorie/{category}', [CategoryController::class, 'show'])->name('category');
Route::get('/product/{product_name}', [ProductController::class, 'index'])->name('product');
Route::get('/search/{search}', [SearchController::class, 'index'])->name('search');
