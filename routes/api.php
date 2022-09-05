<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {

// });

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Users
    Route::resource('users', UserController::class)->except('edit', 'create');
    Route::put('/users/activation/{user}', [UserController::class, 'activation']);

    // Roles and Permissions
    Route::resource('roles', RoleController::class)->except('edit');
    Route::get('/permissions', [RoleController::class, 'permissions']);

    // Stores
    Route::resource('stores', StoreController::class)->except('edit', 'create');

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except('edit', 'create');

    // Customers
    Route::resource('customers', CustomerController::class)->except('edit', 'create');

    // Product Categories
    Route::resource('product_categories', ProductCategoryController::class)->except('edit', 'create');

    // Product and Inventory
    Route::resource('products', ProductController::class)->except('edit', 'create');
    Route::resource('inventories', InventoryController::class)->except('create');


    // Sales Routes
    Route::get('/sale_products', [SaleController::class, 'sale_products']);
    Route::get('/sale_customers', [SaleController::class, 'sale_customers']);
    Route::resource('sales', SaleController::class)->except('edit', 'create');
    Route::get('/sales_totals', [SaleController::class, 'total_amounts']);


    // Expense
    Route::resource('expense_categories', ExpenseCategoryController::class)->except('edit', 'create');
    Route::get('/expenses_totals', [ExpenseController::class, 'total_amounts']);

    Route::resource('expenses', ExpenseController::class)->except('edit', 'create');


    // Auth User
    Route::get('/user', [AuthenticatedSessionController::class, 'authenticated']);
});
